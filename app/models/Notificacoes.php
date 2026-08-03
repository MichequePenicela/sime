<?php

namespace App\Models;

use PDO;

class Notificacoes
{

private PDO $db;

//////////////////////////////////////////////////

public function __construct(PDO $db)
{

$this->db = $db;

}

//////////////////////////////////////////////////////
// ANIVERSÁRIOS (7 DIAS + HOJE)
//////////////////////////////////////////////////////

public function aniversariosProximos(int $dias = 7)
{

$sql="

SELECT

id,
nome,
data_nascimento,

CASE
WHEN DATE_FORMAT(data_nascimento,'%m-%d')
= DATE_FORMAT(CURDATE(),'%m-%d')

THEN 'hoje'

ELSE 'proximo'
END tipo,

TIMESTAMPDIFF(YEAR,data_nascimento,CURDATE())

+

CASE
WHEN DATE_FORMAT(data_nascimento,'%m-%d')
>= DATE_FORMAT(CURDATE(),'%m-%d')
THEN 1
ELSE 0
END idade

FROM membros

WHERE

DATE_FORMAT(data_nascimento,'%m-%d')

BETWEEN

DATE_FORMAT(CURDATE(),'%m-%d')

AND

DATE_FORMAT(
DATE_ADD(CURDATE(),INTERVAL ? DAY),
'%m-%d'
)

ORDER BY

DATE_FORMAT(data_nascimento,'%m-%d')

";

$stmt = $this->db->prepare($sql);

$stmt->execute([$dias]);

return $stmt->fetchAll(PDO::FETCH_ASSOC);

}

//////////////////////////////////////////////////////
// ÚLTIMOS DÍZIMOS
//////////////////////////////////////////////////////

public function ultimosDizimos()
{

$sql="

SELECT

d.id,
d.data,
d.quantia,
m.nome

FROM dizimos d

LEFT JOIN membros m
ON m.id = d.membro_id
WHERE data>= DATE_SUB(CURDATE(), INTERVAL 3 DAY)
ORDER BY d.data DESC

LIMIT 10

";

return $this->db
->query($sql)
->fetchAll(PDO::FETCH_ASSOC);

}

//////////////////////////////////////////////////////
// ÚLTIMOS LANÇAMENTOS
//////////////////////////////////////////////////////

public function ultimosLancamentos()
{

$sql="

SELECT
id,
'entrada' tipo,
data,
valor,
descricao

FROM entradas

WHERE data >= DATE_SUB(CURDATE(), INTERVAL 3 DAY)

UNION ALL

SELECT
id,
'despesa' tipo,
data,
valor,
descricao

FROM despesas

WHERE data >= DATE_SUB(CURDATE(), INTERVAL 3 DAY)

ORDER BY data DESC

LIMIT 10

";

return $this->db
->query($sql)
->fetchAll(PDO::FETCH_ASSOC);

}

}