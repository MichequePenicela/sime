<?php
// Define código HTTP correto
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>404 • Página não encontrada</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">
	
    <!-- Bootstrap -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/bootstrap/bootstrap.min.css">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/fontawesome/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .error-box {
            background: #ffffff;
            color: #212529;
            border-radius: 16px;
            padding: 3rem;
            max-width: 750px;
            width: 100%;
            text-align: center;
            /*box-shadow: 0 20px 40px rgba(0,0,0,.25);*/
        }

        .error-code {
            font-size: 5rem;
            font-weight: 800;
        }

        .error-box p {
            color: #6c757d;
        }

        .btn-home {
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>

<div class="error-box">
    <div class="error-code text-warning">
        <i class="fas fa-triangle-exclamation"></i> 404
    </div>

    <h3 class="mt-3 text-danger">Página não encontrada</h3>
<hr />
    <p class="mt-3">
        A página que você tentou acessar não existe, foi removida
        ou o endereço está incorreto.
    </p>

    <div class="d-grid gap-2 btn-home">
        <a href="<?= BASE_URL ?>/" class="btn btn-outline-primary btn-lg">
            <i class="fas fa-house"></i> Voltar para o início
        </a>

        <button onclick="history.back()" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Página anterior
        </button>
    </div>
</div>

</body>
</html>