<?php
use App\Core\Auth;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Membros</title>

    <!-- CSS -->
    <link rel="stylesheet" href="<?=BASE_URL?>/assets/css/jquery/flatpickr.min.css">
    <link rel="stylesheet" href="<?=BASE_URL?>/assets/css/fontawesome/css/all.min.css">

</head>
<body>

<div class="container my-5">
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
	<div class="text-primary">
        <h3><img src="<?= BASE_URL?>/assets/img/icones/members.png" class="icone-png" alt="Membros"> Gestão de Membros</h3>
	</div>
	<div class="gab-3">
	<?php if (Auth::isGestor()): ?>
        <button class="btn btn-outline-success btn-animado" data-bs-toggle="modal" data-bs-target="#modalCadastro">
            <i class="fas fa-user-plus"></i> Novo Membro
        </button>
	<?php endif?>
		<a href="<?= BASE_URL?>/membros/datamembers">
		<button class="btn btn-secondary" type="button">
            <i class="fas fa-chart-line"></i> Estatisticas
        </button>
		</a>
    </div>
	</div>
</div>

    <!-- PESQUISA -->
    <fieldset>
		<legend class="float-none w-auto px-3">
		<i class="fas fa-search"></i> Pesquisar Membro
		</legend>
		

        <div class="input-group mb-3">
            <input type="text" id="searchInput" class="form-control input-material" placeholder="Digite o nome do membro">
            <button class="btn btn-primary" id="searchButton">
                <i class="fas fa-search"></i>
            </button>
        </div>

        <div id="loadingPesquisa" class="text-center my-3">
            <div class="spinner-border text-primary"></div>
            <div class="mt-2">Pesquisando...</div>
        </div>
        <div id="searchResults"></div>
		<div id="searchPagination" class="mt-3"></div>
    </fieldset>
</div>

