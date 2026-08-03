<?php
namespace App\Reports;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PDO;

class MembrosReport
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Gera o relatório de membros
     */
    public function gerar(array $filtros): string
    {
        $dataInicio = $filtros['data_inicio'] ?? null;
        $dataFim    = $filtros['data_fim'] ?? date('Y-m-d');

        // 1️⃣ Carregar template
        $templatePath = BASE_PATH . '/uploads/reports/Contagem_de_Membros_e_convertidos.xlsx';
        $spreadsheet  = IOFactory::load($templatePath);

        $sheet = $spreadsheet->getSheetByName('Resumo');
        if (!$sheet) {
            throw new \Exception('A planilha "Resumo" não foi encontrada no template.');
        }

        // 2️⃣ Mapear contagens simples
        $map = [
            'Masculino' => ['B9',  "sexo = 'Masculino'"],
            'Feminino'  => ['C9',  "sexo = 'Feminino'"],
            'Branco'    => ['D9',  "(sexo IS NULL OR sexo = '')"],

            'Pais'      => ['B13', "departamento = 'Pais'"],
            'Maes'      => ['C13', "departamento = 'Maes'"],
            'Jovens'    => ['D13', "departamento = 'Jovens'"],
            'Dominical' => ['E13', "departamento = 'Dominical'"],

            'Ativo'     => ['C16', "permanencia = 'Ativo'"],
            'Abandonou' => ['C17', "permanencia = 'Abandonou'"],
            'Mudou-se'  => ['C18', "permanencia = 'Mudou-se'"],
            'Obitou'    => ['E16', "permanencia = 'Obitou'"],
            'PermBranco'=> ['E17', "(permanencia IS NULL OR permanencia = '')"],
        ];

        foreach ($map as [$cell, $where]) {
            $sheet->setCellValue(
                $cell,
                $this->count("{$where} AND data_conversao <= :data_fim", [
                    ':data_fim' => $dataFim
                ])
            );
        }

        // 3️⃣ Cruzamentos (sexo + departamento)
        $sheet->setCellValue('B22', $this->countDeptSexo('Jovens', 'Masculino', $dataFim));
        $sheet->setCellValue('C22', $this->countDeptSexo('Jovens', 'Feminino', $dataFim));
        $sheet->setCellValue('D22', $this->countDeptSexo('Dominical', 'Masculino', $dataFim));
        $sheet->setCellValue('E22', $this->countDeptSexo('Dominical', 'Feminino', $dataFim));

        // 4️⃣ Totais finais (segunda parte do script)
        $sheet->setCellValue('B26', $this->countDepartamento('Pais', $dataInicio, $dataFim));
        $sheet->setCellValue('B28', $this->countDepartamento('Maes', $dataInicio, $dataFim));
        $sheet->setCellValue('B30', $this->countDeptSexoPeriodo('Jovens', 'Masculino', $dataInicio, $dataFim));
        $sheet->setCellValue('E30', $this->countDeptSexoPeriodo('Jovens', 'Feminino', $dataInicio, $dataFim));
        $sheet->setCellValue('B32', $this->countDeptSexoPeriodo('Dominical', 'Masculino', $dataInicio, $dataFim));
        $sheet->setCellValue('E32', $this->countDeptSexoPeriodo('Dominical', 'Feminino', $dataInicio, $dataFim));

        // 5️⃣ Datas no relatório
        $sheet->setCellValue('E3', $dataInicio);
        $sheet->setCellValue('E4', $dataFim);

        // 6️⃣ Salvar arquivo
        $outputPath = BASE_PATH . '/uploads/generated/membros_' . time() . '.xlsx';
        IOFactory::createWriter($spreadsheet, 'Xlsx')->save($outputPath);

        return $outputPath;
    }

    /* =======================
       MÉTODOS AUXILIARES
    ======================= */

    private function count(string $where, array $params): int
    {
        $sql = "SELECT COUNT(*) FROM membros WHERE {$where}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    private function countDeptSexo(string $departamento, string $sexo, string $dataFim): int
    {
        return $this->count(
            "departamento = :dep AND sexo = :sexo AND data_conversao <= :fim",
            [
                ':dep'  => $departamento,
                ':sexo'=> $sexo,
                ':fim' => $dataFim
            ]
        );
    }

    private function countDepartamento(string $departamento, $inicio, $fim): int
    {
        return $this->count(
            "departamento = :dep AND data_conversao BETWEEN :ini AND :fim",
            [
                ':dep' => $departamento,
                ':ini' => $inicio,
                ':fim' => $fim
            ]
        );
    }

    private function countDeptSexoPeriodo(string $dep, string $sexo, $ini, $fim): int
    {
        return $this->count(
            "departamento = :dep AND sexo = :sexo AND data_conversao BETWEEN :ini AND :fim",
            [
                ':dep'  => $dep,
                ':sexo'=> $sexo,
                ':ini' => $ini,
                ':fim' => $fim
            ]
        );
    }
}