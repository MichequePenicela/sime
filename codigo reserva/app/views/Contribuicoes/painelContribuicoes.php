<?php Use App\core\Auth; ?>

<div class="container-fluid py-4">

<!-- ===============================
HEADER DASHBOARD
================================ -->
<div class="card border-0 shadow-sm mb-4">
<div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">

<div>
<h4 class="fw-bold mb-1">
<i class="fa-solid fa-hand-holding-dollar text-primary"></i>
Painel das Contribuições
</h4>

<div class="text-muted small">
Período atual:
<span class="fw-semibold text-primary">
<?= date('01/m/Y') ?> — <?= date('t/m/Y') ?>
</span>
</div>
</div>

<?php if (Auth::isGestor()): ?>
<button class="btn btn-primary px-4 shadow-sm"
data-bs-toggle="modal"
data-bs-target="#modalContribuicao">

<i class="fas fa-plus me-2"></i>
Nova Contribuição
</button>
<?php endif; ?>

</div>
</div>

<!-- ===============================
FILTRO PROFISSIONAL
================================ -->
<div class="card shadow-sm border-0 mb-4">
<div class="card-body">

<div class="row align-items-end g-3">

<div class="col-md-6">
<label class="form-label fw-semibold">Pesquisar Contribuinte</label>

<div class="input-group">
<span class="input-group-text bg-light">
<i class="fa-solid fa-search text-primary"></i>
</span>
<input type="text"
id="searchNome"
class="form-control"
placeholder="Digite o nome do membro...">
</div>
</div>

<div class="col-md-2">
<button class="btn btn-primary w-100 shadow-sm"
id="btnBuscar">

<i class="fa-solid fa-magnifying-glass me-1"></i>
Buscar
</button>
</div>

</div>

</div>
</div>

<!-- ===============================
TABELA PROFISSIONAL
================================ -->
<div class="card shadow-sm border-0">

<div class="card-body p-0">

<table class="table table-hover align-middle mb-0">

<thead>
<tr>
<th class="ps-4">Membro</th>
<th class="text-center">Contribuições</th>
<th class="text-end">Total do Mês</th>
<th class="text-center pe-4">Ações</th>
</tr>
</thead>

<tbody id="tabelaContribuintes"></tbody>

</table>

</div>

</div>

<!-- PAGINAÇÃO -->
<div class="d-flex justify-content-center mt-4"
id="paginacao"></div>

</div>

<div class="modal fade" id="modalContribuicao">
<div class="modal-dialog modal-md modal-dialog-centered">

<form id="formContribuicao" class="modal-content border-0 shadow">

<div class="modal-header bg-primary text-white">
<h5 class="modal-title">
<i class="fa-solid fa-coins me-2"></i>
Registrar Contribuição
</h5>
<button class="btn-close btn-close-white"
data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<label class="fw-semibold">Membro</label>

<input type="hidden" name="membro_id" id="membro_id">

<div class="input-group mb-2">
<span class="input-group-text">
<i class="fa-solid fa-user"></i>
</span>
<input type="text"
id="searchMembro"
class="form-control"
placeholder="Pesquisar membro...">
<button id="btnsearchMembro"
class="btn btn-outline-primary"
type="button">
<i class="fa-solid fa-search"></i>
</button>
</div>

<div id="searchMembroResults"
class="mb-3"></div>

<div class="mb-3">
<label class="fw-semibold">Data</label>
<div class="input-group">
<span class="input-group-text">
<i class="fa-solid fa-calendar"></i>
</span>
<input type="text"
name="data"
class="form-control datepicker">
</div>
</div>

<div class="mb-3">
<label class="fw-semibold">Valor</label>
<div class="input-group">
<span class="input-group-text">
<i class="fa-solid fa-coins"></i>
</span>
<input type="text"
name="quantia"
class="form-control"
required>
</div>
</div>

</div>

<div class="modal-footer bg-light">
<button class="btn btn-primary px-4">
Salvar Contribuição
</button>
</div>

</form>
</div>
</div>
<script src="<?= BASE_URL?>/assets/js/jquery/jquery-3.6.0.min.js"></script>
<script src="<?= BASE_URL?>/assets/js/jquery/flatpickr.js"></script>
<script>
  $(".datepicker").flatpickr({
    dateFormat: "d-m-Y",
	allowInput: true
});
/* ======================================================
 * ESTADO GLOBAL
 * ====================================================== */
let contribuintes = [];
let paginacaoInfo = {
    page: 1,
    perPage: 10,
    total: 0,
    totalPages: 1
};

/* ======================================================
 * CARREGAR CONTRIBUINTES (AJAX)
 * ====================================================== */
function carregarContribuintes(page = 1) {

    const nome = document.getElementById('searchNome').value.trim();

    const params = new URLSearchParams({
        page: page,
        nome: nome
    });

    fetch("<?= BASE_URL ?>/contribuicao/buscarContribuinte?" + params.toString())
        .then(response => response.json())
        .then(res => {

            if (!res.success) {
                contribuintes = [];
                paginacaoInfo = {
                    page: 1,
                    perPage: 10,
                    total: 0,
                    totalPages: 1
                };
                renderTabela();
                renderPaginacao();
                return;
            }

            /* 🔥 CONTRATO CORRETO */
            contribuintes = res.data;

            paginacaoInfo = {
                page: res.pagination.page,
                perPage: res.pagination.perPage,
                total: res.pagination.total,
                totalPages: res.pagination.totalPages
            };

            renderTabela();
            renderPaginacao();
        })
        .catch(err => {
            console.error(err);
            alert('Erro ao comunicar com o servidor');
        });
}

