<?php
namespace App\Reports;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PDO;

class ListaMembrosReport
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Gera relatório de lista de membros
     */
    public function gerar(array $filtros): string
    {
        $dataFim = $filtros['data_fim'] ?? date('Y-m-d');

        /* =======================
           TEMPLATE
        ======================== */
        $templatePath = BASE_PATH . '/uploads/reports/Lista_de_Todos_os_Membros.xlsx';
        $spreadsheet  = IOFactory::load($templatePath);

        /* =======================
           ABA ÚNICA (ActiveSheet)
        ======================== */
        $sheet = $spreadsheet->getActiveSheet();

        // Data de geração (opcional, ajuste a célula se quiser)
		$sheet->setCellValue('G1', $dataFim);
        $sheet->setCellValue('G2', date('d/m/Y'));

        /* =======================
           CONSULTA MEMBROS + MORADIA
        ======================== */
        $sql = "
            SELECT 
                m.nome,
                m.sexo,
                TIMESTAMPDIFF(YEAR, m.data_nascimento, CURDATE()) AS idade,
                m.data_nascimento,
                m.data_conversao,
                m.departamento,
                mo.numero_celular,
                mo.numero_alternativo,
                mo.celular_cuidador,
                mo.bairro,
                mo.referencia,
                m.permanencia
            FROM membros m
            LEFT JOIN moradia mo 
                ON mo.membro_id = m.id
               AND mo.deleted_at IS NULL
            WHERE m.deleted_at IS NULL
              AND m.data_conversao <= :dataFim
            ORDER BY m.nome ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':dataFim' => $dataFim
        ]);

        $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

        /* =======================
           PREENCHER PLANILHA
           Linha inicial: 7
           Mapeamento:
           A = Nome
           B = Sexo
           C = Idade
           D = Nascimento
           E = Conversão
           F = Departamento
           G = Celular
           H = Alternativo
           I = Cuidador
           J = Bairro
           K = Referência
           L = Permanência
        ======================== */
        $row = 7;

        foreach ($registros as $r) {

            $sheet->setCellValue("A{$row}", $r['nome']);
            $sheet->setCellValue("B{$row}", $r['sexo']);
            $sheet->setCellValue("C{$row}", $r['idade']);
            $sheet->setCellValue("D{$row}", $this->dateView($r['data_nascimento']));
            $sheet->setCellValue("E{$row}", $this->dateView($r['data_conversao']));
            $sheet->setCellValue("F{$row}", $r['departamento']);
            $sheet->setCellValue("G{$row}", $r['numero_celular']);
            $sheet->setCellValue("H{$row}", $r['numero_alternativo']);
            $sheet->setCellValue("I{$row}", $r['celular_cuidador']);
            $sheet->setCellValue("J{$row}", $r['bairro']);
            $sheet->setCellValue("K{$row}", $r['referencia']);
            $sheet->setCellValue("L{$row}", $r['permanencia']);

            $row++;
        }

        /* =======================
           SALVAR
        ======================== */
        $arquivo = 'Lista_de_Membros_' . time() . '.xlsx';
        $output  = BASE_PATH . '/storage/generatedReports/' . $arquivo;

        IOFactory::createWriter($spreadsheet, 'Xlsx')->save($output);

        return $output;
    }

    /* =======================
       HELPERS
    ======================== */

    private function dateView(?string $date): ?string
    {
        if (!$date) return null;
        return date('d/m/Y', strtotime($date));
    }
}