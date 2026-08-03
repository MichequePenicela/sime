<?php
namespace App\Models;

use PDO;
use Exception;

class Home
{
    private PDO $db;
	public function __construct(PDO $db)
	{
		$this->db = $db;
	}
public static function countSearch(PDO $db): int
{
    $stmt = $db->prepare("
        SELECT COUNT(*)
        FROM membros
    ");
    $stmt->execute();

    return (int) $stmt->fetchColumn();
}
public static function countusers(PDO $db): int
{
    $stmt = $db->prepare("
        SELECT COUNT(*)
        FROM usuarios
    ");
    $stmt->execute();

    return (int) $stmt->fetchColumn();
}
public static function countreports(PDO $db): int
{
    $stmt = $db->prepare("
        SELECT COUNT(*)
        FROM report_templates
    ");
    $stmt->execute();

    return (int) $stmt->fetchColumn();
}

public static function totalDizimos(PDO $db): float
{
    $sql = "
        SELECT COALESCE(SUM(quantia), 0)
        FROM dizimos
        WHERE deleted_at IS NULL
    ";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return (float) $stmt->fetchColumn();
}
public static function totalContribuicoes(PDO $db): float
    {
        $sql = "
            SELECT COALESCE(SUM(quantia), 0)
            FROM contribuicoes
            WHERE deleted_at IS NULL
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return (float) $stmt->fetchColumn();
    }
    public static function totalOfertas(PDO $db): float
    {
        $sql = "
            SELECT COALESCE(SUM(valor), 0)
            FROM entradas
            WHERE deleted_at IS NULL
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return (float) $stmt->fetchColumn();
    }
    public static function CountDizimistas(PDO $db): int
    {
        $sql = "
        SELECT COUNT(DISTINCT m.id) AS total_dizimistas
        FROM membros m
            INNER JOIN dizimos d ON d.membro_id = m.id
            WHERE d.deleted_at IS NULL
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        return (int) $stmt->fetchColumn();

    }

}