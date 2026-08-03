<?php
use App\Core\Auth;
$cor_header = "#212529"; // Default color
 // Cor do cabeçalho em virtude da permanencia
 if ($membro['permanencia'] == 'Mudou-se') $cor_header = '#6c757d';
 elseif ($membro['permanencia'] == 'Obitou') $cor_header = '#dc3545';
 elseif ($membro['permanencia'] == 'Abandonou') $cor_header = '#997404';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Dashboard - <?php echo htmlspecialchars($membro['nome']); ?></title>
<style>
.header { background-color: <?= $cor_header ?>; color: white; padding:15px; border-radius:5px; margin-bottom:20px;}
.header i { margin-right:5px;}
.table-clickable tbody tr { cursor:pointer; }
.table-borderless td, .table-borderless th { border: none; }
</style>
</head>
<body>
<div class="container-fluid py-4">

    <!-- ================= HEADER DO MEMBRO ================= -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body d-flex justify-content-between align-items-center"
             style="background: <?= $cor_header ?>; color:white; border-radius:8px;">

            <div>
                <h4 class="mb-1">
                    <i class="fas fa-user-circle me-2"></i>
                    <?= htmlspecialchars($membro['nome']); ?>
                </h4>

                <small>
                    <?= $membro['idade']; ?> anos |
                    <?= $membro['sexo']; ?> |
                    <i class="fas fa-phone-volume"></i> <?= htmlspecialchars($membro['moradia_numero_celular'] ?? '-') ?> |
                    <i class="fas fa-phone-volume"></i> <?= htmlspecialchars($membro['moradia_numero_alternativo'] ?? '-') ?> |
                    <i class="fas fa-phone-volume"></i> <?= htmlspecialchars($membro['moradia_celular_cuidador'] ?? '-') ?> |
                    (<strong><?= $membro['permanencia']; ?></strong>)
                </small>
            </div>

            <?php if (Auth::isGestor()): ?>
                <div class="d-flex gap-2">

                    <button class="btn btn-light btn-sm btn-animado"
                            data-bs-toggle="modal"
                            data-bs-target="#modalEditarMembro">
                        <i class="fas fa-pen"></i> Editar
                    </button>

                    <?php if (Auth::isAdmin()): ?>
                        <button class="btn btn-danger btn-sm btn-animado" id="btnDeleteMember">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    <?php endif; ?>

                </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- ================== ABAS ================== -->
    <ul class="nav nav-tabs mb-3" id="membroTabs">

        <li class="nav-item">
            <button class="nav-link active"
                    data-bs-toggle="tab"
                    data-bs-target="#geral">
                <i class="fas fa-id-card"></i> Informações Gerais
            </button>
        </li>

        <li class="nav-item">
            <button class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#parentes">
                <i class="fas fa-users"></i> Parentes
            </button>
        </li>

        <li class="nav-item">
            <button class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#dizimos">
                <i class="fas fa-hand-holding-heart"></i> Dízimos & Contribuições
            </button>
        </li>

    </ul>

    <div class="tab-content">

        <!-- ================= ABA GERAL ================= -->
        <div class="tab-pane fade show active" id="geral">

            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <div class="row">

                    <div class="col-md-6">
                            <strong>Nome:</strong>
                            <?= $membro['nome']; ?>
                        </div>

                        <div class="col-md-6">
                            <strong>Conversão:</strong>
                            <?= $membro['data_conversao']; ?>
                        </div>

                        <div class="col-md-6">
                            <strong>Nascimento:</strong>
                            <?= $membro['data_nascimento']; ?>
                        </div>

                        <div class="col-md-6">
                            <strong>Batizado:</strong>
                            <?= $membro['batizado']; ?>
                        </div>

                        <div class="col-md-6">
                            <strong>Departamento:</strong>
                            <?= $membro['departamento']; ?>
                        </div>

                        <hr class="my-3">

                        <div class="col-md-6">
                            <strong>Bairro:</strong>
                            <?= $membro['bairro']; ?>
                        </div>

                        <div class="col-md-6">
                            <strong>Referência:</strong>
                            <?= $membro['referencia']; ?>
                        </div>

                    </div>

                </div>
            </div>

        </div>

        <!-- ================= ABA PARENTES ================= -->
        <div class="tab-pane fade" id="parentes">

            <div class="card shadow-sm border-0">

                <div class="card-header d-flex justify-content-between">

                    <strong>Parentes</strong>

                    <?php if (Auth::isGestor()): ?>
                        <button class="btn btn-primary btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#parentescoModal">
                            <i class="fas fa-plus"></i>
                        </button>
                    <?php endif; ?>

                </div>

                <div class="card-body">

                    <table class="table table-hover">

                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Parentesco</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php if (!empty($parentesResult)): ?>
                            <?php foreach ($parentesResult as $parente): ?>

                                <tr>
                                    <td><?= $parente['nome_parente']; ?></td>
                                    <td><?= $parente['tipo']; ?></td>

                                    <td>
                                        <?php if (Auth::isGestor()): ?>
                                            <button class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                            <?php endforeach; ?>
                        <?php else: ?>

                            <tr>
                                <td colspan="3">Nenhum parente encontrado</td>
                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        <!-- ================= ABA DIZIMOS ================= -->
