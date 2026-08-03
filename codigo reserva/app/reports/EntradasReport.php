<?php
namespace App\Reports;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PDO;

class EntradasReport
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

        $templatePath = BASE_PATH . '/uploads/reports/Resumo_Mensal_Entradas.xlsx';
        $spreadsheet  = IOFactory::load($templatePath);

        /* =======================
           ABA DETALHES
        ======================== */

        $detalhes = $spreadsheet->getSheetByName('Detalhes');

        $sqlDetalhes = "

            SELECT
                data,
                SUM(entrada) AS total_entradas,
                SUM(dizimo) AS total_dizimos,
                SUM(contribuicao) AS total_contribuicoes

            FROM (

                SELECT data,
                       valor AS entrada,
                       0 AS dizimo,
                       0 AS contribuicao
                FROM entradas
                WHERE deleted_at IS NULL

                UNION ALL

                SELECT data,
                       0,
                       quantia,
                       0
                FROM dizimos
                WHERE deleted_at IS NULL

                UNION ALL

                SELECT data,
                       0,
                       0,
                       quantia
                FROM contribuicoes
                WHERE deleted_at IS NULL

            ) t

            WHERE (:ini IS NULL OR data >= :ini)
              AND (:fim IS NULL OR data <= :fim)

            GROUP BY data
            ORDER BY data ASC
        ";

        $stmt = $this->db->prepare($sqlDetalhes);

        $stmt->execute([
            ':ini'=>$dataInicio,
            ':fim'=>$dataFim
        ]);

        $registos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $row = 2;

        foreach($registos as $r){

            $detalhes->setCellValue("B{$row}", $r['data']);

            $detalhes->setCellValue("C{$row}",
                (float)$r['total_entradas']
            );

            $detalhes->setCellValue("D{$row}",
                (float)$r['total_dizimos']
            );

            // NOVA COLUNA E → CONTRIBUIÇÕES
            $detalhes->setCellValue("E{$row}",
                (float)$r['total_contribuicoes']
            );

            // TOTAL AGORA NA F
            $detalhes->setCellValue("F{$row}",

                (float)$r['total_entradas']
                + (float)$r['total_dizimos']
                + (float)$r['total_contribuicoes']
            );

            $row++;
        }

        /* =======================
           ABA RESUMO
        ======================== */

        $resumo = $spreadsheet->getSheetByName('Resumo');

        /* ---- Totais gerais ---- */

        $sqlTotais = "

            SELECT

                SUM(entrada) entradas,
                SUM(dizimo) dizimos,
                SUM(contribuicao) contribuicoes

            FROM(

                SELECT valor entrada,0 dizimo,0 contribuicao,data
                FROM entradas
                WHERE deleted_at IS NULL

                UNION ALL

                SELECT 0,quantia,0,data
                FROM dizimos
                WHERE deleted_at IS NULL

                UNION ALL

                SELECT 0,0,quantia,data
                FROM contribuicoes
                WHERE deleted_at IS NULL

            ) t

            WHERE (:ini IS NULL OR data >= :ini)
              AND (:fim IS NULL OR data <= :fim)
        ";

        $stmt=$this->db->prepare($sqlTotais);

        $stmt->execute([
            ':ini'=>$dataInicio,
            ':fim'=>$dataFim
        ]);

        $totais=$stmt->fetch(PDO::FETCH_ASSOC);

        $resumo->setCellValue('B8',(float)$totais['dizimos']);
        $resumo->setCellValue('B9',(float)$totais['entradas']);

        // NOVO B10
        $resumo->setCellValue('B10',
            (float)$totais['contribuicoes']
        );

        $resumo->setCellValue('C6',$dataInicio);
        $resumo->setCellValue('C8',$dataFim);

        /* ---- Totais mensais ---- */

        $sqlMensal="

            SELECT

                DATE_FORMAT(data,'%Y-%m') mes,

                SUM(entrada) entradas,
                SUM(dizimo) dizimos,
                SUM(contribuicao) contribuicoes

            FROM(

                SELECT data,valor entrada,0 dizimo,0 contribuicao
                FROM entradas
                WHERE deleted_at IS NULL

                UNION ALL

                SELECT data,0,quantia,0
                FROM dizimos
                WHERE deleted_at IS NULL

                UNION ALL

                SELECT data,0,0,quantia
                FROM contribuicoes
                WHERE deleted_at IS NULL

            ) t

            WHERE (:ini IS NULL OR data >= :ini)
              AND (:fim IS NULL OR data <= :fim)

            GROUP BY mes
            ORDER BY mes ASC
        ";

        $stmt=$this->db->prepare($sqlMensal);

        $stmt->execute([
            ':ini'=>$dataInicio,
            ':fim'=>$dataFim
        ]);

        $mensal=$stmt->fetchAll(PDO::FETCH_ASSOC);

        $row=8;

        $cumulativoContribuicoes=0;

        foreach($mensal as $m){

            $resumo->setCellValue("E{$row}",$m['mes']);

            $resumo->setCellValue("F{$row}",
                (float)$m['entradas']
            );

            $resumo->setCellValue("G{$row}",
                (float)$m['dizimos']
            );

            // cumulativo contribuições
            $cumulativoContribuicoes +=
                (float)$m['contribuicoes'];

            $resumo->setCellValue(
                "H{$row}",
                $cumulativoContribuicoes
            );

            $row++;
        }

        /* =======================
           SALVAR
        ======================== */

        $arquivo='Resumo_Mensal_Entradas_'.time().'.xlsx';

        $output=BASE_PATH.'/storage/generatedReports/'.$arquivo;

        IOFactory::createWriter(
            $spreadsheet,'Xlsx'
        )->save($output);

        return $output;
    }
}