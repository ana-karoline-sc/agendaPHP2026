<?php

/*
|--------------------------------------------------------------------------
| PERFIL DO USUÁRIO
|--------------------------------------------------------------------------
|
| Este arquivo é carregado pelo home.php.
|
| O usuário é identificado através de:
|
| $_SESSION['id_user']
|
| Funções:
|
| - Alterar nome
| - Alterar e-mail
| - Alterar senha
| - Alterar foto
| - Manter foto antiga
| - Usar avatar padrão
|
|--------------------------------------------------------------------------
*/


// ======================================================================
// 1. INICIAR SESSÃO
// ======================================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// ======================================================================
// 2. VERIFICAR LOGIN
// ======================================================================

if (
    !isset($_SESSION['id_user']) ||
    !is_numeric($_SESSION['id_user'])
) {

    echo '
    <div class="container-fluid mt-3">

        <div class="alert alert-danger">

            <h5>
                <i class="fas fa-times"></i>
                Sessão inválida
            </h5>

            Usuário não identificado.
            Faça login novamente.

        </div>

    </div>';

    exit;
}


// ======================================================================
// 3. ID DO USUÁRIO LOGADO
// ======================================================================

$id_user = (int) $_SESSION['id_user'];


// ======================================================================
// 4. VERIFICAR CONEXÃO PDO
// ======================================================================

if (!isset($conect) || !($conect instanceof PDO)) {

    echo '
    <div class="container-fluid mt-3">

        <div class="alert alert-danger">

            <h5>
                <i class="fas fa-times"></i>
                Erro de conexão
            </h5>

            A conexão com o banco de dados não está disponível.

        </div>

    </div>';

    exit;
}


// ======================================================================
// 5. CONFIGURAÇÃO DOS CAMINHOS
// ======================================================================

/*
 * Este arquivo normalmente está dentro de:
 *
 * /agendaphp/paginas/conteudo/
 *
 * Então:
 *
 * dirname(__DIR__, 2)
 *
 * retorna:
 *
 * /agendaphp/
 */

$raizProjeto = dirname(__DIR__, 2);


// Pasta principal das imagens

$pastaFisica =
    $raizProjeto . '/img/';


// Pasta das fotos dos usuários

$pastaUsuarios =
    $pastaFisica . 'user/';


// Pasta do avatar padrão

$pastaAvatarPadrao =
    $pastaFisica . 'avatar_p/';


// Caminhos utilizados pelo navegador

$urlImagem =
    '../img/';

$urlImagemUsuarios =
    '../img/user/';

$urlAvatarPadrao =
    '../img/avatar_p/avatar-padrao.png';


// ======================================================================
// 6. VARIÁVEIS DE MENSAGEM
// ======================================================================

$mensagem = '';

$tipoMensagem = '';


// ======================================================================
// 7. BUSCAR USUÁRIO PELO ID
// ======================================================================

$query = "
    SELECT
        id_user,
        nome_user,
        email_user,
        senha_user,
        foto_user
    FROM tb_user
    WHERE id_user = :id
    LIMIT 1
";


try {

    $stmt =
        $conect->prepare($query);


    $stmt->bindValue(
        ':id',
        $id_user,
        PDO::PARAM_INT
    );


    $stmt->execute();


    $usuario =
        $stmt->fetch(PDO::FETCH_ASSOC);

}
catch (PDOException $e) {

    error_log(
        'ERRO AO BUSCAR PERFIL: ' .
        $e->getMessage()
    );


    echo '
    <div class="container-fluid mt-3">

        <div class="alert alert-danger">

            <h5>
                <i class="fas fa-times"></i>
                Erro
            </h5>

            Não foi possível carregar os dados do usuário.

        </div>

    </div>';

    exit;
}


// ======================================================================
// 8. VERIFICAR USUÁRIO
// ======================================================================

if (!$usuario) {

    echo '
    <div class="container-fluid mt-3">

        <div class="alert alert-danger">

            <h5>
                <i class="fas fa-times"></i>
                Usuário não encontrado
            </h5>

            O usuário da sessão não foi encontrado no banco de dados.

        </div>

    </div>';

    exit;
}


