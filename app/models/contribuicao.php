<?php

namespace App\Models;

use PDO;
use DateTime;

class Contribuicao
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }
/* =====================================================
     * ADICIONAR DÍZIMO
     * ===================================================== */
    public static function addContribuicao(
        PDO $db,
        int $membroId,
        float $quantia,
        string $data
    ): bool {
    
        $sql = "
            INSERT INTO contribuicoes (data, membro_id, quantia)
            VALUES (:data, :membro_id, :quantia)
        ";
    
        try {
    
            $stmt = $db->prepare($sql);
    
            return $stmt->execute([
                ':data'      => self::dateDb($data),
                ':membro_id' => $membroId,
                ':quantia'   => $quantia
            ]);
    
        } catch (\PDOException $e) {
    
            // erro de duplicação do banco
            if ($e->errorInfo[1] == 1062) {
                return false;
            }
    
            throw $e;
        }
    }

    public static function listarContribuintesMes(PDO $db, ?string $nome, int $limit, int $offset): array
    {
        $sql = "
            SELECT 
                m.id,
                m.nome,
                COUNT(c.id) AS qtd_contribuicoes,
                SUM(c.quantia) AS total_mes
            FROM membros m
            INNER JOIN contribuicoes c ON c.membro_id = m.id
            WHERE c.deleted_at IS NULL
              AND MONTH(c.data) = MONTH(CURRENT_DATE())
              AND YEAR(c.data) = YEAR(CURRENT_DATE())
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

    public static function countContribuintesMes(PDO $db): int
    {
        return (int) $db->query("
            SELECT COUNT(DISTINCT membro_id)
            FROM contribuicoes
            WHERE deleted_at IS NULL
              AND MONTH(data) = MONTH(CURRENT_DATE())
              AND YEAR(data) = YEAR(CURRENT_DATE())
        ")->fetchColumn();
    }

    public static function buscarContribuintesGlobal(PDO $db, string $nome, int $limit, int $offset): array
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

    public static function countContribuintesGlobal(PDO $db, string $nome): int
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
            FROM contribuicoes
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
            FROM contribuicoes
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
            UPDATE contribuicoes
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
            UPDATE contribuicoes
            SET deleted_at = NOW()
            WHERE id = :id
            LIMIT 1
        ");

        return $stmt->execute([':id'=>$id]);
    }

    /* =====================================================
     * HELPERS DE DATA
     * ===================================================== */

    /** View → Banco (d-m-Y | Y-m-d → Y-m-d) */
    private static function dateDb(?string $d): ?string
    {
        if (empty($d)) return null;

        $dt = DateTime::createFromFormat('d-m-Y', $d)
           ?: DateTime::createFromFormat('Y-m-d', $d);

        return $dt ? $dt->format('Y-m-d') : null;
    }
}
