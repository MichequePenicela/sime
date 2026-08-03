<div class="container mt-4">

<h3>⚠️ Possíveis membros duplicados encontrados</h3>

<div class="card mt-3">
<div class="card-body">

<h5>Novo Cadastro:</h5>

<ul>
<li><strong>Nome:</strong> <?= htmlspecialchars($dados['nome']) ?></li>
<li><strong>Sexo:</strong> <?= htmlspecialchars($dados['sexo']) ?></li>
<li><strong>Nascimento:</strong> <?= htmlspecialchars($dados['data_nascimento']) ?></li>
</ul>

</div>
</div>

<div class="mt-4">

<h5>Membros similares encontrados:</h5>

<table class="table">
<thead>
<tr>
<th>ID</th>
<th>Nome</th>
<th>Sexo</th>
<th>Data Nascimento</th>
<th>Similaridade</th>
</tr>
</thead>
<tbody>

<?php foreach($similares as $s): ?>

<tr>
<td><?= $s['id'] ?></td>
<td><?= htmlspecialchars($s['nome']) ?></td>
<td><?= $s['sexo'] ?></td>
<td><?= date('d-m-Y', strtotime($s['data_nascimento'])) ?></td>
<td><?= $s['similaridade'] ?>%</td>
</tr>

<?php endforeach; ?>

</tbody>
</table>

</div>

<div class="mt-4">

<button id="btnConfirmarCadastro" class="btn btn-success">
Cadastrar Mesmo Assim
</button>

<a href="<?= BASE_URL ?>/membros" class="btn btn-secondary">
Cancelar
</a>

</div>

</div>
<script src="<?= BASE_URL ?>/assets/js/jquery/jquery-3.6.0.min.js"></script>
<script>

$("#btnConfirmarCadastro").click(function(){

    const btn = $(this);
    btn.prop('disabled', true);

    $.post("<?= BASE_URL ?>/membros/confirmarCadastro", function(resp){

        if(resp.redirect){
            window.location.href = resp.redirect;
            return;
        }

        if(resp.message){
            alert(resp.message);
        }

        btn.prop('disabled', false);

    },'json');

});

</script>