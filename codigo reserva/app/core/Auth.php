<?php
namespace App\Core;

use App\Models\User;

class Auth
{
    private const SESSION_TIMEOUT = 1800; //  30 minutos

    // Hierarquia de níveis
    private const NIVEIS = [
        'Usuario' => 1,
        'Gestor'  => 5,
        'Admin'   => 10,
    ];

    private static bool $initialized = false;

    public static function init(): void
    {
        if (self::$initialized) {
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        self::checkTimeout();

        if (!isset($_SESSION['usuario_id'])) {
            self::logout();
        }

        $db = $GLOBALS['db'] ?? null;
        if (!$db) {
            throw new \Exception('Conexão com banco não encontrada.');
        }

        $id_usuario = $_SESSION['usuario_id'];

        // ⬇️ buscar usuário completo
        $usuario = User::getById($db, $id_usuario);
        if (!$usuario) {
            self::logout();
        }

        // Dados disponíveis na sessão
        $_SESSION['usuario_nome']  = $usuario['nome'];
        $_SESSION['usuario_nivel'] = $usuario['privilegio'];

        $_SESSION['last_activity'] = time();

        self::$initialized = true;
    }

    private static function checkTimeout(): void
    {
        if (isset($_SESSION['last_activity'])) {
            if (time() - $_SESSION['last_activity'] > self::SESSION_TIMEOUT) {
                self::logout();
            }
        }
    }

    /* =========================
       MÉTODOS DE ACESSO
    ==========================*/

    public static function id(): ?int
    {
        return $_SESSION['usuario_id'] ?? null;
    }

    public static function nome(): ?string
    {
        return $_SESSION['usuario_nome'] ?? null;
    }

    public static function nivel(): ?string
    {
        return $_SESSION['usuario_nivel'] ?? null;
    }

    public static function isAdmin(): bool
    {
        return self::nivel() === 'Admin';
    }

    public static function isGestor(): bool
    {
        return in_array(self::nivel(), ['Gestor', 'Admin'], true);
    }

    /**
     * Exige nível mínimo
     * Ex: Auth::requireNivel('Gestor');
     */
    public static function requireNivel(string $nivelMinimo): void
{
    $nivelUsuario = self::nivel();

    if (!$nivelUsuario || !isset(self::NIVEIS[$nivelUsuario])) {
        self::logout();
    }

    if (self::NIVEIS[$nivelUsuario] < self::NIVEIS[$nivelMinimo]) {

        $_SESSION['flash_modal'] = [
            'titulo'   => 'Acesso negado',
            'mensagem' => 'Você não tem permissão para acessar esta funcionalidade.',
            'icon'     => 'ban', // bootstrap icon
            'tipo'     => 'danger'
        ];

        header('Location: ' . BASE_URL . '/');
        exit;
    }
}


    public static function logout(): void
    {
        session_unset();
        session_destroy();

        header('Location: ' . BASE_URL . '/login?expired=1');
        exit;
    }
}