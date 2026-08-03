<?php
use App\Core\Auth;
?>

<style>

/* ======================
HEADER
====================== */

.page-header{

background:#fff;
border-radius:12px;
padding:20px;

box-shadow:0 3px 12px rgba(0,0,0,.06);

margin-bottom:25px;

}


/* ======================
DASHBOARD CARDS
====================== */

.dashboard-card{

border-radius:12px;
border:none;

transition:.25s ease;

}

.dashboard-card:hover{

transform:translateY(-5px);

box-shadow:0 12px 22px rgba(0,0,0,.08);

}

.stat-number{

font-size:28px;
font-weight:700;

}


/* ======================
REGISTRO CARD
====================== */

.registro-card{

border-radius:14px;

overflow:hidden;

transition:.25s ease;

}

.registro-card:hover{

transform:translateY(-6px);

box-shadow:0 15px 25px rgba(0,0,0,.08);

}


/* HEADER CARD */

.registro-card .card-header{

background:#fafafa;

border-bottom:none;

}

/* BADGE TOTAL */

.totalBadge{

font-size:.85rem;

padding:6px 10px;

}


/* CAMPOS */

.camposView div{

padding:6px 0;

border-bottom:1px dashed #eee;

}


/* OBSERVAÇÃO */

.observacaoEdit{

min-height:45px;

background:#fafafa;

border-top:1px solid #f0f0f0;

}


/* TAG */

.observacaoEdit .badge{

background:#e9ecef !important;

color:#333;

font-weight:500;

}


/* MODAL */

.modal-content{

border-radius:14px;

border:none;

box-shadow:0 15px 40px rgba(0,0,0,.15);

}


/* LABEL */

label{

text-transform:uppercase;

font-size:.75rem;

font-weight:600;

letter-spacing:.5px;

color:#6c757d;

}

</style>



<div class="container mt-4">


<!-- HEADER -->

<div class="page-header d-flex justify-content-between align-items-center flex-wrap">

<div>

<h5 class="fw-bold mb-1">

<i class="fas fa-chart-line text-primary me-2"></i>

Participações

</h5>

<small class="text-muted">

<?= date('01/m/Y') ?> — <?= date('t/m/Y') ?>

</small>

</div>


<?php if (Auth::isGestor()): ?>

<button class="btn btn-primary"

data-bs-toggle="modal"

data-bs-target="#novoRegistroModal">

<i class="fas fa-plus me-1"></i>

Novo Registro

</button>

<?php endif ?>

</div>



<!-- DASHBOARD -->

<div class="row mb-4 g-3">

<div class="col-md-6">

<div class="card dashboard-card shadow-sm text-center p-3">

<small class="text-muted">

Visitantes

</small>

<div class="stat-number text-primary">

<?= $stats['visitantes'] ?? 0 ?>

</div>

</div>

</div>


<div class="col-md-6">

<div class="card dashboard-card shadow-sm text-center p-3">

<small class="text-muted">

Convertidos

</small>

<div class="stat-number text-success">

<?= $stats['convertidos'] ?? 0 ?>

</div>

</div>

</div>

</div>



<div class="row g-3">


<?php if(!empty($registros)): ?>
<?php foreach($registros as $r): ?>

<?php
$total=(int)$r['pais']+(int)$r['maes']+(int)$r['jovens']+(int)$r['dominical'];
?>

<div class="col-lg-4 col-md-6">

<div class="card shadow-sm h-100 registro-card"

data-id="<?=$r['id']?>">

<div class="card-header d-flex justify-content-between align-items-center bg-primary-subtle">


<strong class="dataView">

<i class="fas fa-calendar-alt text-primary me-1"></i>

<span><?=date('d/m/Y',strtotime($r['data']))?></span>

</strong>


<?php if (Auth::isGestor()): ?>

<input type="text"

class="form-control input-material form-control-sm dataEdit d-none datepickerEdit"

value="<?=date('Y-m-d',strtotime($r['data']))?>"

data-campo="data">

<div class="d-flex gap-2 align-items-center">
<?php endif ?>
<span class="badge bg-primary totalBadge">

Total <?=$total?>

</span>

<?php if (Auth::isGestor()) :?>
<button class="btn btn-sm btn-outline-warning btnEditar">

