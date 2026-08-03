<?php
/**
 * Front Controller do sistema SIME
 * Todas as requisições passam por este arquivo
 */

session_start();

// -------------------------------------------------
// 1️⃣ DEFINIR O CAMINHO BASE DO PROJETO (FÍSICO)
// -------------------------------------------------
define('BASE_PATH', dirname(__DIR__));

// -------------------------------------------------
// 2️⃣ DEFINIR A BASE DA URL (PARA LINKS, CSS, JS)
// -------------------------------------------------
define('BASE_URL', '/sime');

// -------------------------------------------------
// 3️⃣ CONFIGURAÇÕES GERAIS
// -------------------------------------------------
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Africa/Maputo');

// -------------------------------------------------
// 4️⃣ AUTOLOAD SIMPLES
// -------------------------------------------------
spl_autoload_register(function ($class) {
    $class = str_replace('App\\', '', $class);
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    $file = BASE_PATH . '/app/' . $class . '.php';

    if (file_exists($file)) {
        require $file;
    }
});
// -------------------------------------------------
// AUTOLOAD PARA SPREADSHEET
// -------------------------------------------------

require BASE_PATH . '/vendor/autoload.php';

// -------------------------------------------------
// -------------------------------------------------
// 5️⃣ CONFIGURAÇÕES
// -------------------------------------------------
require BASE_PATH . '/config/app.php';
require BASE_PATH . '/config/database.php';

// -------------------------------------------------
// 6️⃣ ROTAS
// -------------------------------------------------
require BASE_PATH . '/routes/web.php';

// -------------------------------------------------
// 7️⃣ DISPARAR O ROUTER
// -------------------------------------------------
use App\Core\Router;

// Tenta despachar a rota atual
if (!Router::dispatch()) {
    // Se nenhuma rota for encontrada, redireciona para login
    header('Location: ' . BASE_URL . '/notfound');
    exit;
}
