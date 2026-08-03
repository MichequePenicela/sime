<?php
use app\Core\Auth;
$tipo      = $dados['tipo'];
$isEntrada = $tipo === 'entradas';
?>
 <head>
 <link rel="stylesheet" href="<?=BASE_URL?>/assets/css/jquery/flatpickr.min.css">
 <link rel="stylesheet" href="<?=BASE_URL?>/assets/css/global.css">
 </head>
<div class="container mt-4">
    <div class="card shadow-sm border-0 rounded-4">

        <!-- HEADER -->
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3 <?= $isEntrada ? 'bg-success-subtle' : 'bg-danger-subtle' ?>">
            <div class="d-flex align-items-center gap-2">
                <div class="icon-circle <?= $isEntrada ? 'bg-success-subtle' : 'bg-danger-subtle' ?>">
                    <i class="fas <?= $isEntrada ? 'fa-arrow-down text-success' : 'fa-arrow-up text-danger' ?>"></i>
                </div>
                <div>
                    <h6 class="mb-0 text-muted">Detalhes da movimentação</h6>
                    <strong class="fs-5"><?= ucfirst($tipo) ?></strong>
                </div>
            </div>

           <div class="justify-content-right align-items-center">
					<a href="<?= BASE_URL ?>/finance/pesquisa" class="btn btn-outline-secondary btn-sm">
						<i class="fas fa-arrow-left"></i> Voltar
					</a>
                    <?php if(Auth::isGestor()): ?>
					<a href="javascript:void(0)"
					data-bs-toggle="modal"
					data-bs-target="#modalEditar"
					data-id="<?= $dados['id']?>"
					data-tipo="<?= $dados['tipo']?>"
					class="btn btn-warning btn-sm">
				<i class="fas fa-pen"></i> Editar
			</a>

			<button class="btn btn-danger btn-sm"
					data-bs-toggle="modal"
					data-bs-target="#modalExcluir">
				<i class="fas fa-trash"></i> Excluir
			</button>
            <?php endif ?>
		</div>
        </div>

        <!-- BODY -->
        <div class="card-body px-4 py-4">

            <!-- VALOR -->
            <div class="mb-4">
                <div class="text-muted small">Valor</div>
                <div class="display-6 fw-bold <?= $isEntrada ? 'text-success' : 'text-danger' ?>">
				<i class="fa fa-coins me-1"></i>
                   <?= $dados['valor_view'] ?>
                </div>
            </div>

            <hr>

            <!-- INFO -->
            <div class="row gy-4 mt-2">

                <div class="col-md-4">
                    <div class="text-muted small mb-1">Data</div>
                    <div class="fw-semibold">
                        <i class="far fa-calendar-alt me-1"></i>
                        <?= htmlspecialchars($dados['data']) ?>
                    </div>
                </div>
                <?php if (!empty($dados['descricao'])): ?>
                <div class="col-md-8">
                    <div class="text-muted small mb-1">Descrição</div>
                    <div class="fw-semibold">
					<i class="fa-solid fa-pen-to-square me-1"></i>
                        <?= htmlspecialchars($dados['descricao']) ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
        <?php if (!empty($dados['observacao'])): ?>
                    <div class="col-12 card-footer">
                        <div class="text-muted mb-1">Observações</div>
                        <div class="p-3 bg-light small rounded-3">
						<i class="far fa-file-lines me-1"></i>
                            <?= nl2br(htmlspecialchars($dados['observacao'])) ?>
                        </div>
                    </div>
                <?php endif; ?>
</div>
<div class="modal fade" id="modalExcluir" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-triangle-exclamation"></i> Confirmar exclusão
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                Esta ação <strong>não pode ser desfeita</strong>.<br>
                Deseja realmente excluir este lançamento?
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button class="btn btn-danger btn-sm" id="btnConfirmarExclusao">
                    <i class="fas fa-trash"></i> Excluir
                </button>
            </div>
        </div>
    </div>
