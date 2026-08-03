<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SIME - Dashboard</title>

<link href="<?= BASE_URL ?>/assets/css/bootstrap/bootstrap.min.css" rel="stylesheet">
<link href="<?= BASE_URL ?>/assets/css/fontawesome/css/all.min.css" rel="stylesheet">

<style>

body{
    background:#f4f6f9;
}

.dashboard-header{
    background:white;
    border-radius:12px;
    padding:30px;
    box-shadow:0 4px 15px rgba(0,0,0,0.06);
}

.logo{
    max-width:180px;
}

.card-stat{
    border:none;
    border-radius:12px;
    box-shadow:0 4px 12px rgba(0,0,0,0.05);
    transition:.3s;
}

.card-stat:hover{
    transform:translateY(-4px);
}

.stat-icon{
    font-size:26px;
    padding:18px;
    border-radius:10px;
    color:white;
}

.bg-entradas{background:#0d6efd;}
.bg-membros{background:#198754;}
.bg-utilizadores{background:#6f42c1;}
.bg-dizimo{background:#fd7e14;}
.bg-relatorio{background:#20c997;}

.stat-number{
    font-size:26px;
    font-weight:bold;
}

.policy-card{
    border-radius:12px;
    border:none;
    box-shadow:0 4px 15px rgba(0,0,0,0.05);
}

</style>
</head>

<body>

<div class="container my-3">

<!-- HEADER -->
<div class=" mb-3 text-center">
    <img src="<?= BASE_URL ?>/assets/img/logo-sime.png" class="logo mb-0">
    <h4>
        Bem-vindo, 
        <span class="text-primary"><?=($_SESSION['usuario_nome'])?></span>
    </h4>

    <p class="text-muted">
        Privilégios:
        <span class="badge bg-primary">
            <i class="fas fa-user-shield"></i>
            <?=($_SESSION['nivel'])?>
        </span>
    </p>
</div>
<hr />
<!-- CARDS DE ESTATÍSTICAS -->
<div class="row g-4">

<div class="col-md-4">
<div class="card card-stat p-3">
<div class="d-flex align-items-center">
<div class="stat-icon bg-entradas me-3">
<i class="fas fa-arrow-down"></i>
</div>
<div>
<div class="stat-number counter">
    <?= number_format($dados['total_entradas'], 2, ',', '.') ?>
</div>
<small class="text-muted">Entradas</small>
</div>
</div>
</div>
</div>

<div class="col-md-4">
<div class="card card-stat p-3">
<div class="d-flex align-items-center">
<div class="stat-icon bg-membros me-3">
<i class="fas fa-users"></i>
</div>
<div>
<div class="stat-number counter"><?= $membroscadastrados?></div>
<small class="text-muted">Membros Cadastrados</small>
</div>
</div>
</div>
</div>

<div class="col-md-4">
<div class="card card-stat p-3">
<div class="d-flex align-items-center">
<div class="stat-icon bg-utilizadores me-3">
<i class="fas fa-user"></i>
</div>
<div>
<div class="stat-number counter"><?= $usuarioscadastrados?></div>
<small class="text-muted">Utilizadores</small>
</div>
</div>
</div>
</div>

<div class="col-md-4">
<div class="card card-stat p-3">
<div class="d-flex align-items-center">
<div class="stat-icon bg-dizimo me-3">
<i class="fas fa-hand-holding-usd"></i>
</div>
<div>
<div class="stat-number counter"><?= $totaldizimistas?></div>
<small class="text-muted">Dizimistas</small>
</div>
</div>
</div>
</div>

<div class="col-md-4">
<div class="card card-stat p-3">
<div class="d-flex align-items-center">
<div class="stat-icon bg-relatorio me-3">
<i class="fas fa-chart-bar"></i>
</div>
<div>
<div class="stat-number counter"><?= $relatorioscadastrados?></div>
<small class="text-muted">Relatórios</small>
</div>
</div>
</div>
</div>

</div>

</div>
</body>
</html>