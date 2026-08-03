<div class="container-fluid py-4">

  <!-- TÍTULO -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="fw-bold mb-0">Templates de Relatórios</h4>
      <small class="text-muted">Gestão de modelos XLSX do sistema</small>
    </div>

    <button class="btn btn-outline-secondary">
      <i class="bi bi-arrow-repeat"></i> Re-escanear pasta
    </button>
  </div>

  <!-- CARDS DE STATUS -->
  <div class="row mb-4">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h6 class="text-muted">Templates cadastrados</h6>
          <h3 class="fw-bold">12</h3>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card border-0 shadow-sm border-warning">
        <div class="card-body">
          <h6 class="text-muted">Novos templates detectados</h6>
          <h3 class="fw-bold text-warning">2</h3>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card border-0 shadow-sm border-info">
        <div class="card-body">
          <h6 class="text-muted">Templates alterados</h6>
          <h3 class="fw-bold text-info">1</h3>
        </div>
      </div>
    </div>
  </div>

  <!-- TEMPLATES CADASTRADOS -->
  <div class="card shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">
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
          <tr>
            <td>Financeiro Mensal</td>
            <td><code>financeiro-mensal</code></td>
            <td>financeiro_mensal.xlsx</td>
            <td><span class="badge bg-success-subtle text-success">Ativo</span></td>
            <td>12/01/2026</td>
            <td class="text-end">
              <button class="btn btn-sm btn-outline-primary">Editar</button>
              <button class="btn btn-sm btn-outline-danger">Desativar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- TEMPLATES DETECTADOS PELO SCANNER -->
  <div class="card shadow-sm">
    <div class="card-header bg-white fw-semibold">
      Templates detectados na pasta
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Arquivo</th>
            <th>Estado</th>
            <th>Última modificação</th>
            <th class="text-end">Ação</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>dizimos.xlsx</td>
            <td><span class="badge bg-warning-subtle text-warning">Novo</span></td>
            <td>15/01/2026</td>
            <td class="text-end">
              <button
                class="btn btn-sm btn-success"
                data-bs-toggle="modal"
                data-bs-target="#modalCadastrarTemplate"
              >
                Cadastrar
              </button>
            </td>
          </tr>

          <tr>
            <td>membros.xlsx</td>
            <td><span class="badge bg-info-subtle text-info">Alterado</span></td>
            <td>14/01/2026</td>
            <td class="text-end">
              <button class="btn btn-sm btn-outline-primary">
                Atualizar
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</div>

<!-- MODAL CADASTRAR TEMPLATE -->
<div class="modal fade" id="modalCadastrarTemplate" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Cadastrar Template</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nome do relatório</label>
            <input type="text" class="form-control" placeholder="Ex: Financeiro Mensal">
          </div>

          <div class="col-md-6">
            <label class="form-label">Slug</label>
            <input type="text" class="form-control" placeholder="financeiro-mensal">
          </div>

          <div class="col-md-12">
            <label class="form-label">Arquivo</label>
            <input type="text" class="form-control" value="dizimos.xlsx" readonly>
          </div>

          <div class="col-md-6">
            <label class="form-label">Nível mínimo</label>
            <select class="form-select">
              <option>Usuário</option>
              <option>Gestor</option>
              <option>Admin</option>
            </select>
          </div>

          <div class="col-md-6 d-flex align-items-end">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" checked>
              <label class="form-check-label">Template ativo</label>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-success">Salvar template</button>
      </div>

    </div>
  </div>
</div>
