<?php

namespace App\Controllers;

use App\Models\Membro;
use App\Core\Auth;
use PDO;
use DateTime;

class MembroController
{
    private PDO $db;
	private Membro $membro;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->db = $GLOBALS['db'];
		$this->membro = new Membro($this->db);
    }

    /* =====================================================
     * LISTA DE MEMBROS
     * GET /membros
     * ===================================================== */
    public function index(): void
    {
		Auth::init();
        require BASE_PATH . '/app/Views/layouts/header.php';
        require BASE_PATH . '/app/Views/membros/index.php';
        require BASE_PATH . '/app/Views/layouts/footer.php';
    }

    /* =====================================================
     * DASHBOARD DO MEMBRO
     * GET /membros/dashboard?id={id}
     * ===================================================== */
    public function dashboard(): void
    {
		Auth::init();
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->abort('Membro não especificado.');
        }

        $membro = Membro::findByIdWithMoradia($this->db, $id);

        if (!$membro) {
            $this->abort('Membro não encontrado.');
        }

        // 🔥 Datas para VIEW
        $membro['data_nascimento'] = $this->dateView($membro['data_nascimento']);
        $membro['data_conversao']  = $this->dateView($membro['data_conversao']);

        $ultimosDizimos = Membro::getUltimoDizimo($this->db, $id);
        if ($ultimosDizimos) {
            $ultimosDizimos['data'] = $this->dateView($ultimosDizimos['data']);
        }

        $ultimaContribuicao = Membro::getUltimaContribuicao($this->db, $id);
        if ($ultimaContribuicao) {
            $ultimaContribuicao['data'] = $this->dateView($ultimaContribuicao['data']);
        }
        $parentesResult = Membro::getParentes($this->db, $id);

        require BASE_PATH . '/app/Views/layouts/header.php';
        require BASE_PATH . '/app/Views/membros/dashboard.php';
        require BASE_PATH . '/app/Views/layouts/footer.php';
    }
	/* DASHBOARD ESTATISTICAS*/
public function datamembers()
{
	Auth::init();
    require BASE_PATH . '/app/views/layouts/header.php';
    require BASE_PATH . '/app/views/membros/memberstatistics.php';
    require BASE_PATH . '/app/views/layouts/footer.php';
}

