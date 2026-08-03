<?php
namespace App\Reports;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PDO;

class FinanceiroReport
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function gerar(array $filtros): string
    {
        $dataInicio = $filtros['data_inicio'];
        $dataFim    = $filtros['data_fim'];

        /* =======================
           TEMPLATE
        ======================== */
        $templatePath = BASE_PATH . '/uploads/reports/Resumo_Mensal_Financeiro.xlsx';
        $spreadsheet  = IOFactory::load($templatePath);

        /* =======================
           ABA EXTRATO
        ======================== */
        $extrato = $spreadsheet->getSheetByName('Extrato');

        $extrato->setCellValue('C4', $dataInicio);
        $extrato->setCellValue('E4', $dataFim);
        $extrato->setCellValue('G6', date('d/m/y'));

        /* =======================
           UNION ALL COMPLETO
           ENTRADAS + DIZIMOS + CONTRIBUICOES + DESPESAS
        ======================== */

        $sql = "

            SELECT
                id,
                data,
                'entrada' tipo,
                valor,
                descricao
            FROM entradas
            WHERE data BETWEEN :ini AND :fim

            UNION ALL

            SELECT
                id,
                data,
                'entrada' tipo,
                quantia AS valor,
                'Dízimo' AS descricao
            FROM dizimos
            WHERE data BETWEEN :ini AND :fim
            AND deleted_at IS NULL

            UNION ALL

            SELECT
                id,
                data,
                'entrada' tipo,
                quantia AS valor,
                'Contribuição' AS descricao
            FROM contribuicoes
            WHERE data BETWEEN :ini AND :fim
            AND deleted_at IS NULL

            UNION ALL

            SELECT
                id,
                data,
                'saida' tipo,
                valor,
                descricao
            FROM despesas
            WHERE data BETWEEN :ini AND :fim

            ORDER BY data ASC

        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':ini'=>$dataInicio,
            ':fim'=>$dataFim
        ]);

        $lancamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        /* =======================
           PREENCHER EXTRATO
        ======================== */

        $row = 6;

        foreach($lancamentos as $l){

            $extrato->setCellValue("A{$row}",$l['id']);
            $extrato->setCellValue("B{$row}",$l['data']);
            $extrato->setCellValue("C{$row}",$l['tipo']);
            $extrato->setCellValue("D{$row}",$l['valor']);
            $extrato->setCellValue("E{$row}",$l['descricao']);

            $row++;
        }

        /* =======================
           ABA RESUMO
        ======================== */

        $resumo = $spreadsheet->getSheetByName('Resumo');

        // saldo anterior
        $saldoAnterior = $this->saldoAnterior($dataInicio);

        $resumo->setCellValue('C6',$saldoAnterior);

        // totais periodo
        $totalEntradas = $this->totalEntradasPeriodo($dataInicio,$dataFim);

        $totalSaidas = $this->totalSaidasPeriodo($dataInicio,$dataFim);

        $resumo->setCellValue('B6',$totalEntradas);
        $resumo->setCellValue('B7',$totalSaidas);

        /* =======================
           TOTAL POR MÊS
        ======================== */

        $totaisMes=[];

        foreach($lancamentos as $l){

            $mes=date('Y-m',strtotime($l['data']));

            if(!isset($totaisMes[$mes])){

                $totaisMes[$mes]=[
                    'entrada'=>0,
                    'saida'=>0
                ];
            }

            $totaisMes[$mes][$l['tipo']]+=$l['valor'];
        }

        $row=11;

        foreach($totaisMes as $mes=>$t){

            $resumo->setCellValue("A{$row}",$mes);

            $resumo->setCellValue("B{$row}",$t['saida']);

            $resumo->setCellValue("C{$row}",$t['entrada']);

            $row++;
        }

        /* =======================
           SALVAR
        ======================== */

        $arquivo='Resumo_Mensal_Financeiro_'.time().'.xlsx';

        $output=BASE_PATH.'/storage/generatedReports/'.$arquivo;

        IOFactory::createWriter($spreadsheet,'Xlsx')->save($output);

        return $output;
    }


    /* =======================
       SALDO ANTERIOR
    ======================== */

    private function saldoAnterior(string $dataInicio):float
    {

        $sqlEntradas="

            SELECT COALESCE(SUM(valor),0) total FROM entradas
            WHERE data < ?

            UNION ALL

            SELECT COALESCE(SUM(quantia),0) FROM dizimos
            WHERE data < ?
            AND deleted_at IS NULL

            UNION ALL

            SELECT COALESCE(SUM(quantia),0) FROM contribuicoes
            WHERE data < ?
            AND deleted_at IS NULL

        ";

        $stmt=$this->db->prepare($sqlEntradas);

        $stmt->execute([
            $dataInicio,
            $dataInicio,
            $dataInicio
        ]);

        $entradasTotal=0;

        foreach($stmt->fetchAll(PDO::FETCH_COLUMN) as $v){

            $entradasTotal+=(float)$v;
        }

        $saida=$this->db->prepare("
            SELECT COALESCE(SUM(valor),0)
            FROM despesas
            WHERE data < ?
        ");

        $saida->execute([$dataInicio]);

        return $entradasTotal-(float)$saida->fetchColumn();
    }



    /* =======================
       TOTAL ENTRADAS PERIODO
    ======================== */

    private function totalEntradasPeriodo($ini,$fim):float
    {

        $sql="

            SELECT COALESCE(SUM(valor),0) FROM entradas
            WHERE data BETWEEN ? AND ?

            UNION ALL

            SELECT COALESCE(SUM(quantia),0)
            FROM dizimos
            WHERE data BETWEEN ? AND ?
            AND deleted_at IS NULL

            UNION ALL

            SELECT COALESCE(SUM(quantia),0)
            FROM contribuicoes
            WHERE data BETWEEN ? AND ?
            AND deleted_at IS NULL

        ";

        $stmt=$this->db->prepare($sql);

        $stmt->execute([
            $ini,$fim,
            $ini,$fim,
            $ini,$fim
        ]);

        $total=0;

        foreach($stmt->fetchAll(PDO::FETCH_COLUMN) as $v){

            $total+=(float)$v;
        }

        return $total;
    }



    /* =======================
       TOTAL SAIDAS
    ======================== */

    private function totalSaidasPeriodo($ini,$fim):float
    {

        $stmt=$this->db->prepare("

            SELECT COALESCE(SUM(valor),0)
            FROM despesas
            WHERE data BETWEEN ? AND ?
            AND deleted_at IS NULL

        ");

        $stmt->execute([$ini,$fim]);

        return (float)$stmt->fetchColumn();
    }
}