<?php
namespace App\Models;

use PDO;

class Usuario
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }
	public function search(string $term)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM usuarios 
            WHERE
			(nome LIKE :q OR usuario LIKE :q)
        ");
        $stmt->execute(['q' => "%$term%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function all(): array
    {
        return $this->db
            ->query("SELECT * FROM usuarios WHERE deleted_at IS NULL ORDER BY id DESC")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function stats(): array
    {
        return [
            'total'    => $this->db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn(),
            'ativos'   => $this->db->query("SELECT COUNT(*) FROM usuarios WHERE status='Ativo'")->fetchColumn(),
            'inativos' => $this->db->query("SELECT COUNT(*) FROM usuarios WHERE status='Inativo'")->fetchColumn(),
        ];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, nome, usuario, privilegio, status, criado_em
            FROM usuarios
            WHERE id = :id
        ");
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function add(array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO usuarios (nome, usuario, senha, privilegio, status)
            VALUES (:nome, :usuario, :senha, :privilegio, 'Ativo')
        ");

        return $stmt->execute([
            'nome'       => $data['nome'],
            'usuario'    => $data['usuario'],
            'senha'      => password_hash($data['senha'], PASSWORD_DEFAULT),
            'privilegio' => $data['privilegio'] ?? 'user'
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $sql = "
            UPDATE usuarios SET
                nome = :nome,
                usuario = :usuario,
                privilegio = :privilegio
        ";

        $params = [
            'nome'       => $data['nome'],
            'usuario'    => $data['usuario'],
            'privilegio' => $data['privilegio'],
            'id'         => $id
        ];

        if (!empty($data['senha'])) {
            $sql .= ", senha = :senha";
            $params['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = :id AND deleted_at IS NULL";

        return $this->db->prepare($sql)->execute($params);
    }

    public function toggleStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("
            UPDATE usuarios
            SET status = :status,
                deleted_at = :deleted
            WHERE id = :id
        ");

        return $stmt->execute([
            'status'  => $status,
            'deleted' => $status === 'Inativo' ? date('Y-m-d H:i:s') : null,
            'id'      => $id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE usuarios
            SET status = 'Inativo',
                deleted_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute(['id' => $id]);
    }
}
