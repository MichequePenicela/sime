<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Painel do Usuário</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/fontawesome/css/all.min.css">

<style>

.user-avatar{
    width:90px;
    height:90px;
    border-radius:50%;
    background:#e9ecef;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:38px;
}

.info-card{
    border-radius:10px;
    transition:.2s ease;
}

.info-card:hover{
    transform:translateY(-2px);
}

.soft-shadow{
    box-shadow:0 3px 10px rgba(0,0,0,.05);
}

</style>
</head>

<body class="bg-light">

<div class="container-fluid py-4">

    <!-- HEADER -->
    <!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4">

<div>

<h4 class="fw-bold mb-0">
<i class="fas fa-user-shield text-primary me-2"></i>
Painel do Usuário
</h4>

<small class="text-muted">
Detalhes gerais e controle do usuário
</small>

</div>

<a href="<?= BASE_URL ?>/usuarios"
class="btn btn-outline-secondary">

<i class="fas fa-arrow-left"></i>
Voltar

</a>

</div>


    <!-- CARD PRINCIPAL -->
    <div class="card border-0 soft-shadow mb-4">

        <div class="card-body">

            <div class="row align-items-center">

                <!-- IDENTIDADE -->
                <div class="col-md-3 text-center border-end">

<div class="user-avatar mx-auto mb-3">

<i class="fas fa-user text-primary"></i>

</div>

<h5 class="mb-1">

<?= htmlspecialchars($usuario['nome']) ?>

</h5>

<div class="text-muted">

@<?= htmlspecialchars($usuario['usuario']) ?>

</div>


<!-- STATUS -->
<div class="mt-3">

<?php if($usuario['status']=='Ativo'): ?>

<span class="badge bg-success fs-6">

<i class="fas fa-check-circle"></i>
Ativo

</span>

<?php else: ?>

<span class="badge bg-danger fs-6">

<i class="fas fa-ban"></i>
Inativo

</span>

<?php endif; ?>

</div>

</div>


                <!-- INFORMAÇÕES -->
                <div class="col-md-9">

<div class="row g-3">

<!-- PRIVILEGIO -->
<div class="col-md-4">

<div class="card info-card border-0 soft-shadow h-100">

<div class="card-body">

<small class="text-muted">

<i class="fas fa-user-tag"></i>
Privilégio

</small>

<h6 class="mt-2 mb-0 fw-bold">

<?php
$priv = strtolower($usuario['privilegio']);

$icon = match($priv){

'admin' => 'fa-user-shield text-danger',
'gestor'=> 'fa-user-gear text-warning',

default => 'fa-user text-secondary'

};
?>

<i class="fas <?= $icon ?>"></i>

<?= ucfirst($usuario['privilegio']) ?>

</h6>

</div>
</div>
</div>

                        <!-- CRIADO -->
<div class="col-md-4">

<div class="card info-card border-0 soft-shadow h-100">

<div class="card-body">

<small class="text-muted">

<i class="fas fa-calendar-alt"></i>
Criado em

</small>

<h6 class="fw-bold mt-2 mb-0">

<?= date('d/m/Y', strtotime($usuario['criado_em'])) ?>

</h6>

</div>
</div>

</div>



<!-- ID -->
<div class="col-md-4">

<div class="card info-card border-0 soft-shadow h-100">

<div class="card-body">

<small class="text-muted">

<i class="fas fa-hashtag"></i>
ID do Usuário

</small>

<h6 class="fw-bold mt-2 mb-0">

#<?= $usuario['id'] ?>

</h6>

</div>
</div>

</div>


                        <!-- AÇÕES -->
                        <div class="col-12">
                        <hr>

                            <div class="d-flex gap-2 flex-wrap">

                                <button
                                    class="btn btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditar">
                                    <i class="fas fa-edit"></i> Editar
                                </button>

                                <?php if ($usuario['status'] === 'Ativo'): ?>
                                    <button
                                        class="btn btn-outline-danger"
                                        id="btnToggleStatus"
                                        data-status="Inativo">
                                        <i class="fas fa-user-slash"></i> Desativar
                                    </button>
                                <?php else: ?>
                                    <button
                                        class="btn btn-outline-success"
                                        id="btnToggleStatus"
                                        data-status="Ativo">
                                        <i class="fas fa-user-check"></i> Ativar
                                    </button>
                                <?php endif; ?>

                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>

</div>

<!-- MODAL EDITAR -->
<div class="modal fade" id="modalEditar" tabindex="-1">
<div class="modal-dialog modal-lg modal-dialog-centered">
<div class="modal-content">

