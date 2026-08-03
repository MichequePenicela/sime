<?php
namespace App\Controllers;
use App\Models\User;
use App\Core\Auth;

class PoliticasController
{
    public function politica()
    {
		Auth::init();
        // Carrega layout e view
		require BASE_PATH . '/app/views/layouts/header.php';
        require BASE_PATH . '/app/Views/legal/politica_privacidade.html';
        require BASE_PATH . '/app/Views/layouts/rodape.php';
    }

    public function dados()
    {
		Auth::init();
        // Carrega layout e view
		require BASE_PATH . '/app/views/layouts/header.php';
        require BASE_PATH . '/app/Views/legal/protecao_dados.html';
        require BASE_PATH . '/app/Views/layouts/rodape.php';
    }
    public function organizacao()
    {
        Auth::init();
        // Carrega layout e view
		require BASE_PATH . '/app/views/layouts/header.php';
        require BASE_PATH . '/app/Views/legal/dados_organizacao.html';
        require BASE_PATH . '/app/Views/layouts/rodape.php';
    }
}