<?php 

// Credenciais da Railway
$host = 'turntable.proxy.rlwy.net';
$port = '26772'; 
$db   = 'railway';
$user = 'root';
$pass = 'SUA_SENHA_AQUI'; 

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass);
    //define o modo de erro para exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
}

?>