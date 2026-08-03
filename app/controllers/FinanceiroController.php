<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Models\Financeiro;
use Throwable;

class FinanceiroController
{
    private $db;

    public function __construct()
    {
        $this->db = $GLOBALS['db'];
    }

    /* ======================================================
     * DASHBOARD PRINCIPAL
     * ====================================================== */
    public function index(): void
    {
		Auth::init();
        $inicio = date('Y-m-01');
        $fim    = date('Y-m-t');

        $dados = [
            'entradas' => Financeiro::totalEntradas($this->db, $inicio, $fim),
            'despesas' => Financeiro::totalDespesas($this->db, $inicio, $fim),
            'dizimos'  => Financeiro::totalDizimos($this->db, $inicio, $fim),
            'contribuicoes'  => Financeiro::totalContribuicoes($this->db, $inicio, $fim),
        ];
		$dados['total_entradas'] = $dados['entradas']+$dados['dizimos']+$dados['contribuicoes'];
        $dados['saldo'] = $dados['total_entradas']- $dados['despesas'];
		
        require BASE_PATH . '/app/Views/layouts/header.php';
        require BASE_PATH . '/app/Views/finance/index.php';
        require BASE_PATH . '/app/Views/layouts/footer.php';
    }

    /* ======================================================
     * INSERÇÕES (AJAX)
     * ====================================================== */
    public function addEntrada(): void
{
    $ok = Financeiro::addEntrada($this->db, $_POST);

    $this->json([
        'success' => $ok,
        'message' => $ok
            ? 'Entrada registrada com sucesso'
            : 'Entrada duplicada detectada'
    ]);
}

public function addDespesa(): void
{
    $ok = Financeiro::addDespesa($this->db, $_POST);

    $this->json([
        'success' => $ok,
        'message' => $ok
            ? 'Despesa registrada com sucesso'
            : 'Despesa duplicada detectada'
    ]);
}

    /* ======================================================
     * TELA DE PESQUISA
     * ====================================================== */
    public function pesquisa(): void
    {
		Auth::init();
        require BASE_PATH . '/app/Views/layouts/header.php';
        require BASE_PATH . '/app/Views/finance/pesquisa.php';
        require BASE_PATH . '/app/Views/layouts/footer.php';
    }

    /* ======================================================
     * DASHBOARD DE DETALHES
     * ====================================================== */
    public function dashboard(): void
    {
		Auth::init();
        $id   = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $tipo = filter_input(INPUT_GET, 'tipo', FILTER_SANITIZE_SPECIAL_CHARS);

        if (!$id || !in_array($tipo, ['entradas', 'despesas'])) {
            die('Parâmetros inválidos');
        }

        $dados = $tipo === 'entradas'
            ? Financeiro::getEntradaById($this->db, $id)
            : Financeiro::getDespesaById($this->db, $id);

        if (!$dados || !empty($dados['deleted_at'])) {
            die('Registro não encontrado');
        }

        // Padronização para View
        $dados['tipo']  = $tipo;
        $dados['data']  = date('d-m-Y', strtotime($dados['data']));
        $dados['valor_view'] = number_format($dados['valor'], 2, ',', '.');
		$dados['valor_edit'] = (float) $dados['valor'];
		
        require BASE_PATH . '/app/Views/layouts/header.php';
        require BASE_PATH . '/app/Views/finance/dashboard.php';
        require BASE_PATH . '/app/Views/layouts/footer.php';
    }

    /* ======================================================
     * BUSCAR COM PAGINAÇÃO (AJAX)
     * ====================================================== */
    public function buscar(): void
{
	Auth::init();
    try {
        $tipo       = $_POST['tipo'] ?? null;
        $dataInicio = $_POST['data_inicio'] ?? null;
        $dataFim    = $_POST['data_fim'] ?? null;

        $page     = max(1, (int)($_POST['page'] ?? 1));
        $perPage  = 10;
        $offset   = ($page - 1) * $perPage;

        if (!in_array($tipo, ['entradas', 'despesas'])) {
            $this->jsonWarning('Selecione o tipo de pesquisa');
            return;
        }

        if (empty($dataInicio) || empty($dataFim)) {
            $this->jsonWarning('Preencha a data inicial e a data final');
            return;
        }

        if ($tipo === 'entradas') {
            $dados = Financeiro::buscarEntradas($this->db, $_POST, $perPage, $offset);
            $total = Financeiro::countEntradas($this->db, $_POST);
        } else {
            $dados = Financeiro::buscarDespesas($this->db, $_POST, $perPage, $offset);
            $total = Financeiro::countDespesas($this->db, $_POST);
        }

        // 👉 CASO NÃO ENCONTRE RESULTADOS
        if ($total === 0) {
            $this->json([
                'success' => false,
                'type'    => 'empty',
                'message' => 'Nenhum resultado encontrado para o período selecionado',
                'data'    => []
            ]);
            return;
        }

        // 👉 CASO TENHA RESULTADOS
        $this->json([
            'success'    => true,
            'data'       => $dados,
            'pagination' => [
                'page'         => $page,
                'per_page'    => $perPage,
                'total'       => $total,
                'total_pages' => (int) ceil($total / $perPage)
            ]
        ]);

    } catch (Throwable $e) {
        $this->jsonError('Erro interno ao processar pesquisa');
    }
}


    /* ======================================================
     * EDITAR (AJAX)
     * ====================================================== */
    public function editar(): void
    {
		Auth::init();
        try {
            $id   = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $tipo = $_POST['tipo'] ?? null;

            if (!$id || !in_array($tipo, ['entradas', 'despesas'])) {
                $this->jsonError('Dados inválidos');
            }

            $ok = Financeiro::editar($this->db, $id, $tipo, $_POST);
            $this->json(['success' => $ok]);

        } catch (Throwable $e) {
            $this->jsonError('Erro interno ao editar');
        }
    }

    /* ======================================================
     * EXCLUIR (SOFT DELETE - AJAX)
     * ====================================================== */
    public function excluir(): void
    {
		Auth::init();
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            $id   = $input['id']   ?? null;
            $tipo = $input['tipo'] ?? null;

            if (!is_numeric($id) || !in_array($tipo, ['entradas', 'despesas'])) {
                $this->jsonError('Parâmetros inválidos');
            }

            $ok = Financeiro::excluir($this->db, (int)$id, $tipo);

            if (!$ok) {
                $this->jsonError('Erro ao excluir registro');
            }

            $this->json(['success' => true]);

        } catch (Throwable $e) {
            $this->jsonError('Erro interno');
        }
    }

    /* ======================================================
     * HELPERS PRIVADOS
     * ====================================================== */
    private function json(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    private function jsonWarning(string $msg): void
    {
        $this->json([
            'success' => false,
            'type'    => 'warning',
            'message' => $msg,
            'data'    => []
        ]);
    }

    private function jsonError(string $msg): void
    {
        $this->json([
            'success' => false,
            'type'    => 'danger',
            'message' => $msg,
            'data'    => []
        ]);
    }
}
