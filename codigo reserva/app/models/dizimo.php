<?php

namespace App\Models;

use PDO;

class Dizimo
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public static function listarDizimistasMes(PDO $db, ?string $nome, int $limit, int $offset): array
    {
        $sql = "
            SELECT 
                m.id,
                m.nome,
                COUNT(d.id) AS qtd_dizimos,
                SUM(d.quantia) AS total_mes
            FROM membros m
            INNER JOIN dizimos d ON d.membro_id = m.id
            WHERE d.deleted_at IS NULL
              AND MONTH(d.data) = MONTH(CURRENT_DATE())
              AND YEAR(d.data) = YEAR(CURRENT_DATE())
            GROUP BY m.id
            ORDER BY m.nome ASC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function countDizimistasMes(PDO $db): int
    {
        return (int) $db->query("
            SELECT COUNT(DISTINCT membro_id)
            FROM dizimos
            WHERE deleted_at IS NULL
              AND MONTH(data) = MONTH(CURRENT_DATE())
              AND YEAR(data) = YEAR(CURRENT_DATE())
        ")->fetchColumn();
    }

    public static function buscarDizimistasGlobal(PDO $db, string $nome, int $limit, int $offset): array
    {
        $stmt = $db->prepare("
            SELECT DISTINCT m.id, m.nome
            FROM membros m
            WHERE m.nome LIKE :nome
            AND deleted_at IS NULL
            ORDER BY m.nome ASC
            LIMIT :limit OFFSET :offset
        ");

        $stmt->bindValue(':nome', "%{$nome}%");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function countDizimistasGlobal(PDO $db, string $nome): int
    {
        $stmt = $db->prepare("
            SELECT COUNT(*)
            FROM membros
            WHERE nome LIKE :nome
            AND deleted_at IS NULL
        ");

        $stmt->bindValue(':nome', "%{$nome}%");
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public static function getMembro(PDO $db, int $id): ?array
    {
        $stmt = $db->prepare("SELECT id, nome FROM membros WHERE id = :id AND deleted_at IS NULL LIMIT 1");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function listarPorMembro(PDO $db,int $membroId,?string $inicio,?string $fim,int $limit,int $offset): array {

        $sql = "
            SELECT id, data, quantia, observacao
            FROM dizimos
            WHERE membro_id = :membro_id
              AND deleted_at IS NULL
        ";

        if ($inicio && $fim) {
            $sql .= " AND data BETWEEN :inicio AND :fim ";
        }

        $sql .= " ORDER BY data DESC LIMIT :limit OFFSET :offset ";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':membro_id', $membroId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        if ($inicio && $fim) {
            $stmt->bindValue(':inicio', $inicio);
            $stmt->bindValue(':fim', $fim);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function countPorMembro(PDO $db,int $membroId,?string $inicio,?string $fim): int {

        $sql = "
            SELECT COUNT(*)
            FROM dizimos
            WHERE membro_id = :membro_id
              AND deleted_at IS NULL
        ";

        if ($inicio && $fim) {
            $sql .= " AND data BETWEEN :inicio AND :fim ";
        }

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':membro_id', $membroId, PDO::PARAM_INT);

        if ($inicio && $fim) {
            $stmt->bindValue(':inicio', $inicio);
            $stmt->bindValue(':fim', $fim);
        }

        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function atualizar(int $id, string $data, float $quantia, ?string $obs): bool
    {
        $stmt = $this->db->prepare("
            UPDATE dizimos
            SET data = :data,
                quantia = :quantia,
                observacao = :obs
            WHERE id = :id
            LIMIT 1
        ");

        return $stmt->execute([
            ':id' => $id,
            ':data' => $data,
            ':quantia' => $quantia,
            ':obs' => $obs
        ]);
    }

    /* =========================
     * SOFT DELETE
     * ========================= */
    public function softDelete(int $id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE dizimos
            SET deleted_at = NOW()
            WHERE id = :id
            LIMIT 1
        ");

        return $stmt->execute([':id'=>$id]);
    }
}