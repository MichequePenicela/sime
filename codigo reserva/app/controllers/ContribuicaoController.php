<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Flash;
use App\Models\Contribuicao;
use PDO;
use DateTime;

class ContribuicaoController
{
    use Flash;

    private PDO $db;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->db = $GLOBALS['db'];
    }

    public function index(): void
    {
        Auth::init();

        require BASE_PATH . '/app/Views/layouts/header.php';
        require BASE_PATH . '/app/Views/contribuicoes/painelContribuicoes.php';
        require BASE_PATH . '/app/Views/layouts/footer.php';
    }

    public function painel(): void
    {
        Auth::init();

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->abort('Membro não especificado.');
        }

        $membro = Contribuicao::getMembro($this->db, $id);
        if (!$membro) {
            $this->abort('Membro não encontrado.');
        }

        require BASE_PATH . '/app/Views/layouts/header.php';
        require BASE_PATH . '/app/Views/contribuicoes/painelContribuinte.php';
        require BASE_PATH . '/app/Views/layouts/footer.php';
    }

    /* =====================================================
     * ADICIONAR CONTRIBUIÇÃO
     * 
     * ===================================================== */
    public function addContribuicao(): void
    {
		Auth::init();
		Auth::requireNivel('Gestor');
        $this->onlyPost();

        $membroId = filter_input(INPUT_POST, 'membro_id', FILTER_VALIDATE_INT);
        $quantia  = filter_input(INPUT_POST, 'quantia', FILTER_VALIDATE_FLOAT);
        $dataView = $_POST['data'] ?? null;

        if (!$membroId || !$quantia) {
            $this->jsonError('Dados obrigatórios não informados');
        }

        // 🔥 Data VIEW → DB
        $dataDb = $this->dateDb($dataView) ?? date('Y-m-d');

        $success = Contribuicao::addContribuicao(
            $this->db,
            $membroId,
            $quantia,
            $dataDb
        );
        
        $this->json([
            'success' => $success,
            'message' => $success
                ? 'Dízimo registrado com sucesso'
                : 'Dízimo já registrado anteriormente'
        ]);
    }

    public function buscarContribuinte(): void
    {
        Auth::init();

        try {
            $page    = max(1, (int) ($_GET['page'] ?? 1));
            $perPage = 10;
            $offset = ($page - 1) * $perPage;
            $nome = trim($_GET['nome'] ?? '');

            if ($nome === '') {
                $dados = Contribuicao::listarContribuintesMes($this->db, null, $perPage, $offset);
                $total = Contribuicao::countContribuintesMes($this->db);
            } else {
                $dados = Contribuicao::buscarContribuintesGlobal($this->db, $nome, $perPage, $offset);
                $total = Contribuicao::countContribuintesGlobal($this->db, $nome);
            }

            $this->json([
                'success' => true,
                'data' => $dados,
                'pagination' => [
                    'page' => $page,
                    'perPage' => $perPage,
                    'total' => $total,
                    'totalPages' => max(1, ceil($total / $perPage))
                ]
            ]);

        } catch (\Throwable $e) {
            $this->json(['success'=>false,'message'=>'Erro ao buscar membros contribuintes']);
        }
    }

    public function contribuicaoPorMembro(): void
    {
        Auth::init();

        $membroId = filter_input(INPUT_GET, 'membro_id', FILTER_VALIDATE_INT);
        if (!$membroId) {
            $this->jsonError('Membro inválido');
        }

        $inicio = $this->dateDb($_GET['inicio'] ?? null);
        $fim    = $this->dateDb($_GET['fim'] ?? null);

        $page    = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;
        $offset  = ($page - 1) * $perPage;

        $dados = Contribuicao::listarPorMembro($this->db,$membroId,$inicio,$fim,$perPage,$offset);
        $total = Contribuicao::countPorMembro($this->db,$membroId,$inicio,$fim);

        foreach ($dados as &$d) {
            $d['data'] = $this->dateView($d['data']);
        }

        $this->json([
            'success'=>true,
            'data'=>$dados,
            'pagination'=>[
                'page'=>$page,
                'perPage'=>$perPage,
                'total'=>$total,
                'totalPages'=>max(1, ceil($total / $perPage))
            ]
        ]);
    }

    public function atualizarcontribuicao(): void
    {
        Auth::init();

        $input = json_decode(file_get_contents('php://input'), true);

        $id      = (int) ($input['id'] ?? 0);
        $data    = $this->dateDb($input['data'] ?? null);
        $quantia = (float) ($input['quantia'] ?? 0);
        $obs     = $input['observacao'] ?? null;

        if (!$id || !$data || !$quantia) {
            $this->jsonError('Dados incompletos');
        }

        try {
            $model = new Contribuicao($this->db);
            $model->atualizar($id,$data,$quantia,$obs);

            $this->json(['success'=>true,'message'=>'Contribuição atualizado com sucesso.']);

        } catch (\Throwable $e) {
            $this->jsonError('Erro ao atualizar contribuição.');
        }
    }

    /* =========================
     * SOFT DELETE
     * ========================= */
    public function deletecontribuicao(int $id): void
    {
        Auth::init();

        if(!$id){
            $this->jsonError('ID inválido');
        }

        try{
            $model = new Contribuicao($this->db);
            $model->softDelete($id);

            $this->json([
                'success'=>true,
                'message'=>'Contribuição apagado com sucesso.'
            ]);

        }catch(\Throwable $e){
            $this->jsonError('Erro ao apagar contribuição');
        }
    }
//HELPERS
private function onlyPost(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }
}
    private function dateView(?string $date): ?string
    {
        return $date ? date('d-m-Y', strtotime($date)) : null;
    }

    private function dateDb(?string $date): ?string
    {
        if (!$date) return null;

        $dt = DateTime::createFromFormat('d-m-Y', $date)
           ?: DateTime::createFromFormat('Y-m-d', $date);

        return $dt?->format('Y-m-d');
    }

    private function json(array $data): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function jsonError(string $message): void
    {
        $this->json(['success'=>false,'message'=>$message]);
    }

    private function abort(string $mensagem): void
    {
        $this->flashError($mensagem);
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? BASE_URL));
        exit;
    }
}