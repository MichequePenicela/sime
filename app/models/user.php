<?php
namespace App\Models;

use PDO;

class User
{
    // Busca nome pelo ID (já existente)
    public static function getNomeById(PDO $db, int $id)
    {
        $stmt = $db->prepare("SELECT nome FROM usuarios WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Busca usuário pelo login (username) para autenticação
    public static function getByUsername(PDO $db, string $username)
    {
        $stmt = $db->prepare("SELECT id, usuario, senha, nome, privilegio FROM usuarios WHERE usuario = :usuario AND status = 'ativo' ");
        $stmt->bindValue(':usuario', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // retorna array associativo
    }
	public static function getById(PDO $db, int $id): ?array
	{
		$stmt = $db->prepare("SELECT id, nome, privilegio FROM usuarios WHERE id = ?");
		$stmt->execute([$id]);
		return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
	}
}

