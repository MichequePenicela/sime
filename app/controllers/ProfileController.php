<?php
namespace App\Controllers;
class ProfileController
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->db = $db;
    }

    public function index()
    {
        $userId = $_SESSION['user_id'];

        $stmt = $this->db->prepare("SELECT id, nome, senha FROM usuarios WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        require base_path('app/views/profile/index.php');
    }

    public function updatePassword()
    {
        if (!Security::validateToken($_POST['csrf_token'])) {
            die("Token inválido");
        }

        $userId = $_SESSION['user_id'];

        $stmt = $this->db->prepare("SELECT senha FROM usuarios WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $senhaAntiga = $_POST['senha_antiga'];
        $novaSenha = $_POST['nova_senha'];
        $repetirSenha = $_POST['repetir_senha'];

        if (!Security::verifyPassword($senhaAntiga, $user['senha'])) {
            die("Senha antiga incorreta");
        }

        if ($novaSenha !== $repetirSenha) {
            die("As senhas não coincidem");
        }

        $hash = Security::hashPassword($novaSenha);

        $update = $this->db->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
        $update->execute([$hash, $userId]);

        echo "Senha alterada com sucesso!";
    }
}