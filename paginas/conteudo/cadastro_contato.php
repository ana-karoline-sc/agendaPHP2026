<?php
// Inclui o arquivo de conexão com o banco de dados
include('../config/conexao.php');

// Verifica se o formulário foi enviado
if (isset($_POST['botao'])) {

    // Recupera os dados enviados pelo formulário
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $id_usuario = $_POST['id_user'];

    // Nome da imagem padrão
    $foto = 'avatar-padrao.png';

    // Extensões permitidas
    $formatP = array('png', 'jpg', 'jpeg', 'JPG', 'gif');

    // Verifica se uma foto foi enviada
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {

        // Pega a extensão do arquivo
        $extensao = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);

        // Verifica se a extensão é permitida
        if (in_array($extensao, $formatP)) {

            // Pasta onde as imagens serão salvas
            $pasta = "../img/cont/";

            // Arquivo temporário
            $temporario = $_FILES['foto']['tmp_name'];

            // Cria um nome único para a imagem
            $novoNome = uniqid() . "." . $extensao;

            // Move a imagem para a pasta
            if (move_uploaded_file($temporario, $pasta . $novoNome)) {

                // Guarda o nome da imagem
                $foto = $novoNome;

            } else {

                echo "Erro: não foi possível fazer o upload da imagem.";
                $foto = 'avatar-padrao.png';
            }

        } else {

            echo "Formato de imagem inválido. Use PNG, JPG, JPEG ou GIF.";
            $foto = 'avatar-padrao.png';
        }
    }

    // Comando SQL para cadastrar o contato
    $cadastro = "INSERT INTO tb_contatos 
    (nome_contatos, fone_contatos, email_contatos, foto_contatos, id_user)
    VALUES 
    (:nome, :telefone, :email, :foto, :id_user)";

    try {

        // Prepara a consulta
        $result = $conect->prepare($cadastro);

        // Passa os valores para a consulta
        $result->bindParam(':nome', $nome, PDO::PARAM_STR);
        $result->bindParam(':telefone', $telefone, PDO::PARAM_STR);
        $result->bindParam(':email', $email, PDO::PARAM_STR);
        $result->bindParam(':foto', $foto, PDO::PARAM_STR);
        $result->bindParam(':id_user', $id_usuario, PDO::PARAM_INT);

        // Executa o cadastro
        $result->execute();

        // Verifica se cadastrou
        if ($result->rowCount() > 0) {

            echo '
            <div class="container mt-3">
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">
                        ×
                    </button>

                    <h5>
                        <i class="icon fas fa-check"></i> Sucesso!
                    </h5>

                    Contato cadastrado com sucesso!
                </div>
            </div>';

            // Redireciona para a home depois de 2 segundos
            echo '<meta http-equiv="refresh" content="2;url=home.php">';

        } else {

            echo '
            <div class="container mt-3">
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">
                        ×
                    </button>

                    <h5>
                        <i class="icon fas fa-times"></i> Erro!
                    </h5>

                    Não foi possível cadastrar o contato.
                </div>
            </div>';
        }

    } catch (PDOException $e) {

        echo '
        <div class="container mt-3">
            <div class="alert alert-danger">
                <h5>Erro no banco de dados!</h5>
                ' . htmlspecialchars($e->getMessage()) . '
            </div>
        </div>';
    }
}
?>

