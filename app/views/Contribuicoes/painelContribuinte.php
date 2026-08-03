<?php
use App\Core\Auth;
?>
<head>
    <link rel="stylesheet" href="<?=BASE_URL?>/assets/css/global.css">
<link rel="stylesheet" href="<?=BASE_URL?>/assets/css/jquery/flatpickr.min.css">
</head>
<div class="container-fluid py-4">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="fw-bold mb-0"><?= $membro['nome'] ?? 'Contribuinte' ?></h4>
      <small class="text-muted">Histórico de contribuições</small>
    </div>

    <a href="<?= BASE_URL ?>/contribuicao/" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Voltar
    </a>
  </div>

  <!-- FILTRO -->
  <div class="card shadow-sm mb-4">
    <div class="card-body">
	<fieldset>
	<legend class="float-none w-auto px-3">
	<i class="fas fa-filter"></i> Filtrar por datas
	</legend>
      <form id="formFiltro">
        <input type="hidden" value="<?= $membro['id'] ?>">

        <div class="row g-3 align-items-end">
          <div class="col-md-3">
            <label class="form-label">Data inicial</label>
            <input type="text" name="data_inicio" class="form-control datepicker input-material" placeholder="dd-mm-aaaa">
          </div>

          <div class="col-md-3">
            <label class="form-label">Data final</label>
            <input type="text" name="data_fim" class="form-control datepicker input-material" placeholder="dd-mm-aaaa">
          </div>

          <div class="col-md-2">
            <button class="btn btn-primary w-100">
              <i class="bi bi-search"></i> Buscar
            </button>
          </div>
        </div>
      </form>
	</fieldset>
	
  <!-- TABELA -->
	  <table class="table table-hover">
        <thead>
          <tr>
            <th>Data</th>
            <th class="text-end">Valor</th>
            <th>Observação</th>
            <th class="text-center">Ação</th>
          </tr>
        </thead>
        <tbody id="tabelaContribuicoes"></tbody>
      </table>
	  <div id="paginacaoContribuicoes" class="mt-3"></div>
    </div>
    </div>
  </div>
<?php if (Auth::isGestor()): ?>
<!-- MODAL EDITAR -->
<div class="modal fade" id="modalEditarContribuicao" tabindex="-1">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Editar Dízimo</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="formEditarContribuicao">
        <div class="modal-body">

          <input type="hidden" id="editId">

          <div class="mb-3">
            <label class="form-label">Data</label>
            <input type="text" id="editData" class="form-control datepicker input-material">
          </div>

          <div class="mb-3">
            <label class="form-label">Valor</label>
            <input type="text" id="editQuantia" class="form-control input-material">
          </div>

          <div class="mb-3">
            <label class="form-label">Observação</label>
            <textarea id="editObs" class="form-control input-material"></textarea>
          </div>

        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button class="btn btn-success">
            <i class="bi bi-save"></i> Salvar
          </button>
        </div>
      </form>

    </div>
  </div>
</div>
<?php endif?>
<script src="<?= BASE_URL?>/assets/js/jquery/jquery-3.6.0.min.js"></script>
<script src="<?= BASE_URL?>/assets/js/jquery/flatpickr.js"></script>
<script>
const membroId = <?= (int) $_GET['id'] ?>;
let page = 1;
let totalPages = 1;
let contribuicoes = [];

flatpickr(".datepicker", {
  dateFormat: "d-m-Y",
  allowInput: true
});

function carregarContribuicoes(p = 1) {

  page = p;

  const inicio = document.querySelector('[name="data_inicio"]').value;
  const fim    = document.querySelector('[name="data_fim"]').value;

  const params = new URLSearchParams({
    membro_id: membroId,
    inicio,
    fim,
    page
  });

  fetch("<?= BASE_URL ?>/contribuicao/contribuicaoPorMembro?" + params)
    .then(r => r.json())
    .then(res => {
      contribuicoes = res.success ? res.data : [];
      totalPages = res.pagination?.totalPages ?? 1;
      renderTabela();
      renderPaginacao();
    })
    .catch(() => {
      contribuicoes = [];
      renderTabela();
    });
}

