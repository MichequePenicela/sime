<?php
namespace App\Controllers;

use App\Models\Notificacoes;
use App\Core\Auth;

class NotificacoesController
{
    private $db;

    public function __construct()
    {
        $this->db = $GLOBALS['db'];
    }

    public function get(): void
    {
        Auth::init();

        $resumo = Notificacoes::getResumo($this->db);
        $total  = Notificacoes::total($this->db);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'total'   => $total,
            'dados'   => $resumo
        ]);
        exit;
    }
}