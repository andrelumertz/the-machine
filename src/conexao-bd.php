<?php 

// VERIFICA SE ESTÁ NA NUVEM (RENDER) OU LOCAL (XAMPP)
if (getenv('DB_HOST')) {
    
    // ==========================================
    // AMBIENTE DE PRODUÇÃO (TiDB / RENDER)
    // ==========================================
    $host = getenv('DB_HOST'); 
    $port = getenv('DB_PORT') ?: '4000'; 
    $db   = getenv('DB_NAME');
    $user = getenv('DB_USER');
    $pass = getenv('DB_PASSWORD');

    // Resolve o aviso de Deprecated automaticamente
    $ssl_key = defined('Pdo\Mysql::ATTR_SSL_CA') ? Pdo\Mysql::ATTR_SSL_CA : PDO::MYSQL_ATTR_SSL_CA;

    $options = [
        $ssl_key => '/etc/ssl/certs/ca-certificates.crt',
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ];
    
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

} else {
    
    // ==========================================
    // AMBIENTE LOCAL (WINDOWS / XAMPP)
    // ==========================================
    $host = '127.0.0.1';
    $port = '3307';               // <- A porta 3307 que configuramos no XAMPP
    $db   = 'blackout_cafe';      // O banco que criamos no Beekeeper
    $user = 'root';               
    $pass = '';                   

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ];

    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
}

// TENTA FAZER A CONEXÃO
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
    exit;
}

?>