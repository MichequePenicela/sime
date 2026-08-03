 <?php
use App\Core\Auth;
?>
 <head>
 <style>

body{
    background:#f5f7fb;
}

/* BOTÕES */
.btn{
    border-radius:8px;
    font-weight:500;
}

/* CARDS DASHBOARD */
.dashboard-card{
    border:none;
    border-radius:12px;
    overflow:hidden;
    transition:.2s;
    box-shadow:0 4px 14px rgba(0,0,0,0.05);
}

.dashboard-card:hover{
    transform:translateY(-3px);
}

.dashboard-card .card-header{
    font-weight:600;
    letter-spacing:.5px;
    color:#fff;
}

.dashboard-card .card-body{
    padding:20px;
}

.dashboard-card h3{
    font-size:1.8rem;
    margin:0;
}

.card-entrada{ border-left:6px solid #198754; }
.card-despesa{ border-left:6px solid #dc3545; }
.card-saldo{ border-left:6px solid #0d6efd; }

/* MODAIS */
.modal-content{
    border-radius:12px;
    border:none;
    box-shadow:0 10px 35px rgba(0,0,0,0.15);
}

.modal-header{
    border-bottom:1px solid #eee;
}

.modal-footer{
    border-top:1px solid #eee;
}

/* INPUT MATERIAL */
.input-material{
    border-radius:8px;
    padding:10px;
}

.input-group-text{
    border-radius:8px 0 0 8px;
}

/* LISTA MEMBROS */
.list-group-item{
    cursor:pointer;
    transition:.15s;
}

.list-group-item:hover{
    background:#f0f4ff;
}

</style>
 </head>
 <body>
 <div class="container-fluid mt-4">

    <!-- TÍTULO -->
    <div class="page-header">

<div class="d-flex justify-content-between flex-wrap gap-2 align-items-center">

    <div>
        <h5 class="mb-0 fw-bold">Painel Financeiro</h5>
        <small class="text-muted">
            <?= date('01/m/Y') ?> — <?= date('t/m/Y') ?>
        </small>
    </div>

    <div class="d-flex flex-wrap gap-2">

<?php if (Auth::isGestor()): ?>

<button class="btn btn-outline-success btn-animado" data-bs-toggle="modal" data-bs-target="#modalEntrada">
<i class="fas fa-coins me-1"></i> Nova Oferta
</button>

<button class="btn btn-outline-danger btn-animado" data-bs-toggle="modal" data-bs-target="#modalDespesa">
<i class="fas fa-cart-shopping me-1"></i> Nova Despesa
</button>

<button class="btn btn-outline-primary btn-animado" data-bs-toggle="modal" data-bs-target="#modalDizimo">
<i class="fas fa-hand-holding-heart me-1"></i> Registrar Dízimo
</button>

<?php endif?>

<a href="<?= BASE_URL?>/finance/pesquisa" class="btn btn-secondary">
<i class="fas fa-search me-1"></i> Pesquisar
</a>

    </div>
</div>

</div>

    <!-- CARDS PRINCIPAIS -->
    <div class="row g-4">

        <!-- ENTRADAS -->
        <div class="col-md-4">
        <div class="card dashboard-card card-entrada bg-white">
        <div class="card-header bg-success">Entradas</div>
 
                <div class="card-body">
                    <h3 class="fw-bold text-success counter">
					<img src="<?= BASE_URL?>/assets/img/icones/moneyAdd.png" class="icone-png" alt="Entrada" title="Entradas Total do Mes">
                        <?= number_format($dados['total_entradas'], 2, ',', '.') ?>
                    </h3>
                </div>
				<div class="card-footer bg-secondary-subtle border-top small text-muted">
            <div class="d-flex justify-content-between">
                <span>
                    <i class="fa-solid fa-hand-holding-heart me-1"></i>
                    Dízimos
                </span>
                <strong class="counter">
                    <?= number_format($dados['dizimos'], 2, ',', '.') ?>
                </strong>
            </div>

            <div class="d-flex justify-content-between mt-1">
                <span>
                    <i class="fa-solid fa-coins me-1"></i>
                    Ofertas
                </span>
                <strong class="counter">
                    <?= number_format($dados['entradas'], 2, ',', '.') ?>
                </strong>
            </div>

            <div class="d-flex justify-content-between mt-1">
                <span>
                    <i class="fa-solid fa-donate me-1"></i>
                    Contribuições
                </span>
                <strong class="counter">
                    <?= number_format($dados['contribuicoes'], 2, ',', '.') ?>
                </strong>
            </div>
        </div>
            </div>
        </div>
        <!--

        <div class="col-md-2">
            <div class="card shadow-sm border-0 card-dizimo bg-primary-subtle">
            <div class="card-header bg-primary">
            <h6 class="text-uppercase text-muted">Dízimos</h6>
                </div>
                <div class="card-body">
                    <h3 class="fw-bold text-primary">
					<img src="<?= BASE_URL?>/assets/img/icones/envelop.png" class="icone-png" alt="Dizimo" title="Dizimos do Mes">
                        <?= number_format($dados['dizimos'], 2, ',', '.') ?>
                    </h3>
                </div>
                <div class="card-footer bg-secondary-subtle border-top small text-muted">
                   <p>Rodape</p>
                </div>
            </div>
        </div>

                <div class="col-md-2">
            <div class="card shadow-sm border-0 card-dizimo bg-primary-subtle">
            <div class="card-header bg-primary">
            <h6 class="text-uppercase text-muted">Contribuições</h6>
                </div>
                <div class="card-body">
                    <h3 class="fw-bold text-primary">
					<img src="<?= BASE_URL?>/assets/img/icones/envelop.png" class="icone-png" alt="Contribuições" title="Contribuições do Mês">
                        <?= number_format($dados['contribuicoes'], 2, ',', '.') ?>
                    </h3>
                </div>
                <div class="card-footer bg-secondary-subtle border-top small text-muted">
                   <p>Rodape</p>
                </div>
            </div>
        </div>
-->
        <!-- DESPESAS -->
        <div class="col-md-4">
        <div class="card dashboard-card card-despesa bg-white">
            <div class="card-header bg-danger">Despesas</div>
                <div class="card-body">
                    <h3 class="fw-bold text-danger counter">
					<img src="<?= BASE_URL?>/assets/img/icones/shopping-cart.png" class="icone-png" alt="Despesa" title="Despesas do Mes">
                        <?= number_format($dados['despesas'], 2, ',', '.') ?>
                    </h3>
                </div>
                <div class="card-footer bg-secondary-subtle border-top small text-muted">
                   <p>Rodape:</p>
                   <p>Cumulativo de todas as despesas ate data inicial</p>
                </div>
            </div>
        </div>

        <!-- SALDO -->
        <div class="col-md-4">
        <div class="card dashboard-card card-saldo bg-white">
                <div class="card-header bg-primary">Saldo</div>
                <div class="card-body">
                    <h3 class="fw-bold counter <?= $dados['saldo'] >= 0 ? 'text-success' : 'text-danger' ?>">
					<img src="<?= BASE_URL?>/assets/img/icones/money.png" class="icone-png" alt="Saldo" title="Saldo Do Mes">
                        <?= number_format($dados['saldo'], 2, ',', '.') ?>
                    </h3>
                </div>
                <div class="card-footer bg-secondary-subtle border-top small text-muted">
                   <p>Rodape:</p>
                   <p>Saldo mes anterior<!--<?= number_format($resumo['totalEntradas'], 2, ',', '.') ?>--></p>
                </div>
            </div>
        </div>

    </div>
    </div>

<!-- ================= MODAIS ================= -->

<!-- MODAL ENTRADA -->
<div class="modal fade" id="modalEntrada">
    <div class="modal-dialog modal-md">
        <form id="formEntrada" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nova Oferta</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label>Data</label>
				<div class="input-group">
					<span class="input-group-text">
						<i class="fa-solid fa-calendar-alt text-danger"></i>
					</span>
                    <input type="text" name="data" class="form-control datepicker input-material" required>
                </div>
				</div>

                <div class="mb-3">
                    <label>Valor</label>
					<div class="input-group">
					<span class="input-group-text">
						<i class="fa-solid fa-coins text-success"></i>
					</span>
                    <input type="text" name="valor" class="form-control input-material" required>
                </div>
				</div>
				
				<div class="mb-3">
                    <label>Descrição</label>
					<div class="input-group">
					<span class="input-group-text">
						<i class="fa-solid fa-comment text-primary"></i>
					</span>
                    <textarea name="descricao" class="form-control input-material"></textarea>
                </div>
				</div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-success">Salvar</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL DESPESA -->
<div class="modal fade" id="modalDespesa">
    <div class="modal-dialog modal-md">
        <form id="formDespesa" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nova Despesa</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label>Data</label>
					<div class="input-group">
					<span class="input-group-text">
						<i class="fa-solid fa-calendar-alt text-danger"></i>
					</span>
                    <input type="text" name="data" class="form-control input-material datepicker" required>
                </div>
				</div>

                <div class="mb-3">
                    <label>Valor</label>
					<div class="input-group">
					<span class="input-group-text">
						<i class="fa-solid fa-coins text-danger"></i>
					</span>
                    <input type="text" name="valor" class="form-control input-material" required>
                </div>
				</div>

                <div class="mb-3">
                    <label>Descrição</label>
					<div class="input-group">
					<span class="input-group-text">
						<i class="fa-solid fa-comment text-primary"></i>
					</span>
                    <textarea name="descricao" class="form-control input-material" required></textarea>
                </div>
				</div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-danger">Salvar</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL DÍZIMO -->
<div class="modal fade" id="modalDizimo">
    <div class="modal-dialog modal-md">
        <form id="formDizimo" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Dízimo</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
			 <label>Membro</label>
                <div class="mb-3">
                    <input type="hidden" name="membro_id" id="membro_id">
					<div class="input-group">
					<span class="input-group-text">
						<i class="fa-solid fa-search"></i>
					</span>
					<input type="text" id="searchMembro" class="form-control input-material" placeholder="Pesquisar membro...">
					<button id="btnsearchMembro" class="btn btn-primary" type="button"><i class="fa-solid fa-search"></i></button>
                </div>
				</div>
				
				<div id="searchMembroResults"></div>

                <div class="mb-3">
                    <label>Data</label>
					<div class="input-group">
					<span class="input-group-text">
						<i class="fa-solid fa-calendar-alt text-danger"></i>
					</span>
                    <input type="text" name="data" class="form-control input-material datepicker">
                </div>
				</div>

                <div class="mb-3">
                    <label>Valor</label>
					<div class="input-group">
					<span class="input-group-text">
						<i class="fa-solid fa-coins text-primary"></i>
					</span>
                    <input type="text" name="quantia" class="form-control input-material" required>
                </div>
				</div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>
</body>
<script src="<?= BASE_URL?>/assets/js/jquery/jquery-3.6.0.min.js"></script>
<script src="<?= BASE_URL?>/assets/js/jquery/flatpickr.js"></script>

<script>
/* ===============================
   FLATPICKR
================================ */
$(".datepicker").flatpickr({
    dateFormat: "d-m-Y",
	allowInput: true
});

/* ===============================
   PESQUISA DE MEMBRO (DÍZIMO)
================================ */
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
$("#formDizimo").on("submit", function (e) {
    e.preventDefault();

    if (!$("#membro_id").val()) {
        showToast("Aviso", "Selecione um membro antes de salvar o dízimo");
        return;
    }

    $.ajax({
        url: "<?= BASE_URL ?>/membros/addDizimo",
        method: "POST",
        data: $(this).serialize(),
        dataType: "json",
        success: function (resp) {
            showToast(resp.message, resp.success ? "success" : "danger");
            if (resp.success) location.reload();
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            showToast("Erro", "Erro ao comunicar com o servidor (Dízimo)");
        }
    });
});

/* ===============================
   SUBMIT ENTRADA
================================ */
$("#formEntrada").on("submit", function (e) {
    e.preventDefault();

    $.ajax({
        url: "<?= BASE_URL ?>/finance/addEntrada",
        method: "POST",
        data: $(this).serialize(),
        dataType: "json",
        success: function (resp) {
            showToast(resp.message, resp.success ? "success" : "danger");
            if (resp.success) location.reload();
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            showToast("Erro", "Erro ao comunicar com o servidor (Entrada)");
        }
    });
});

/* ===============================
   SUBMIT DESPESA
================================ */
$("#formDespesa").on("submit", function (e) {
    e.preventDefault();

    $.ajax({
        url: "<?= BASE_URL ?>/finance/addDespesa",
        method: "POST",
        data: $(this).serialize(),
        dataType: "json",
        success: function (resp) {
            showToast(resp.message, resp.success ? "success" : "danger");
            if (resp.success) location.reload();
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            showToast("Erro", "Erro ao comunicar com o servidor (Despesa)");
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
$("#modalDizimo").on("hidden.bs.modal", function () {
    $("#formDizimo")[0].reset();
    $("#searchMembroResults").html("");
    $("#btnsearchMembro").removeClass("d-none");
    $("#searchMembro").prop("disabled", false);

});

$("#modalEntrada").on("hidden.bs.modal", function () {
    $("#formEntrada")[0].reset();
});

$("#modalDespesa").on("hidden.bs.modal", function () {
    $("#formDespesa")[0].reset();
});
</script>