<!-- ================= ABA DIZIMOS ================= -->
<div class="tab-pane fade" id="dizimos">

<div class="row g-3">

<!-- ================= DÍZIMOS ================= -->
<div class="col-md-6">

<div class="card shadow-sm border-0 h-100">

<div class="card-header d-flex justify-content-between">

<strong>Dízimos</strong>

<?php if (Auth::isGestor()): ?>
<button class="btn btn-success btn-sm"
data-bs-toggle="modal"
data-bs-target="#dizimoModal">
<i class="fas fa-plus"></i>
</button>
<?php endif; ?>

</div>

<div class="card-body">

<table class="table table-hover">

<thead>
<tr>
<th>Quantia</th>
<th>Data</th>
</tr>
</thead>

<tbody>

<?php if ($ultimosDizimos): ?>

<tr>
<td><?= $ultimosDizimos['quantia']; ?></td>
<td><?= $ultimosDizimos['data']; ?></td>
</tr>

<?php else: ?>

<tr>
<td colspan="2">Nenhum registro</td>
</tr>

<?php endif; ?>

</tbody>

</table>

</div>
</div>
</div>


<!-- ================= CONTRIBUIÇÕES ================= -->
<div class="col-md-6">

<div class="card shadow-sm border-0 h-100">

<div class="card-header d-flex justify-content-between bg-primary-subtle">

<strong>Contribuições</strong>

</div>

<div class="card-body">

<table class="table table-hover">

<thead>
<tr>
<th>Quantia</th>
<th>Data</th>
</tr>
</thead>

<tbody>

<?php if ($ultimaContribuicao): ?>

<tr>
<td><?= $ultimaContribuicao['quantia']; ?></td>
<td><?= $ultimaContribuicao['data']; ?></td>
</tr>

<?php else: ?>

<tr>
<td colspan="2">Nenhum registro</td>
</tr>

<?php endif; ?>

</tbody>

</table>

</div>
</div>
</div>

</div> <!-- row -->

</div>

    </div>

