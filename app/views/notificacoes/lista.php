<div class="container mt-4">
<h4>Notificações</h4>
<hr>

<?php foreach($lista as $n): ?>
    <div class="card mb-2 <?= $n['lida'] ? '' : 'border-primary' ?>">
        <div class="card-body">
            <h6><?= htmlspecialchars($n['titulo']) ?></h6>
            <p class="small text-muted">
                <?= date('d/m/Y H:i', strtotime($n['criado_em'])) ?>
            </p>

            <a href="<?= BASE_URL ?>/notificacoes/ver/<?= $n['id'] ?>"
               class="btn btn-sm btn-outline-primary">
               Ver detalhe
            </a>
        </div>
    </div>
<?php endforeach; ?>
</div>