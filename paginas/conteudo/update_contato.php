<?php

// =====================================================
// CONEXÃO COM O BANCO
// =====================================================

include('../config/conexao.php');


// =====================================================
// 1. VERIFICA SE O ID FOI ENVIADO
// =====================================================

if (!isset($_GET['id'])) {

    header("Location: home.php");
    exit;

}


// =====================================================
// 2. PEGA O ID
// =====================================================

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {

    echo '
    <div class="container mt-3">
        <div class="alert alert-danger">
            ID do contato inválido!
        </div>
    </div>
    ';

    exit;
}


// =====================================================
// 3. BUSCA O CONTATO
// =====================================================

$select = "SELECT * FROM tb_contatos WHERE id_contatos = :id";

try {

    $resultado = $conect->prepare($select);

    $resultado->bindParam(
        ':id',
        $id,
        PDO::PARAM_INT
    );

    $resultado->execute();

    $show = $resultado->fetch(PDO::FETCH_OBJ);


    // Verifica se encontrou o contato
    if (!$show) {

        echo '
        <div class="container mt-3">

            <div class="alert alert-danger">

                Não há dados com o ID informado!

            </div>

        </div>
        ';

        exit;
    }


    // Guarda os dados do contato
    $idCont = $show->id_contatos;
    $nome   = $show->nome_contatos;
    $fone   = $show->fone_contatos;
    $email  = $show->email_contatos;
    $foto   = $show->foto_contatos;


} catch (PDOException $e) {

    echo '
    <div class="container mt-3">

        <div class="alert alert-danger">

            <strong>ERRO DE SELECT NO PDO:</strong><br>

            ' . $e->getMessage() . '

        </div>

    </div>
    ';

    exit;
}


// =====================================================
// 4. VERIFICA SE O FORMULÁRIO FOI ENVIADO
// =====================================================