</div>
<!-- Modal Editar Membro -->
<div class="modal fade" id="modalEditarMembro" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 rounded-4 shadow">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-edit"></i> Editar Membro
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="formEditarMembro">
                <div class="modal-body bg-light">

                    <!-- IDs -->
                    <input type="hidden" name="membro_id" value="<?= (int)$membro['id'] ?>">

                    <!-- ================== DADOS DO MEMBRO ================== -->
                    <h6 class="border-bottom pb-2 mb-3 text-primary">
                        <i class="fas fa-id-card"></i> Dados do Membro
                    </h6>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nome</label>
							<div class="input-group">
							<span class="input-group-text">
                                <i class="fas fa-user-tie text-primary"></i>
							</span>
                            <input type="text"
                                   name="nome"
                                   class="form-control input-material"
                                   value="<?= htmlspecialchars($membro['nome']) ?>"
                                   required>
                        </div>
						</div>

                        <div class="col-md-3">
                            <label class="form-label">Data de Nascimento</label>
							<div class="input-group">
							<span class="input-group-text">
                               <i class="fa-solid fa-calendar-alt text-danger"></i> 
							</span>
                            <input type="text"
                                   name="data_nascimento"
                                   class="form-control input-material datepicker"
                                   value="<?= $membro['data_nascimento'] ?>">
                        </div>
						</div>

                        <div class="col-md-3">
                            <label class="form-label">Data de Conversão</label>
							<div class="input-group">
							<span class="input-group-text">
                               <i class="fa-solid fa-calendar-alt text-danger"></i> 
							</span>
                            <input type="text"
                                   name="data_conversao"
                                   class="form-control input-material datepicker"
								   value="<?= $membro['data_conversao'] ?>">
                        </div>
						</div>

                        <div class="col-md-4">
                            <label class="form-label">Batizado</label>
						<div class="input-group">
							<span class="input-group-text">
                                <i class="bi bi-water fs-5 text-primary"></i>
							</span>
                            <select name="batizado" class="form-select input-material">
                                <option value="Nao" <?= $membro['batizado'] === 'Nao' ? 'selected' : '' ?>>Não</option>
                                <option value="Sim" <?= $membro['batizado'] === 'Sim' ? 'selected' : '' ?>>Sim</option>
                            </select>
                        </div>
						</div>
						<div class="col-md-4">
								<label class="form-label">Departamento</label>
							<div class="input-group">
								<span class="input-group-text">
									<i class="bi bi-people fs-5 text-primary"></i>
								</span>
								<select name="departamento" class="form-select input-material">
									<option value="Pais" <?= $membro['departamento'] === 'Pais' ? 'selected' : '' ?>>Pais</option>
									<option value="Maes" <?= $membro['departamento'] === 'Maes' ? 'selected' : '' ?>>Mães</option>
									<option value="Jovens" <?= $membro['departamento'] === 'Jovens' ? 'selected' : '' ?>>Jovens</option>
									<option value="Dominical" <?= $membro['departamento'] === 'Dominical' ? 'selected' : '' ?>>Dominical</option>
								</select>
							</div>
						</div>
					<div class="col-md-4">
							<label class="form-label">Permanência</label>
							<div class="input-group">
								<span class="input-group-text">
									<i class="bi bi-person-check fs-5 text-primary"></i>
								</span>
								<select name="permanencia" class="form-select input-material">
									<option value="Ativo" <?= $membro['permanencia'] === 'Ativo' ? 'selected' : '' ?>>Ativo</option>
									<option value="Abandonou" <?= $membro['permanencia'] === 'Abandonou' ? 'selected' : '' ?>>Abandonou</option>
									<option value="Mudou-se" <?= $membro['permanencia'] === 'Mudou-se' ? 'selected' : '' ?>>Mudou-se</option>
									<option value="Obitou" <?= $membro['permanencia'] === 'Obitou' ? 'selected' : '' ?>>Obitou</option>
								</select>
							</div>
						</div>

                    <!-- ================== MORADIA E CONTACTO ================== -->
                    <h6 class="border-bottom pb-2 my-4 text-primary">
                        <i class="fas fa-home"></i> Moradia e Contacto
                    </h6>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Bairro</label>
							<div class="input-group">
							<span class="input-group-text">
                                <i class="fas fa-map text-danger"></i>
							</span>
                            <input type="text"
                                   name="bairro"
                                   class="form-control input-material"
                                   value="<?= htmlspecialchars($membro['bairro'] ?? '') ?>">
                        </div>
						</div>

                        <div class="col-md-6">
                            <label class="form-label">Ponto de Referência</label>
							<div class="input-group">
							<span class="input-group-text">
                                <i class="fas fa-map-marker-alt text-danger"></i>
							</span>
                            <input type="text"
                                   name="referencia"
                                   class="form-control input-material"
                                   value="<?= htmlspecialchars($membro['referencia'] ?? '') ?>">
                        </div>
						</div>

                        <div class="col-md-4">
                            <label class="form-label">Celular Principal</label>
							<div class="input-group">
							<span class="input-group-text">
                                <i class="fas fa-phone-volume text-primary"></i>
							</span>
                            <input type="text"
                                   name="numero_celular"
                                   class="form-control input-material"
                                   value="<?= htmlspecialchars($membro['moradia_numero_celular'] ?? '') ?>">
                        </div>
						</div>

                        <div class="col-md-4">
                            <label class="form-label">Celular Alternativo</label>
							<div class="input-group">
							<span class="input-group-text">
                                <i class="fas fa-phone-volume text-primary"></i>
							</span>
                            <input type="text"
                                   name="numero_alternativo"
                                   class="form-control input-material"
                                   value="<?= htmlspecialchars($membro['moradia_numero_alternativo'] ?? '') ?>">
                        </div>
						</div>

                        <div class="col-md-4">
                            <label class="form-label">Celular do Cuidador</label>
							<div class="input-group">
							<span class="input-group-text">
                                <i class="fas fa-phone-volume text-primary"></i>
							</span>
                            <input type="text"
                                   name="celular_cuidador"
                                   class="form-control input-material"
                                   value="<?= htmlspecialchars($membro['moradia_celular_cuidador'] ?? '') ?>">
                        </div>
						</div>
                    </div>

                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