// ======================================================================
// 9. DADOS ATUAIS
// ======================================================================

$nome_user =
    $usuario['nome_user'];

$email_user =
    $usuario['email_user'];

$senha_user =
    $usuario['senha_user'];

$foto_user =
    $usuario['foto_user'];


// ======================================================================
// 10. PROCESSAR FORMULÁRIO
// ======================================================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['upPerfil'])
) {


    // ==================================================================
    // 10.1 RECEBER DADOS
    // ==================================================================

    $nome =
        trim(
            $_POST['nome'] ?? ''
        );


    $email =
        trim(
            $_POST['email'] ?? ''
        );


    $senha_nova =
        $_POST['senha'] ?? '';


    // ==================================================================
    // 10.2 GUARDAR DADOS ANTIGOS
    // ==================================================================

    $email_antigo =
        $email_user;


    $senha_antiga =
        $senha_user;


    $foto_antiga =
        $foto_user;


    // ==================================================================
    // 10.3 VALIDAR NOME
    // ==================================================================

    if ($nome === '') {

        $mensagem =
            'O nome não pode ficar vazio.';

        $tipoMensagem =
            'danger';
    }


    // ==================================================================
    // 10.4 VALIDAR E-MAIL
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
    // 10.5 VERIFICAR SE O E-MAIL JÁ PERTENCE A OUTRO USUÁRIO
    // ==================================================================

    else {

        try {

            $sqlEmail = "
                SELECT
                    id_user
                FROM tb_user
                WHERE email_user = :email
                AND id_user <> :id
                LIMIT 1
            ";


            $stmtEmail =
                $conect->prepare($sqlEmail);


            $stmtEmail->bindValue(
                ':email',
                $email,
                PDO::PARAM_STR
            );


            $stmtEmail->bindValue(
                ':id',
                $id_user,
                PDO::PARAM_INT
            );


            $stmtEmail->execute();


            $emailExistente =
                $stmtEmail->fetch(PDO::FETCH_ASSOC);


            if ($emailExistente) {

                $mensagem =
                    'Este endereço de e-mail já está sendo usado por outro usuário.';

                $tipoMensagem =
                    'danger';
            }

        }
        catch (PDOException $e) {

            error_log(
                'ERRO AO VERIFICAR EMAIL: ' .
                $e->getMessage()
            );


            $mensagem =
                'Não foi possível verificar o endereço de e-mail.';

            $tipoMensagem =
                'danger';
        }
    }


    // ==================================================================
    // 11. PROCESSAR FOTO
    // ==================================================================

    if ($tipoMensagem !== 'danger') {


        // --------------------------------------------------------------
        // Garantir pasta principal
        // --------------------------------------------------------------

        if (!is_dir($pastaFisica)) {

            $mensagem =
                'A pasta de imagens não existe: ' .
                $pastaFisica;

            $tipoMensagem =
                'danger';
        }


        // --------------------------------------------------------------
        // Criar pasta de usuários
        // --------------------------------------------------------------

        elseif (!is_dir($pastaUsuarios)) {

            if (
                !mkdir(
                    $pastaUsuarios,
                    0755,
                    true
                )
            ) {

                $mensagem =
                    'Não foi possível criar a pasta de usuários: ' .
                    $pastaUsuarios;

                $tipoMensagem =
                    'danger';
            }
        }


        // --------------------------------------------------------------
        // Verificar permissão
        // --------------------------------------------------------------

        elseif (
            !is_writable($pastaFisica)
        ) {

            $mensagem =
                'O PHP não possui permissão para gravar na pasta: ' .
                $pastaFisica;

            $tipoMensagem =
                'danger';
        }
    }


    // ==================================================================
    // 12. PROCESSAR UPLOAD
    // ==================================================================

    $novoNome =
        $foto_antiga;


    $arquivoNovoCriado =
        false;


    $caminhoFotoNova =
        null;


    $temUpload =
        isset($_FILES['foto']) &&
        isset($_FILES['foto']['error']) &&
        $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE;


    if (
        $tipoMensagem !== 'danger' &&
        $temUpload
    ) {


        // ==============================================================
        // 12.1 ERRO DO UPLOAD
        // ==============================================================

        if (
            $_FILES['foto']['error'] !== UPLOAD_ERR_OK
        ) {

            switch (
                $_FILES['foto']['error']
            ) {

                case UPLOAD_ERR_INI_SIZE:

                    $mensagem =
                        'A imagem ultrapassa o limite definido no PHP.';

                    break;


                case UPLOAD_ERR_FORM_SIZE:

                    $mensagem =
                        'A imagem ultrapassa o limite permitido pelo formulário.';

                    break;


                case UPLOAD_ERR_PARTIAL:

                    $mensagem =
                        'A imagem foi enviada apenas parcialmente.';

                    break;


                case UPLOAD_ERR_NO_TMP_DIR:

                    $mensagem =
                        'A pasta temporária do PHP não existe.';

                    break;


                case UPLOAD_ERR_CANT_WRITE:

                    $mensagem =
                        'O PHP não conseguiu gravar o arquivo temporário.';

                    break;


                case UPLOAD_ERR_EXTENSION:

                    $mensagem =
                        'Uma extensão do PHP interrompeu o upload.';

                    break;


                default:

                    $mensagem =
                        'Erro desconhecido no upload. Código: ' .
                        (int) $_FILES['foto']['error'];

                    break;
            }


            $tipoMensagem =
                'danger';
        }


        // ==============================================================
        // 12.2 TAMANHO
        // ==============================================================

        elseif (
            !isset($_FILES['foto']['size']) ||
            $_FILES['foto']['size'] <= 0
        ) {

            $mensagem =
                'O arquivo enviado está vazio ou é inválido.';

            $tipoMensagem =
                'danger';
        }


        elseif (
            $_FILES['foto']['size'] >
            5 * 1024 * 1024
        ) {

            $mensagem =
                'A imagem não pode ter mais de 5 MB.';

            $tipoMensagem =
                'danger';
        }


        // ==============================================================
        // 12.3 ARQUIVO TEMPORÁRIO
        // ==============================================================

        elseif (
            !isset($_FILES['foto']['tmp_name']) ||
            !is_uploaded_file(
                $_FILES['foto']['tmp_name']
            )
        ) {

            $mensagem =
                'O arquivo enviado não foi reconhecido como um upload válido.';

            $tipoMensagem =
                'danger';
        }


        // ==============================================================
        // 12.4 VERIFICAR IMAGEM
        // ==============================================================

        else {

            $informacoesImagem =
                @getimagesize(
                    $_FILES['foto']['tmp_name']
                );


            if (
                $informacoesImagem === false
            ) {

                $mensagem =
                    'O arquivo selecionado não é uma imagem válida.';

                $tipoMensagem =
                    'danger';
            }


            else {

                // ======================================================
                // MIME PERMITIDOS
                // ======================================================

                $tiposPermitidos = array(

                    'image/jpeg' => 'jpg',

                    'image/png' => 'png',

                    'image/gif' => 'gif'

                );


                $mime =
                    $informacoesImagem['mime'] ?? '';


                if (
                    !isset(
                        $tiposPermitidos[$mime]
                    )
                ) {

                    $mensagem =
                        'Formato inválido. Escolha uma imagem JPG, JPEG, PNG ou GIF.';

                    $tipoMensagem =
                        'danger';
                }


                else {

                    // ==================================================
                    // EXTENSÃO
                    // ==================================================

                    $extensao =
                        $tiposPermitidos[$mime];


                    // ==================================================
                    // GERAR NOME
                    // ==================================================

                    try {

                        $nomeAleatorio =
                            bin2hex(
                                random_bytes(12)
                            );

                    }
                    catch (Exception $e) {

                        $nomeAleatorio =
                            uniqid(
                                '',
                                true
                            );
                    }


                    $novoNome =
                        'user_' .
                        $nomeAleatorio .
                        '.' .
                        $extensao;


                    // ==================================================
                    // CAMINHO
                    // ==================================================

                    $caminhoFotoNova =
                        $pastaUsuarios .
                        $novoNome;


                    // ==================================================
                    // SALVAR
                    // ==================================================

                    if (
                        move_uploaded_file(
                            $_FILES['foto']['tmp_name'],
                            $caminhoFotoNova
                        )
                    ) {

                        $arquivoNovoCriado =
                            true;
                    }


                    else {

                        $mensagem =
                            'O PHP recebeu a imagem, mas não conseguiu salvá-la.';

                        $tipoMensagem =
                            'danger';
                    }
                }
            }
        }
    }


    // ==================================================================
    // 13. ATUALIZAR BANCO
    // ==================================================================

    if (
        $tipoMensagem !== 'danger'
    ) {


        // ==============================================================
        // SENHA
        // ==============================================================

        if ($senha_nova !== '') {

            $senha =
                password_hash(
                    $senha_nova,
                    PASSWORD_DEFAULT
                );


            if ($senha === false) {

                $mensagem =
                    'Não foi possível gerar a nova senha.';

                $tipoMensagem =
                    'danger';
            }


            $senhaAlterada =
                true;
        }

        else {

            $senha =
                $senha_antiga;

            $senhaAlterada =
                false;
        }
    }


    // ==================================================================
    // 14. EXECUTAR UPDATE
    // ==================================================================

    if (
        $tipoMensagem !== 'danger'
    ) {

        try {

            $conect->beginTransaction();


            $update = "
                UPDATE tb_user
                SET
                    foto_user = :foto,
                    nome_user = :nome,
                    email_user = :email,
                    senha_user = :senha
                WHERE id_user = :id
            ";


            $result =
                $conect->prepare($update);


            $result->bindValue(
                ':foto',
                $novoNome,
                PDO::PARAM_STR
            );


            $result->bindValue(
                ':nome',
                $nome,
                PDO::PARAM_STR
            );


            $result->bindValue(
                ':email',
                $email,
                PDO::PARAM_STR
            );


            $result->bindValue(
                ':senha',
                $senha,
                PDO::PARAM_STR
            );


            $result->bindValue(
                ':id',
                $id_user,
                PDO::PARAM_INT
            );


            $result->execute();


            $conect->commit();


            // ==========================================================
            // ATUALIZAR SESSÃO
            // ==========================================================

            $_SESSION['id_user'] =
                $id_user;


            $_SESSION['loginUser'] =
                $email;


            /*
             * Mantemos esta variável somente
             * por compatibilidade com códigos antigos.
             *
             * Ela NÃO contém a senha.
             */

            $_SESSION['senhaUser'] =
                $id_user;


            // ==========================================================
            // APAGAR FOTO ANTIGA
            // ==========================================================

            if (
                $arquivoNovoCriado
            ) {

                if (
                    !empty($foto_antiga) &&
                    $foto_antiga !== 'avatar-padrao.png'
                ) {

                    $nomeFotoAntiga =
                        basename(
                            $foto_antiga
                        );


                    // --------------------------------------------------
                    // Procurar em /img/user/
                    // --------------------------------------------------

                    $fotoAntigaUser =
                        $pastaUsuarios .
                        $nomeFotoAntiga;


                    if (
                        file_exists(
                            $fotoAntigaUser
                        )
                    ) {

                        @unlink(
                            $fotoAntigaUser
                        );
                    }


                    // --------------------------------------------------
                    // Procurar em /img/
                    // --------------------------------------------------

                    $fotoAntigaImg =
                        $pastaFisica .
                        $nomeFotoAntiga;


                    if (
                        file_exists(
                            $fotoAntigaImg
                        )
                    ) {

                        @unlink(
                            $fotoAntigaImg
                        );
                    }
                }
            }


            // ==========================================================
            // ATUALIZAR VARIÁVEIS DA PÁGINA
            // ==========================================================

            $nome_user =
                $nome;


            $email_user =
                $email;


            $senha_user =
                $senha;


            $foto_user =
                $novoNome;


            // ==========================================================
            // MENSAGEM
            // ==========================================================

            $mensagem =
                'Perfil atualizado com sucesso.';


            $tipoMensagem =
                'success';


            // ==========================================================
            // AVISAR SOBRE LOGIN
            // ==========================================================

            if (
                $email !== $email_antigo ||
                $senhaAlterada
            ) {

                $mensagem .=
                    ' O e-mail ou a senha foram alterados. ' .
                    'Sua sessão foi atualizada automaticamente.';

                $tipoMensagem =
                    'warning';
            }

        }
        catch (PDOException $e) {

            // ==========================================================
            // ROLLBACK
            // ==========================================================

            if (
                $conect->inTransaction()
            ) {

                $conect->rollBack();
            }


            // ==========================================================
            // APAGAR FOTO NOVA
            // ==========================================================

            if (
                $arquivoNovoCriado &&
                $caminhoFotoNova !== null &&
                file_exists($caminhoFotoNova)
            ) {

                @unlink(
                    $caminhoFotoNova
                );
            }


            // ==========================================================
            // LOG
            // ==========================================================

            error_log(
                'ERRO AO ATUALIZAR PERFIL: ' .
                $e->getMessage()
            );


            // ==========================================================
            // MENSAGEM
            // ==========================================================

            $mensagem =
                'Não foi possível atualizar os dados do usuário.';

            $tipoMensagem =
                'danger';
        }
    }
}