if (isset($_POST['upContato'])) {


    // =================================================
    // PEGA OS DADOS DIGITADOS
    // =================================================

    $nome  = trim($_POST['nome']);
    $fone  = trim($_POST['telefone']);
    $email = trim($_POST['email']);


    // =================================================
    // MANTÉM A FOTO ANTIGA POR PADRÃO
    // =================================================

    $novoNome = $foto;

    // Variável para mensagem
    $mensagem = '';

    // Define se houve erro no upload
    $erroFoto = false;


    // =================================================
    // 5. VERIFICA SE FOI ESCOLHIDA UMA NOVA FOTO
    // =================================================

    if (
        isset($_FILES['foto']) &&
        $_FILES['foto']['name'] != ''
    ) {


        // =================================================
        // VERIFICA O ERRO DO UPLOAD
        // =================================================

        if ($_FILES['foto']['error'] != UPLOAD_ERR_OK) {

            $erroFoto = true;


            switch ($_FILES['foto']['error']) {

                case UPLOAD_ERR_INI_SIZE:

                    $mensagem =
                        'A imagem é maior que o tamanho permitido pelo PHP.';

                    break;


                case UPLOAD_ERR_FORM_SIZE:

                    $mensagem =
                        'A imagem é maior que o tamanho permitido pelo formulário.';

                    break;


                case UPLOAD_ERR_PARTIAL:

                    $mensagem =
                        'A imagem foi enviada apenas parcialmente.';

                    break;


                case UPLOAD_ERR_NO_FILE:

                    $mensagem =
                        'Nenhuma imagem foi enviada.';

                    break;


                case UPLOAD_ERR_NO_TMP_DIR:

                    $mensagem =
                        'A pasta temporária do PHP não foi encontrada.';

                    break;


                case UPLOAD_ERR_CANT_WRITE:

                    $mensagem =
                        'O PHP não conseguiu gravar o arquivo temporário.';

                    break;


                case UPLOAD_ERR_EXTENSION:

                    $mensagem =
                        'Uma extensão do PHP bloqueou o upload.';

                    break;


                default:

                    $mensagem =
                        'Erro no upload da imagem. Código: '
                        . $_FILES['foto']['error'];

                    break;
            }


        } else {


            // =================================================
            // 6. FORMATOS PERMITIDOS
            // =================================================

            $formatP = array(
                'png',
                'jpg',
                'jpeg',
                'gif'
            );


            // =================================================
            // 7. PEGA A EXTENSÃO DA IMAGEM
            // =================================================

            $extensao = strtolower(
                pathinfo(
                    $_FILES['foto']['name'],
                    PATHINFO_EXTENSION
                )
            );


            // =================================================
            // 8. VERIFICA O FORMATO
            // =================================================

            if (in_array($extensao, $formatP)) {


                // =================================================
                // 9. DEFINE A PASTA DAS IMAGENS
                // =================================================

                /*
                 * __DIR__ pega a pasta atual do arquivo.
                 *
                 * O arquivo update-contato.php está dentro de:
                 *
                 * /var/www/html/agendaphp/paginas/
                 *
                 * Então:
                 *
                 * ../img/cont/
                 *
                 * aponta para:
                 *
                 * /var/www/html/agendaphp/img/cont/
                 */

              $pasta = '/var/www/html/agendaphp/img/cont/';


                // =================================================
                // 10. VERIFICA SE A PASTA EXISTE
                // =================================================

                if (!is_dir($pasta)) {

                    if (!mkdir($pasta, 0775, true)) {

                        $erroFoto = true;

                        $mensagem =
                            'A pasta para salvar a imagem não existe e não foi possível criá-la.';
                    }
                }


                // =================================================
                // 11. PEGA O ARQUIVO TEMPORÁRIO
                // =================================================

                if (!$erroFoto) {

                    $temporario = $_FILES['foto']['tmp_name'];


                    // =================================================
                    // 12. VERIFICA SE O ARQUIVO FOI REALMENTE ENVIADO
                    // =================================================

                    if (!is_uploaded_file($temporario)) {

                        $erroFoto = true;

                        $mensagem =
                            'O PHP não reconheceu a imagem como um arquivo enviado pelo formulário.';

                    }

                }


                // =================================================
                // 13. CRIA UM NOME ÚNICO PARA A IMAGEM
                // =================================================

                if (!$erroFoto) {

                    $novoNome =
                        uniqid('', true) . '.' . $extensao;


                    // Caminho completo onde a imagem será salva
                    $caminho = $pasta . $novoNome;


                    // =================================================
                    // 14. MOVE A IMAGEM PARA A PASTA
                    // =================================================

                    if (move_uploaded_file($temporario, $caminho)) {


                        // =================================================
                        // 15. APAGA A FOTO ANTIGA
                        // =================================================

                        if (
                            !empty($foto) &&
                            $foto != 'avatar-padrao.png'
                        ) {

                            $fotoAntiga = $pasta . $foto;


                            if (file_exists($fotoAntiga)) {

                                unlink($fotoAntiga);

                            }
                        }


                    } else {

                        // Não conseguiu salvar
                        $erroFoto = true;

                        $mensagem =
                            'Erro: não foi possível salvar a imagem.';

                        // Mantém a foto antiga
                        $novoNome = $foto;

                    }

                }


            } else {

                // =================================================
                // FORMATO INVÁLIDO
                // =================================================

                $erroFoto = true;

                $mensagem =
                    'Formato de imagem inválido! Use somente PNG, JPG, JPEG ou GIF.';

                // Mantém a foto antiga
                $novoNome = $foto;

            }

        }

    }


    // =====================================================
    // 16. ATUALIZA OS DADOS NO BANCO
    // =====================================================

    $update = "
        UPDATE tb_contatos
        SET
            nome_contatos = :nome,
            fone_contatos = :fone,
            email_contatos = :email,
            foto_contatos = :foto
        WHERE id_contatos = :id
    ";


    try {

        $result = $conect->prepare($update);


        // =================================================
        // ID
        // =================================================

        $result->bindParam(
            ':id',
            $idCont,
            PDO::PARAM_INT
        );


        // =================================================
        // NOME
        // =================================================

        $result->bindParam(
            ':nome',
            $nome,
            PDO::PARAM_STR
        );


        // =================================================
        // TELEFONE
        // =================================================

        $result->bindParam(
            ':fone',
            $fone,
            PDO::PARAM_STR
        );


        // =================================================
        // E-MAIL
        // =================================================

        $result->bindParam(
            ':email',
            $email,
            PDO::PARAM_STR
        );


        // =================================================
        // FOTO
        // =================================================

        $result->bindParam(
            ':foto',
            $novoNome,
            PDO::PARAM_STR
        );


        // =================================================
        // EXECUTA O UPDATE
        // =================================================

        $result->execute();


        // =================================================
        // 17. VERIFICA O RESULTADO
        // =================================================

        if ($result->rowCount() > 0) {


            // Se atualizou normalmente
            if (!$erroFoto) {

                echo '
                <div class="container mt-3">

                    <div class="alert alert-success">

                        <h5>
                            <i class="fas fa-check"></i>
                            Ok !!!
                        </h5>

                        Os dados foram atualizados com sucesso.

                    </div>

                </div>
                ';


                // Redireciona depois de 3 segundos
                header("Refresh: 3; url=home.php");


            } else {

                // Dados foram atualizados,
                // mas houve problema com a foto

                echo '
                <div class="container mt-3">

                    <div class="alert alert-danger">

                        <h5>
                            <i class="fas fa-exclamation-triangle"></i>
                            Erro na imagem
                        </h5>

                        ' . $mensagem . '

                    </div>

                </div>
                ';

            }


        } else {


            // =================================================
            // NENHUMA ALTERAÇÃO
            // =================================================

            if ($erroFoto) {

                echo '
                <div class="container mt-3">

                    <div class="alert alert-danger">

                        <h5>
                            <i class="fas fa-exclamation-triangle"></i>
                            Erro na imagem
                        </h5>

                        ' . $mensagem . '

                    </div>

                </div>
                ';

            } else {

                echo '
                <div class="container mt-3">

                    <div class="alert alert-warning">

                        <h5>
                            <i class="fas fa-exclamation-triangle"></i>
                            Atenção!
                        </h5>

                        Nenhuma alteração foi realizada.

                    </div>

                </div>
                ';

            }

        }


    } catch (PDOException $e) {


        // =================================================
        // ERRO NO UPDATE
        // =================================================

        echo '
        <div class="container mt-3">

            <div class="alert alert-danger">

                <strong>
                    ERRO DE UPDATE NO PDO:
                </strong>

                <br>

                ' . $e->getMessage() . '

            </div>

        </div>
        ';

    }

}

