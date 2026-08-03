<?php
namespace App\Models;

use PDO;

class Notificacoes
{
    public static function getResumo(PDO $db): array
    {
        return [
            'aniversarios'   => self::aniversariosProximos($db),
            'usuarios'       => self::novosUsuarios($db),
            'dizimos'        => self::novosDizimos($db),
            'entradas'       => self::novasEntradas($db),
            'despesas'       => self::novasDespesas($db),
            'contribuicoes'  => self::novasContribuicoes($db),
            'relatorios'     => self::novosRelatorios($db),
        ];
    }

    public static function total(PDO $db): int
    {
        $r = self::getResumo($db);
        return array_sum($r);
    }

    private static function aniversariosProximos(PDO $db): int
    {
        $sql = "
            SELECT COUNT(*) 
            FROM membros
            WHERE DATE_FORMAT(data_nascimento, '%m-%d')
            BETWEEN DATE_FORMAT(CURDATE(), '%m-%d')
            AND DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 7 DAY), '%m-%d')
        ";

        return (int)$db->query($sql)->fetchColumn();
    }

    private static function novosUsuarios(PDO $db): int
    {
        return (int)$db->query("
            SELECT COUNT(*) FROM usuarios
            WHERE criado_em >= DATE_SUB(NOW(), INTERVAL 3 DAY)
        ")->fetchColumn();
    }

    private static function novosDizimos(PDO $db): int
    {
        return (int)$db->query("
            SELECT COUNT(*) FROM dizimos
            WHERE data_lancamento >= DATE_SUB(NOW(), INTERVAL 3 DAY)
        ")->fetchColumn();
    }

    private static function novasEntradas(PDO $db): int
    {
        return (int)$db->query("
            SELECT COUNT(*) FROM entradas
            WHERE datalancamento >= DATE_SUB(NOW(), INTERVAL 3 DAY)
        ")->fetchColumn();
    }

    private static function novasDespesas(PDO $db): int
    {
        return (int)$db->query("
            SELECT COUNT(*) FROM despesas
            WHERE data_lancamento >= DATE_SUB(NOW(), INTERVAL 3 DAY)
        ")->fetchColumn();
    }

    private static function novasContribuicoes(PDO $db): int
    {
        return (int)$db->query("
            SELECT COUNT(*) FROM contribuicoes
            WHERE data_lancamento >= DATE_SUB(NOW(), INTERVAL 3 DAY)
        ")->fetchColumn();
    }

    private static function novosRelatorios(PDO $db): int
    {
        return (int)$db->query("
            SELECT COUNT(*) FROM report_templates
            WHERE criado_em >= DATE_SUB(NOW(), INTERVAL 3 DAY)
        ")->fetchColumn();
    }
}