// ======================================================================
// 15. DEFINIR IMAGEM DO PERFIL
// ======================================================================

$imagemPerfil =
    $urlAvatarPadrao;


if (
    !empty($foto_user) &&
    $foto_user !== 'avatar-padrao.png'
) {

    $nomeFoto =
        basename(
            $foto_user
        );


    // ==================================================================
    // PROCURAR EM /img/user/
    // ==================================================================

    if (
        file_exists(
            $pastaUsuarios .
            $nomeFoto
        )
    ) {

        $imagemPerfil =
            $urlImagemUsuarios .
            rawurlencode(
                $nomeFoto
            );
    }


    // ==================================================================
    // PROCURAR EM /img/
    // ==================================================================

    elseif (
        file_exists(
            $pastaFisica .
            $nomeFoto
        )
    ) {

        $imagemPerfil =
            $urlImagem .
            rawurlencode(
                $nomeFoto
            );
    }
}

?>


<!-- ================================================================= -->
<!-- CONTENT WRAPPER                                                   -->
<!-- ================================================================= -->

<div class="content-wrapper">


    <!-- ============================================================= -->
    <!-- CABEÇALHO                                                      -->
    <!-- ============================================================= -->

    <section class="content-header">

        <div class="container-fluid">

            <div class="row mb-2">

                <div class="col-sm-6">

                    <h1>
                        Editar Perfil
                    </h1>

                </div>

            </div>

        </div>

    </section>


    <!-- ============================================================= -->
    <!-- CONTEÚDO                                                       -->
    <!-- ============================================================= -->

    <section class="content">

        <div class="container-fluid">

            <div class="row">


                <!-- ================================================= -->
                <!-- COLUNA ESQUERDA                                   -->
                <!-- ================================================= -->

                <div class="col-md-6">

                    <div class="card card-primary">


                        <!-- HEADER -->

                        <div class="card-header">

                            <h3 class="card-title">

                                Editar Perfil

                            </h3>

                        </div>


                        <!-- FORM -->

                        <form
                            action=""
                            method="POST"
                            enctype="multipart/form-data"
                            autocomplete="off"
                        >


                            <div class="card-body">


                                <!-- ================================= -->
                                <!-- NOME                              -->
                                <!-- ================================= -->

                                <div class="form-group">

                                    <label for="nome">

                                        Nome

                                    </label>


                                    <input
                                        type="text"
                                        class="form-control"
                                        name="nome"
                                        id="nome"
                                        required
                                        maxlength="150"
                                        value="<?php

                                            echo htmlspecialchars(
                                                $nome_user,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );

                                        ?>"
                                    >

                                </div>


                                <!-- ================================= -->
                                <!-- E-MAIL                            -->
                                <!-- ================================= -->

                                <div class="form-group">

                                    <label for="email">

                                        Endereço de E-mail

                                    </label>


                                    <input
                                        type="email"
                                        class="form-control"
                                        name="email"
                                        id="email"
                                        required
                                        maxlength="255"
                                        value="<?php

                                            echo htmlspecialchars(
                                                $email_user,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );

                                        ?>"
                                    >

                                </div>


                                <!-- ================================= -->
                                <!-- SENHA                             -->
                                <!-- ================================= -->

                                <div class="form-group">

                                    <label for="senha">

                                        Nova Senha

                                    </label>


                                    <input
                                        type="password"
                                        class="form-control"
                                        name="senha"
                                        id="senha"
                                        placeholder="Digite somente se quiser alterar"
                                        autocomplete="new-password"
                                    >


                                    <small
                                        class="form-text text-muted"
                                    >

                                        Deixe vazio se não quiser
                                        alterar a senha.

                                    </small>

                                </div>


                                <!-- ================================= -->
                                <!-- FOTO                               -->
                                <!-- ================================= -->

                                <div class="form-group">

                                    <label for="foto">

                                        Avatar do usuário

                                    </label>


                                    <div class="custom-file">


                                        <input
                                            type="file"
                                            class="custom-file-input"
                                            name="foto"
                                            id="foto"
                                            accept=".jpg,.jpeg,.png,.gif,image/jpeg,image/png,image/gif"
                                        >


                                        <label
                                            class="custom-file-label"
                                            for="foto"
                                            data-browse="Procurar"
                                        >

                                            Arquivo de imagem

                                        </label>


                                    </div>


                                    <small
                                        class="form-text text-muted"
                                    >

                                        Formatos permitidos:

                                        JPG, JPEG, PNG e GIF.

                                        <br>

                                        Tamanho máximo: 5 MB.

                                    </small>

                                </div>


                            </div>


                            <!-- ======================================= -->
                            <!-- FOOTER                                  -->
                            <!-- ======================================= -->

                            <div class="card-footer">


                                <button
                                    type="submit"
                                    name="upPerfil"
                                    value="1"
                                    class="btn btn-primary"
                                >

                                    <i class="fas fa-save"></i>

                                    Alterar dados do usuário

                                </button>


                            </div>


                        </form>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- COLUNA DIREITA                                    -->
                <!-- ================================================= -->

                <div class="col-md-6">


                    <div class="card">


                        <!-- HEADER -->

                        <div class="card-header">

                            <h3 class="card-title">

                                Dados do Usuário

                            </h3>

                        </div>


                        <!-- BODY -->

                        <div
                            class="card-body"
                            style="
                                text-align:center;
                                padding-bottom:40px;
                            "
                        >


                            <!-- ===================================== -->
                            <!-- FOTO                                   -->
                            <!-- ===================================== -->

                            <img
                                src="<?php

                                    echo htmlspecialchars(
                                        $imagemPerfil,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                ?>"
                                alt="Foto de <?php

                                    echo htmlspecialchars(
                                        $nome_user,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                ?>"
                                title="<?php

                                    echo htmlspecialchars(
                                        $nome_user,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                ?>"
                                style="
                                    width:200px;
                                    height:200px;
                                    object-fit:cover;
                                    border-radius:50%;
                                    margin-top:20px;
                                    border:4px solid #007bff;
                                "
                            >


                            <!-- ===================================== -->
                            <!-- NOME                                   -->
                            <!-- ===================================== -->

                            <h1
                                style="
                                    margin-top:20px;
                                "
                            >

                                <?php

                                echo htmlspecialchars(
                                    $nome_user,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );

                                ?>

                            </h1>


                            <!-- ===================================== -->
                            <!-- E-MAIL                                 -->
                            <!-- ===================================== -->

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $email_user,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );

                                ?>

                            </strong>


                        </div>

                    </div>

                </div>


            </div>

        </div>

    </section>

</div>


<!-- ================================================================= -->
<!-- JAVASCRIPT                                                        -->
<!-- ================================================================= -->

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        var campoFoto =
            document.getElementById('foto');


        var labelFoto =
            document.querySelector(
                'label.custom-file-label[for="foto"]'
            );


        if (
            campoFoto &&
            labelFoto
        ) {

            campoFoto.addEventListener(
                'change',
                function () {

                    if (
                        this.files &&
                        this.files.length > 0
                    ) {

                        labelFoto.textContent =
                            this.files[0].name;

                    }

                    else {

                        labelFoto.textContent =
                            'Arquivo de imagem';

                    }

                }
            );

        }

    }
);

</script>