?>


<!-- =====================================================
     CONTENT WRAPPER
===================================================== -->

<div class="content-wrapper">


    <!-- =================================================
         CABEÇALHO
    ================================================== -->

    <section class="content-header">

        <div class="container-fluid">

            <div class="row mb-2">

                <div class="col-sm-6">

                    <h1>
                        Editar Contato
                    </h1>

                </div>

            </div>

        </div>

    </section>


    <!-- =================================================
         CONTEÚDO
    ================================================== -->

    <section class="content">

        <div class="container-fluid">

            <div class="row">


                <!-- =========================================
                     FORMULÁRIO DE EDIÇÃO
                ========================================== -->

                <div class="col-md-6">

                    <div class="card card-primary">


                        <!-- CABEÇALHO DO CARD -->

                        <div class="card-header">

                            <h3 class="card-title">
                                Editar Contato
                            </h3>

                        </div>


                        <!-- =================================
                             FORMULÁRIO
                        ================================== -->

                        <form
                            method="post"
                            enctype="multipart/form-data"
                        >


                            <div class="card-body">


                                <!-- =========================
                                     NOME
                                ========================== -->

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
                                        value="<?php echo htmlspecialchars($nome); ?>"
                                    >

                                </div>


                                <!-- =========================
                                     TELEFONE
                                ========================== -->

                                <div class="form-group">

                                    <label for="telefone">
                                        Telefone
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        name="telefone"
                                        id="telefone"
                                        required
                                        value="<?php echo htmlspecialchars($fone); ?>"
                                    >

                                </div>


                                <!-- =========================
                                     E-MAIL
                                ========================== -->

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
                                        value="<?php echo htmlspecialchars($email); ?>"
                                    >

                                </div>


                                <!-- =========================
                                     FOTO
                                ========================== -->

                                <div class="form-group">

                                    <label for="foto">
                                        Foto do contato
                                    </label>


                                    <div class="custom-file">

                                        <input
                                            type="file"
                                            class="custom-file-input"
                                            name="foto"
                                            id="foto"
                                            accept=".jpg,.jpeg,.png,.gif"
                                        >

                                        <label
                                            class="custom-file-label"
                                            for="foto"
                                        >
                                            Escolher imagem
                                        </label>

                                    </div>

                                </div>


                            </div>


                            <!-- =================================
                                 RODAPÉ DO FORMULÁRIO
                            ================================== -->

                            <div class="card-footer">

                                <button
                                    type="submit"
                                    name="upContato"
                                    class="btn btn-primary"
                                >

                                    Finalizar edição do contato

                                </button>

                            </div>


                        </form>

                    </div>

                </div>


                <!-- =========================================
                     DADOS DO CONTATO
                ========================================== -->

                <div class="col-md-6">

                    <div class="card">


                        <!-- CABEÇALHO -->

                        <div class="card-header">

                            <h3 class="card-title">

                                Dados do Contato

                            </h3>

                        </div>


                        <!-- CORPO -->

                        <div
                            class="card-body"
                            style="
                                text-align: center;
                                margin-bottom: 98px;
                            "
                        >


                            <!-- =========================
                                 FOTO DO CONTATO
                            ========================== -->

                            <?php

                            if ($foto == 'avatar-padrao.png') {

                                $caminhoFoto =
                                    '../img/avatar_p/' . $foto;

                            } else {

                                $caminhoFoto =
                                    '../img/cont/' . $foto;

                            }

                            ?>


                            <img
                                src="<?php echo htmlspecialchars($caminhoFoto); ?>"
                                alt="Foto do contato"
                                style="
                                    width: 200px;
                                    height: 200px;
                                    object-fit: cover;
                                    border-radius: 100%;
                                    margin-top: 0px;
                                "
                            >


                            <!-- =========================
                                 NOME
                            ========================== -->

                            <h1>

                                <?php
                                echo htmlspecialchars($nome);
                                ?>

                            </h1>


                            <!-- =========================
                                 TELEFONE
                            ========================== -->

                            <strong>

                                <?php
                                echo htmlspecialchars($fone);
                                ?>

                            </strong>


                            <!-- =========================
                                 E-MAIL
                            ========================== -->

                            <p>

                                <?php
                                echo htmlspecialchars($email);
                                ?>

                            </p>


                        </div>

                    </div>

                </div>


            </div>

        </div>

    </section>

</div>


<!-- =====================================================
     JAVASCRIPT PARA MOSTRAR O NOME DO ARQUIVO ESCOLHIDO
===================================================== -->

<script>

document.getElementById('foto').addEventListener('change', function () {

    var nomeArquivo = this.files[0]
        ? this.files[0].name
        : 'Escolher imagem';

    var label = document.querySelector(
        'label[for="foto"]'
    );

    label.textContent = nomeArquivo;

});

</script>