</div>
<!--Modal Editar-->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">

            <div class="modal-header bg-warning-subtle">
                <h5 class="modal-title">
                    <i class="fas fa-pen"></i> Editar <?= ucfirst($dados['tipo']) ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body bg-light">
                <form id="formEditar">

                    <input type="hidden" name="id" value="<?= (int)$dados['id'] ?>">
                    <input type="hidden" name="tipo" value="<?= $dados['tipo'] ?>">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Data</label>
                            <input type="text"
                                   name="data"
                                   class="form-control input-material datepicker"
                                   value="<?= date('d-m-Y', strtotime($dados['data'])) ?>"
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Valor</label>
                            <input type="text"
                                   name="valor"
                                   class="form-control input-material"
								   step="0.01"
                                   value="<?= $dados['valor_edit'] ?>"
                                   required>
                        </div>
						
                        <div class="col-md-12">
                            <label class="form-label">Descrição</label>
                            <input type="text"
                                   name="descricao"
                                   class="form-control input-material"
                                   value="<?= htmlspecialchars($dados['descricao'] ?? '') ?>"
                                   required>
                        </div>


                        <div class="col-12">
                            <label class="form-label">Observações</label>
                            <textarea name="observacao"
                                      class="form-control input-material"
                                      rows="3"><?= htmlspecialchars($dados['observacao'] ?? '') ?></textarea>
                        </div>
                    </div>

                </form>
            </div>

            <div class="modal-footer bg-success-subtle">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button class="btn btn-primary" id="btnSalvarEdicao">
                    <i class="fas fa-save"></i> Salvar alterações
                </button>
            </div>

        </div>
    </div>
</div>
<script src="<?= BASE_URL?>/assets/js/jquery/jquery-3.6.0.min.js"></script>
<script src="<?= BASE_URL?>/assets/js/jquery/flatpickr.js"></script>
<script>
/* ===============================
   TOAST BOOTSTRAP
================================ */
function showToast(message, type = "success") {
    const toastEl = document.getElementById("appToast");
    const toastBody = document.getElementById("toastMessage");

    toastEl.className = `toast align-items-center text-bg-${type} border-0`;
    toastBody.textContent = message;

    const toast = new bootstrap.Toast(toastEl, { delay: 6500 });
    toast.show();
}
/* ===============================
   FLATPICKR
================================ */
$(".datepicker").flatpickr({
    dateFormat: "d-m-Y"
});
/*MODAL DE EXCLUIR*/
document.getElementById('btnConfirmarExclusao')?.addEventListener('click', function () {

    this.disabled = true;
    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Excluindo...';

    fetch('<?= BASE_URL ?>/finance/excluir', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            id: <?= (int)$dados['id'] ?>,
            tipo: '<?= $dados['tipo'] ?>'
        })
    })
    .then(r => r.json())
    .then(resp => {
    if (resp.success) {
        window.location.href = '<?= BASE_URL ?>/finance/pesquisa';
    } else {
        showToast(resp.message || 'Erro ao excluir', 'danger');
    }
})

    .catch(() => {
        showToast('Erro de comunicação com o servidor', 'danger');
    })
    .finally(() => {
        this.disabled = false;
        this.innerHTML = '<i class="fas fa-trash"></i> Excluir';
    });
});
/*MODAL DE EDITAR ENTRADA OU DESPESA*/
document.getElementById('btnSalvarEdicao').addEventListener('click', function () {

    const form = document.getElementById('formEditar');
    const formData = new FormData(form);

    fetch('<?= BASE_URL ?>/finance/editar', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(resp => {
        if (resp.success) {
            location.reload(); // Atualiza o dashboard
        } else {
            showToast(resp.message || 'Erro ao editar registro','danger');
        }
    })
    .catch(() => {
        showToast('Erro de comunicação com o servidor','danger');
    });
});
</script>



