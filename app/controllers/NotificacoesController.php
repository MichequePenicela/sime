<?php

namespace App\Controllers;

use App\Models\Notificacoes;
use App\Core\Auth;
use PDO;

class NotificacoesController
{

private Notificacoes $model;

public function __construct()
{

if(session_status()===PHP_SESSION_NONE){

session_start();

}

$db=$GLOBALS['db'];

$this->model=new Notificacoes($db);

}

//////////////////////////////////////////////////
// PAGINA NORMAL
//////////////////////////////////////////////////

public function index()
{
Auth::init();
$aniversarios=$this->model->aniversariosProximos();

$dizimos=$this->model->ultimosDizimos();

$lancamentos=$this->model->ultimosLancamentos();

$data=[

'aniversarios'=>$aniversarios,
'dizimos'=>$dizimos,
'lancamentos'=>$lancamentos

];

extract($data);

require BASE_PATH.'/app/views/layouts/header.php';

require BASE_PATH.'/app/views/notificacoes/index.php';

require BASE_PATH.'/app/views/layouts/footer.php';

require BASE_PATH.'/app/views/layouts/rodape.php';

}

//////////////////////////////////////////////////
// AJAX DO SINO 🔥
//////////////////////////////////////////////////

public function ajax()
{

header('Content-Type: application/json');

$aniversarios=$this->model->aniversariosProximos();

$dizimos=$this->model->ultimosDizimos();

$lancamentos=$this->model->ultimosLancamentos();

echo json_encode([

'success'=>true,

'total'=>

count($aniversarios)
+count($dizimos)
+count($lancamentos),

'aniversarios'=>array_slice($aniversarios,0,),

'dizimos'=>array_slice($dizimos,0),

'lancamentos'=>array_slice($lancamentos,0,)

]);

exit;

}

}