<?php
ob_start();

// Inicia a sessão apenas se ainda não tiver sido iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está logado
if (!isset($_SESSION['loginUser'])) {
    header("Location: ../index.php?acao=negado");
    exit;
}

// Inclui o script de saída
include_once('sair.php');
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<?php

// Inclui a conexão com o banco
include_once('../config/conexao.php');

// E-mail do usuário logado
$usuarioLogado = $_SESSION['loginUser'];

// Consulta o usuário
$selectUser = "SELECT * FROM tb_user WHERE email_user = :emailUserLogado";

try {

    // Prepara a consulta
    $resultadoUser = $conect->prepare($selectUser);

    // Passa o e-mail
    $resultadoUser->bindParam(
        ':emailUserLogado',
        $usuarioLogado,
        PDO::PARAM_STR
    );

    // Executa
    $resultadoUser->execute();

    // Conta os resultados
    $contar = $resultadoUser->rowCount();

    if ($contar > 0) {

        // Pega os dados
        $show = $resultadoUser->fetch(PDO::FETCH_OBJ);

        $id_user = $show->id_user;
        $foto_user = $show->foto_user;
        $nome_user = $show->nome_user;
        $email_user = $show->email_user;

    } else {

        echo '
        <div class="alert alert-danger">
            <strong>Aviso!</strong>
            Não há dados de perfil :(
        </div>';

    }

} catch (PDOException $e) {

    error_log("ERRO DE LOGIN DO PDO: " . $e->getMessage());

    echo '
    <div class="alert alert-danger">
        <strong>Aviso!</strong>
        Ocorreu um erro ao acessar os dados do perfil.
    </div>';

}

?>

<meta charset="utf-8">

<meta http-equiv="X-UA-Compatible" content="IE=edge">

<title>Agenda Eletrônica</title>

<meta name="viewport" content="width=device-width, initial-scale=1">


<!-- =====================================================
     DATATABLES 2.1.3
===================================================== -->

<link rel="stylesheet"
      href="https://cdn.datatables.net/2.1.3/css/dataTables.dataTables.css">


<!-- =====================================================
     DATATABLES BUTTONS 3.1.1
===================================================== -->

<link rel="stylesheet"
      href="https://cdn.datatables.net/buttons/3.1.1/css/buttons.dataTables.css">


<!-- =====================================================
     FONT AWESOME
===================================================== -->

<link rel="stylesheet"
      href="../plugins/fontawesome-free/css/all.min.css">


<!-- =====================================================
     IONICONS
===================================================== -->

<link rel="stylesheet"
      href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">


<!-- =====================================================
     TEMPUSDOMINUS
===================================================== -->

<link rel="stylesheet"
      href="../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">


<!-- =====================================================
     ICHECK
===================================================== -->

<link rel="stylesheet"
      href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">


<!-- =====================================================
     JQVMAP
===================================================== -->

<link rel="stylesheet"
      href="../plugins/jqvmap/jqvmap.min.css">


<!-- =====================================================
     ADMINLTE
===================================================== -->

<link rel="stylesheet"
      href="../dist/css/adminlte.min.css">


<!-- =====================================================
     OVERLAY SCROLLBARS
===================================================== -->

<link rel="stylesheet"
      href="../plugins/overlayScrollbars/css/OverlayScrollbars.min.css">


<!-- =====================================================
     DATERANGE PICKER
===================================================== -->

<link rel="stylesheet"
      href="../plugins/daterangepicker/daterangepicker.css">


<!-- =====================================================
     SUMMERNOTE
===================================================== -->

<link rel="stylesheet"
      href="../plugins/summernote/summernote-bs4.css">


<!-- =====================================================
     GOOGLE FONT
===================================================== -->

<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700"
      rel="stylesheet">


<!-- =====================================================
     CSS PERSONALIZADO
===================================================== -->

<link rel="stylesheet"
      href="../dist/css/estilo.css">

</head>


<body class="hold-transition sidebar-mini layout-fixed">

<div class="wrapper">


<!-- =====================================================
     NAVBAR
===================================================== -->

<nav class="main-header navbar navbar-expand navbar-white navbar-light">


    <!-- Menu da esquerda -->

    <ul class="navbar-nav">

        <li class="nav-item">

            <a class="nav-link"
               data-widget="pushmenu"
               href="#"
               role="button">

                <i class="fas fa-bars"></i>

            </a>

        </li>

    </ul>


    <!-- Menu da direita -->

    <ul class="navbar-nav ml-auto">


        <!-- Menu do usuário -->

        <li class="nav-item dropdown">

            <a class="nav-link"
               data-toggle="dropdown"
               href="#"
               title="Perfil e Saída">

                <i class="fas fa-user-circle"></i>

            </a>


            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">


                <!-- Alterar perfil -->

                <div class="dropdown-divider"></div>

                <a href="home.php?acao=perfil"
                   class="dropdown-item">

                    <i class="fas fa-user-alt mr-2"></i>

                    Alterar Perfil

                </a>


                <!-- Sair -->

                <div class="dropdown-divider"></div>

                <a href="?sair"
                   class="dropdown-item">

                    <i class="fas fa-sign-out-alt mr-2"></i>

                    Sair da Agenda

                </a>


            </div>

        </li>


        <!-- Control Sidebar -->

        <li class="nav-item">

            <a class="nav-link"
               data-widget="control-sidebar"
               data-slide="true"
               href="#"
               role="button">

                <i class="fas fa-th-large"></i>

            </a>

        </li>

    </ul>

</nav>


<!-- =====================================================
     SIDEBAR
===================================================== -->

<aside class="main-sidebar sidebar-dark-primary elevation-4">


    <!-- Logo -->

    <a href="home.php"
       class="brand-link">

        <span class="brand-text font-weight-light">

            Agenda Eletrônica

        </span>

    </a>


    <!-- Sidebar -->

    <div class="sidebar">


        <!-- Usuário -->

        <div class="user-panel mt-3 pb-3 mb-3 d-flex">


            <div class="image">

                <?php

                if ($foto_user == 'avatar-padrao.png') {

                    echo '
                    <img
                        src="../img/avatar_p/' . $foto_user . '"
                        alt="' . $foto_user . '"
                        title="' . $foto_user . '"
                        style="
                            width:40px;
                            height:40px;
                            object-fit:cover;
                            border-radius:100%;
                        "
                    >';

                } else {

                    echo '
                    <img
                        src="../img/user/' . $foto_user . '"
                        alt="' . $foto_user . '"
                        title="' . $foto_user . '"
                        style="
                            width:40px;
                            height:40px;
                            object-fit:cover;
                            border-radius:100%;
                        "
                    >';

                }

                ?>

            </div>


            <div class="info">

                <a href="#"
                   class="d-block">

                    <?php echo $nome_user; ?>

                </a>

            </div>


        </div>


        <!-- Menu -->

        <nav class="mt-2">

            <ul class="nav nav-pills nav-sidebar flex-column"
                data-widget="treeview"
                role="menu"
                data-accordion="false">


                <!-- Principal -->

                <li class="nav-item">

                    <a href="home.php?acao=relatorio"
                       class="nav-link">

                        <i class="nav-icon fas fa-tachometer-alt"></i>

                        <p>
                            Principal
                        </p>

                    </a>

                </li>


                <!-- Relatório -->

                <li class="nav-item">

                    <a href="home.php?acao=relatorio"
                       class="nav-link">

                        <i class="nav-icon fas fa-chart-pie"></i>

                        <p>
                            Relatório
                        </p>

                    </a>

                </li>


            </ul>

        </nav>

    </div>

</aside>