<i class="fas fa-pen"></i>

</button>


<button class="btn btn-sm btn-outline-danger btnExcluir"

data-id="<?=$r['id']?>">

<i class="fas fa-trash"></i>

</button>

</div>

<?php endif ?>

</div>


<div class="card-body small">


<div class="row camposView">

<div class="col-6">

Pais <strong><?=$r['pais']?></strong>

</div>

<div class="col-6">

Mães <strong><?=$r['maes']?></strong>

</div>

<div class="col-6">

Jovens <strong><?=$r['jovens']?></strong>

</div>

<div class="col-6">

Dominical <strong><?=$r['dominical']?></strong>

</div>

<div class="col-6">

Visitantes <strong><?=$r['visitantes']?></strong>

</div>

<div class="col-6">

Convertidos <strong><?=$r['convertidos']?></strong>

</div>

</div>


<?php if (Auth::isGestor()): ?>

<div class="row camposEdit d-none">

<?php foreach(['pais','maes','jovens','dominical','visitantes','convertidos'] as $c): ?>

<div class="col-6 mb-2">

<input type="number"

class="form-control input-material form-control-sm campoEdit"

data-campo="<?=$c?>"

value="<?=$r[$c]?>">

</div>

<?php endforeach; ?>

</div>

<?php endif ?>

</div>



<div class="card-footer input-material text-muted small observacaoEdit"

contenteditable="false"

data-campo="observacao"

data-original="<?= htmlspecialchars($r['observacao'] ?? '', ENT_QUOTES) ?>">

<?php 

$obs = $r['observacao'] ?? '';

if(trim($obs) !== ''):

$tags = array_filter(array_map('trim', explode(',', $obs)));

foreach($tags as $tag):

?>

<span class="badge me-1 mb-1">

<?= htmlspecialchars($tag) ?>

</span>

<?php endforeach; endif; ?>

</div>

</div>

</div>


<?php endforeach;?>
<?php endif; ?>


</div>

</div>

<!-- MODAL NOVO REGISTRO -->
<div class="modal fade" id="novoRegistroModal" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">

<form id="formNovoRegistro">

<div class="modal-header">
<h5 class="modal-title">Novo Registro</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<div class="row g-3">

<div class="col-12">
    <label class="small text-muted">Data</label>
<input type="text"
       name="data"
       class="form-control input-material datepicker"
       placeholder="Data"
       required>
</div>

<?php foreach(['pais','maes','jovens','dominical','visitantes','convertidos'] as $c): ?>
<div class="col-6">
<label class="small text-muted "><?=$c?>:</label>
<input type="number"
       name="<?=$c?>"
       class="form-control input-material"
       placeholder="<?=$c?>"
       required>
</div>
<?php endforeach; ?>

<div class="col-12">
<textarea name="observacao"
          class="form-control input-material"
          placeholder="Observação"></textarea>
</div>

</div>
</div>

<div class="modal-footer">
<button type="button"
        class="btn btn-secondary"
        data-bs-dismiss="modal">
    Cancelar
</button>

<button type="submit"
        class="btn btn-primary">
    Salvar
</button>
</div>

</form>

</div>
</div>
</div>
<style>
   label{
    text-transform: uppercase;
   } 

</style>
<script src="<?= BASE_URL ?>/assets/js/jquery/jquery-3.6.0.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/jquery/flatpickr.js"></script>

<script>
// ======================
// FLATPICKR
// ======================
$('.datepicker').flatpickr({dateFormat:"Y-m-d"});
$('.datepickerEdit').flatpickr({dateFormat:"Y-m-d"});

// ======================
// NOVO REGISTRO (AJAX)
// ======================
document.getElementById('formNovoRegistro')
.addEventListener('submit', async function(e){

    e.preventDefault();

    const form = this;
    const btn  = form.querySelector('button[type="submit"]');

    btn.disabled = true;
    btn.innerHTML = 'Salvando...';

    try {

        const formData = new FormData(form);

        const response = await fetch("<?= BASE_URL ?>/membros/add", {
            method: "POST",
            body: formData
        });

        const data = await response.json();

        if(!data.success){
            alert(data.message);
            return;
        }

        form.reset();

        const modal = bootstrap.Modal.getInstance(
            document.getElementById('novoRegistroModal')
        );
        modal.hide();

        location.reload(); // reload seguro (GET)

    } catch(error){
        alert("Erro ao salvar registro");
        console.error(error);
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Salvar';
    }

});