/* ======================================================
 * RENDER TABELA
 * ====================================================== */
function renderTabela(){

const tbody = document.getElementById('tabelaContribuintes');
tbody.innerHTML = "";

if(!contribuintes.length){

tbody.innerHTML = `
<tr>
<td colspan="4" class="text-center py-5 text-muted">
<i class="fa-solid fa-circle-info fs-3 d-block mb-2 text-primary"></i>
Nenhum contribuinte encontrado
</td>
</tr>
`;
return;
}

contribuintes.forEach(c=>{

tbody.innerHTML+=`
<tr>
<td class="ps-4 fw-semibold">${c.nome}</td>

<td class="text-center">
<span class="badge bg-primary-subtle text-primary">
${c.qtd_contribuicoes}
</span>
</td>

<td class="text-end fw-bold text-success">
 ${Number(c.total_mes).toLocaleString('pt-PT',{
minimumFractionDigits:2
})} MT
</td>

<td class="text-center pe-4">
<a href="<?= BASE_URL ?>/contribuicao/dashboard?id=${c.id}"
class="btn btn-md btn-outline-primary">
<i class="fa-solid fa-eye"></i>
</a>
</td>
</tr>
`;
});
}

/* ======================================================
 * RENDER PAGINAÇÃO
 * ====================================================== */
function renderPaginacao() {
    const div = document.getElementById('paginacao');
    div.innerHTML = "";

    if (paginacaoInfo.totalPages <= 1) return;

    const prev = document.createElement('button');
    prev.className = 'btn btn-outline-secondary me-2';
    prev.textContent = 'Anterior';
    prev.disabled = paginacaoInfo.page === 1;
    prev.onclick = () => carregarContribuintes(paginacaoInfo.page - 1);

    const info = document.createElement('span');
    info.className = 'mx-3 align-self-center fw-semibold';
    info.textContent = `Página ${paginacaoInfo.page} de ${paginacaoInfo.totalPages}`;

    const next = document.createElement('button');
    next.className = 'btn btn-outline-secondary ms-2';
    next.textContent = 'Próxima';
    next.disabled = paginacaoInfo.page === paginacaoInfo.totalPages;
    next.onclick = () => carregarContribuintes(paginacaoInfo.page + 1);

    div.append(prev, info, next);
}

/* ======================================================
 * EVENTOS
 * ====================================================== */
document.getElementById('btnBuscar').addEventListener('click', () => {
    carregarContribuintes(1);
});

document.getElementById('searchNome').addEventListener('keyup', e => {
    if (e.key === 'Enter') {
        carregarContribuintes(1);
    }
});

/* ======================================================
 * LOAD INICIAL
 * ====================================================== */
carregarContribuintes();
</script>
<script>
  /* ===============================
   PESQUISA DE MEMBRO (DÍZIMO)
================================ */
$("#btnsearchMembro").on("click", function () {

const termo = $("#searchMembro").val().trim();

if (termo.length < 2) {
    $("#searchMembroResults").html(
        '<div class="text-muted">Digite ao menos 2 letras</div>'
    );
    return;
}

$.ajax({
    url: "<?= BASE_URL ?>/membros/search",
    method: "GET",
    data: { nome: termo, page: 1 }, // 🔥 força página 1
    dataType: "json",
    success: function (res) {

        let html = "";

        // 🔥 NOVO FORMATO PAGINADO
        if (!res.success || !res.data.length) {
            html = '<div class="text-danger">Nenhum membro encontrado</div>';
        } else {

            html += '<ul class="list-group">';

            res.data.forEach(m => {
                html += `
                    <li class="list-group-item list-group-item-action membro-item"
                        data-id="${m.id}"
                        data-nome="${m.nome}">
                        ${m.nome}
                    </li>
                `;
            });

            html += '</ul>';
        }

        $("#searchMembroResults").html(html);
    },
    error: function () {
        $("#searchMembroResults").html(
            '<div class="text-danger">Erro ao pesquisar membro</div>'
        );
    }
});
});

/* ===============================
   SELECIONAR MEMBRO
================================ */
$(document).on("click", ".membro-item", function () {
    $("#membro_id").val($(this).data("id"));
    $("#searchMembro").val($(this).data("nome"));
    $("#searchMembroResults").html("");
    // 🔥 Desabilitar Campo de pesquisa
    $("#searchMembro").prop("disabled", true);
    // 🔥 Desabilitar botao de pesquisa
    $("#btnsearchMembro").addClass("d-none");
});

/* ===============================
   SUBMIT DÍZIMO
================================ */
$("#formContribuicao").on("submit", function (e) {
    e.preventDefault();

    if (!$("#membro_id").val()) {
        showToast("Aviso", "Selecione um membro antes de salvar a contribuição");
        return;
    }

    $.ajax({
        url: "<?= BASE_URL ?>/contribuicao/addContribuicao",
        method: "POST",
        data: $(this).serialize(),
        dataType: "json",
        success: function (resp) {
            showToast(resp.message, resp.success ? "success" : "danger");
            if (resp.success) location.reload();
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            showToast("Erro", "Erro ao comunicar com o servidor");
        }
    });
});

/* ===============================
   prevenir duplo click
================================ */
$("form").on("submit", function(){
    $(this).find("button[type=submit]").prop("disabled", true);
});
/* ===============================
   LIMPAR MODAIS
================================ */
$("#modalContribuicao").on("hidden.bs.modal", function () {
    $("#formContribuicao")[0].reset();
    $("#searchMembroResults").html("");
    $("#btnsearchMembro").removeClass("d-none");
    $("#searchMembro").prop("disabled", false);

});
</script>