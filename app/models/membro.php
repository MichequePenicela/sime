<?php
namespace App\Models;

use PDO;
use Exception;
use DateTime;

class Membro
{
	private PDO $db;
	public function __construct(PDO $db)
	{
		$this->db = $db;
	}
    /* =====================================================
     * PESQUISA POR NOME
     * ===================================================== */
    /* =====================================================
 * PESQUISA PAGINADA
 * ===================================================== */
public static function searchPaginated(
    PDO $db,
    string $nome,
    int $limit,
    int $offset
): array {
    $stmt = $db->prepare("
        SELECT
            id,
            nome,
            sexo,
            departamento,
            permanencia,
            TIMESTAMPDIFF(YEAR, data_nascimento, CURDATE()) AS idade
        FROM membros
        WHERE nome LIKE :nome
          AND deleted_at IS NULL
        ORDER BY nome ASC
        LIMIT :limit OFFSET :offset
    ");

    $stmt->bindValue(':nome', '%' . trim($nome) . '%');
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public static function countSearch(PDO $db, string $nome): int
{
    $stmt = $db->prepare("
        SELECT COUNT(*)
        FROM membros
        WHERE nome LIKE :nome
          AND deleted_at IS NULL
    ");
    $stmt->bindValue(':nome', '%' . trim($nome) . '%');
    $stmt->execute();

    return (int) $stmt->fetchColumn();
}

    /* =====================================================
     * BUSCAR MEMBRO + MORADIA (LEFT JOIN)
     * ===================================================== */
    public static function findByIdWithMoradia(PDO $db, int $id): ?array
    {
        $stmt = $db->prepare("
            SELECT 
            m.*,
            TIMESTAMPDIFF(YEAR, m.data_nascimento, CURDATE()) AS idade,
            mo.bairro,
            mo.referencia,
            mo.numero_celular     AS moradia_numero_celular,
            mo.numero_alternativo AS moradia_numero_alternativo,
            mo.celular_cuidador   AS moradia_celular_cuidador
        FROM membros m
        LEFT JOIN moradia mo 
            ON mo.membro_id = m.id
           AND mo.deleted_at IS NULL
        WHERE m.id = :id
          AND m.deleted_at IS NULL
        LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $membro = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$membro) {
            return null;
        }

        /* 🔥 PADRONIZA DATAS PARA VIEW */
        $membro['data_nascimento'] = self::dateView($membro['data_nascimento']);
        $membro['data_conversao']  = self::dateView($membro['data_conversao']);

        return $membro;
    }
		/* ===============================
 * SOFT DELETE MEMBRO
 * =============================== */
 public static function softDelete(PDO $db, int $membroId): bool
{
    try {
        $db->beginTransaction();

        // Soft delete do membro
        $stmtMembro = $db->prepare("
            UPDATE membros
            SET deleted_at = NOW()
            WHERE id = :id
              AND deleted_at IS NULL
        ");
        $stmtMembro->execute([
            'id' => $membroId
        ]);

        // Soft delete da moradia vinculada
        $stmtMoradia = $db->prepare("
            UPDATE moradia
            SET deleted_at = NOW()
            WHERE membro_id = :membro_id
              AND deleted_at IS NULL
        ");
        $stmtMoradia->execute([
            'membro_id' => $membroId
        ]);

        $db->commit();
        return true;

    } catch (\Throwable $e) {

        $db->rollBack();
        error_log('Erro soft delete membro: ' . $e->getMessage());
        return false;
    }
}

	/* ===============================
 * DASHBOARD ESTATÍSTICAS
 * =============================== */
public function dashboardStats($inicio = null, $fim = null): array
{
    $where  = [];
    $params = [];
	$whereSql = '';

    if ($inicio) {
        $where[] = "data_conversao >= :inicio";
        $params[':inicio'] = $inicio . ' 00:00:00';
    }

    if ($fim) {
        $where[] = "data_conversao <= :fim";
        $params[':fim'] = $fim . ' 23:59:59';
    }

    $whereSql = $where ? implode(' AND ', $where) : '1=1';

    $queries = [
        'total'       => "SELECT COUNT(*) FROM membros WHERE deleted_at IS NULL AND $whereSql",
        'ativos'      => "SELECT COUNT(*) FROM membros WHERE deleted_at IS NULL AND permanencia='ativo' AND $whereSql",
        'abandonou'   => "SELECT COUNT(*) FROM membros WHERE deleted_at IS NULL AND permanencia='abandonou' AND $whereSql",
        'mudou'       => "SELECT COUNT(*) FROM membros WHERE deleted_at IS NULL AND permanencia='mudou-se' AND $whereSql",
        'convertidos' => "SELECT COUNT(*) FROM membros WHERE deleted_at IS NULL AND data_conversao BETWEEN :inicio AND :fim",
        'dominical'   => "SELECT COUNT(*) FROM membros WHERE deleted_at IS NULL AND departamento='Dominical' AND $whereSql",
        'jovens'      => "SELECT COUNT(*) FROM membros WHERE deleted_at IS NULL AND departamento='Jovens' AND $whereSql",
        'maes'        => "SELECT COUNT(*) FROM membros WHERE deleted_at IS NULL AND departamento='Maes' AND $whereSql",
        'pais'        => "SELECT COUNT(*) FROM membros WHERE deleted_at IS NULL AND departamento='Pais' AND $whereSql",
		'apagados'        => "SELECT COUNT(*) FROM membros WHERE deleted_at IS NOT NULL AND $whereSql",
    ];

    $stats = [];

    foreach ($queries as $key => $sql) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $stats[$key] = (int) $stmt->fetchColumn();
    }

    return $stats;
}




    /* =====================================================
     * UPDATE MEMBRO
     * ===================================================== */
    public static function update(PDO $db, int $id, array $dados): bool
    {
        $stmt = $db->prepare("
            UPDATE membros SET
                nome            = :nome,
                data_nascimento = :data_nascimento,
                data_conversao  = :data_conversao,
                departamento    = :departamento,
				permanencia    = :permanencia,
                batizado        = :batizado
            WHERE id = :id
        ");

        return $stmt->execute([
            ':nome'            => trim($dados['nome']),
            ':data_nascimento' => self::dateDb($dados['data_nascimento'] ?? null),
            ':data_conversao'  => self::dateDb($dados['data_conversao'] ?? null),
            ':departamento'    => $dados['departamento'] ?? null,
			 ':permanencia'    => $dados['permanencia'] ?? null,
            ':batizado'        => $dados['batizado'] ?? 'nao',
            ':id'              => $id
        ]);
    }

    /* =====================================================
     * UPDATE MORADIA
     * ===================================================== */
    public static function updateMoradia(PDO $db, int $membroId, array $dados): bool
    {
        $stmt = $db->prepare("
            UPDATE moradia SET
                bairro             = :bairro,
                referencia         = :referencia,
                numero_celular     = :celular,
                numero_alternativo = :alternativo,
                celular_cuidador   = :cuidador
            WHERE membro_id = :membro_id
        ");

        return $stmt->execute([
            ':bairro'      => $dados['bairro'] ?? null,
            ':referencia'  => $dados['referencia'] ?? null,
            ':celular'     => $dados['moradia_numero_celular'] ?? null,
            ':alternativo' => $dados['moradia_numero_alternativo'] ?? null,
            ':cuidador'    => $dados['moradia_celular_cuidador'] ?? null,
            ':membro_id'   => $membroId
        ]);
    }

    /* =====================================================
     * ÚLTIMO DÍZIMO
     * ===================================================== */
    public static function getUltimoDizimo(PDO $db, int $membroId): ?array
    {
        $stmt = $db->prepare("
            SELECT data, quantia
            FROM dizimos
            WHERE membro_id = :id
             AND deleted_at IS NULL
            ORDER BY data DESC
            LIMIT 1
        ");
        $stmt->execute([':id' => $membroId]);
        $dizimo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dizimo) {
            return null;
        }

        $dizimo['data'] = self::dateView($dizimo['data']);
        return $dizimo;
    }

    public static function getUltimaContribuicao(PDO $db, int $membroId): ?array
    {
        $stmt = $db->prepare("
            SELECT data, quantia
            FROM contribuicoes
            WHERE membro_id = :id
            AND deleted_at IS NULL
            ORDER BY data DESC
            LIMIT 1
        ");
        $stmt->execute([':id' => $membroId]);
        $dizimo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dizimo) {
            return null;
        }

        $dizimo['data'] = self::dateView($dizimo['data']);
        return $dizimo;
    }

    /* =====================================================
     * PARENTES
     * ===================================================== */
    public static function getParentes(PDO $db, int $membroId): array
    {
        $stmt = $db->prepare("
            SELECT
                mp.*,
                m.nome AS nome_parente,
                p.tipo
            FROM membro_parentesco mp
            JOIN membros m      ON mp.parente_id = m.id
            JOIN parentesco p   ON mp.parentesco_id = p.parentesco_id
            WHERE mp.membro_id = :membro_id
            ORDER BY m.nome ASC
        ");
        $stmt->execute([':membro_id' => $membroId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

        /* =====================================================
     * TIPO DE PARENTES
     * ===================================================== */
    public static function getTiposParentesco(PDO $db): array
{
        $stmt = $db->query("
            SELECT parentesco_id, tipo
            FROM parentesco
            ORDER BY tipo
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    /* =====================================================
     * ADICIONAR DÍZIMO
     * ===================================================== */
    public static function addDizimo(
        PDO $db,
        int $membroId,
        float $quantia,
        string $data
    ): bool {
    
        $sql = "
            INSERT INTO dizimos (data, membro_id, quantia)
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

    /* =====================================================
 * BUSCAR POSSÍVEIS MEMBROS DUPLICADOS
 * UTILIZADO ANTES DO CADASTRO
 * ===================================================== */
public static function buscarSimilares(PDO $db, array $dados): array
{
    $nome  = trim(strtolower($dados['nome'] ?? ''));
    $sexo  = $dados['sexo'] ?? null;
   // $departamento  = $dados['departamento'] ?? null;
   // $permanencia  = $dados['permanencia'] ?? null;
    $dataNascimento = self::dateDb($dados['data_nascimento'] ?? null);
    //$dataConversao = self::dateDb($dados['data_conversao'] ?? null);

    if ($nome === '') {
        return [];
    }

    // =============================
    // NORMALIZAÇÃO DO NOME
    // =============================
    $nome = preg_replace('/\s+/', ' ', $nome);
    $palavras = explode(' ', $nome);

    $palavras = array_filter($palavras, fn($p) => mb_strlen($p) >= 3);

    if (!$palavras) {
        return [];
    }

    // =============================
    // WHERE DINÂMICO
    // =============================
    $where = [];

    foreach ($palavras as $i => $p) {
        $where[] = "LOWER(nome) LIKE :p$i";
    }

    $sql = "
        SELECT
            id,
            nome,
            sexo,
            data_nascimento
        FROM membros
        WHERE (" . implode(' OR ', $where) . ")
        LIMIT 50
    ";

    $stmt = $db->prepare($sql);

    foreach ($palavras as $i => $p) {
        $stmt->bindValue(":p$i", "%$p%", PDO::PARAM_STR);
    }

    $stmt->execute();

    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$resultados) {
        return [];
    }

    // =============================
    // SCORE DE SIMILARIDADE
    // =============================
    $similares = [];

    foreach ($resultados as $r) {

        $score = 0;
        $percent = 0;

        similar_text(
            strtolower($r['nome']),
            $nome,
            $percent
        );

        if ($percent >= 60) {
            $score += 2;
        }

        if ($sexo && $r['sexo'] === $sexo) {
            $score += 1;
        }

        if ($dataNascimento && $r['data_nascimento'] === $dataNascimento) {
            $score += 2;
        }

        if ($score >= 2) {
            $r['similaridade'] = round($percent);
            $similares[] = $r;
        }
    }

    usort($similares, fn($a,$b)=>
        $b['similaridade'] <=> $a['similaridade']
    );

    return $similares;
}
    /* =====================================================
     * CRIAR MEMBRO + MORADIA (TRANSACTION)
     * ===================================================== */
    public static function create(PDO $db, array $data): int|false
    {
        try {
    
            $db->beginTransaction();
    
            $stmt = $db->prepare("
                INSERT INTO membros (
                    nome, sexo, data_nascimento,
                    data_conversao, permanencia,
                    departamento, batizado
                ) VALUES (
                    :nome, :sexo, :data_nascimento,
                    :data_conversao, :permanencia,
                    :departamento, :batizado
                )
            ");
    
            $stmt->execute([
                ':nome' => trim($data['nome']),
                ':sexo' => $data['sexo'],
                ':data_nascimento' => self::dateDb($data['data_nascimento'] ?? null),
                ':data_conversao' => self::dateDb($data['data_conversao'] ?? null),
                ':permanencia' => $data['permanencia'] ?? 'Ativo',
                ':departamento' => $data['departamento'],
                ':batizado' => $data['batizado'] ?? 'Nao'
            ]);
    
            $membroId = (int)$db->lastInsertId();
    
            $stmt = $db->prepare("
                INSERT INTO moradia (
                    membro_id, bairro, referencia,
                    numero_celular, numero_alternativo, celular_cuidador
                ) VALUES (
                    :membro_id, :bairro, :referencia,
                    :numero_celular, :numero_alternativo, :celular_cuidador
                )
            ");
    
            $stmt->execute([
                ':membro_id' => $membroId,
                ':bairro' => $data['bairro'] ?? null,
                ':referencia' => $data['referencia'] ?? null,
                ':numero_celular' => $data['numero_celular'] ?? null,
                ':numero_alternativo' => $data['numero_alternativo'] ?? null,
                ':celular_cuidador' => $data['celular_cuidador'] ?? null
            ]);
    
            $db->commit();
    
            return $membroId;
    
        } catch (\Throwable $e) {
    
            $db->rollBack();
            error_log($e->getMessage());
    
            return false;
        }
    }
	
	// Dentro da classe Membro
/**
 * Retorna os registros agregados (participações) para os cards
 * @param string|null $mes (ex: "02")
 * @param string|null $ano (ex: "2026")
 * @return array
 */
public function getParticipacoes(?string $mes = null, ?string $ano = null): array
{
    $mes = $mes ?? date('m');
    $ano = $ano ?? date('Y');

    $stmt = $this->db->prepare("
        SELECT 
            DATE_FORMAT(data_conversao, '%Y-%m-%d') AS data,
            SUM(CASE WHEN departamento='Pais' THEN 1 ELSE 0 END) AS pais,
            SUM(CASE WHEN departamento='Maes' THEN 1 ELSE 0 END) AS maes,
            SUM(CASE WHEN departamento='Jovens' THEN 1 ELSE 0 END) AS jovens,
            SUM(CASE WHEN departamento='Dominical' THEN 1 ELSE 0 END) AS dominical,
            COUNT(*) AS visitantes,
            SUM(CASE WHEN data_conversao IS NOT NULL THEN 1 ELSE 0 END) AS convertidos
        FROM membros
        WHERE MONTH(data_conversao) = :mes
          AND YEAR(data_conversao) = :ano
          AND deleted_at IS NULL
        GROUP BY DATE(data_conversao)
        ORDER BY data DESC
    ");

    $stmt->execute([
        ':mes' => $mes,
        ':ano' => $ano
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Cria novo registro (participação)
 * Reaproveita a função create existente
 */
public function criarParticipacao(array $dados): bool
{
	
    return self::create($this->db, $dados);
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

    /** Banco → View (Y-m-d → d-m-Y) */
    private static function dateView(?string $d): ?string
    {
        if (empty($d)) return null;
        return date('d-m-Y', strtotime($d));
    }
}