</div>

<!-- Modal Dízimo -->
<div class="modal fade" id="dizimoModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white"><h5 class="modal-title">Adicionar Dízimo</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <form id="dizimoForm">
            <input type="hidden" name="membro_id" value="<?= $membro['id'] ?>">
            <div class="mb-2"><label>Data</label><input type="text" name="data" class="form-control input-material datepicker" required></div>
            <div class="mb-2"><label>Quantia</label><input type="number" name="quantia" class="form-control input-material" required min="0" step="0.01"></div>
            <button type="submit" class="btn btn-success">Salvar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Parentesco -->
<div class="modal fade" id="parentescoModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white"><h5 class="modal-title">Adicionar Parente</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="input-group mb-2">
          <input type="text" id="searchParenteInput" class="form-control input-material" placeholder="Digite o nome">
          <button id="btnSearchParente" class="btn btn-primary">Buscar</button>
        </div>
        <div id="searchParenteResults"></div>
        <div id="formParentesco" class="mt-3 d-none">

            <input type="hidden" id="parenteSelecionado">

            <div class="mb-2">
                <label>Tipo de parentesco</label>
                <select id="selectParentesco" class="form-select input-material"></select>
            </div>

            <button id="btnSalvarParentesco" class="btn btn-outline-success btn-sm">
                Salvar parentesco
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnLimparSelecao">Limpar Selecao</button>
    </div>

      </div>
    </div>
  </div>
