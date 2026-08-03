<div class="container-fluid py-4">

  <!-- TÍTULO -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="fw-bold mb-0">Dashboard de Dízimos</h4>
      <small class="text-muted">
	  Dizimistas do mês atual:
	  <span class="text-primary">
            <?= date('01/m/Y') ?> — <?= date('t/m/Y') ?>
        </span>
	  </small>
    </div>
  </div>

  <!-- FILTRO -->
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <div class="row g-3 align-items-end">

        <div class="col-md-4">
          <label class="form-label">Pesquisar dizimista</label>
          <input type="text" id="searchNome" class="form-control input-material"
                 placeholder="Digite o nome do dizimista">
        </div>

        <div class="col-md-2">
          <button class="btn btn-primary w-100" id="btnBuscar">
            <i class="fas fa-search"></i> Buscar
          </button>
        </div>

      </div>
    </div>
  </div>

  <!-- TABELA -->
    <div class="card-body p-0">
      <table class="table table-hover mb-0 align-middle table-borderless">
        <thead>
          <tr>
            <th>Dizimista</th>
            <th class="text-center">Qtd. Dízimos</th>
            <th class="text-end">Total do Mês</th>
            <th class="text-center">Ações</th>
          </tr>
        </thead>
        <tbody id="tabelaDizimistas">
          <!-- AJAX -->
        </tbody>
      </table>
    </div>

  <!-- PAGINAÇÃO -->
  <div class="d-flex justify-content-center mt-4" id="paginacao"></div>

</div>
<script>
/* ======================================================
 * ESTADO GLOBAL
 * ====================================================== */
let dizimistas = [];
let paginacaoInfo = {
    page: 1,
    perPage: 10,
    total: 0,
    totalPages: 1
};

/* ======================================================
 * CARREGAR DIZIMISTAS (AJAX)
 * ====================================================== */
function carregarDizimistas(page = 1) {

    const nome = document.getElementById('searchNome').value.trim();

    const params = new URLSearchParams({
        page: page,
        nome: nome
    });

    fetch("<?= BASE_URL ?>/membros/buscarDizimista?" + params.toString())
        .then(response => response.json())
        .then(res => {

            if (!res.success) {
                dizimistas = [];
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
            dizimistas = res.data;

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
function renderTabela() {
    const tbody = document.getElementById('tabelaDizimistas');
    tbody.innerHTML = "";

    if (!dizimistas.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center text-muted py-4">
                    Nenhum dizimista encontrado
                </td>
            </tr>
        `;
        return;
    }

    dizimistas.forEach(d => {
        tbody.innerHTML += `
            <tr>
                <td>${d.nome}</td>
                <td class="text-center">${d.qtd_dizimos}</td>
                <td class="text-end fw-bold">
                    ${Number(d.total_mes).toLocaleString('pt-PT', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })}
                </td>
                <td class="text-center">
                    <a href="<?= BASE_URL ?>/membros/painelIndividual?id=${d.id}"
                       class="btn btn-sm btn-outline-primary">
                        Ver detalhes
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
    prev.onclick = () => carregarDizimistas(paginacaoInfo.page - 1);

    const info = document.createElement('span');
    info.className = 'mx-3 align-self-center fw-semibold';
    info.textContent = `Página ${paginacaoInfo.page} de ${paginacaoInfo.totalPages}`;

    const next = document.createElement('button');
    next.className = 'btn btn-outline-secondary ms-2';
    next.textContent = 'Próxima';
    next.disabled = paginacaoInfo.page === paginacaoInfo.totalPages;
    next.onclick = () => carregarDizimistas(paginacaoInfo.page + 1);

    div.append(prev, info, next);
}

/* ======================================================
 * EVENTOS
 * ====================================================== */
document.getElementById('btnBuscar').addEventListener('click', () => {
    carregarDizimistas(1);
});

document.getElementById('searchNome').addEventListener('keyup', e => {
    if (e.key === 'Enter') {
        carregarDizimistas(1);
    }
});

/* ======================================================
 * LOAD INICIAL
 * ====================================================== */
carregarDizimistas();
</script>
