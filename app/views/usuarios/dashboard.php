<?php
use App\Core\Auth;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Controle de Usuários</title>

<style>

/* HEADER */
.page-header{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:20px;
}

/* CARDS */
.stat-card{
border-radius:10px;
transition:.2s;
}

.stat-card:hover{
transform:translateY(-3px);
box-shadow:0 6px 15px rgba(0,0,0,.10);
}

.stat-icon{
font-size:28px;
opacity:.8;
}

/* SEARCH */
.search-box input{
border-radius:50px;
}

/* TABLE */
.table thead{
font-size:.85rem;
text-transform:uppercase;
letter-spacing:.5px;
}

.badge-status{
font-size:.75rem;
padding:6px 10px;
border-radius:20px;
}

</style>

</head>

<body>

<div class="container-fluid py-4">

<!-- HEADER -->
<div class="page-header">

<h4 class="fw-bold mb-0">

<i class="fas fa-users-cog text-primary me-2"></i>
Controle de Usuários

</h4>

<?php if (Auth::isAdmin()): ?>

<button class="btn btn-primary shadow-sm"
data-bs-toggle="modal"
data-bs-target="#modalUsuario">

<i class="fas fa-user-plus"></i>
Novo Usuário

</button>

<?php endif?>

</div>


<!-- CARDS -->
<div class="row g-3 mb-4">

<div class="col-md-4">

<div class="card stat-card shadow-sm border-0">

<div class="card-body d-flex justify-content-between align-items-center">

<div>

<small class="text-muted">
Total Usuários
</small>

<h3 id="totalUsuarios" class="mb-0 fw-bold">
0
</h3>

</div>

<i class="fas fa-users stat-icon text-primary"></i>

</div>

</div>

</div>


<div class="col-md-4">

<div class="card stat-card shadow-sm border-0">

<div class="card-body d-flex justify-content-between align-items-center">

<div>

<small class="text-muted">
Usuários Ativos
</small>

<h3 id="usuariosAtivos" class="mb-0 fw-bold text-success">
0
</h3>

</div>

<i class="fas fa-user-check stat-icon text-success"></i>

</div>

</div>

</div>


<div class="col-md-4">

<div class="card stat-card shadow-sm border-0">

<div class="card-body d-flex justify-content-between align-items-center">

<div>

<small class="text-muted">
Usuários Inativos
</small>

<h3 id="usuariosInativos" class="mb-0 fw-bold text-danger">
0
</h3>

</div>

<i class="fas fa-user-times stat-icon text-danger"></i>

</div>

</div>

</div>

</div>


<!-- PESQUISA -->
<div class="card shadow-sm mb-3 border-0">

<div class="card-body">

<div class="input-group search-box">

<span class="input-group-text bg-white border-end-0">

<i class="fas fa-search text-muted"></i>

</span>

<input type="text"

id="searchUsuario"

class="form-control border-start-0 input-material"

placeholder="Pesquisar por nome ou usuário (mínimo 3 letras)">

</div>

</div>

</div>



<!-- TABELA -->
<div class="card shadow-sm border-0">

<div class="card-body table-responsive">

<table class="table table-hover align-middle">

<thead class="table-light">

<tr>

<th>#</th>

<th>Nome</th>

<th>Usuário</th>

<th>Privilégio</th>

<th>Status</th>

<th class="text-end">Ações</th>

</tr>

</thead>

<tbody id="usuariosTable">

<tr>

<td colspan="6" class="text-center text-muted">

Digite ao menos 3 caracteres para pesquisar

</td>

</tr>

</tbody>

</table>

</div>

</div>

</div>



<script src="<?= BASE_URL ?>/assets/js/jquery/jquery-3.6.0.min.js"></script>

<script>

$(function(){

function renderTabela(data){

let html='';

let ativos=0;
let inativos=0;

if(!data.length){

$('#usuariosTable').html(`

<tr>

<td colspan="6"

class="text-center text-muted">

Nenhum usuário encontrado

</td>

</tr>

`);

return;

}

data.forEach(u=>{

if(u.status.toLowerCase()==='ativo') ativos++;
else inativos++;

html+=`

<tr>

<td>${u.id}</td>

<td class="fw-semibold">

${u.nome}

</td>

<td>

<span class="text-muted">

${u.usuario}

</span>

</td>

<td>

<span class="badge bg-info-subtle text-dark">

${u.privilegio}

</span>

</td>

<td>

<span class="badge badge-status bg-${u.status.toLowerCase()==='ativo'?'success':'danger'}">

${u.status}

</span>

</td>

<td class="text-end">

<a href="<?= BASE_URL ?>/usuarios/painel?id=${u.id}"

class="btn btn-sm btn-outline-info me-1"

title="Visualizar">

<i class="fas fa-eye"></i>

</a>

<button

class="btn btn-sm btn-outline-danger deleteUsuario"

data-id="${u.id}">

<i class="fas fa-trash"></i>

</button>

</td>

</tr>

`;

});

$('#usuariosTable').html(html);

$('#totalUsuarios').text(data.length);

$('#usuariosAtivos').text(ativos);

$('#usuariosInativos').text(inativos);

}



$('#searchUsuario').on('keyup',function(){

const termo=$(this).val().trim();

if(termo.length<3){

return;

}

$.get(

'<?= BASE_URL ?>/usuarios/search',

{q:termo},

res=>renderTabela(res),

'json'

);

});



$('#usuarioForm').submit(function(e){

e.preventDefault();

if($('#senha').val()!==$('#confirmarSenha').val()){

alert('As senhas não coincidem');

return;

}

$.post(

'<?= BASE_URL ?>/usuarios/add',

$(this).serialize(),

function(res){

if(res.success){

$('#modalUsuario').modal('hide');

$('#usuarioForm')[0].reset();

$('#searchUsuario').trigger('keyup');

}else{

alert(res.message);

}

},

'json'

);

});

});

</script>

</body>

</html>