<!-- MODAL CADASTRO -->
<?php if (Auth::isGestor()): ?>
<div class="modal fade" id="modalCadastro" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow border-0 rounded-4">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus"></i> Cadastrar Membro
                </h5>
                <button type="button" class="btn-close btn-close-white btn-animado" data-bs-dismiss="modal"></button>
            </div>

            <form id="membroForm">
                <div class="modal-body bg-light">

                    <!-- DADOS PESSOAIS -->
                    <fieldset>
                        <legend class="float-none"><i class="fas fa-id-card"></i> Dados do Membro</legend>

                        <div class="row g-3">
                           <div class="col-md-6">
							<div class="input-group">
							<span class="input-group-text">
                                <i class="fas fa-user-tie text-primary"></i>
							</span>
                                <input type="text" name="nome" class="form-control input-material" placeholder="Nome completo">
                            </div>
							</div>
                            <div class="col-md-6">
							<div class="input-group">
							<span class="input-group-text">
                              <i class="fas fa-venus-mars text-primary"></i>
							</span>
                                <select name="sexo" class="form-select input-material">
                                    <option value="">Sexo</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Feminino">Feminino</option>
                                </select>
							</div>
							</div>
                           

                            <div class="col-md-6">
							<div class="input-group">
							<span class="input-group-text">
                                <i class="fas fa-calendar-alt text-danger"></i>
							</span>
                                <input type="text" name="data_nascimento" class="form-control datepicker input-material" placeholder="Data de nascimento">
                            </div>
							</div>
                            <div class="col-md-6">
							<div class="input-group">
							<span class="input-group-text">
                                <i class="fas fa-calendar-alt text-danger"></i>
							</span>
                                <input type="text" name="data_conversao" class="form-control datepicker input-material" placeholder="Data de conversão">
                            </div>
							</div>

                            <div class="col-md-6">
							<div class="input-group">
							<span class="input-group-text">
                                <i class="fas fa-user-check text-primary"></i>
							</span>
                                <select name="permanencia" class="form-select input-material">
									<option value="" disabled selected><---Selecionar estado de permanencia---></option>
                                    <option value="Ativo">Ativo</option>
                                    <option value="Abandonou">Abandonou</option>
                                    <option value="Mudou-se">Mudou-se</option>
                                    <option value="Obitou">Óbito</option>
                                </select>
                            </div>
							</div>
                            <div class="col-md-6">
							<div class="input-group">
							<span class="input-group-text">
                                <i class="fas fa-water text-primary"></i>
							</span>
                                <select name="batizado" class="form-select input-material">
									<option value="" disabled selected><---Selecionar estado do baptismo---></option>
                                    <option value="Sim">Batizado</option>
                                    <option value="Nao">Não Batizado</option>
                                </select>
                            </div>
							</div>
							</div>
						<div class="row g-3">
						<div class="col-md-12">
							  <label class="form-label fw-semibold mb-2 badge text-secondary">Departamento</label>

							  <div class="d-flex flex-wrap gap-2">

								<input type="radio" class="btn-check" name="departamento" id="depPais" value="Pais" autocomplete="off">
								<label class="btn btn-outline-primary btn-animado" for="depPais">Pais</label>

								<input type="radio" class="btn-check" name="departamento" id="depMaes" value="Maes" autocomplete="off">
								<label class="btn btn-outline-primary btn-animado" for="depMaes">Mães</label>

								<input type="radio" class="btn-check" name="departamento" id="depJovens" value="Jovens" autocomplete="off">
								<label class="btn btn-outline-primary btn-animado" for="depJovens">Jovens</label>

								<input type="radio" class="btn-check" name="departamento" id="depDominical" value="Dominical" autocomplete="off">
								<label class="btn btn-outline-primary btn-animado" for="depDominical">Dominical</label>

							  </div>
						</div>
						</div>
                    </fieldset>

                    <!-- MORADIA -->
                    <fieldset>
                        <legend class="float-none"><i class="fas fa-home"></i> Moradia e Contacto</legend>

                        <div class="row g-3">
                            <div class="col-md-4">
							<div class="input-group">
							<span class="input-group-text">
                                <i class="fas fa-phone-volume text-primary"></i>
							</span>
                                <input type="text" name="numero_celular" class="form-control input-material" placeholder="Celular">
                            </div>
							</div>

                            <div class="col-md-4">
							<div class="input-group">
							<span class="input-group-text">
                                <i class="fas fa-phone-volume text-primary"></i>
							</span>
                                <input type="text" name="numero_alternativo" class="form-control input-material" placeholder="Alternativo">
                            </div>
							</div>

                            <div class="col-md-4">
							<div class="input-group">
							<span class="input-group-text">
                                <i class="fas fa-phone-alt text-primary"></i>
							</span>
                                <input type="text" name="celular_cuidador" class="form-control input-material" placeholder="Cuidador">
                            </div>
							</div>
							<div class="col-md-6 mt-4">
							<div class="input-group">
							<span class="input-group-text">
                                <i class="fas fa-map-marker-alt text-danger"></i>
							</span>
                                <input type="text" name="bairro" class="form-control input-material" placeholder="Bairro">
                            </div>
							</div>

                            <div class="col-md-6">
							<div class="input-group">
							<span class="input-group-text">
                                <i class="fas fa-location-dot text-danger"></i>
							</span>
                                <textarea name="referencia" class="form-control input-material" placeholder="Ponto de referência"></textarea>
                            </div>
							</div>
                        </div>
                    </fieldset>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button class="btn btn-success">
                        <i class="fas fa-save"></i> Salvar Membro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif?>

<!-- JS -->
<script src="<?= BASE_URL ?>/assets/js/jquery/jquery-3.6.0.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/jquery/flatpickr.js"></script>

