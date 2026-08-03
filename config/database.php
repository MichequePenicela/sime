<?php
// Configurações do banco
$host = 'localhost';
$dbname = 'sime';
$user = 'root';
$pass = '';

// Conexão PDO
try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