</div>
<script src="<?= BASE_URL?>/assets/js/jquery/jquery-3.6.0.min.js"></script>
<script src="<?= BASE_URL?>/assets/js/jquery/flatpickr.js"></script>
<script>
$(function(){

        // ---------- FLATPICKR ----------
        $(".datepicker").flatpickr({
        dateFormat: "d-m-Y",
        allowInput: true
    });
    /* ======================================================
    * TOAST
    * ====================================================== */
    function showToast(title, message, type = 'success') {
        const toastEl = $('#appToast');

        toastEl
            .removeClass('text-bg-success text-bg-danger')
            .addClass(type === 'success' ? 'text-bg-success' : 'text-bg-danger');

        $('#toastTitle').text(title);
        $('#toastBody').text(message);

        new bootstrap.Toast(toastEl[0]).show();
    }
        // ---------- DELETAR MEMBRO ----------
        // ---------- DELETAR MEMBRO ----------
            $("#btnDeleteMember").click(function(){

            $("#globalConfirmTitle").text("Confirmar exclusão");
            $("#globalConfirmMessage").text("Deseja realmente excluir este membro?");

            var modal = new bootstrap.Modal($("#globalConfirmModal")[0]);
            modal.show();

            $("#globalConfirmBtn")
                .off("click") // MUITO IMPORTANTE
                .on("click", function(){

                    $.post("<?= BASE_URL?>/membros/delete",
                    {id: <?= $membro['id'] ?>},
                    function(resp){

                        if(resp.success){
                            window.location.href = "<?= BASE_URL?>/membros";
                        }

                    }, 'json');

                });

            });
        

        // ---------- DELETAR PARENTES ----------
        $(".btnDeleteParente").click(function(){
            const parenteId = $(this).data("id");
            $.post("<?= BASE_URL?>/deleteParente", {parente_id: parenteId, membro_id: <?= $membro['id'] ?>}, function(resp){
                if(resp.success) location.reload();
            }, 'json');
        });

        // ---------- ADICIONAR DÍZIMO ----------
        $("#dizimoForm").submit(function(e){
            e.preventDefault();
            $.post("<?= BASE_URL?>/membros/addDizimo", $(this).serialize(), function(resp){
                showToast(resp.message);
                if(resp.success) location.reload();
            }, 'json');
        });
        //-------------ATUALIZAR MEMBRO--------------------
        document.getElementById('formEditarMembro').addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('<?= BASE_URL ?>/membros/update', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(resp => {
            if (!resp.success) {
                showToast(resp.message || 'Erro ao atualizar','danger');
                return;
            }
            showToast('Dados atualizados com sucesso');
            location.reload();
        })
        .catch(error => {
        console.error('ERRO FETCH:', error);
        showToast('Erro interno ao atualizar','danger');
    });
    });

        // ---------- PESQUISA PARENTESCO ----------
$("#btnSearchParente").click(function(){

const nome = $("#searchParenteInput").val();
const container = $("#searchParenteResults");

container.html("");

$.getJSON("<?= BASE_URL?>/membros/search", {
    nome: nome,
    page: 1 // 🔥 sempre página 1
}, function(resp){

    const data = resp.data || [];

    if(data.length === 0){
        container.html('<div class="alert alert-warning">Nenhum membro encontrado</div>');
        return;
    }

    data.forEach(m => {

        const div = $(`
            <div class="p-2 border rounded mb-1 resultado-parente" style="cursor:pointer">
                ${m.nome}
            </div>
        `);

        div.click(function(){

            if(m.id === <?= $membro['id']?>){
                showToast("Não pode ser parente de si mesmo","danger");
                return;
            }

            // 🔥 guardar id selecionado
            $("#parenteSelecionado").val(m.id);

            // 🔥 preencher input pesquisa
            $("#searchParenteInput").val(m.nome);

            // 🔥 limpar resultados
            container.html("");
            // 🔥 Desabilitar Campo de pesquisa
            $("#searchParenteInput").prop("disabled", true);
            // 🔥 Desabilitar botao de pesquisa
            $("#btnSearchParente").addClass("d-none");
            // 🔥 Limpar Toda selecao do Campo de pesquisa
                $("#btnLimparSelecao").click(function(){
                $("#parenteSelecionado").val("");
                $("#searchParenteInput").val("").prop("disabled", false);
                $("#btnSearchParente").prop("disabled", false);
                $("#formParentesco").addClass("d-none");
                $("#btnSearchParente").removeClass("d-none");
            });


            // 🔥 carregar tipos parentesco
            $.getJSON("<?= BASE_URL?>/membros/listarTiposParentesco", function(tipos){

                let html = '<option value="">Selecione...</option>';

                tipos.forEach(t=>{
                    html += `<option value="${t.id}">${t.tipo}</option>`;
                });

                $("#selectParentesco").html(html);

                // 🔥 mostrar form
                $("#formParentesco").removeClass("d-none");

            });

        });

        container.append(div);
    });

});

});

});
</script>

</body>
</html>
