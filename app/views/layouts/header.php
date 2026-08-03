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

            <ul class="navbar-nav align-items-center gap-3">

                <!-- NOTIFICAÇÕES -->
               <li class="nav-item dropdown">

<a class="nav-link position-relative"
href="#"
data-bs-toggle="dropdown">

<i class="fas fa-bell fa-lg"></i>

<span id="badgeNotificacoes"
class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">

0

</span>

</a>

<div class="dropdown-menu dropdown-menu-end p-0"
style="width:320px">

<div id="listaNotificacoes">

<div class="p-3 text-center">

Carregando...

</div>

</div>

</div>

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

async function carregarNotificacoes(){

try{

let resp=await fetch(
"<?= BASE_URL ?>/notificacoes/ajax"
);

let data=await resp.json();

if(!data.success) return;


// BADGE

document
.getElementById('badgeNotificacoes')
.textContent=data.total;


// DROPDOWN

let html='';


//////////////////////////////////////////////////
// ANIVERSÁRIOS
//////////////////////////////////////////////////

if(data.aniversarios.length){

html+=`

<a href="<?=BASE_URL?>/notificacoes"
class="dropdown-item">

🎂 ${data.aniversarios.length}
aniversários próximos

</a>

`;

}


//////////////////////////////////////////////////
// DÍZIMOS
//////////////////////////////////////////////////

if(data.dizimos.length){

html+=`

<a href="<?=BASE_URL?>/notificacoes"
class="dropdown-item">

💰 ${data.dizimos.length}
dízimos recentes

</a>

`;

}


//////////////////////////////////////////////////
// LANÇAMENTOS
//////////////////////////////////////////////////

if(data.lancamentos.length){

html+=`

<a href="<?=BASE_URL?>/notificacoes"
class="dropdown-item">

📊 ${data.lancamentos.length}
lançamentos financeiros

</a>

`;

}


if(!html){

html=`<div class="p-3 text-center text-muted">
Sem notificações
</div>`;

}


document
.getElementById('listaNotificacoes')
.innerHTML=html;

}catch(e){

console.log(e);

}

}


// carregar ao abrir página

carregarNotificacoes();


// atualizar cada 60s (profissional)

setInterval(carregarNotificacoes,60000);

</script>
</body>
</html>