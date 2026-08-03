<?php
// Assume que a sessão já foi iniciada no bootstrap
// e que $nome_nav já vem do controller (MVC correto)
use App\Core\Auth;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/bootstrap/bootstrap.min.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/jquery/flatpickr.min.css">
    <!-- CSS do sistema -->
	<link rel="stylesheet" href="<?=BASE_URL?>/assets/css/global.css">

    <title>SIME</title>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container-fluid">

        <!-- LOGO -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= BASE_URL ?>/">
            <img src="<?= BASE_URL ?>/assets/img/logo-sime-alt.png" height="50">
            <span class="fw-semibold">SIME</span>
        </a>

        <!-- BOTÃO MOBILE -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <!-- MENU PRINCIPAL -->
            <ul class="navbar-nav me-auto">

                <!-- MEMBROS -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                    <img src="<?= BASE_URL?>/assets/img/icones/members.png" class="rounded-circle icone-png"> Membros
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/membros">
                            <i class="fas fa-search"></i> Buscar / Adicionar
                        </a></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/membros/dadosCulto">
                            <i class="fas fa-users"></i> Participações
                        </a></li>

                        <li><a class="dropdown-item" href="<?= BASE_URL?>/membros/datamembers">
                            <i class="fas fa-chart-line"></i> Estatisticas
                        </a></li>
                    </ul>
                </li>

                <!-- FINANÇAS -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                    <img src="<?= BASE_URL?>/assets/img/icones/money.png" class="rounded-circle icone-png"> Finanças
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/finance">
                            <i class="fas fa-search"></i> Movimentos
                        </a></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/membros/painelDizimos">
                            <i class="fa-solid fa-hand-holding-heart"></i> Dizimos
                        </a></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/contribuicao">
                            <i class="fas fa-donate"></i> Contribuições
                        </a></li>
                        <!--
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/finance/dashboard">
                            <i class="fas fa-chart-bar"></i> Dashboard
                        </a></li>
                        -->
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/relatorios/runReport">
                    <img src="<?= BASE_URL?>/assets/img/icones/relatorio.png" class="rounded-circle icone-png"> Relatórios
                    </a>
                    </li>
                <!-- ADMIN -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                    <img src="<?= BASE_URL?>/assets/img/icones/gestao.png" class="rounded-circle icone-png"> Administração
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/usuarios">
                            <i class="fas fa-users-cog"></i> Usuários
                        </a></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/relatorios">
                            <i class="fas fa-file-lines"></i> Relatórios
                        </a></li>
                        <!--
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/administracao">
                            <i class="fas fa-cog"></i> Sistema
                        </a></li>
                        -->
                    </ul>
                </li>

            </ul>

            <!-- PERFIL -->
            <ul class="navbar-nav align-items-center gap-3">

                <!-- NOTIFICAÇÕES -->
                <li class="nav-item">
                    <a class="nav-link position-relative" href="#">
                        <i class="fas fa-bell fs-2"></i>
                        <span class="position-absolute top-4 start-100 translate-middle badge rounded-circle bg-danger" id="notificacaoBadge">
                        </span>
                    </a>
                </li>

                <!-- PERFIL -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                        <img src="<?= BASE_URL?>/assets/img/icones/perfil.png" class="rounded-circle icone-png">
                        <span><?= htmlspecialchars($_SESSION['usuario_nome']) ?></span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="dropdown-header text-center">
                            <?= htmlspecialchars($_SESSION['usuario_nome']) ?><br>
                            <small class="text-muted"><?= $_SESSION['nivel'] ?></small>
                        </li>
<!--
                        <li><hr class="dropdown-divider"></li>

                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/profile">
                            <i class="fas fa-user"></i> Perfil
                        </a></li>

                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/logout">
                            <i class="fas fa-sign-out-alt"></i> Sair
                        </a></li>
-->
                    </ul>
                </li>

            </ul>
			<a href="<?= BASE_URL ?>/logout" class="btn btn-outline-danger">
               <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </div>
    </div>
</nav>

<script src="<?= BASE_URL ?>/assets/js/bootstrap/bootstrap.bundle.min.js"></script>
<script>
fetch("<?= BASE_URL ?>/notificacoes")
.then(r => r.json())
.then(data => {

    if(data.total > 0){
        let badge = document.getElementById("notificacaoBadge");
        badge.textContent = data.total;
        badge.classList.remove("d-none");
    }

});
</script>
</body>
</html>
