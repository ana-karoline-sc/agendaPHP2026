<?php

// ================================================================
// RELATÓRIO DE CONTATOS DO USUÁRIO LOGADO
// ================================================================

// Verifica se a sessão já foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// ================================================================
// VERIFICAR LOGIN
// ================================================================

if (!isset($_SESSION['loginUser'])) {

    header('Location: ../index.php?acao=negado');
    exit;
}


// ================================================================
// CONEXÃO
// ================================================================

// Se $conect já existir pelo home.php, não cria outra conexão.
if (!isset($conect)) {
    include_once('../config/conexao.php');
}


// ================================================================
// LOCALIZAR USUÁRIO LOGADO
// ================================================================

$emailLogado = $_SESSION['loginUser'];

$idUsuarioLogado = null;

try {

    $sqlUsuario = "
        SELECT
            id_user,
            nome_user,
            email_user
        FROM tb_user
        WHERE email_user = :email
        LIMIT 1
    ";

    $stmtUsuario = $conect->prepare($sqlUsuario);

    $stmtUsuario->bindValue(
        ':email',
        $emailLogado,
        PDO::PARAM_STR
    );

    $stmtUsuario->execute();

    $usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);


    if (!$usuario) {

        echo '
        <div class="container-fluid mt-3">

            <div class="alert alert-danger">

                <h5>
                    <i class="fas fa-times"></i>
                    Erro
                </h5>

                Usuário logado não foi encontrado.

            </div>

        </div>';

        exit;
    }


    // ID REAL do usuário logado
    $idUsuarioLogado = (int) $usuario['id_user'];


} catch (PDOException $e) {

    error_log(
        'Erro ao localizar usuário no relatório: ' .
        $e->getMessage()
    );

    echo '
    <div class="container-fluid mt-3">

        <div class="alert alert-danger">

            <h5>
                <i class="fas fa-times"></i>
                Erro
            </h5>

            Não foi possível identificar o usuário.

        </div>

    </div>';

    exit;
}

?>

<!-- ============================================================= -->
<!-- CONTENT WRAPPER                                               -->
<!-- ============================================================= -->

