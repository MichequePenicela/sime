<?php
namespace App\Controllers;

use App\Models\Usuario;
use App\Core\Auth;
use PDO;

class UsuariosController
{
    private PDO $db;
    private Usuario $usuario;

    public function __construct()
    {

        // PDO global vindo do database.php
        $this->db = $GLOBALS['db'];

        // 🚨 AQUI ESTAVA O PROBLEMA
        $this->usuario = new Usuario($this->db);
    }

    /* ===============================
     * DASHBOARD
     * =============================== */
    public function dashboard()
    {
	Auth::init();
	Auth::requireNivel('Gestor');
        $usuarios = $this->usuario->all();
        $stats    = $this->usuario->stats();
		require BASE_PATH . '/app/views/layouts/header.php';
        require BASE_PATH . '/app/views/usuarios/dashboard.php';
    }

    /* ===============================
     * SEARCH (AJAX)
     * =============================== */
    public function search()
    {
	Auth::init();
        $term = $_GET['q'] ?? '';
        echo json_encode($this->usuario->search($term));
    }

    /* ===============================
     * ADD USUÁRIO
     * =============================== */
    public function add()
    {
	Auth::init();
	Auth::requireNivel('Admin');
        if (
            empty($_POST['nome']) ||
            empty($_POST['usuario']) ||
            empty($_POST['senha'])
        ) {
            return $this->jsonError('Preencha todos os campos obrigatórios');
        }

        $this->usuario->add($_POST);
        $this->jsonSuccess('Usuário criado com sucesso');
    }
	/* ===========================
     * PAINEL DO USUÁRIO
     * =========================== */
    public function painel()
    {
	Auth::init();
	Auth::requireNivel('Gestor');
        $id = (int) ($_GET['id'] ?? 0);

        if (!$id) {
            die('Usuário inválido');
        }

        $usuario = $this->usuario->find($id);

        if (!$usuario) {
            die('Usuário não encontrado');
        }
		require BASE_PATH . '/app/Views/layouts/header.php';
        require BASE_PATH . '/app/views/usuarios/painel.php';
		 require BASE_PATH . '/app/Views/layouts/footer.php';
    }
	 /* ===========================
     * ATUALIZAR USUÁRIO (AJAX)
     * =========================== */
    	public function update()
{
Auth::init();
Auth::requireNivel('Admin');
    $id = (int)($_POST['id'] ?? 0);

    if (!$id) {
        return $this->jsonError('ID inválido');
    }

    if ($_POST['senha'] !== ($_POST['senha_confirmar'] ?? '')) {
        return $this->jsonError('As senhas não coincidem');
    }

    $ok = $this->usuario->update($id, $_POST);

    $ok
        ? $this->jsonSuccess('Usuário atualizado com sucesso')
        : $this->jsonError('Erro ao atualizar usuário');
}
/*=======================
*Ativar Desativar
===========================*/
public function toggleStatus()
{
Auth::init();
Auth::requireNivel('Admin');
    $id     = (int)($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';

    if (!$id || !in_array($status, ['Ativo', 'Inativo'])) {
        return $this->jsonError('Dados inválidos');
    }

    $this->usuario->toggleStatus($id, $status);
    $this->jsonSuccess('Status atualizado');
}


/* ===========================
     * HELPERS JSON
     * =========================== */
    private function jsonSuccess(string $msg)
    {
        echo json_encode(['success' => true, 'message' => $msg]);
        exit;
    }

    private function jsonError(string $msg)
    {
        echo json_encode(['success' => false, 'message' => $msg]);
        exit;
    }
}