public function datamember()
{
	Auth::init();
    try {
        $inicio = $_GET['inicio'] ?? null;
        $fim    = $_GET['fim'] ?? null;

        echo json_encode([
            'success' => true,
            'data' => $this->membro->dashboardStats($inicio, $fim)
        ], JSON_UNESCAPED_UNICODE);

    } catch (\Throwable $e) {

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}



    /* =====================================================
     * PESQUISA DE MEMBROS (AJAX)
     * GET /membros/search
     * ===================================================== */
public function search(): void
{
    Auth::init();

    $nome = trim($_GET['nome'] ?? '');
    $page = max(1, (int) ($_GET['page'] ?? 1));

    $perPage = 20;
    $offset  = ($page - 1) * $perPage;

    $data  = Membro::searchPaginated($this->db, $nome, $perPage, $offset);
    $total = Membro::countSearch($this->db, $nome);

    $this->json([
        'success' => true,
        'data' => $data,
        'pagination' => [
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'totalPages' => max(1, ceil($total / $perPage))
        ]
    ]);
}


    /* =====================================================
     * CADASTRO DE MEMBRO (AJAX)
     * POST /membros/save
     * ===================================================== */
    /*public function save(): void
    {
		Auth::init();
		Auth::requireNivel('Gestor');
        $this->onlyPost();

        if (empty($_POST['nome']) || empty($_POST['departamento']) || empty($_POST['sexo']) || empty($_POST['data_nascimento']) || empty($_POST['data_conversao'])) {
            $this->jsonError('Preencha Todos campos obrigatórios');
        }

        $success = Membro::create($this->db, $_POST);

        $this->json([
            'success' => $success,
            'message' => $success
                ? 'Membro cadastrado com sucesso!'
                : 'Erro ao cadastrar membro.'
        ]);
    }*/

    public function verificarSimilaridade()
    {
        Auth::init();
        Auth::requireNivel('Gestor');
    
        $_SESSION['novo_membro'] = $_POST;
    
        $similares = Membro::buscarSimilares($this->db, $_POST);
    
        //  SE EXISTIR SIMILARES  REDIRECIONA PARA COMPARAÇÃO
        if (!empty($similares)) {
    
            $_SESSION['similares'] = $similares;
    
            echo json_encode([
                'success' => true,
                'redirect' => BASE_URL . '/membros/comparacao'
            ]);
            exit;
        }
    
        // SALVA DIRETO
        $membroId = Membro::create($this->db, $_POST);
    
        echo json_encode([
            'success' => (bool)$membroId,
            'message' => $membroId
                ? 'Membro cadastrado com sucesso'
                : 'Erro ao cadastrar',
            'redirect' => $membroId
                ? BASE_URL . '/membros/dashboard?id=' . $membroId
                : null
        ]);
    }

public function comparacao()
{
    Auth::init();
    Auth::requireNivel('Gestor');

    $similares = $_SESSION['similares'] ?? [];
    $dados     = $_SESSION['novo_membro'] ?? [];

    if (!$dados) {
        header("Location: " . BASE_URL . "/membros");
        exit;
    }

    require BASE_PATH . '/app/Views/layouts/header.php';
    require BASE_PATH . '/app/Views/membros/comparacao.php';
    require BASE_PATH . '/app/Views/layouts/footer.php';
}

public function confirmarCadastro()
{
    Auth::init();
    Auth::requireNivel('Gestor');

    if (empty($_SESSION['novo_membro'])) {

        echo json_encode([
            'success' => false,
            'message' => 'Sessão expirada'
        ]);
        return;
    }

    $dados = $_SESSION['novo_membro'];

    $membroId = Membro::create($this->db, $dados);

    unset($_SESSION['novo_membro']);
    unset($_SESSION['similares']);

    echo json_encode([
        'success' => (bool)$membroId,
        'message' => $membroId
            ? 'Cadastro confirmado com sucesso'
            : 'Erro ao confirmar cadastro',
        'redirect' => $membroId
            ? BASE_URL . '/membros/dashboard?id=' . $membroId
            : null
    ]);
}
	/**
 * ATUALIZAR MEMBRO + MORADIA (AJAX)
 * POST /membros/update
 */
public function update()
{
	Auth::init();
	Auth::requireNivel('Gestor');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        $this->jsonError('Método não permitido');
    }

    $membroId = (int) ($_POST['membro_id'] ?? 0);
    if ($membroId <= 0) {
        $this->jsonError('Membro inválido');
    }

    try {
        $this->db->beginTransaction();

        // ================= MEMBRO =================
        $okMembro = Membro::update($this->db, $membroId, [
            'nome'            => $_POST['nome'] ?? '',
            'data_nascimento' => $_POST['data_nascimento'] ?? null,
            'data_conversao'  => $_POST['data_conversao'] ?? null,
            'departamento'    => $_POST['departamento'] ?? null,
			'permanencia'    => $_POST['permanencia'] ?? null,
            'batizado'        => $_POST['batizado'] ?? 'nao',
        ]);

        if (!$okMembro) {
            throw new \Exception('Erro ao atualizar membro');
        }

        // ================= MORADIA =================
        $okMoradia = Membro::updateMoradia($this->db, $membroId, [
            'bairro'                    => $_POST['bairro'] ?? null,
            'referencia'                => $_POST['referencia'] ?? null,
            'moradia_numero_celular'    => $_POST['numero_celular'] ?? null,
            'moradia_numero_alternativo'=> $_POST['numero_alternativo'] ?? null,
            'moradia_celular_cuidador'  => $_POST['celular_cuidador'] ?? null,
        ]);

        if (!$okMoradia) {
            throw new \Exception('Erro ao atualizar moradia');
        }

        $this->db->commit();

        $this->json([
            'success' => true,
            'message' => 'Membro atualizado com sucesso'
        ]);

    } catch (\Throwable $e) {
        $this->db->rollBack();

        http_response_code(500);
        $this->json([
            'success' => false,
            'message' => 'Erro ao atualizar membro',
            'debug'   => $e->getMessage() // remove em produção se quiser
        ]);
    }
}

    /* =====================================================
     * ADICIONAR DÍZIMO (AJAX)
     * POST /membros/addDizimo
     * ===================================================== */
    public function addDizimo(): void
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

        $success = Membro::addDizimo(
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

    /* =====================================================
     * LISTAR TIPO DE PARENTESCO
     * ===================================================== */
    public function listarTiposParentesco(): void
{
    $tipos = Membro::getTiposParentesco($this->db);
    $this->json($tipos);
}

    /* =====================================================
     * ADICIONAR PARENTESCO (AJAX)
     * POST /membros/addParentesco
     * ===================================================== */
    /*public function addParentesco(): void
    {
		Auth::init();
		Auth::requireNivel('Gestor');
        $this->onlyPost();

        $data = json_decode(file_get_contents('php://input'), true);

        $membroId     = (int) ($data['membro_id'] ?? 0);
        $parenteId    = (int) ($data['parente_id'] ?? 0);
        $parentescoId = (int) ($data['parentesco_id'] ?? 0);

        if (!$membroId || !$parenteId || !$parentescoId) {
            $this->jsonError('Dados inválidos');
        }

        if ($membroId === $parenteId) {
            $this->jsonError('Um membro não pode ser parente de si mesmo');
        }

        if (Membro::verificarParentesco($this->db, $membroId, $parenteId)) {
            $this->jsonError('Este parentesco já existe');
        }

        $success = Membro::addParentesco(
            $this->db,
            $membroId,
            $parenteId,
            $parentescoId
        );

        $this->json([
            'success' => $success,
            'message' => $success
                ? 'Parentesco registrado com sucesso'
                : 'Erro ao registrar parentesco'
        ]);
    }*/

    /* =====================================================
     * DELETAR MEMBRO (AJAX)
     * POST /membros/delete
     * ===================================================== */
    public function delete(): void
    {
		Auth::init();
		Auth::requireNivel('Admin');
        $this->onlyPost();

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->jsonError('ID inválido');
        }

        $success = Membro::softDelete($this->db, $id);

        $this->json([
            'success' => $success,
            'message' => $success
                ? 'Membro excluído'
                : 'Erro ao excluir membro'
        ]);
    }

    /* =====================================================
     * DELETAR PARENTE (AJAX)
     * POST /membros/deleteParente
     * ===================================================== */
    /*public function deleteParente(): void
    {
		Auth::init();
		Auth::requireNivel('Gestor');
        $this->onlyPost();

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->jsonError('ID inválido');
        }

        $success = Membro::deleteParente($this->db, $id);

        $this->json([
            'success' => $success,
            'message' => $success
                ? 'Parente excluído'
                : 'Erro ao excluir parente'
        ]);
    }*/

    /* =====================================================
     * HELPERS
     * ===================================================== */

    private function dateView(?string $date): ?string
    {
        if (!$date) return null;
        return date('d-m-Y', strtotime($date));
    }

    private function dateDb(?string $date): ?string
    {
        if (empty($date)) return null;

        $dt = DateTime::createFromFormat('d-m-Y', $date)
           ?: DateTime::createFromFormat('Y-m-d', $date);

        return $dt ? $dt->format('Y-m-d') : null;
    }

    private function onlyPost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }
    }

    private function json(array $data): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    private function jsonError(string $message): void
    {
        $this->json([
            'success' => false,
            'message' => $message
        ]);
    }

    private function abort(string $message): void
    {
        http_response_code(400);
        echo $message;
        exit;
    }
}
