<?php
// Garante que BASE_URL esteja disponível
if (!defined('BASE_URL')) {
    define('BASE_URL', '/sime/public'); 
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Entrar no Sistema</title>

<!-- Bootstrap -->
<link href="<?= BASE_URL ?>/assets/css/bootstrap/bootstrap.min.css" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/fontawesome/css/all.min.css">
<link rel="stylesheet" href="<?=BASE_URL?>/assets/css/global.css">

<style>
    .message{ text-align:center; margin-top:25px; }
    .error{ color:red; }
	.logo{width:120%}
</style>
</head>
<body class="bg-light">

<div class="container-fluid">
<div class="row min-vh-100">

    <!-- LADO ESQUERDO PROFISSIONAL -->
    <div class="col-lg-6 d-none d-lg-flex flex-column justify-content-between p-5 text-white"
         style="background:linear-gradient(135deg,#0d6efd,#0a58ca);">

        <div>
            <img src="<?= BASE_URL ?>/assets/img/logo-sime.png"
                 class="mb-4"
                 style="max-width:200px;">
                 
            <h3 class="fw-bold mb-3">Sistema de Gestão</h3>

            <p class="opacity-75">
                Plataforma segura para gestão administrativa,
                financeira e operacional da organização.
            </p>
        </div>

        <div class="small opacity-75">
            <div class="mb-2">
                <i class="fas fa-lock me-2"></i>
                Ambiente seguro e restrito
            </div>

            <div>
                <i class="fas fa-location-dot me-2"></i>
                Província de Manica • Distrito de Guro
            </div>
        </div>
    </div>

    <!-- LADO LOGIN -->
    <div class="col-lg-6 d-flex align-items-center justify-content-center">

        <div class="card border-0 shadow-lg p-4" style="max-width:450px;width:100%;border-radius:16px;">

            <div class="text-center mb-4">
                <img src="<?= BASE_URL ?>/assets/img/icones/perfil.png"
                     style="width:70px;"
                     class="mb-3">

                <h4 class="fw-bold mb-1">Entrar no Sistema</h4>
                <small class="text-muted">Informe suas credenciais</small>
            </div>

            <form id="loginForm" class="needs-validation" novalidate>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Usuário</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-user text-primary"></i>
                        </span>
                        <input type="text"
                               class="form-control"
                               name="usuario"
                               placeholder="Digite o usuário"
                               required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Senha</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-lock text-danger"></i>
                        </span>

                        <input type="password"
                               class="form-control"
                               id="password"
                               name="senha"
                               placeholder="Digite a senha"
                               required>

                        <span class="input-group-text toggle-password bg-white"
                              style="cursor:pointer;">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <a href="recuperar_senha.php"
                       class="small text-decoration-none">
                       Esqueci minha senha
                    </a>
                </div>

                <div class="d-grid">
                    <button type="submit"
                            class="btn btn-primary btn-lg fw-semibold">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Entrar
                    </button>
                </div>

            </form>

            <div id="message" class="message mt-3"></div>

        </div>

    </div>

</div>
</div>
<!-- Bootstrap JS -->
<script src="<?= BASE_URL ?>/assets/js/bootstrap/bootstrap.bundle.min.js"></script>
<!-- Mostrar / ocultar senha -->
<script>
document.querySelector('.toggle-password').addEventListener('click', function () {
    const input = document.getElementById('password');
    const icon = this.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye','fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash','fa-eye');
    }
});

<!-- AJAX do login -->

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const messageDiv = document.getElementById('message');

    form.addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(form);

        fetch('<?= BASE_URL ?>/login', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                messageDiv.innerHTML = `<img src="<?= BASE_URL ?>/assets/img/loading.gif" class="loading-gif" alt="Redirecionando...">`;
                setTimeout(()=>{ window.location.href = "<?= BASE_URL ?>"; }, 1000);
            } else {
                messageDiv.textContent = data.message;
                messageDiv.className = 'message error';
            }
        })
        .catch(err=>{
            messageDiv.textContent = "Erro ao processar requisição.";
            messageDiv.className = 'message error';
        });
    });
});
</script>

</body>
</html>