<script>
$(function () {

    // ---------- FLATPICKR ----------
    $(".datepicker").flatpickr({
    dateFormat: "d-m-Y",
    allowInput: true
});


    // PESQUISA
let searchPage = 1;
let searchTotalPages = 1;
let lastSearch = '';

$(function () {

    $('#loadingPesquisa').hide();

    // BOTÃO PESQUISAR
    $('#searchButton').on('click', function () {
        pesquisarMembros(1);
    });

    // FUNÇÃO PRINCIPAL
    window.pesquisarMembros = function (page = 1) {

        const nome = $('#searchInput').val().trim();
        if (!nome) return;

        lastSearch = nome;
        searchPage = page;

        $('#loadingPesquisa').show();
        $('#searchResults').html('');
        $('#searchPagination').html('');

        $.get('<?= BASE_URL ?>/membros/search', {
            nome,
            page
        }, function (res) {

            $('#loadingPesquisa').hide();

            if (!res.success || res.data.length === 0) {
                $('#searchResults').html(
                    '<div class="alert alert-warning">Nenhum membro encontrado</div>'
                );
                return;
            }

            searchTotalPages = res.pagination.totalPages;

            renderTabela(res.data);
            renderPaginacao();

        }, 'json');
    };

    // TABELA
    function renderTabela(data) {

        let html = `
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Sexo</th>
                    <th>Idade</th>
                    <th>Departamento</th>
                    <th>Permanência</th>
                </tr>
            </thead>
            <tbody>`;

        data.forEach(m => {
            html += `
            <tr style="cursor:pointer" data-id="${m.id}">
                <td>${m.nome}</td>
                <td>${m.sexo}</td>
                <td>${m.idade}</td>
                <td>${m.departamento ?? '-'}</td>
                <td>${m.permanencia ?? '-'}</td>
            </tr>`;
        });

        html += '</tbody></table>';

        $('#searchResults').html(html);

        // CLICK NA LINHA
        $('#searchResults tr[data-id]').on('click', function () {
            window.location.href =
                '<?= BASE_URL ?>/membros/dashboard?id=' + $(this).data('id');
        });
    }

    // PAGINAÇÃO
    function renderPaginacao() {

        if (searchTotalPages <= 1) return;

        let html = `
        <nav class="mt-3">
            <ul class="pagination justify-content-center">`;

        for (let i = 1; i <= searchTotalPages; i++) {
            html += `
            <li class="page-item ${i === searchPage ? 'active' : ''}">
                <button class="page-link" data-page="${i}">
                    ${i}
                </button>
            </li>`;
        }

        html += `
            </ul>
        </nav>`;

        $('#searchPagination').html(html);

        // EVENTOS DOS BOTÕES
        $('#searchPagination button').on('click', function () {
            const page = $(this).data('page');
            pesquisarMembros(page);
        });
    }

});

    /*// CADASTRO
    $('#membroForm').submit(function (e) {
        e.preventDefault();
        $.post('<?= BASE_URL ?>/membros/save', $(this).serialize(), function (res) {
            showToast('Aviso', res.message,'warning');
            if (res.success) {
                $('#membroForm')[0].reset();
                $('#modalCadastro').modal('hide');
            }
        }, 'json');
    });
*/
});
</script>
<script>
$(function () {

    const form = $('#membroForm');
    let enviando = false;

    form.on('submit', function (e) {

        e.preventDefault();

        if (enviando) return;

        enviando = true;

        $.ajax({

            url: '<?= BASE_URL ?>/membros/verificarSimilaridade',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',

            success: function(res){

                if (res.redirect){
                    window.location.href = res.redirect;
                    return;
                }

                if (res.message){
                    showToast(
                        res.success ? 'Sucesso' : 'Aviso',
                        res.message,
                        res.success ? 'success' : 'warning'
                    );
                }

                if (res.success){

                    form.trigger('reset');
                    $('#modalCadastro').modal('hide');

                    if (typeof carregarMembros === 'function'){
                        carregarMembros();
                    }
                }
            },

            error: function(xhr){

                console.error(xhr.responseText);

                showToast('Erro','Erro interno no servidor','error');
            },

            complete: function(){
                enviando = false;
            }
        });

    });

});
</script>

</body>
</html>