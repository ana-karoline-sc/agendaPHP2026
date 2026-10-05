<?php

// ======================================================================
// SESSÃO
// ======================================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// ======================================================================
// CONEXÃO
// ======================================================================

require_once 'config/conexao.php';


// ======================================================================
// SE JÁ ESTIVER LOGADO
// ======================================================================

if (
    isset($_SESSION['id_user']) &&
    is_numeric($_SESSION['id_user'])
) {

    header(
        "Location: paginas/home.php?acao=bemvindo"
    );

    exit;
}


// ======================================================================
// MENSAGEM
// ======================================================================

$mensagem = '';

$tipoMensagem = '';


// ======================================================================
// PROCESSAR LOGIN
// ======================================================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['login'])
) {


    // ==================================================================
    // RECEBER DADOS
    // ==================================================================

    $email =
        trim(
            $_POST['email'] ?? ''
        );

    $senha =
        $_POST['senha'] ?? '';


    // ==================================================================
    // VALIDAR CAMPOS
    // ==================================================================

    if (
        $email === '' ||
        $senha === ''
    ) {

        $mensagem =
            'Todos os campos são obrigatórios.';

        $tipoMensagem =
            'danger';
    }


    // ==================================================================
    // VALIDAR E-MAIL
    // ==================================================================

    elseif (
        !filter_var(
            $email,
            FILTER_VALIDATE_EMAIL
        )
    ) {

        $mensagem =
            'Digite um endereço de e-mail válido.';

        $tipoMensagem =
            'danger';
    }


    // ==================================================================
    // CONSULTAR USUÁRIO
    // ==================================================================

    else {

        try {

            $sql = "
                SELECT
                    id_user,
                    nome_user,
                    email_user,
                    senha_user,
                    foto_user
                FROM tb_user
                WHERE email_user = :email
                LIMIT 1
            ";


            $stmt =
                $conect->prepare($sql);


            $stmt->bindValue(
                ':email',
                $email,
                PDO::PARAM_STR
            );


            $stmt->execute();


            $usuario =
                $stmt->fetch(
                    PDO::FETCH_ASSOC
                );


            // ==========================================================
            // USUÁRIO NÃO ENCONTRADO
            // ==========================================================

            if (!$usuario) {

                $mensagem =
                    'E-mail ou senha incorretos.';

                $tipoMensagem =
                    'danger';
            }


            // ==========================================================
            // VERIFICAR SENHA
            // ==========================================================

            elseif (
                !password_verify(
                    $senha,
                    $usuario['senha_user']
                )
            ) {

                $mensagem =
                    'E-mail ou senha incorretos.';

                $tipoMensagem =
                    'danger';
            }


            // ==========================================================
            // LOGIN CORRETO
            // ==========================================================

            else {


                // ------------------------------------------------------
                // REGENERAR ID DA SESSÃO
                // ------------------------------------------------------

                session_regenerate_id(true);


                // ------------------------------------------------------
                // NOVA SESSÃO
                // ------------------------------------------------------

                $_SESSION['id_user'] =
                    (int) $usuario['id_user'];


                $_SESSION['loginUser'] =
                    $usuario['email_user'];


                /*
                 * Mantemos senhaUser somente por compatibilidade
                 * com algum código antigo.
                 *
                 * O valor é o ID do usuário, NÃO a senha.
                 */

                $_SESSION['senhaUser'] =
                    (int) $usuario['id_user'];


                // ------------------------------------------------------
                // REDIRECIONAMENTO
                // ------------------------------------------------------

                header(
                    "Location: paginas/home.php?acao=bemvindo"
                );

                exit;
            }

        }
        catch (PDOException $e) {

            error_log(
                'ERRO DE LOGIN PDO: ' .
                $e->getMessage()
            );


            $mensagem =
                'Ocorreu um erro ao tentar fazer login. ' .
                'Tente novamente mais tarde.';

            $tipoMensagem =
                'danger';
        }
    }
}

?>


<!DOCTYPE html>

<html lang="pt-br">