// ======================
// EDITAR
// ======================
$(document).on('click','.btnEditar',function(){

let card=$(this).closest('.registro-card');
let obs=card.find('.observacaoEdit');

// Alternar campos principais
card.find('.camposView').toggleClass('d-none');
card.find('.camposEdit').toggleClass('d-none');
card.find('.dataView').toggleClass('d-none');
card.find('.dataEdit').toggleClass('d-none');

// ==========================
// OBSERVAÇÃO
// ==========================

if(obs.attr('contenteditable') === 'true'){

    // ===== SAINDO DO MODO EDIÇÃO =====

    // pega exatamente o que o usuário digitou
    let texto = obs.text().trim();

    // salva como fonte oficial
    obs.attr('data-original', texto);

    // gera badges a partir do texto salvo
    let tags = texto.split(',')
                    .map(t => t.trim())
                    .filter(t => t !== '');

    let html = '';
    tags.forEach(t => {
        html += `<span class="badge bg-secondary me-1 mb-1">${t}</span>`;
    });

    obs.html(html);
    obs.attr('contenteditable','false');

}else{

    // ===== ENTRANDO EM MODO EDIÇÃO =====

    // usa sempre o texto original do banco
    let original = obs.attr('data-original') || '';

    obs.text(original); // IMPORTANTE: usar .text() aqui
    obs.attr('contenteditable','true').focus();

}

});

// ======================
// SALVAR CAMPOS
// ======================
$(document).on('blur','.campoEdit,.dataEdit',function(){

let card=$(this).closest('.registro-card');
let dados={id:card.data('id')};

card.find('.campoEdit').each(function(){
dados[$(this).data('campo')]=$(this).val();
});

dados['data']=card.find('.dataEdit').val();

fetch("<?=BASE_URL?>/membros/editarDadosCulto",{
method:"POST",
headers:{'Content-Type':'application/json'},
body:JSON.stringify(dados)
}).then(()=>{

let total=0;
['pais','maes','jovens','dominical'].forEach(c=>{
total+=parseInt(dados[c]||0);
});

card.find('.totalBadge').text('Total '+total);

});

});

// ======================
// OBSERVAÇÃO
// ======================
$(document).on('blur','[contenteditable]',function(){

let card=$(this).closest('.registro-card');

fetch("<?=BASE_URL?>/membros/editarDadosCulto",{
method:"POST",
headers:{'Content-Type':'application/json'},
body:JSON.stringify({
id:card.data('id'),
observacao:$(this).text()
})
});

});

// ===============================
// EXCLUSÃO COM MODAL GLOBAL
// ===============================
$(document).on('click', '.btnExcluir', function () {

let id = $(this).data('id');

// Define título e mensagem
$("#globalConfirmTitle").text("Confirmar Exclusão");
$("#globalConfirmMessage").text("Tem certeza que deseja excluir este registro? Esta ação não poderá ser desfeita.");

// Abre modal
let modal = new bootstrap.Modal(document.getElementById('globalConfirmModal'));
modal.show();

// Remove eventos antigos (IMPORTANTE)
$("#globalConfirmBtn").off("click");

// Define novo evento
$("#globalConfirmBtn").on("click", function () {

    $(this).prop("disabled", true).text("Excluindo...");

    fetch("<?= BASE_URL ?>/membros/excluirDadosCulto", {
        method: "POST",
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
    })
    .then(res => res.json())
    .then(resp => {

        if (resp.success) {

            modal.hide();

            // Remove o card visualmente sem reload (mais profissional)
            $('.registro-card[data-id="'+id+'"]')
                .fadeOut(300, function () {
                    $(this).remove();
                });

        } else {
            alert("Erro ao excluir registro.");
        }

    })
    .catch(() => {
        alert("Erro inesperado.");
    })
    .finally(() => {
        $("#globalConfirmBtn")
            .prop("disabled", false)
            .text("Confirmar");
    });

});

});
</script>