<head>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/jquery/flatpickr.min.css">
</head>
<body>
<div class="container mt-4">
    <fieldset class="border p-3 rounded">
        <legend class="float-none w-auto px-3">
            <h5><i class="fas fa-search"></i> Pesquisa Financeira</h5>
        </legend>

        <form id="formPesquisa" class="row g-3">
            <!-- Tipo -->
            <div class="col-md-4">
                <label class="form-label">Tipo</label>
                <select name="tipo" class="form-select input-material">
                    <option value="">-- Selecione --</option>
                    <option value="entradas">Entradas</option>
                    <option value="despesas">Despesas</option>
                </select>
            </div>

            <!-- Data inicial -->
            <div class="col-md-4">
                <label class="form-label">Data inicial</label>
				<div class="input-group">
					<span class="input-group-text">
						<i class="fa-solid fa-calendar-alt"></i>
					</span>
                <input type="text" name="data_inicio" class="form-control input-material datepicker"placeholder="dd-mm-aaa">
				</div>
            </div>

            <!-- Data final -->
            <div class="col-md-4">
                <label class="form-label">Data final</label>
				<div class="input-group">
					<span class="input-group-text">
						<i class="fa-solid fa-calendar-alt"></i>
					</span>
                <input type="text" name="data_fim" class="form-control input-material datepicker" placeholder="dd-mm-aaa">
				</div>
            </div>

            <div class="col-12">
                <button class="btn btn-primary">
                    <i class="fas fa-search"></i> Pesquisar
                </button>
            </div>
        </form>

        <!-- RESULTADOS -->
        <div id="resultadoPesquisaclass" class="mt-4"></div>

        <!-- PAGINAÇÃO -->
        <div id="paginacao" class="d-flex justify-content-center mt-3"></div>
    </fieldset>
</div>
</body>
<script src="<?= BASE_URL ?>/assets/js/jquery/jquery-3.6.0.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/jquery/flatpickr.js"></script>

<script>
/* ======================================================
 * ESTADO GLOBAL
 * ====================================================== */
let dadosPesquisa = [];
let paginacaoInfo = {
    page: 1,
    perPage: 10,
    total: 0,
    totalPages: 1
};

/* ======================================================
 * FLATPICKR
 * ====================================================== */
$(".datepicker").flatpickr({
    dateFormat: "d-m-Y",
    allowInput: true
});
/* ======================================================
 * RENDER TABELA
 * ====================================================== */
function renderTabela() {
    const container = document.getElementById('resultadoPesquisaclass');
    container.innerHTML = '';

    if (!Array.isArray(dadosPesquisa) || dadosPesquisa.length === 0) {
        showToast('Aviso', 'Nenhum resultado encontrado', 'warning');
        return;
    }

    const table = document.createElement('table');
    table.className = 'table table-hover';

    table.innerHTML = `
        <thead>
            <tr>
                <th>Data</th>
                <th class="text-end">Valor</th>
                <th>Descrição</th>
            </tr>
        </thead>
        <tbody></tbody>
    `;

    const tbody = table.querySelector('tbody');

    dadosPesquisa.forEach(row => {
        const tr = document.createElement('tr');
        tr.style.cursor = 'pointer';
        tr.innerHTML = `
            <td>${row.data}</td>
            <td class="text-end">${row.valor}</td>
            <td>${row.descricao}</td>
        `;
        tr.addEventListener('click', () => {
            window.location.href =
                `<?= BASE_URL ?>/finance/dashboard?id=${row.id}&tipo=${row.tipo}`;
        });
        tbody.appendChild(tr);
    });

    container.appendChild(table);
    renderPaginacao();
}

/* ======================================================
 * RENDER PAGINAÇÃO
 * ====================================================== */
function renderPaginacao() {
    const div = document.getElementById('paginacao');
    div.innerHTML = '';

    if (paginacaoInfo.totalPages <= 1) return;

    const prev = document.createElement('button');
    prev.className = 'btn btn-outline-secondary me-2';
    prev.textContent = 'Anterior';
    prev.disabled = paginacaoInfo.page === 1;
    prev.onclick = () => carregarPagina(paginacaoInfo.page - 1);

    const info = document.createElement('span');
    info.className = 'mx-2 align-self-center';
    info.textContent = `Página ${paginacaoInfo.page} de ${paginacaoInfo.totalPages}`;

    const next = document.createElement('button');
    next.className = 'btn btn-outline-secondary ms-2';
    next.textContent = 'Próxima';
    next.disabled = paginacaoInfo.page === paginacaoInfo.totalPages;
    next.onclick = () => carregarPagina(paginacaoInfo.page + 1);

    div.append(prev, info, next);
}

/* ======================================================
 * CARREGAR PÁGINA (AJAX)
 * ====================================================== */
function carregarPagina(page = 1) {
    const form = document.getElementById('formPesquisa');
    const formData = new FormData(form);
    formData.append('page', page);

    fetch('<?= BASE_URL ?>/finance/buscar', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        console.log('RESPOSTA BACKEND:', res); // debug

        if (!res.success) {
            showToast('Aviso', res.message || 'Sem resultados', res.type || 'warning');
            dadosPesquisa = [];
            paginacaoInfo = { page: 1, perPage: 10, total: 0, totalPages: 1 };
            renderTabela();
            return;
        }

        // 🔥 Normaliza dados
        if (res.data && Array.isArray(res.data.data)) {
            dadosPesquisa = res.data.data;
        } else {
            console.error('Formato inesperado:', res.data);
            dadosPesquisa = [];
        }

        // 🔥 Normaliza paginação
        if (res.pagination) {
    paginacaoInfo = {
        page: res.pagination.page,
        perPage: res.pagination.per_page,
        total: res.pagination.total,
        totalPages: res.pagination.total_pages
    };
} else {
    paginacaoInfo = { page: 1, perPage: 10, total: 0, totalPages: 1 };
}


        renderTabela();
        showToast('Sucesso', 'Pesquisa realizada com sucesso');
    })
    .catch(err => {
        console.error('FETCH ERROR:', err);
        showToast('Erro', 'Falha ao comunicar com o servidor', 'danger');
    });
}

/* ======================================================
 * SUBMIT PESQUISA
 * ====================================================== */
document.getElementById('formPesquisa').addEventListener('submit', function (e) {
    e.preventDefault();
    carregarPagina(1);
});
</script>


