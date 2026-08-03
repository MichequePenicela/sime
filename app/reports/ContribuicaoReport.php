<?php
namespace App\Reports;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PDO;

class ContribuicaoReport
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function gerar(array $filtros): string
    {
        $dataInicio = $filtros['data_inicio'] ?? null;
        $dataFim    = $filtros['data_fim'] ?? null;

        /* =======================
           TEMPLATE
        ======================== */
        $templatePath = BASE_PATH . '/uploads/reports/Relatorio_de_Outras_Ofertas_e_Contribuicoes.xlsx';
        $spreadsheet  = IOFactory::load($templatePath);

        /* =======================
           ABA DETALHES
        ======================== */
        $detalhes = $spreadsheet->getSheetByName('Detalhes');
        if (!$detalhes) {
            throw new \Exception('A aba "Detalhes" não existe no template.');
        }

        // Cabeçalho
        $detalhes->setCellValue('F3', $dataInicio);
        $detalhes->setCellValue('F4', $dataFim);
        $detalhes->setCellValue('F5', date('d-m-y'));

        // Dados
        $sql = "
            SELECT 
                c.data,
                m.nome,
                c.quantia
            FROM contribuicoes c
            JOIN membros m ON m.id = c.membro_id
            WHERE (:ini IS NULL OR c.data >= :ini)
              AND (:fim IS NULL OR c.data <= :fim)
              AND C.deleted_at IS NULL
            ORDER BY c.data ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':ini' => $dataInicio,
            ':fim' => $dataFim
        ]);

        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $row = 4;
        foreach ($dados as $d) {
            $detalhes->setCellValue("B{$row}", $d['data']);
            $detalhes->setCellValue("C{$row}", $d['nome']);
            $detalhes->setCellValue("D{$row}", (float)$d['quantia']);
            $row++;
        }

        /* =======================
           ABA RESUMO
        ======================== */
        $resumo = $spreadsheet->getSheetByName('Resumo');
        if (!$resumo) {
            throw new \Exception('A aba "Resumo" não existe no template.');
        }
		$resumo->setCellValue('D1', $dataInicio);
        $resumo->setCellValue('D2', $dataFim);
        $resumo->setCellValue('D3', date('d-m-y'));
        // Agrupado por membro
        $sqlResumo = "
            SELECT
                m.nome,
                COUNT(c.id)   AS total_contribuicoes,
                SUM(c.quantia) AS total_valor
            FROM contribuicoes c
            JOIN membros m ON m.id = c.membro_id
            WHERE (:ini IS NULL OR c.data >= :ini)
              AND (:fim IS NULL OR c.data <= :fim)
              AND C.deleted_at IS NULL
            GROUP BY m.id, m.nome
            ORDER BY m.nome ASC
        ";

        $stmt = $this->db->prepare($sqlResumo);
        $stmt->execute([
            ':ini' => $dataInicio,
            ':fim' => $dataFim
        ]);

        $resumoDados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $row = 5;
        foreach ($resumoDados as $r) {
            $resumo->setCellValue("A{$row}", $r['nome']);
            $resumo->setCellValue("B{$row}", (int)$r['total_contribuicoes']);
            $resumo->setCellValue("C{$row}", (float)$r['total_valor']);
            $row++;
        }

        /* =======================
           TOTAIS GERAIS
        ======================== */
        $sqlTotais = "
            SELECT
                COUNT(*) AS total_contribuicoes,
                SUM(quantia) AS total_valor
            FROM contribuicoes
            WHERE (:ini IS NULL OR data >= :ini)
              AND (:fim IS NULL OR data <= :fim)
              AND deleted_at IS NULL
        ";

        $stmt = $this->db->prepare($sqlTotais);
        $stmt->execute([
            ':ini' => $dataInicio,
            ':fim' => $dataFim
        ]);

        $totais = $stmt->fetch(PDO::FETCH_ASSOC);

        $resumo->setCellValue('G1', (int)$totais['total_contribuicoes']);
        $resumo->setCellValue('G2', (float)$totais['total_valor']);

        /* =======================
           SALVAR
        ======================== */
        $arquivo = 'Relatorio_de_Outras_Ofertas_e_Contribuicoes.xlsx_' . time() . '.xlsx';
        $output  = BASE_PATH . '/storage/generatedReports/' . $arquivo;

        IOFactory::createWriter($spreadsheet, 'Xlsx')->save($output);

        return $output;
    }
}