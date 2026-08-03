<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Models\Participacoes;
use Throwable;

class ParticipacoesController
{
    private $db;

    public function __construct()
    {
        $this->db = $GLOBALS['db'];
    }

    public function dadosCulto(): void
    {
        Auth::init();

        $registros = Participacoes::getMesAtual($this->db);
        $stats     = Participacoes::getStatsMesAtual($this->db);

        require BASE_PATH . '/app/Views/layouts/header.php';
        require BASE_PATH . '/app/Views/membros/participacao.php';
        require BASE_PATH . '/app/Views/layouts/footer.php';
    }

    public function add(): void
    {
        Auth::init();

        try {

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->jsonError('Método inválido');
            }

            if (empty($_POST['data'])) {
                $this->jsonWarning('Informe a data');
            }

            if (Participacoes::existsByDate($this->db, $_POST['data'])) {
                $this->jsonWarning('Já existe registro para esta data');
            }

            $ok = Participacoes::add($this->db, $_POST);

            $this->json([
                'success' => $ok,
                'message' => $ok
                    ? 'Participação registrada com sucesso'
                    : 'Erro ao registrar participação'
            ]);

        } catch (Throwable $e) {
            $this->jsonError('Erro interno');
        }
    }

    public function editarDadosCulto()
    {
        header('Content-Type: application/json');
    
        $data = json_decode(file_get_contents("php://input"), true);
    
        if(!$data){
            echo json_encode(['success'=>false,'erro'=>'sem dados']);
            return;
        }
        $ok = Participacoes::editar($this->db,$data);
    
        echo json_encode([
            'success'=>$ok,
            'data'=>$data
        ]);
    }

    public function excluirDadosCulto(): void
    {
        Auth::init();
        $input=json_decode(file_get_contents('php://input'),true);
        $this->json(['success'=>Participacoes::excluir($this->db,$input['id']??0)]);
    }

    private function json(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    private function jsonWarning(string $msg): void
    {
        $this->json(['success'=>false,'type'=>'warning','message'=>$msg]);
    }

    private function jsonError(string $msg): void
    {
        $this->json(['success'=>false,'type'=>'danger','message'=>$msg]);
    }
}