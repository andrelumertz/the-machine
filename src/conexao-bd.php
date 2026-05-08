<?php 

//Credenciais protegidas por variáveis de Ambiente
$host = getenv('DB_HOST') ?: 'localhost'; //se não achar na nuvem, usa localhost
$port = getenv('DB_PORT') ?: '3306';
$db   = getenv('DB_NAME') ?: 'blackout_cafe';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass);
    //define o modo de erro para exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
}

?>