function renderTabela() {

  const tbody = document.getElementById('tabelaContribuicoes');
  tbody.innerHTML = '';

  if (!contribuicoes.length) {
    tbody.innerHTML = `
      <tr>
        <td colspan="4" class="text-center text-muted bg-warning-subtle py-4">
          <i class="fas fa-info-circle text-warning fs-4"></i>  Nenhuma Contribuição encontrada
        </td>
      </tr>`;
    return;
  }

  contribuicoes.forEach(c => {
    tbody.innerHTML += `
      <tr>
        <td>${c.data}</td>
        <td class="text-end fw-bold">
          ${Number(c.quantia).toLocaleString('pt-PT', { minimumFractionDigits: 2 })}
        </td>
        <td>${c.observacao ?? '-'}</td>
        <td class="text-center">
          <button class="btn btn-sm btn-outline-primary"
            onclick="editar(${c.id})">
            <i class="fas fa-pen"></i>Editar
          </button>
		  <button class="btn btn-sm btn-outline-danger"
            onclick="apagar(${c.id})">
            <i class="fas fa-close"></i>Apagar
          </button>
        </td>
      </tr>
    `;
  });
}

/* =========================
 * EDITAR
 * ========================= */
function editar(id) {

  const c = contribuicoes.find(x => x.id == id);
  if (!c) return;

  document.getElementById('editId').value      = c.id;
  document.getElementById('editData').value    = c.data;
  document.getElementById('editQuantia').value = c.quantia;
  document.getElementById('editObs').value     = c.observacao ?? '';

  bootstrap.Modal
    .getOrCreateInstance(document.getElementById('modalEditarContribuicao'))
    .show();
}
/* =========================
 * CONFIRMAÇÃO ANTES DE APAGAR
 * ========================= */
function apagar(id){

confirmarAcao({
  titulo:'Apagar Contribuição',
  mensagem:'Deseja realmente apagar essa contribuição?',
  onConfirm: async ()=>{

    const res = await fetch(`<?= BASE_URL ?>/contribuicao/deletecontribuicao/${id}`,{
      method:'POST'
    });

    const json = await res.json();

    if(!json.success){
      alert(json.message);
      return;
    }

    carregarContribuicoes();
  }
});

}

/* =========================
 * SALVAR EDIÇÃO
 * ========================= */
document.getElementById('formEditarContribuicao').addEventListener('submit', async e => {
  e.preventDefault();

  const payload = {
    id:        document.getElementById('editId').value,
    data:      document.getElementById('editData').value,
    quantia:   document.getElementById('editQuantia').value,
    observacao:document.getElementById('editObs').value
  };

  try {
    const response = await fetch("<?= BASE_URL ?>/contribuicao/atualizarcontribuicao", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest"
      },
      body: JSON.stringify(payload)
    });

    const res = await response.json();

    if (!res.success) {
      alert(res.message || "Erro ao atualizar dízimo");
      return;
    }

    // Fecha modal
    bootstrap.Modal
      .getInstance(document.getElementById('modalEditarContribuicao'))
      .hide();

    // Recarrega tabela
    carregarContribuicoes();

  } catch (err) {
    alert("Erro de comunicação com o servidor");
  }
});

/* =========================
 * Paginacao
 * ========================= */
function renderPaginacao() {

  const container = document.getElementById('paginacaoContribuicoes');

  // limpa sempre antes de renderizar
  container.innerHTML = '';

  if (totalPages <= 1) return;

  let html = `<nav>
    <ul class="pagination justify-content-center">`;

  for (let i = 1; i <= totalPages; i++) {
    html += `
      <li class="page-item ${i === page ? 'active' : ''}">
        <button class="page-link" data-page="${i}">
          ${i}
        </button>
      </li>`;
  }

  html += `</ul></nav>`;

  container.innerHTML = html;

  // eventos
  container.querySelectorAll('.page-link').forEach(btn => {
    btn.addEventListener('click', () => {
      carregarContribuicoes(Number(btn.dataset.page));
    });
  });
}
/* =========================
 * FILTRO
 * ========================= */
document.getElementById('formFiltro').addEventListener('submit', e => {
  e.preventDefault();
  carregarContribuicoes(1);
});

carregarContribuicoes();
</script>