<div class="content-wrapper">


    <!-- ========================================================= -->
    <!-- CABEÇALHO                                                  -->
    <!-- ========================================================= -->

    <section class="content-header">

        <div class="container-fluid">

            <div class="row mb-2">

                <div class="col-sm-6">

                    <h1>
                        Relatório de Contatos
                    </h1>

                </div>

            </div>

        </div>

    </section>


    <!-- ========================================================= -->
    <!-- CONTEÚDO                                                    -->
    <!-- ========================================================= -->

    <section class="content">

        <div class="container-fluid">


            <!-- ================================================= -->
            <!-- INFORMAÇÃO DO USUÁRIO                             -->
            <!-- ================================================= -->

            <div class="alert alert-info">

                <i class="fas fa-user"></i>

                Contatos de:

                <strong>
                    <?php
                    echo htmlspecialchars(
                        $usuario['nome_user'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </strong>

            </div>


            <!-- ================================================= -->
            <!-- CARD                                                -->
            <!-- ================================================= -->

            <div class="card">


                <!-- ================================================= -->
                <!-- CARD HEADER                                        -->
                <!-- ================================================= -->

                <div class="card-header">

                    <h3 class="card-title">

                        <i class="fas fa-address-book mr-2"></i>

                        Lista de contatos

                    </h3>

                </div>


                <!-- ================================================= -->
                <!-- CARD BODY                                          -->
                <!-- ================================================= -->

                <div class="card-body">


                    <!-- =================================================
                         DATATABLE
                    ================================================== -->

                    <table
                        id="example"
                        class="display nowrap"
                        style="width:100%"
                    >


                        <!-- =================================================
                             CABEÇALHO
                        ================================================== -->

                        <thead>

                            <tr>

                                <th style="text-align:center;">
                                    #
                                </th>

                                <th style="text-align:center;">
                                    Foto
                                </th>

                                <th>
                                    Nome
                                </th>

                                <th>
                                    Telefone
                                </th>

                                <th>
                                    E-mail
                                </th>

                                <th>
                                    Ações
                                </th>

                            </tr>

                        </thead>


                        <!-- =================================================
                             CORPO
                        ================================================== -->

                        <tbody>

<?php

// ================================================================
// BUSCAR SOMENTE OS CONTATOS DO USUÁRIO LOGADO
// ================================================================

$sqlContatos = "
    SELECT
        id_contatos,
        nome_contatos,
        fone_contatos,
        email_contatos,
        foto_contatos,
        id_user
    FROM tb_contatos
    WHERE id_user = :id_user
    ORDER BY id_contatos DESC
";


try {

    $stmtContatos = $conect->prepare($sqlContatos);


    // ============================================================
    // IMPORTANTE:
    //
    // Aqui está o filtro que separa os contatos por usuário.
    // ============================================================

    $stmtContatos->bindValue(
        ':id_user',
        $idUsuarioLogado,
        PDO::PARAM_INT
    );


    $stmtContatos->execute();


    $contador = 1;


    // ============================================================
    // PERCORRER CONTATOS
    // ============================================================

    while (
        $contato = $stmtContatos->fetch(PDO::FETCH_ASSOC)
    ) {

        $idContato = (int) $contato['id_contatos'];

        $nomeContato = $contato['nome_contatos'];

        $telefoneContato = $contato['fone_contatos'];

        $emailContato = $contato['email_contatos'];

        $fotoContato = $contato['foto_contatos'];


        // ========================================================
        // DEFINIR FOTO
        // ========================================================

        if (
            empty($fotoContato) ||
            $fotoContato === 'avatar-padrao.png'
        ) {

            $caminhoFoto =
                '../img/avatar_p/avatar-padrao.png';

        } else {

            $fotoContatoSeguro =
                basename($fotoContato);

            $caminhoFoto =
                '../img/cont/' .
                rawurlencode($fotoContatoSeguro);
        }

?>

                            <tr>


                                <!-- ================================= -->
                                <!-- NÚMERO                            -->
                                <!-- ================================= -->

                                <td style="text-align:center;">

                                    <?php
                                    echo $contador++;
                                    ?>

                                </td>


                                <!-- ================================= -->
                                <!-- FOTO                              -->
                                <!-- ================================= -->

                                <td style="text-align:center;">

                                    <img
                                        src="<?php
                                            echo htmlspecialchars(
                                                $caminhoFoto,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                        alt="Foto de <?php
                                            echo htmlspecialchars(
                                                $nomeContato,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                        title="<?php
                                            echo htmlspecialchars(
                                                $nomeContato,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                        style="
                                            width:45px;
                                            height:45px;
                                            object-fit:cover;
                                            border-radius:50%;
                                        "
                                    >

                                </td>


                                <!-- ================================= -->
                                <!-- NOME                              -->
                                <!-- ================================= -->

                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $nomeContato,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                    ?>

                                </td>


                                <!-- ================================= -->
                                <!-- TELEFONE                          -->
                                <!-- ================================= -->

                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $telefoneContato,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                    ?>

                                </td>


                                <!-- ================================= -->
                                <!-- EMAIL                             -->
                                <!-- ================================= -->

                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $emailContato,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                    ?>

                                </td>


                                <!-- ================================= -->
                                <!-- AÇÕES                             -->
                                <!-- ================================= -->

                                <td>

                                    <div class="btn-group">


                                        <!-- =========================
                                             EDITAR
                                        ========================== -->

                                        <a
                                            href="home.php?acao=editar&id=<?php
                                                echo $idContato;
                                            ?>"
                                            class="btn btn-success"
                                            title="Editar Contato"
                                        >

                                            <i class="fas fa-user-edit"></i>

                                        </a>


                                        <!-- =========================
                                             EXCLUIR
                                        ========================== -->

                                        <a
                                            href="conteudo/del-contato.php?idDel=<?php
                                                echo $idContato;
                                            ?>"
                                            onclick="return confirm('Deseja remover o contato?')"
                                            class="btn btn-danger"
                                            title="Remover Contato"
                                        >

                                            <i class="fas fa-user-times"></i>

                                        </a>


                                    </div>

                                </td>


                            </tr>

<?php

    }


// ================================================================
// CASO NÃO TENHA CONTATOS
// ================================================================

    if ($contador === 1) {

?>

                            <tr>

                                <td
                                    colspan="6"
                                    class="text-center text-muted py-4"
                                >

                                    <i class="fas fa-info-circle mr-1"></i>

                                    Nenhum contato cadastrado para este
                                    usuário.

                                </td>

                            </tr>

<?php

    }


} catch (PDOException $e) {

    error_log(
        'Erro no relatório de contatos: ' .
        $e->getMessage()
    );

?>

                            <tr>

                                <td
                                    colspan="6"
                                    class="text-center text-danger py-4"
                                >

                                    <i class="fas fa-exclamation-triangle mr-1"></i>

                                    Não foi possível carregar os contatos.

                                </td>

                            </tr>

<?php

}

?>

                        </tbody>


                        <!-- =================================================
                             RODAPÉ
                        ================================================== -->

                        <tfoot>

                            <tr>

                                <th style="text-align:center;">
                                    #
                                </th>

                                <th style="text-align:center;">
                                    Foto
                                </th>

                                <th>
                                    Nome
                                </th>

                                <th>
                                    Telefone
                                </th>

                                <th>
                                    E-mail
                                </th>

                                <th>
                                    Ações
                                </th>

                            </tr>

                        </tfoot>


                    </table>


                    <br>


                    <!-- =================================================
                         BOTÃO PDF
                    ================================================== -->

                    <div class="col-lg-12 d-flex justify-content-center">

                        <a
                            href="conteudo/relatoriopdf.php"
                            class="btn btn-lg btn-primary"
                        >

                            <i class="fas fa-file-pdf"></i>

                            Gerar relatório

                        </a>

                    </div>


                </div>

            </div>

        </div>

    </section>

</div>
