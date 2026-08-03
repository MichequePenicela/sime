<div class="container py-4">

<h4 class="mb-4 fw-bold">

<i class="fas fa-bell text-primary me-2"></i>

Notificações

</h4>


<!-- ================= ANIVERSÁRIOS ================= -->

<div class="card shadow-sm mb-4">

<div class="card-header bg-light fw-bold">

🎂 Aniversários

</div>

<div class="card-body">

<?php if(!empty($aniversarios)): ?>

<?php

$hoje = strtotime(date('Y-m-d'));

$anoAtual = date('Y');

$diasProximos = 7;

$limite = strtotime("+$diasProximos days",$hoje);

/*
AGRUPAR POR DATA
*/

$grupos = [];

foreach($aniversarios as $a){

$dataNascimento = strtotime($a['data_nascimento']);

$mesDia = date('m-d',$dataNascimento);

$aniversarioAnoAtual = strtotime($anoAtual.'-'.$mesDia);

if($aniversarioAnoAtual < $hoje){

$aniversarioAnoAtual = strtotime(($anoAtual+1).'-'.$mesDia);

}

$idade = date('Y',$aniversarioAnoAtual) - date('Y',$dataNascimento);

$chave = date('Y-m-d',$aniversarioAnoAtual);

$grupos[$chave][] = [

'nome'=>$a['nome'],

'idade'=>$idade,

'data'=>$aniversarioAnoAtual

];

}

ksort($grupos);

?>

<div class="row g-3">

<?php foreach($grupos as $data=>$membros):

$dataTs = strtotime($data);

?>

<div class="col-12 col-md-6">

<div class="card shadow-sm h-100">

<!-- HEADER -->

<div class="card-header fw-bold

<?php

if($dataTs == $hoje){

echo 'bg-warning';

}elseif($dataTs <= $limite){

echo 'bg-info text-white';

}else{

echo 'bg-light';

}

?>

">

📅 <?=date('d/m/Y',$dataTs)?>

</div>


<!-- BODY -->

<div class="card-body">

<ul class="mb-0">

<?php foreach($membros as $m): ?>

<li>

<?php if($dataTs == $hoje): ?>

🎉 <strong><?=$m['nome']?></strong>

<span class="badge bg-warning text-dark">

<?=$m['idade']?> anos

</span>

<?php else: ?>

<i class="fas fa-birthday-cake text-danger"></i>

<?=$m['nome']?> —

<strong><?=$m['idade']?> anos</strong>

<?php endif ?>

</li>

<?php endforeach ?>

</ul>

</div>


<!-- FOOTER -->

<div class="card-footer small text-muted">

<?php

if($dataTs == $hoje){

echo "Hoje";

}elseif($dataTs <= $limite){

echo "Próximos dias";

}else{

echo "Futuro";

}

?>

</div>

</div>

</div>

<?php endforeach ?>

</div>

<?php else: ?>

<div class="text-muted">

Sem aniversários próximos

</div>

<?php endif ?>

</div>

</div>


<!-- ================= DÍZIMOS ================= -->

<!-- ================= DÍZIMOS ================= -->

<div class="card shadow-sm mb-4">

  <div class="card-header bg-light fw-bold">
    <img src="<?= BASE_URL ?>/assets/img/icones/envelop.png" width="35"> Dízimos
  </div>

  <div class="card-body">

    <?php if(!empty($dizimos)): ?>

      <?php
      // Agrupar dízimos por data
      $grupos = [];

      foreach($dizimos as $d){
          $data = date('Y-m-d', strtotime($d['data']));
          $grupos[$data][] = [
              'nome' => $d['nome'],
              'quantia' => $d['quantia'],
              'data' => $d['data']
          ];
      }

      ksort($grupos); // ordena pela data
      ?>

      <div class="row g-3">

        <?php foreach($grupos as $data => $registros): ?>

          <div class="col-12 col-md-6">

            <div class="card h-100 shadow-sm">

              <!-- HEADER -->
              <div class="card-header bg-success text-white fw-bold">
                📅 <?= date('d/m/Y', strtotime($data)) ?>
              </div>

              <!-- BODY -->
              <div class="card-body">
                <ul class="mb-0">
                  <?php foreach($registros as $r): ?>
                    <li class="mb-1">
                      <strong class="text-uppercase"><?= $r['nome'] ?></strong> dizimou
                      <span class="badge bg-success">
                        <?= number_format($r['quantia'], 2, ',', '.') ?> MT
                      </span>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>

              <!-- FOOTER -->
              <div class="card-footer small text-muted">
                <?php
                  // calcular total do dia
                  $totalDia = 0;
                  foreach($registros as $r){
                      $totalDia += $r['quantia'];
                  }
                ?>
                Total do dia: <?= number_format($totalDia, 2, ',', '.') ?> MT
              </div>

            </div>

          </div>

        <?php endforeach; ?>

      </div>

    <?php else: ?>

      <div class="text-muted">
        Sem registros recentes
      </div>

    <?php endif; ?>

  </div>

</div>


<!-- ================= LANÇAMENTOS ================= -->

<div class="card shadow-sm">

<div class="card-header bg-light fw-bold">

<img src="<?= BASE_URL?>/assets/img/icones/money.png" width="35">

Lançamentos Financeiros

</div>

<div class="card-body">

<?php if(!empty($lancamentos)): ?>

<div class="row g-3">

<?php foreach($lancamentos as $l): 

$bgTipo = ($l['tipo']=='entrada')
? 'bg-success'
: 'bg-danger';

?>

<div class="col-12 col-md-6 col-lg-4 col-xl-3">

<div class="card h-100 shadow-sm border-0">

<!-- HEADER -->

<div class="card-header text-white <?=$bgTipo?> fw-bold text-uppercase">

<?=$l['tipo']?>

</div>


<!-- BODY -->

<div class="card-body">

<div class="mb-2">

<small class="text-muted">

Valor

</small>

<br>

<strong class="fs-5">

<?=number_format($l['valor'],2,',','.')?> MT

</strong>

</div>

<hr>

<div>

<small class="text-muted">

Descrição

</small>

<br>

<?=$l['descricao']?>

</div>

</div>


<!-- FOOTER -->

<div class="card-footer text-muted small">

📅 <?=$l['data']?>

</div>

</div>

</div>

<?php endforeach ?>

</div>

<?php else: ?>

<div class="text-muted">

Nenhum lançamento recente

</div>

<?php endif ?>

</div>

</div>

</div>