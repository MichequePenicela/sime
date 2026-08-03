<head>
    <style>
        .page-header {
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            color: #fff;
            padding: 2rem;
            border-radius: 12px;
        }

        .card-custom {
            border: none;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        .card-custom .card-header {
            background: transparent;
            border-bottom: 1px solid #f1f1f1;
            font-weight: 600;
        }

        .btn-modern {
            border-radius: 8px;
            padding: 8px 18px;
            font-weight: 500;
            transition: all .2s ease;
        }

        .btn-modern:hover {
            transform: translateY(-2px);
        }

        .table thead th {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .badge-format {
            font-size: 0.75rem;
            padding: 6px 10px;
            border-radius: 20px;
        }

        .form-label {
            font-weight: 500;
        }

        .section-title {
            font-weight: 600;
            margin-bottom: 1rem;
        }
    </style>
</head>

<div class="container-fluid py-4">

    <!-- HEADER PROFISSIONAL -->
    <div class="page-header mb-4">
        <h3 class="fw-bold mb-1">
            <img src="<?= BASE_URL?>/assets/img/icones/relatorio.png"> Relatórios
        </h3>
        <small>
            Gere relatórios financeiros e administrativos do sistema
        </small>
    </div>

    <!-- CARD FORM -->
    <div class="card card-custom mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-2 text-primary"></i>Selecionar Relatório
        </div>

        <div class="card-body">
            <form id="formGerarRelatorio" method="POST">

                <div class="row g-4">

                    <div class="col-md-6">
                        <label class="form-label">Relatório</label>
                        <select name="slug" class="form-select" required>
                            <option selected disabled>Selecione um relatório</option>
                            <?php foreach ($relatorios as $r): ?>
                                <option value="<?= htmlspecialchars($r['slug']) ?>">
                                    <?= htmlspecialchars($r['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Formato de saída</label>
                        <select name="formato" class="form-select" required>
                            <option value="xlsx">Excel (.xlsx)</option>
                            <option value="pdf" disabled>PDF (em breve)</option>
                        </select>
                    </div>

                </div>

                <div class="row g-4 mt-1">
                    <div class="col-md-6">
                        <label class="form-label">Data inicial</label>
                        <input type="text" name="data_inicio"
                               class="form-control datepicker"
                               placeholder="Selecione a data inicial">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Data final</label>
                        <input type="text" name="data_fim"
                               class="form-control datepicker"
                               placeholder="Selecione a data final">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3 mt-4">
                    <button type="reset" class="btn btn-light btn-modern">
                        <i class="fas fa-eraser me-1"></i>Limpar
                    </button>

                    <button type="submit" class="btn btn-primary btn-modern">
                        <i class="fas fa-file-export me-1"></i>Gerar Relatório
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- HISTÓRICO -->
    <div class="card card-custom">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>
                <i class="fas fa-clock me-2 text-primary"></i>Relatórios Gerados
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Relatório</th>
                        <th>Período</th>
                        <th>Formato</th>
                        <th>Gerado em</th>
                        <th class="text-end">Ação</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($relatoriosGerados)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>
                            Nenhum relatório gerado recentemente
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($relatoriosGerados as $g): ?>
                        <tr>
                            <td class="fw-semibold">
                                <?= htmlspecialchars($g['nome']) ?>
                            </td>

                            <td>
                                <?= date('d/m/Y', strtotime($g['data_inicio']) ?? '-') ?>
                                –
                                <?= date('d/m/Y', strtotime($g['data_fim'] ?? '-')) ?>
                            </td>

                            <td>
                                <span class="badge bg-primary-subtle text-primary badge-format">
                                    <?= strtoupper($g['formato']) ?>
                                </span>
                            </td>

                            <td>
                                <?= date('d/m/Y H:i', strtotime($g['criado_em'])) ?>
                            </td>

                            <td class="text-end">
                                <a href="<?= BASE_URL ?>/relatorios/download/<?= $g['id'] ?>"
                                   class="btn btn-sm btn-outline-primary btn-modern">
                                    <i class="fas fa-download me-1"></i>Download
                                </a>

                                <a href="<?= BASE_URL ?>/relatorios/deleteReportLog/<?= $g['id'] ?>"
                                   class="btn btn-sm btn-outline-danger btn-modern">
                                    <i class="fas fa-trash me-1"></i>Apagar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="<?= BASE_URL ?>/assets/js/jquery/jquery-3.6.0.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/jquery/flatpickr.js"></script>

<script>
$(".datepicker").flatpickr({
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "d-m-Y",
    allowInput: true
});

document.getElementById('formGerarRelatorio').addEventListener('submit', async function (e) {
    e.preventDefault();

    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Gerando...';

    try {
        const formData = new FormData(this);

        const response = await fetch('<?= BASE_URL ?>/relatorios/run', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (!data.success) {
            alert(data.error);
            return;
        }

        alert('Relatório gerado com sucesso!');
        window.location.href = data.download_url;

    } catch (e) {
        alert('Erro ao gerar relatório');
        console.error(e);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-file-export me-1"></i>Gerar Relatório';
    }
});
</script>