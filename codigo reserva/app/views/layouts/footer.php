<head>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/bootstrap/bootstrap-icons/bootstrap-icons.css">
</head>
<!-- 
/*=====================
  GLOBAL CONFIRM MODAL
*/=====================
 -->
<div class="modal fade" id="globalConfirmModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header text-danger">
        <h5 class="modal-title d-flex align-items-center gap-2">
        <i class="fas fa-triangle-exclamation"></i>
        <span id="globalConfirmTitle">
          Confirmar ação</span>
        </h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" id="globalConfirmMessage">
        Deseja continuar?
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">
          Cancelar
        </button>

        <button class="btn btn-danger" id="globalConfirmBtn">
          Confirmar
        </button>
      </div>

    </div>
  </div>
</div>
<!--
/*=======================================
*TOAST BOOTSTRAP
 */======================================
-->
<div class="toast-container position-fixed bottom-0 end-0 p-3">

  <div id="appToast" class="toast" role="alert">
    <div class="toast-header">
      <i class="bi bi-<?= ['icon'] ?> me-2"></i>
      <strong class="me-auto" id="toastTitle">Mensagem</strong>
      <button class="btn-close" data-bs-dismiss="toast"></button>
    </div>
    <div class="toast-body" id="toastBody"></div>
  </div>

</div>
<?php if (!empty($_SESSION['flash_modal'])): ?>
<div class="modal fade" id="flashModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-<?= $_SESSION['flash_modal']['tipo'] ?>">
      <div class="modal-header bg-<?= $_SESSION['flash_modal']['tipo'] ?>-subtle">
        <h5 class="modal-title text-<?= $_SESSION['flash_modal']['tipo'] ?>">
          <i class="bi bi-<?= $_SESSION['flash_modal']['icon'] ?>"></i>
          <?= $_SESSION['flash_modal']['titulo'] ?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <?= $_SESSION['flash_modal']['mensagem'] ?>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
<!-- CONTADOR PREMIUM -->
<script>

document.querySelectorAll('.counter').forEach(el=>{

let text=el.innerText;

let final=parseFloat(

text.replace(/\./g,'')

.replace(',','.')

);

if(isNaN(final)) return;

let start=0;

let duration=900;

let startTime=null;

function animate(time){

if(!startTime) startTime=time;

let progress=time-startTime;

let value=Math.min(

(final*(progress/duration)),

final

);

if(text.includes(',')){

el.innerText=value.toLocaleString(

'pt-BR',

{minimumFractionDigits:2}

);

}else{

el.innerText=Math.floor(value);

}

if(progress<duration){

requestAnimationFrame(animate);

}

}

requestAnimationFrame(animate);

});

</script>
<!-- Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('flashModal');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
});

document.addEventListener('click', function (e) {
  const btn = e.target.closest('[data-copy]');
  if (!btn) return;

  const texto = btn.getAttribute('data-copy');

  navigator.clipboard.writeText(texto).then(() => {
    btn.setAttribute('data-bs-title', 'Copiado!');
    bootstrap.Tooltip.getOrCreateInstance(btn).show();

    setTimeout(() => {
      btn.setAttribute('data-bs-title', 'Copiar');
    }, 1500);
  });
});

// Inicializar tooltips (uma vez)
document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
  new bootstrap.Tooltip(el);
});
/* ======================================================
 * GLOBAL CONFIRM MODAL
 * ====================================================== */
window.confirmarAcao = function({
  titulo = 'Confirmar ação',
  mensagem = 'Deseja continuar?',
  onConfirm = null
}){

  document.getElementById('globalConfirmTitle').innerText = titulo;
  document.getElementById('globalConfirmMessage').innerText = mensagem;

  const modalEl = document.getElementById('globalConfirmModal');

  const btn = document.getElementById('globalConfirmBtn');

  // limpa evento antigo
  const newBtn = btn.cloneNode(true);
  btn.parentNode.replaceChild(newBtn, btn);

  newBtn.addEventListener('click', async () => {

    if(onConfirm){
      await onConfirm();
    }

    bootstrap.Modal
      .getInstance(modalEl)
      .hide();
  });

  bootstrap.Modal
    .getOrCreateInstance(modalEl)
    .show();
}
/* ======================================================
 * TOAST BOOTSTRAP
 * ====================================================== */
function showToast(title, message, type = 'success') {
    const toastEl = $('#appToast');
    toastEl.removeClass(
        'text-bg-success text-bg-danger text-bg-warning text-bg-info text-bg-secondary'
    );

    switch (type) {
        case 'success': toastEl.addClass('text-bg-success'); break;
        case 'warning': toastEl.addClass('text-bg-warning'); break;
        case 'info': toastEl.addClass('text-bg-info'); break;
        case 'danger':
        case 'error': toastEl.addClass('text-bg-danger'); break;
        default: toastEl.addClass('text-bg-secondary');
    }

    $('#toastTitle').text(title);
    $('#toastBody').text(message);
    bootstrap.Toast.getOrCreateInstance(toastEl[0]).show();
}
</script>
<?php unset($_SESSION['flash_modal']); ?>