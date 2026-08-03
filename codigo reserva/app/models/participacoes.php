<?php
namespace App\Models;

use PDO;
use DateTime;

class Participacoes
{

/* =========================
ADD
========================= */
public static function add(PDO $db,array $d):bool
{
    if(self::existsByDate($db,$d['data']??'')){return false;}

    $stmt=$db->prepare("
        INSERT INTO participacoes
        (data,pais,maes,jovens,dominical,visitantes,convertidos,observacao)
        VALUES
        (:data,:pais,:maes,:jovens,:dominical,:visitantes,:convertidos,:observacao)
    ");

    return $stmt->execute([
        ':data'=>self::date($d['data']??''),
        ':pais'=>(int)($d['pais']??0),
        ':maes'=>(int)($d['maes']??0),
        ':jovens'=>(int)($d['jovens']??0),
        ':dominical'=>(int)($d['dominical']??0),
        ':visitantes'=>(int)($d['visitantes']??0),
        ':convertidos'=>(int)($d['convertidos']??0),
        ':observacao'=>trim($d['observacao']??'')
    ]);
}

/* =========================
LISTAR MES
========================= */
public static function getMesAtual(PDO $db):array
{
    $stmt=$db->prepare("
        SELECT *
        FROM participacoes
        WHERE deleted_at IS NULL
        AND data BETWEEN :i AND :f
        ORDER BY data DESC
    ");

    $stmt->execute([
        ':i'=>date('Y-m-01'),
        ':f'=>date('Y-m-t')
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* =========================
STATS
========================= */
public static function getStatsMesAtual(PDO $db):array
{
    $stmt=$db->query("
        SELECT
        SUM(visitantes) visitantes,
        SUM(convertidos) convertidos
        FROM participacoes
        WHERE deleted_at IS NULL
        AND data BETWEEN CURDATE()-INTERVAL DAY(CURDATE())-1 DAY AND LAST_DAY(CURDATE())
    ");

    return $stmt->fetch(PDO::FETCH_ASSOC)?:[];
}

/* =========================
EDITAR ENTERPRISE++
ACEITA EDICAO PARCIAL
========================= */
public static function editar(PDO $db,array $d):bool
{
    if(empty($d['id'])) return false;

    $permitidos=[
        'data','pais','maes','jovens','dominical',
        'visitantes','convertidos','observacao'
    ];

    $set=[];
    $bind=[':id'=>(int)$d['id']];

    foreach($permitidos as $c){

        if(!array_key_exists($c,$d)) continue;

        // DATA
        if($c==='data'){
            $data=self::date($d['data']??'');
            if(!$data) continue;

            $set[]="data=:data";
            $bind[':data']=$data;
            continue;
        }

        // OBS
        if($c==='observacao'){
            $set[]="observacao=:observacao";
            $bind[':observacao']=trim($d['observacao']??'');
            continue;
        }

        // NUMEROS
        if($d[$c]==='') continue;

        $set[]="$c=:$c";
        $bind[":$c"]=(int)$d[$c];
    }

    if(!$set) return true; // NÃO É ERRO

    $sql="UPDATE participacoes SET ".implode(',',$set)." WHERE id=:id";

    return $db->prepare($sql)->execute($bind);
}

/* =========================
EXCLUIR
========================= */
public static function excluir(PDO $db,int $id):bool
{
    return $db->prepare("
        UPDATE participacoes
        SET deleted_at=NOW()
        WHERE id=:id
    ")->execute([':id'=>$id]);
}

/* =========================
EXISTE DATA
========================= */
public static function existsByDate(PDO $db,string $data):bool
{
    $stmt=$db->prepare("
        SELECT COUNT(*)
        FROM participacoes
        WHERE data=:d
        AND deleted_at IS NULL
    ");

    $stmt->execute([':d'=>self::date($data)]);

    return (bool)$stmt->fetchColumn();
}

/* =========================
FORMATAR DATA
========================= */
private static function date(string $d):?string
{
    if(!$d) return null;

    $formats=['d/m/Y','d-m-Y','Y-m-d'];

    foreach($formats as $f){
        $dt=DateTime::createFromFormat($f,$d);
        if($dt) return $dt->format('Y-m-d');
    }

    return null;
}

}