<form id="editarUsuarioForm">
    <input type="hidden" name="id" value="<?= $usuario['id'] ?>">

    <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">
            <i class="fas fa-user-edit"></i> Editar Usuário
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body">
        <div class="row g-3">

            <div class="col-md-4">
                <label>Nome</label>
                <input type="text" name="nome" class="form-control input-material"
                       value="<?= htmlspecialchars($usuario['nome']) ?>">
            </div>

            <div class="col-md-4">
                <label>Usuário</label>
                <input type="text" name="usuario" class="form-control input-material"
                       value="<?= htmlspecialchars($usuario['usuario']) ?>">
            </div>
			<div class="col-md-4">
                <label>Privilégio</label>
                <select name="privilegio" class="form-select input-material">
                    <option value="usuario" <?= $usuario['privilegio']=='Usuario'?'selected':'' ?>>Usuário</option>
                    <option value="gestor" <?= $usuario['privilegio']=='Gestor'?'selected':'' ?>>Gestor</option>
                    <option value="admin" <?= $usuario['privilegio']=='Admin'?'selected':'' ?>>Administrador</option>
                </select>
            </div>

            <div class="col-md-6">
                <label>Nova Senha</label>
                <input type="password" name="senha" class="form-control input-material"
                       placeholder="Min. 8 caracteres">
            </div>
			<div class="col-md-6">
				<label>Confirmar Senha</label>
				<input type="password" name="senha_confirmar" class="form-control input-material">
			</div>

        </div>
    </div>

    <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-success">
            <i class="fas fa-save"></i> Salvar Alterações
        </button>
    </div>

</form>

</div>
</div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title">
          <i class="fas fa-exclamation-triangle"></i> Confirmar ação
        </h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p id="confirmMessage" class="mb-0"></p>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">
          Cancelar
        </button>
        <button class="btn btn-danger" id="confirmActionBtn">
          Confirmar
        </button>
      </div>

    </div>
  </div>
</div>


<script src="<?= BASE_URL ?>/assets/js/jquery/jquery-3.6.0.min.js"></script>

<script>
$(function(){

/* ===============================
 * TOAST
 * =============================== */
function showToast(title, message, type = 'success') {
    const toastEl = $('#appToast');

    toastEl
        .removeClass('text-bg-success text-bg-danger')
        .addClass(type === 'success' ? 'text-bg-success' : 'text-bg-danger');

    $('#toastTitle').text(title);
    $('#toastBody').text(message);

    new bootstrap.Toast(toastEl[0]).show();
}

/* ===============================
 * LOADING BUTTON
 * =============================== */
function setLoading(btn, loading = true) {
    if (loading) {
        btn
            .prop('disabled', true)
            .data('html', btn.html())
            .html('<span class="spinner-border spinner-border-sm me-2"></span>Processando...');
    } else {
        btn
            .prop('disabled', false)
            .html(btn.data('html'));
    }
}

/* ===============================
 * MODAL CONFIRMAÇÃO
 * =============================== */
function confirmAction(message, btnClass, callback) {
    $('#confirmMessage').html(message);

    const btn = $('#confirmActionBtn');
    btn
        .removeClass()
        .addClass('btn ' + btnClass)
        .off('click')
        .on('click', function(){
            callback(btn);
        });

    new bootstrap.Modal('#confirmModal').show();
}

/* ===============================
 * ATIVAR / DESATIVAR USUÁRIO
 * =============================== */
$('#btnToggleStatus').on('click', function(){

    const status = $(this).data('status');

    confirmAction(
        status === 'Inativo'
            ? 'Deseja realmente <strong>desativar</strong> este usuário?'
            : 'Deseja realmente <strong>ativar</strong> este usuário?',
        status === 'Inativo' ? 'btn-danger' : 'btn-success',
        function(btn){

            setLoading(btn, true);

            $.post('<?= BASE_URL ?>/usuarios/toggleStatus', {
                id: <?= $usuario['id'] ?>,
                status: status
            }, function(res){

                setLoading(btn, false);

                if(res.success){
                    showToast('Sucesso', res.message);
                    setTimeout(() => location.reload(), 1200);
                } else {
                    showToast('Erro', res.message, 'error');
                }

            }, 'json');
        }
    );
});

/* ===============================
 * EDITAR USUÁRIO
 * =============================== */
$('#editarUsuarioForm').on('submit', function(e){
    e.preventDefault();

    confirmAction(
        'Deseja <strong>salvar as alterações</strong> deste usuário?',
        'btn-success',
        function(btn){

            setLoading(btn, true);

            $.post(
                '<?= BASE_URL ?>/usuarios/update',
                $('#editarUsuarioForm').serialize(),
                function(res){

                    setLoading(btn, false);

                    if(res.success){
                        showToast('Usuário atualizado', res.message);
                        setTimeout(() => location.reload(), 1200);
                    } else {
                        showToast('Erro', res.message, 'error');
                    }

                },
                'json'
            );
        }
    );
});

});
</script>

</body>
</html>