<!-- Content Wrapper -->
<div class="content-wrapper">

    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">

            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1>Cadastro de Contatos</h1>
                </div>

            </div>

        </div>
    </section>


    <!-- Main content -->
    <section class="content">

        <div class="container-fluid">

            <div class="row">

                <!-- Formulário -->
                <div class="col-md-4">

                    <div class="card card-primary">

                        <div class="card-header">

                            <h3 class="card-title">
                                Cadastrar contato
                            </h3>

                        </div>


                        <!-- Formulário -->
                        <form
                            role="form"
                            action=""
                            method="post"
                            enctype="multipart/form-data"
                        >

                            <div class="card-body">

                                <!-- Nome -->
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
                                        placeholder="Digite o nome do contato"
                                    >

                                </div>


                                <!-- Telefone -->
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
                                        placeholder="(00) 00000-0000"
                                    >

                                </div>


                                <!-- E-mail -->
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
                                        placeholder="Digite um e-mail"
                                    >

                                </div>


                                <!-- Foto -->
                                <div class="form-group">

                                    <label for="foto">
                                        Foto do contato
                                    </label>

                                    <div class="input-group">

                                        <div class="custom-file">

                                            <input
                                                type="file"
                                                class="custom-file-input"
                                                name="foto"
                                                id="foto"
                                                accept=".png,.jpg,.jpeg,.gif"
                                            >

                                            <label
                                                class="custom-file-label"
                                                for="foto"
                                            >
                                                Arquivo de imagem
                                            </label>

                                        </div>

                                    </div>

                                </div>


                                <!-- ID do usuário -->
                                <input
                                    type="hidden"
                                    name="id_user"
                                    id="id_user"
                                    value="<?php echo isset($id_user) ? $id_user : ''; ?>"
                                >


                                <!-- Checkbox -->
                                <div class="form-check">

                                    <input
                                        type="checkbox"
                                        class="form-check-input"
                                        id="exampleCheck1"
                                        required
                                    >

                                    <label
                                        class="form-check-label"
                                        for="exampleCheck1"
                                    >
                                        Autorizo o cadastro do meu contato
                                    </label>

                                </div>

                            </div>


                            <!-- Botão -->
                            <div class="card-footer">

                                <button
                                    type="submit"
                                    name="botao"
                                    class="btn btn-primary"
                                >
                                    Cadastrar Contato
                                </button>

                            </div>

                        </form>

                    </div>

                </div>


                <!-- Lista de contatos -->
                <div class="col-md-8">

                    <div class="card">

                        <div class="card-header">

                            <h3 class="card-title">
                                Contatos Recentes
                            </h3>

                        </div>


                        <div class="card-body p-0">

                            <table class="table table-striped">

                                <thead>

                                    <tr>

                                        <th style="width: 10px">
                                            #
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

                                        <th style="width: 120px">
                                            Ações
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

<?php

// Consulta os últimos 6 contatos do usuário
$select = "SELECT * FROM tb_contatos 
           WHERE id_user = :id_user 
           ORDER BY id_contatos DESC 
           LIMIT 6";

try {

    // Prepara a consulta
    $result = $conect->prepare($select);

    // Passa o ID do usuário
    $result->bindParam(':id_user', $id_user, PDO::PARAM_INT);

    // Executa a consulta
    $result->execute();

    // Inicializa o contador
    $cont = 1;

    // Verifica se existem contatos
    if ($result->rowCount() > 0) {

        // Percorre os contatos
        while ($show = $result->fetch(PDO::FETCH_OBJ)) {

?>

                                    <tr>

                                        <td>
                                            <?php echo $cont++; ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($show->nome_contatos); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($show->fone_contatos); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($show->email_contatos); ?>
                                        </td>

                                        <td>

                                            <div class="btn-group">

                                                <!-- Editar contato -->
                                                <a
                                                    href="home.php?acao=editar&id=<?php echo $show->id_contatos; ?>"
                                                    class="btn btn-success"
                                                    title="Editar Contato"
                                                >
                                                    <i class="fas fa-user-edit"></i>
                                                </a>


                                                <!-- Excluir contato -->
                                                <a
                                                    href="conteudo/del-contato.php?idDel=<?php echo $show->id_contatos; ?>"
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

    } else {

?>

                                    <tr>

                                        <td colspan="5" class="text-center py-3 text-muted">

                                            <i class="fas fa-info-circle me-1"></i>

                                            Nenhum contato cadastrado no momento.

                                        </td>

                                    </tr>

<?php

    }

} catch (PDOException $e) {

?>

                                    <tr>

                                        <td colspan="5" class="text-center py-3 text-danger">

                                            <strong>ERRO DE PDO:</strong>

                                            <?php echo htmlspecialchars($e->getMessage()); ?>

                                        </td>

                                    </tr>

<?php

}

?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

</div>
