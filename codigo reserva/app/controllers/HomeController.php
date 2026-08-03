<?php
namespace App\Controllers;

use App\Models\User;
use PDO;
use App\Core\Auth;
use App\Models\Home;

class HomeController
{
    private PDO $db;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->db = $GLOBALS['db'];
    }
    public function index()
    {
		Auth::init();
        $membroscadastrados = Home::countSearch($this->db);
        $usuarioscadastrados =Home::countusers($this->db);
        $relatorioscadastrados =Home::countreports($this->db);
        $totaldizimistas =Home::CountDizimistas($this->db);

        $dados = [
            'entradas' => Home::totalOfertas($this->db),
            'dizimos'  => Home::totalDizimos($this->db),
            'contribuicoes'  => Home::totalContribuicoes($this->db),
        ];
        $dados['total_entradas'] = $dados['entradas']+$dados['dizimos']+$dados['contribuicoes'];
        // Carrega layout e view
		require BASE_PATH . '/app/views/layouts/header.php';
        require BASE_PATH . '/app/Views/home.php';
        require BASE_PATH . '/app/Views/layouts/rodape.php';
        require BASE_PATH . '/app/Views/layouts/footer.php';
    }
	public function erro404()
	{
        require BASE_PATH . '/app/Views/404.php';
	}
}