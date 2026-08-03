<?php

namespace App\Controllers;

use App\Core\Auth;
use PDO;
class AdministracaoController
{
    private PDO $db;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->db = $GLOBALS['db'];
    }
    public function index(){
        Auth::init();
        Auth::isAdmin();
        require BASE_PATH . '/app/Views/layouts/header.php';
        require BASE_PATH. '/app/Views/admin/index.php';
        require BASE_PATH . '/app/Views/layouts/footer.php';
    }
}