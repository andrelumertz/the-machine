<?php
require __DIR__ . '/src/conexao-bd.php'; //Puxa conexão da Railway

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['password'];

    //Busca o usuário no banco de dados
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $statement = $pdo->prepare($sql);
    $statement->execute([$email]);
    $usuario = $statement->fetch();

    //Valida a senha usando a senha simples cadastrada no Beekeeper
    if ($usuario && $senha === $usuario['senha']) {
        session_start();
        $_SESSION['usuario_id'] = $usuario['id'];
        header("Location: admin.php"); // Redireciona para o painel
        exit();
    } else {
        $erro = "E-mail ou senha inválidos!";
    }
}
?>
<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/reset.css" />
    <link rel="stylesheet" href="css/index.css" />
    <link rel="stylesheet" href="css/admin.css" />
    <link rel="stylesheet" href="css/form.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="icon" href="img/logo-blackout.png" type="image/x-icon" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <title>Blackout - Login</title>
  </head>
  <body>
    <main>
      <section class="container-admin-banner">
        <img src="img/logo-blackout.png" class="logo-admin" alt="logo-blackout" />
        <h1>Login Blackout</h1>
        <img class="ornaments" src="img/ornaments-coffee.png" alt="ornaments" />
      </section>
      <section class="container-form">
        <form method="POST">
          <label for="email">E-mail</label>
          <input type="email" id="email" name="email" placeholder="Digite o seu e-mail" required />

          <label for="password">Senha</label>
          <input type="password" id="password" name="password" placeholder="Digite a sua senha" required />

          <?php if (isset($erro)): ?>
            <p style="color: #ff6b6b; font-weight: 500; margin-top: 10px; text-align: center;"><?php echo $erro; ?></p>
          <?php endif; ?>

          <input type="submit" class="botao-cadastrar" value="Entrar" />
        </form>
      </section>
    </main>
  </body>
</html>
