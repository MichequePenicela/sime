<?php
namespace App\Models;

use PDO;
use DateTime;

class Financeiro
{
    /* =====================================================
     * DASHBOARD (TOTAIS)
     * ===================================================== */
    public static function totalEntradas(PDO $db, string $inicio, string $fim): float
    {
        return self::total($db, 'entradas', 'valor', $inicio, $fim);
    }

    public static function totalDespesas(PDO $db, string $inicio, string $fim): float
    {
        return self::total($db, 'despesas', 'valor', $inicio, $fim);
    }

    public static function totalDizimos(PDO $db, string $inicio, string $fim): float
    {
        $sql = "
            SELECT COALESCE(SUM(quantia), 0)
            FROM dizimos
            WHERE data BETWEEN :inicio AND :fim
            AND deleted_at IS NULL
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':inicio' => self::date($inicio),
            ':fim'    => self::date($fim)
        ]);
        return (float) $stmt->fetchColumn();
    }
    public static function totalContribuicoes(PDO $db, string $inicio, string $fim): float
    {
        $sql = "
            SELECT COALESCE(SUM(quantia), 0)
            FROM contribuicoes
            WHERE data BETWEEN :inicio AND :fim
            AND deleted_at IS NULL
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':inicio' => self::date($inicio),
            ':fim'    => self::date($fim)
        ]);
        return (float) $stmt->fetchColumn();
    }

    private static function total(
        PDO $db,
        string $tabela,
        string $campo,
        string $inicio,
        string $fim
    ): float {
        $sql = "
            SELECT COALESCE(SUM({$campo}), 0)
            FROM {$tabela}
            WHERE data BETWEEN :inicio AND :fim
              AND deleted_at IS NULL
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':inicio' => self::date($inicio),
            ':fim'    => self::date($fim)
        ]);
        return (float) $stmt->fetchColumn();
    }

    /* =====================================================
     * BUSCAS COM PAGINAÇÃO
     * ===================================================== */
    public static function buscarEntradas(PDO $db, array $filtros): array
    {
        return self::buscar($db, 'entradas', $filtros);
    }

    public static function buscarDespesas(PDO $db, array $filtros): array
    {
        return self::buscar($db, 'despesas', $filtros);
    }

    private static function buscar(PDO $db, string $tabela, array $filtros): array
    {
        $page    = max(1, (int)($filtros['page'] ?? 1));
        $perPage = max(1, (int)($filtros['per_page'] ?? 10));
        $offset  = ($page - 1) * $perPage;

        $where  = " WHERE deleted_at IS NULL ";
        $params = [];

        if (!empty($filtros['data_inicio'])) {
            $where .= " AND data >= :inicio";
            $params[':inicio'] = self::date($filtros['data_inicio']);
        }

        if (!empty($filtros['data_fim'])) {
            $where .= " AND data <= :fim";
            $params[':fim'] = self::date($filtros['data_fim']);
        }

        /* ---------- TOTAL ---------- */
        $countStmt = $db->prepare("
            SELECT COUNT(*)
            FROM {$tabela}
            {$where}
        ");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        /* ---------- DADOS ---------- */
        $sql = "
            SELECT id, data, descricao, valor
            FROM {$tabela}
            {$where}
            ORDER BY data DESC
            LIMIT :limit OFFSET :offset
        ";
        $stmt = $db->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($dados as &$d) {
            $d['tipo']  = $tabela;
            $d['data']  = self::dateView($d['data']);
            $d['valor'] = self::moneyView($d['valor']);
        }

        return [
            'data' => $dados,
            'pagination' => [
                'page'       => $page,
                'perPage'    => $perPage,
                'total'      => $total,
                'totalPages' => (int) ceil($total / $perPage)
            ]
        ];
    }
	/* =====================================================
 * CONTAGEM PARA PAGINAÇÃO
 * ===================================================== */
public static function countEntradas(PDO $db, array $filtros): int
{
    return self::count($db, 'entradas', $filtros);
}

public static function countDespesas(PDO $db, array $filtros): int
{
    return self::count($db, 'despesas', $filtros);
}

private static function count(PDO $db, string $tabela, array $filtros): int
{
    $sql = "
        SELECT COUNT(*)
        FROM {$tabela}
        WHERE deleted_at IS NULL
    ";

    $params = [];

    if (!empty($filtros['data_inicio'])) {
        $sql .= " AND data >= :inicio";
        $params[':inicio'] = self::date($filtros['data_inicio']);
    }

    if (!empty($filtros['data_fim'])) {
        $sql .= " AND data <= :fim";
        $params[':fim'] = self::date($filtros['data_fim']);
    }

    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    return (int) $stmt->fetchColumn();
}


    /* =====================================================
     * BUSCA POR ID
     * ===================================================== */
    public static function getEntradaById(PDO $db, int $id): array|false
    {
        return self::getById($db, 'entradas', $id);
    }

    public static function getDespesaById(PDO $db, int $id): array|false
    {
        return self::getById($db, 'despesas', $id);
    }

    private static function getById(PDO $db, string $tabela, int $id)
    {
        $stmt = $db->prepare("
            SELECT *
            FROM {$tabela}
            WHERE id = :id
              AND deleted_at IS NULL
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* =====================================================
     * EDITAR / EXCLUIR
     * ===================================================== */
    public static function editar(PDO $db, int $id, string $tipo, array $dados): bool
    {
        if (!in_array($tipo, ['entradas', 'despesas'])) {
            return false;
        }

        $stmt = $db->prepare("
            UPDATE {$tipo}
            SET data = :data,
                descricao = :descricao,
                valor = :valor,
                observacao = :observacao
            WHERE id = :id
              AND deleted_at IS NULL
        ");

        return $stmt->execute([
            ':data'       => self::date($dados['data']),
            ':descricao'  => trim($dados['descricao']),
            ':valor'      => self::money($dados['valor']),
            ':observacao' => $dados['observacao'] ?? null,
            ':id'         => $id
        ]);
    }

    public static function excluir(PDO $db, int $id, string $tipo): bool
    {
        if (!in_array($tipo, ['entradas', 'despesas'])) {
            return false;
        }

        $stmt = $db->prepare("
            UPDATE {$tipo}
            SET deleted_at = NOW()
            WHERE id = :id
        ");
        return $stmt->execute([':id' => $id]);
    }

    private static function exists(PDO $db, string $tabela, array $d): bool
{
    $stmt = $db->prepare("
        SELECT id
        FROM {$tabela}
        WHERE data = :data
        AND valor = :valor
        AND descricao = :descricao
        AND deleted_at IS NULL
        LIMIT 1
    ");

    $stmt->execute([
        ':data'      => self::date($d['data']),
        ':valor'     => self::money($d['valor']),
        ':descricao' => trim($d['descricao'])
    ]);

    return (bool) $stmt->fetchColumn();
}



    /* =====================================================
     * INSERTS
     * ===================================================== */
    public static function addEntrada(PDO $db, array $d): bool
    {
        return self::insert($db, 'entradas', $d);
    }

    public static function addDespesa(PDO $db, array $d): bool
    {
        return self::insert($db, 'despesas', $d);
    }

    private static function insert(PDO $db, string $tabela, array $d): bool
{
    if (self::exists($db, $tabela, $d)) {
        return false;
    }

    $stmt = $db->prepare("
        INSERT INTO {$tabela} (data, valor, descricao)
        VALUES (:data, :valor, :descricao)
    ");

    return $stmt->execute([
        ':data'      => self::date($d['data']),
        ':valor'     => self::money($d['valor']),
        ':descricao' => trim($d['descricao'])
    ]);
}


    /* =====================================================
     * HELPERS
     * ===================================================== */
    private static function date(string $d): string
    {
        $dt = DateTime::createFromFormat('d-m-Y', $d)
           ?: DateTime::createFromFormat('Y-m-d', $d);
        return $dt ? $dt->format('Y-m-d') : date('Y-m-d');
    }

    private static function dateView(string $d): string
    {
        return date('d-m-Y', strtotime($d));
    }

    private static function money(string|float $v): float
    {
        return (float) str_replace(',', '.', preg_replace('/[^\d,]/', '', $v));
    }

    private static function moneyView(float $v): string
    {
        return number_format($v, 2, ',', '.');
    }
}
