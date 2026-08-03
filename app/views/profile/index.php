<?php use Security; ?>

<div class="container mt-4">
    <h3>Perfil do Usuário</h3>

    <div class="card mt-3">
        <div class="card-body">

            <p><strong>ID:</strong> <?= Security::escape($_SESSION['user_id']); ?></p>
            <p><strong>Nome:</strong> <?= Security::escape($_SESSION['user_nome']); ?></p>

            <hr>

            <h5>Alterar Senha</h5>

            <form method="POST" action="/profile/updatePassword">

                <input type="hidden" name="csrf_token" value="<?= Security::generateToken(); ?>">

                <div class="mb-3">
                    <label>Senha Antiga</label>
                    <input type="password" name="senha_antiga" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Nova Senha</label>
                    <input type="password" name="nova_senha" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Repetir Nova Senha</label>
                    <input type="password" name="repetir_senha" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">
                    Alterar Senha
                </button>

            </form>

        </div>
    </div>
</div>