<head>

    <meta charset="utf-8">

    <meta
        http-equiv="X-UA-Compatible"
        content="IE=edge"
    >

    <title>
        New Agenda 2.0 | Log in
    </title>

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >


    <!-- Font Awesome -->

    <link
        rel="stylesheet"
        href="plugins/fontawesome-free/css/all.min.css"
    >


    <!-- Ionicons -->

    <link
        rel="stylesheet"
        href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css"
    >


    <!-- iCheck -->

    <link
        rel="stylesheet"
        href="plugins/icheck-bootstrap/icheck-bootstrap.min.css"
    >


    <!-- AdminLTE -->

    <link
        rel="stylesheet"
        href="dist/css/adminlte.min.css"
    >


    <!-- Google Font -->

    <link
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700"
        rel="stylesheet"
    >

</head>


<body class="hold-transition login-page">


<div class="login-box">


    <!-- ============================================================= -->
    <!-- LOGO -->
    <!-- ============================================================= -->

    <div class="login-logo">

        <a href="index.php">

            <b>New Agenda</b> 2.0

        </a>

    </div>


    <!-- ============================================================= -->
    <!-- CARD -->
    <!-- ============================================================= -->

    <div class="card">

        <div class="card-body login-card-body">


            <p class="login-box-msg">

                Para acessar entre com E-mail e Senha

            </p>


            <!-- ===================================================== -->
            <!-- MENSAGEM -->
            <!-- ===================================================== -->

            <?php

            if ($mensagem !== '') {

                echo '
                <div class="alert alert-' .
                    htmlspecialchars(
                        $tipoMensagem,
                        ENT_QUOTES,
                        'UTF-8'
                    ) .
                ' alert-dismissible fade show">

                    <button
                        type="button"
                        class="close"
                        data-dismiss="alert"
                    >
                        <span>&times;</span>
                    </button>

                    ' .
                    htmlspecialchars(
                        $mensagem,
                        ENT_QUOTES,
                        'UTF-8'
                    ) .

                '</div>';
            }

            ?>


            <!-- ===================================================== -->
            <!-- FORMULÁRIO -->
            <!-- ===================================================== -->

            <form
                action="index.php"
                method="POST"
                autocomplete="off"
            >


                <!-- ================================================= -->
                <!-- E-MAIL -->
                <!-- ================================================= -->

                <div class="input-group mb-3">

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="Digite seu E-mail..."
                        required
                        autocomplete="username"
                        value="<?php
                            echo htmlspecialchars(
                                $_POST['email'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >

                    <div class="input-group-append">

                        <div class="input-group-text">

                            <span class="fas fa-envelope"></span>

                        </div>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- SENHA -->
                <!-- ================================================= -->

                <div class="input-group mb-3">

                    <input
                        type="password"
                        name="senha"
                        class="form-control"
                        placeholder="Digite sua Senha..."
                        required
                        autocomplete="current-password"
                    >

                    <div class="input-group-append">

                        <div class="input-group-text">

                            <span class="fas fa-lock"></span>

                        </div>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- BOTÃO -->
                <!-- ================================================= -->

                <div class="row">

                    <div class="col-12">

                        <button
                            type="submit"
                            name="login"
                            value="1"
                            class="btn btn-primary btn-block"
                        >

                            Acessar a Agenda

                        </button>

                    </div>

                </div>


            </form>


            <!-- ===================================================== -->
            <!-- CADASTRO -->
            <!-- ===================================================== -->

            <p
                style="
                    text-align:center;
                    padding-top:25px;
                "
            >

                <a
                    href="cad_user.php"
                    class="text-center"
                >

                    Se ainda não tem cadastro
                    clique aqui!

                </a>

            </p>


        </div>

    </div>

</div>


<!-- ================================================================ -->
<!-- jQuery -->
<!-- ================================================================ -->

<script src="plugins/jquery/jquery.min.js"></script>


<!-- ================================================================ -->
<!-- Bootstrap -->
<!-- ================================================================ -->

<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>


<!-- ================================================================ -->
<!-- AdminLTE -->
<!-- ================================================================ -->

<script src="dist/js/adminlte.min.js"></script>


</body>

</html>
