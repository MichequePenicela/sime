<?php
namespace App\Models;

use PDO;
use App\Reports\MembrosReport;
use App\Reports\FinanceiroReport;
use App\Reports\EntradasReport;
use App\Reports\DizimistasReport;
use App\Reports\ListaMembrosReport;
use App\Reports\ContribuicaoReport;

class Relatorio
{
    private string $reportsDir;

    public function __construct(private PDO $db)
    {
        $this->reportsDir = BASE_PATH . '/uploads/reports';
    }


    /* =======================
       UTILIDADES
    ======================== */
    public function templateExiste(string $arquivo): bool
    {
        return file_exists($this->reportsDir . '/' . $arquivo);
    }

    public function slugExiste(string $slug): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM report_templates WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetchColumn() > 0;
    }

    /* =======================
       LISTAR
    ======================== */
    public function listarRelatorios(): array
    {
        return $this->db->query("
            SELECT id, nome, slug, arquivo, ativo, ultima_modificacao
            FROM report_templates
            ORDER BY ultima_modificacao DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =======================
       SCAN RECENTES
    ======================== */
    public function scanTemplatesRecentes(int $limite = 10): array
    {
        if (!is_dir($this->reportsDir)) return [];

        $files = glob($this->reportsDir . '/*.xlsx');
        $out = [];

        foreach ($files as $f) {
            $out[] = [
                'arquivo' => basename($f),
                'mtime'   => filemtime($f)
            ];
        }

        usort($out, fn($a,$b) => $b['mtime'] <=> $a['mtime']);

        return array_slice($out, 0, $limite);
    }

    /* =======================
       NÃO CADASTRADOS
    ======================== */
    public function templatesNaoCadastrados(): array
    {
        if (!is_dir($this->reportsDir)) return [];

        $arquivos = array_map('basename', glob($this->reportsDir . '/*.xlsx'));

        $cadastrados = $this->db
            ->query("SELECT arquivo FROM report_templates")
            ->fetchAll(PDO::FETCH_COLUMN);

        return array_values(array_diff($arquivos, $cadastrados));
    }

    /* =======================
       ADD TEMPLATE
    ======================== */
    public function cadastrar(
        string $nome,
        string $slug,
        string $arquivo,
        string $nivel,
        int $ativo
    ): bool {
        $stmt = $this->db->prepare("
            INSERT INTO report_templates
            (nome, slug, arquivo, nivel, ativo)
            VALUES (?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $nome,
            $slug,
            $arquivo,
            $nivel,
            $ativo
        ]);
    }
	
    /* =======================
       LISTAGEM RUNREPORT
    ======================== */
    public function listarDisponiveis(string $nivelUsuario): array
    {
        $stmt = $this->db->prepare("
            SELECT id, nome, slug, arquivo
            FROM report_templates
            WHERE ativo = 1
            ORDER BY nome ASC
        ");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorSlug(string $slug): ?array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM report_templates
            WHERE slug = ? AND ativo = 1
            LIMIT 1
        ");
        $stmt->execute([$slug]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        return $res ?: null;
    }

    /* =======================
       GERAÇÃO XLSX
    ======================== */
    public function gerarXlsx(
        string $slug,
        string $template,
        array $filtros
    ): string {
        return match ($slug) {
            'lista-de-todos-os-membros' => (new ListaMembrosReport($this->db))
                ->gerar($filtros),			
            'contagem-de-membros-e-convertidos' => (new MembrosReport($this->db))
                ->gerar($filtros),
			'resumo-mensal-financeiro' => (new FinanceiroReport($this->db))
                ->gerar($filtros),
			'resumo-mensal-entradas' => (new EntradasReport($this->db))
                ->gerar($filtros),
			'resumo-geral-de-dizimistas' => (new DizimistasReport($this->db))
                ->gerar($filtros),
            'relatorio-de-outras-ofertas-e-contribuicoes' => (new ContribuicaoReport($this->db))
                ->gerar($filtros),
            default => throw new \Exception('Relatório não implementado')
        };
    }

    /* =======================
       HISTÓRICO
    ======================== */
    public function registrarHistorico(
		int $reportTemplateId,
        string $slug,
        string $formato,
        ?string $dataInicio,
        ?string $dataFim,
        string $arquivo
    ): int {
        $stmt = $this->db->prepare("
            INSERT INTO report_logs
            (report_template_id, slug, formato, data_inicio, data_fim, arquivo, criado_em)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");

        $stmt->execute([
			$reportTemplateId,
            $slug,
            $formato,
            $dataInicio,
            $dataFim,
            $arquivo
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function ultimosGerados(int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                h.id,
                r.nome,
                h.formato,
                h.data_inicio,
                h.data_fim,
                h.criado_em
            FROM report_logs h
            JOIN report_templates r ON r.slug = h.slug
			AND deleted_at IS NULL
            ORDER BY h.criado_em DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
		
	public function buscarHistoricoPorId(int $id): ?array
{
    $stmt = $this->db->prepare("
        SELECT 
            h.id,
            h.report_template_id,
            h.slug,
            h.formato,
            h.data_inicio,
            h.data_fim,
            h.arquivo,
            h.criado_em,
            r.nome
        FROM report_logs h
        JOIN report_templates r 
            ON r.id = h.report_template_id
        WHERE h.id = ?
		AND deleted_at IS NULL
        LIMIT 1
    ");

    $stmt->execute([$id]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);

    return $res ?: null;
}
/*====================
Apagar Historico
====================*/
public function deleteReportLog(int $id): bool
{
    /* ========================
       1️⃣ Buscar arquivo antes
    ======================== */
    $buscar = $this->db->prepare("
        SELECT arquivo
        FROM report_logs
        WHERE id = :id
        AND deleted_at IS NULL
        LIMIT 1
    ");

    $buscar->execute(['id'=>$id]);

    $registro = $buscar->fetch(PDO::FETCH_ASSOC);

    if(!$registro){
        return false;
    }

    /* ========================
       2️⃣ Apagar arquivo físico
    ======================== */

    if(!empty($registro['arquivo'])){

        $path = BASE_PATH .
        '/storage/generatedReports/' .
        $registro['arquivo'];

        if(file_exists($path)){
            unlink($path); // remove ficheiro
        }
    }

    /* ========================
       3️⃣ Soft Delete
    ======================== */

    $stmt = $this->db->prepare("
        UPDATE report_logs
        SET deleted_at = NOW()
        WHERE id = :id
        AND deleted_at IS NULL
    ");

    return $stmt->execute([
        'id'=>$id
    ]);
}
}