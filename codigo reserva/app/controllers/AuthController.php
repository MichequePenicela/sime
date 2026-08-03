<?php
namespace App\Controllers;

use App\Models\User;

class AuthController
{
    public function showLoginForm()
    {
        // Se já estiver logado, redireciona
        if (isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '/');
            exit;
        }

        // Mostra a View do login
        require BASE_PATH . '/app/Views/login/login.php';
    }

    public function login()
    {
		session_regenerate_id(true);
$_SESSION['last_activity'] = time();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $db = $GLOBALS['db'];
        $usuario = $_POST['usuario'] ?? '';
        $senha = $_POST['senha'] ?? '';

        $user = User::getByUsername($db, $usuario);

        if ($user && password_verify($senha, $user['senha'])) {
            // Autenticação bem-sucedida
            $_SESSION['usuario_id'] = $user['id'];
			$_SESSION['nivel'] = $user['privilegio'];
            echo json_encode(['success' => true]);
        } else {
            // Usuário ou senha inválidos
            echo json_encode(['success' => false, 'message' => 'Usuário ou senha inválidos']);
        }
        exit;
    }

    public function logout()
    {
        session_destroy();
        header('Location: ' . BASE_URL . '/login'); // Corrigido
        exit;
    }
}

