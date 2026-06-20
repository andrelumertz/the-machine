<?php 

// Credenciais protegidas por variáveis de Ambiente
$host = getenv('DB_HOST'); 
$port = getenv('DB_PORT') ?: '4000'; // Se não achar, o padrão do TiDB é 4000
$db   = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASSWORD');

try {
    // Configuração para exigir SSL na conexão com o TiDB
    $options = [
        PDO::MYSQL_ATTR_SSL_CA => '/etc/ssl/certs/ca-certificates.crt',
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ];

    // Criando a conexão passando o array de opções
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass, $options);

} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
    exit;
}

?>