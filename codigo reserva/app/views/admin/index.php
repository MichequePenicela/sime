<?php
use App\Core\Auth;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Configurações do Sistema</title>

<link rel="stylesheet" href="<?=BASE_URL?>/assets/css/bootstrap/bootstrap.min.css">
<link rel="stylesheet" href="<?=BASE_URL?>/assets/css/fontawesome/css/all.min.css">
<link rel="stylesheet" href="<?=BASE_URL?>/assets/css/global.css">

<style>

.page-header{
    border-bottom:1px solid #e9ecef;
    margin-bottom:30px;
    padding-bottom:15px;
}

.card-config{
    border:0;
    border-radius:12px;
    transition:0.25s;
    cursor:pointer;
    background:#fff;
}

.card-config:hover{
    transform:translateY(-4px);
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
}

.config-icon{
    font-size:28px;
    width:55px;
    height:55px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:12px;
}

.icon-blue{background:#e7f1ff;color:#0d6efd;}
.icon-green{background:#e6f7ed;color:#198754;}
.icon-purple{background:#f3e8ff;color:#6f42c1;}
.icon-orange{background:#fff4e6;color:#fd7e14;}
.icon-red{background:#fdeaea;color:#dc3545;}
.icon-dark{background:#e9ecef;color:#212529;}

</style>
</head>

<body>

<div class="container my-5">

<!-- HEADER -->
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h3 class="fw-bold mb-1">
            <i class="fas fa-sliders-h text-primary me-2"></i>
            Configurações do Sistema
        </h3>
        <small class="text-muted">
            Administração geral, segurança e parametrização do sistema
        </small>
    </div>

    <div>
        <span class="badge bg-primary-subtle text-primary">
            Painel Administrativo
        </span>
    </div>
</div>

<!-- GRID -->
<div class="row g-4">

<!-- USUÁRIOS -->
<div class="col-md-4 col-lg-3">
<a href="<?=BASE_URL?>/usuarios">
<div class="card card-config shadow-sm h-100">
<div class="card-body d-flex gap-3 align-items-center">
<div class="config-icon icon-blue">
<i class="fas fa-users-cog"></i>
</div>
<div>
<h6 class="mb-1 fw-semibold">Usuários e Permissões</h6>
<small class="text-muted">Controle de acessos</small>
</div>
</div>
</div>
</a>
</div>

<!-- CONFIG GERAL -->
<div class="col-md-4 col-lg-3">
<a href="<?=BASE_URL?>/system/settings">
<div class="card card-config shadow-sm h-100">
<div class="card-body d-flex gap-3 align-items-center">
<div class="config-icon icon-purple">
<i class="fas fa-cogs"></i>
</div>
<div>
<h6 class="mb-1 fw-semibold">Configurações Gerais</h6>
<small class="text-muted">Parâmetros do sistema</small>
</div>
</div>
</div>
</a>
</div>

<!-- FINANCEIRO -->
<div class="col-md-4 col-lg-3">
<a href="<?=BASE_URL?>/finance/settings">
<div class="card card-config shadow-sm h-100">
<div class="card-body d-flex gap-3 align-items-center">
<div class="config-icon icon-green">
<i class="fas fa-coins"></i>
</div>
<div>
<h6 class="mb-1 fw-semibold">Financeiro</h6>
<small class="text-muted">Contas e regras</small>
</div>
</div>
</div>
</a>
</div>

<!-- RELATÓRIOS -->
<div class="col-md-4 col-lg-3">
<a href="<?=BASE_URL?>/relatorios">
<div class="card card-config shadow-sm h-100">
<div class="card-body d-flex gap-3 align-items-center">
<div class="config-icon icon-orange">
<i class="fas fa-chart-line"></i>
</div>
<div>
<h6 class="mb-1 fw-semibold">Relatórios</h6>
<small class="text-muted">Gestão de relatórios</small>
</div>
</div>
</div>
</a>
</div>

<!-- NOTIFICAÇÕES -->
<div class="col-md-4 col-lg-3">
<a href="<?=BASE_URL?>/notificacoes">
<div class="card card-config shadow-sm h-100">
<div class="card-body d-flex gap-3 align-items-center">
<div class="config-icon icon-dark">
<i class="fas fa-bell"></i>
</div>
<div>
<h6 class="mb-1 fw-semibold">Notificações</h6>
<small class="text-muted">Alertas e avisos</small>
</div>
</div>
</div>
</a>
</div>

<!-- SEGURANÇA -->
<div class="col-md-4 col-lg-3">
<a href="<?=BASE_URL?>/security">
<div class="card card-config shadow-sm h-100">
<div class="card-body d-flex gap-3 align-items-center">
<div class="config-icon icon-red">
<i class="fas fa-shield-alt"></i>
</div>
<div>
<h6 class="mb-1 fw-semibold">Segurança</h6>
<small class="text-muted">Proteção do sistema</small>
</div>
</div>
</div>
</a>
</div>

<!-- INTEGRAÇÕES -->
<div class="col-md-4 col-lg-3">
<a href="<?=BASE_URL?>/integracoes">
<div class="card card-config shadow-sm h-100">
<div class="card-body d-flex gap-3 align-items-center">
<div class="config-icon icon-purple">
<i class="fas fa-plug"></i>
</div>
<div>
<h6 class="mb-1 fw-semibold">Integrações</h6>
<small class="text-muted">APIs e serviços externos</small>
</div>
</div>
</div>
</a>
</div>

<!-- BACKUP -->
<div class="col-md-4 col-lg-3">
<a href="<?=BASE_URL?>/backup">
<div class="card card-config shadow-sm h-100">
<div class="card-body d-flex gap-3 align-items-center">
<div class="config-icon icon-dark">
<i class="fas fa-database"></i>
</div>
<div>
<h6 class="mb-1 fw-semibold">Backup</h6>
<small class="text-muted">Restauração e segurança</small>
</div>
</div>
</div>
</a>
</div>

<!-- APARÊNCIA -->
<div class="col-md-4 col-lg-3">
<a href="<?=BASE_URL?>/appearance">
<div class="card card-config shadow-sm h-100">
<div class="card-body d-flex gap-3 align-items-center">
<div class="config-icon icon-blue">
<i class="fas fa-palette"></i>
</div>
<div>
<h6 class="mb-1 fw-semibold">Aparência</h6>
<small class="text-muted">Tema e layout</small>
</div>
</div>
</div>
</a>
</div>

<!-- DADOS IGREJA -->
<div class="col-md-4 col-lg-3">
<a href="<?=BASE_URL?>/igreja">
<div class="card card-config shadow-sm h-100">
<div class="card-body d-flex gap-3 align-items-center">
<div class="config-icon icon-green">
<i class="fas fa-church"></i>
</div>
<div>
<h6 class="mb-1 fw-semibold">Dados da Igreja</h6>
<small class="text-muted">Informações institucionais</small>
</div>
</div>
</div>
</a>
</div>

</div>
</div>

</body>
</html>