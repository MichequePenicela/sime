<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Models\Relatorio;

class RelatoriosController
{
    private Relatorio $relatorio;

    public function __construct()
    {
        Auth::init();
        $this->relatorio = new Relatorio($GLOBALS['db']);
    }

    public function index()
    {
		Auth::requireNivel('Admin');
		 $relatorios = $this->relatorio->listarRelatorios();
        $templatesNaoBd = $this->relatorio->templatesNaoCadastrados();
        $templatesRecentes = $this->relatorio->scanTemplatesRecentes();
        require BASE_PATH . '/app/views/layouts/header.php';
        require BASE_PATH . '/app/views/relatorios/index.php';
        require BASE_PATH . '/app/views/layouts/footer.php';
    }
	/* =======================
       AJAX SCAN
    ======================== */
    public function scan()
    {
        Auth::requireNivel('Admin');

        header('Content-Type: application/json');

        echo json_encode([
            'novos' => $this->relatorio->templatesNaoCadastrados()
        ]);
        exit;
    }
	/* =======================
       AJAX SCAN
    ======================== */
	public function addReport()
{
    Auth::requireNivel('Admin');

    $input = json_decode(file_get_contents('php://input'), true);

    $nome     = trim($input['nome'] ?? '');
    $slug     = trim($input['slug'] ?? '');
    $template = trim($input['template'] ?? '');
    $nivel    = trim($input['nivel'] ?? 'Usuário');
    $ativo    = isset($input['ativo']) ? (int)$input['ativo'] : 0;

    if (!$nome || !$slug || !$template) {
        echo json_encode(['success' => false, 'error' => 'Preencha todos os campos']);
        return;
    }

    if (!$this->relatorio->templateExiste($template)) {
        echo json_encode(['success' => false, 'error' => 'Template não existe no diretório']);
        return;
    }

    $this->relatorio->cadastrar($nome, $slug, $template, $nivel, $ativo);

    echo json_encode(['success' => true]);
}

	public function runReport()
    {
		Auth::init();
		Auth::requireNivel('Gestor');
		$nivelUsuario = Auth::nivel();
		
		$relatorios = $this->relatorio->listarDisponiveis($nivelUsuario);
		$relatoriosGerados = $this->relatorio->ultimosGerados(10);
        require BASE_PATH . '/app/views/layouts/header.php';
        require BASE_PATH . '/app/views/relatorios/report.php';
        require BASE_PATH . '/app/views/layouts/footer.php';
    }
	
	/**
     * Correr relatórios 
     * - Apenas ativos
     */
	 
private function validarIntervaloMeses(
    ?string $inicio,
    ?string $fim,
    int $maxMeses
): void {
    if (!$inicio || !$fim) {
        return; // sem datas, não valida
    }

    $dtInicio = new \DateTimeImmutable($inicio);
    $dtFim    = new \DateTimeImmutable($fim);

    if ($dtFim < $dtInicio) {
        throw new \Exception('A data final não pode ser menor que a data inicial.');
    }

    $limite = $dtInicio
	->modify("+{$maxMeses} months")
	->modify('-1 day');
	
    if ($dtFim > $limite){
        throw new \Exception(
            "Este relatório permite no máximo {$maxMeses} meses de intervalo."
        );
    }
}
public function run()
    {
        Auth::requireNivel('Gestor');
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Método inválido']);
            return;
        }

        $slug       = $_POST['slug'] ?? null;
        $formato    = $_POST['formato'] ?? 'xlsx';
        $dataInicio = $_POST['data_inicio'] ?? null;
        $dataFim    = $_POST['data_fim'] ?? null;

        if (!$slug) {
            echo json_encode(['success' => false, 'error' => 'Relatório não selecionado']);
            return;
        }

        $template = $this->relatorio->buscarPorSlug($slug);
        if (!$template) {
            echo json_encode(['success' => false, 'error' => 'Relatório não encontrado']);
            return;
        }

        try {

    /* ===============================
       LIMITES POR RELATÓRIO
    =============================== */
    $limites = [
        'resumo-mensal-financeiro' => 12,
        'resumo-mensal-entradas'      => 12,
        //'resumo-geral-de-dizimistas' => 6,
        // outros slugs se quiser
    ];

    if (isset($limites[$slug])) {
        $this->validarIntervaloMeses(
            $dataInicio,
            $dataFim,
            $limites[$slug]
        );
    }

    /* ===============================
       GERAR RELATÓRIO
    =============================== */
    $arquivo = $this->relatorio->gerarXlsx(
        slug: $slug,
        template: $template['arquivo'],
        filtros: [
            'data_inicio' => $dataInicio,
            'data_fim'    => $dataFim
        ]
    );

    $id = $this->relatorio->registrarHistorico(
        reportTemplateId: (int)$template['id'],
        slug: $slug,
        formato: $formato,
        dataInicio: $dataInicio,
        dataFim: $dataFim,
        arquivo: basename($arquivo)
    );

    echo json_encode([
        'success' => true,
        'download_url' => BASE_URL . '/relatorios/download/' . $id
    ]);

} catch (\Throwable $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
	}
/*=========================

*DOWNLOAD
=========================*/

public function download(int $id)
    {
        Auth::requireNivel('Gestor');

        $registro = $this->relatorio->buscarHistoricoPorId($id);
        if (!$registro) {
            die('Arquivo não encontrado');
        }

        $path = BASE_PATH . '/storage/generatedReports/' . $registro['arquivo'];
        if (!file_exists($path)) {
            die('Arquivo não existe no servidor');
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$registro['arquivo'].'"');
        header('Content-Length: ' . filesize($path));

        readfile($path);
        exit;
    }
	
	public function deleteReportLog(int $id)
{
    Auth::requireNivel('Admin');

    if ($id <= 0) {
        $_SESSION['flash_modal'] = [
            'tpo' => 'danger',
            'mensagem' => 'Log inválido.'
        ];
        header('Location: ' . BASE_URL . '/relatorios/runReport');
        exit;
    }

    $ok = $this->relatorio->deleteReportLog($id);

    if ($ok) {
        $_SESSION['flash_modal'] = [
		'titulo'   => 'Registro Eliminado',
		'mensagem' => 'Historico removido com sucesso.',
		'icon'     => 'check2-circle', // bootstrap icon
        'tipo'     => 'success'
		
        ];
    } else {
        $_SESSION['flash_modal'] = [
		'titulo'   => 'Erro',
		'mensagem' => 'Ocorreu um Erro ao remover registro.',
		'icon'     => 'x-circle-fill', // bootstrap icon
        'tipo'     => 'danger'
        ];
    }

    header('Location: ' . BASE_URL . '/relatorios/runReport');
    exit;
}

}