<div class="container-fluid py-4">

  <!-- TÍTULO -->
  <div class="page-header">
  <div class="d-flex justify-content-between flex-wrap gap-2 align-items-center">
    <div>
      <h4 class="fw-bold mb-0">Templates de Relatórios</h4>
      <small class="text-muted">Gestão de modelos XLSX do sistema</small>
    </div>
    <button class="btn btn-outline-secondary btn-animado" id="btnScanTemplates" type="button">
      <i class="bi bi-arrow-repeat"></i> Re-escanear pasta
    </button>
  </div>
  </div>

  <!-- CARDS -->
  <div class="row mb-4">
    <div class="col-md-4">
      <div class="card shadow-sm border-success bg-success-subtle">
        <div class="card-body">
          <h6 class="text-muted">Templates cadastrados</h6>
          <h3 class="fw-bold text-success"><?= count($relatorios) ?></h3>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow-sm border-warning bg-warning-subtle">
        <div class="card-body">
          <h6 class="text-muted">Novos templates detectados</h6>
          <h3 class="fw-bold text-warning" id="countNovos"><?= count($templatesNaoBd) ?></h3>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow-sm border-info bg-primary-subtle">
        <div class="card-body">
          <h6 class="text-muted">Templates recentes</h6>
          <h3 class="fw-bold text-info"><?= count($templatesRecentes) ?></h3>
        </div>
      </div>
    </div>
  </div>
<!-- TEMPLATES CADASTRADOS -->
  <div class="card shadow-sm mb-4">
    <div class="card-header bg-success-subtle fw-semibold">
      Templates cadastrados
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Nome</th>
            <th>Slug</th>
            <th>Arquivo</th>
            <th>Status</th>
            <th>Última modificação</th>
            <th class="text-end">Ações</th>
          </tr>
        </thead>
        <tbody>

        <?php if (empty($relatorios)): ?>
          <tr>
            <td colspan="6" class="text-center text-muted py-4">
              Nenhum template cadastrado
            </td>
          </tr>
        <?php endif; ?>

        <?php foreach ($relatorios as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['nome']) ?></td>
            <td><code><?= $r['slug'] ?></code><button 
			class="btn btn-outline-secondary px-1 py-0 ms-2"
			data-copy="<?= htmlspecialchars($r['slug']) ?>"
			data-bs-toggle="tooltip" data-bs-title="Copiar">
			<i class="bi bi-files fa-sm"></i>
			</button></td>
            <td><?= $r['arquivo'] ?></td>
            <td>
              <?php if ($r['ativo']): ?>
                <span class="badge bg-success-subtle text-success"><i class="fas fa-check-circle"></i>Ativo</span>
              <?php else: ?>
                <span class="badge bg-danger-subtle text-danger"><i class="fas fa-ban"></i>Inativo</span>
              <?php endif; ?>
            </td>
            <td>
              <i class="bi bi-calendar-date"></i>
              <?= date('d/m/Y', strtotime($r['ultima_modificacao'])) ?>
            </td>
            <td class="text-end">
              <a href="/relatorios/editar/<?= $r['id'] ?>" class="btn btn-md btn-outline-primary" title="Editar">
              <i class="fas fa-pen-to-square"></i>
              </a>
              <a href="/relatorios/desativar/<?= $r['id'] ?>" class="btn btn-md btn-outline-danger" title="Desativar">
                <i class="fas fa-ban"></i>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>

        </tbody>
      </table>
    </div>
  </div>


  <!-- TEMPLATES DETECTADOS -->
  <div class="card shadow-sm">
    <div class="card-header fw-semibold">Templates detectados na pasta</div>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Arquivo</th>
            <th>Estado</th>
            <th>Última modificação</th>
            <th class="text-end">Ação</th>
          </tr>
        </thead>
        <tbody id="scannerResults">
          <?php foreach ($templatesNaoBd as $arquivo): ?>
            <tr>
              <td><?= htmlspecialchars($arquivo) ?></td>
              <td><span class="badge bg-warning-subtle text-warning">Novo</span></td>
              <td><?= date('d/m/Y', filemtime(BASE_PATH.'/Uploads/reports/'.$arquivo)) ?></td>
              <td class="text-end">
                <button
                  class="btn btn-sm btn-success"
                  data-bs-toggle="modal"
                  data-bs-target="#modalCadastrarTemplate"
                  data-arquivo="<?= htmlspecialchars($arquivo) ?>"
                >
                  Cadastrar
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- MODAL -->
<div class="modal fade" id="modalCadastrarTemplate" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Cadastrar Template</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <!-- SEM action / SEM method -->
        <form id="formCadastrarTemplate">
          <div class="row g-3">

            <div class="col-md-6">
              <label class="form-label">Nome</label>
              <input id="templateNome" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Slug</label>
              <input id="templateSlug" class="form-control" required>
            </div>

            <div class="col-md-12">
              <label class="form-label">Arquivo</label>
              <input id="templateArquivo" class="form-control" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label">Nível mínimo</label>
              <select id="templateNivel" class="form-select">
                <option value="Usuario">Usuário</option>
                <option value="Gestor">Gestor</option>
                <option value="Admin">Admin</option>
              </select>
            </div>

            <div class="col-md-6 d-flex align-items-end">
              <div class="form-check">
                <input id="templateAtivo" class="form-check-input" type="checkbox" checked>
                <label class="form-check-label">Ativo</label>
              </div>
            </div>

          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-success" id="btnSalvarTemplate">Salvar</button>
      </div>

    </div>
  </div>
</div>

<script>
/* SCAN */
document.getElementById('btnScanTemplates').onclick = async (e) => {
  const btn = e.currentTarget;
  btn.disabled = true;

  const res = await fetch('<?= BASE_URL ?>/relatorios/scan');
  const data = await res.json();

  document.getElementById('countNovos').innerText = data.novos.length;
  btn.disabled = false;
};

/* MODAL */
document.getElementById('modalCadastrarTemplate')
  .addEventListener('show.bs.modal', e => {

    const arquivo = e.relatedTarget.getAttribute('data-arquivo');
    document.getElementById('templateArquivo').value = arquivo;

    const nome = arquivo.replace(/\.[^/.]+$/, '').replace(/[_\-]/g, ' ');
    document.getElementById('templateNome').value = nome;
    document.getElementById('templateSlug').value = nome.toLowerCase().replace(/\s+/g, '-');
});

/* SALVAR VIA AJAX */
document.getElementById('btnSalvarTemplate').onclick = async () => {

  const payload = {
    nome: document.getElementById('templateNome').value.trim(),
    slug: document.getElementById('templateSlug').value.trim(),
    template: document.getElementById('templateArquivo').value.trim(),
    nivel: document.getElementById('templateNivel').value,
    ativo: document.getElementById('templateAtivo').checked ? 1 : 0
  };

  const res = await fetch('<?= BASE_URL ?>/relatorios/addReport', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  });

  const data = await res.json();

  if (!data.success) {
    alert(data.error);
    return;
  }

  bootstrap.Modal.getInstance(
    document.getElementById('modalCadastrarTemplate')
  ).hide();

  location.reload();
};
</script>