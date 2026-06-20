<?php
require __DIR__ . '/src/conexao-bd.php'; // Puxa conexão da Railway

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['password'];

    // Busca o usuário no banco de dados
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $statement = $pdo->prepare($sql);
    $statement->execute([$email]);
    $usuario = $statement->fetch();

    // Valida a senha usando a senha simples cadastrada no Beekeeper
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
<html lang="pt-br" class="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Blackout Café - Login Administrativo</title>
    
    <!-- Tailwind CSS Engine -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                        barlow: ['Barlow', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Importação de fontes premium adicionais -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&family=Barlow:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="icon" href="img/logo-blackout.png" type="image/x-icon" />
</head>
<body class="bg-neutral-950 text-neutral-200 font-sans selection:bg-neutral-800 antialiased min-h-screen flex flex-col justify-between relative overflow-x-hidden">

    <!-- Brilho de fundo sutil simulando a iluminação de filamento de carbono de cafeteria industrial -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[500px] h-[500px] bg-amber-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>

    <!-- Área de Conteúdo Centralizada -->
    <main class="flex-grow flex items-center justify-center px-6 py-16 relative z-10">
        
        <div class="w-full max-w-md bg-neutral-900/40 border border-neutral-900/80 rounded-3xl p-8 backdrop-blur-xl shadow-2xl">
            
            <!-- Logo e Cabeçalho de Boas-Vindas -->
            <div class="flex flex-col items-center text-center mb-8">
                <div class="w-16 h-16 mb-4 rounded-2xl bg-neutral-950 border border-neutral-800 flex items-center justify-center overflow-hidden">
                    <img src="img/logo-blackout.png" class="w-12 h-12 object-contain" alt="Logo Blackout Café" onerror="this.src='https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=120&q=80'" />
                </div>
                <span class="px-2.5 py-0.5 text-[10px] font-bold tracking-widest bg-amber-500/10 text-amber-400 border border-amber-500/20 rounded-full uppercase mb-2">
                    Painel de Controle
                </span>
                <h1 class="text-2xl font-extrabold text-white tracking-tight">BLACKOUT CAFÉ</h1>
                <p class="text-xs text-neutral-500 mt-1">Insira suas credenciais para gerenciar o cardápio</p>
            </div>

            <!-- Formulário de Autenticação -->
            <form method="POST" class="space-y-6">
                
                <!-- Campo de E-mail -->
                <div class="space-y-2">
                    <label for="email" class="text-xs font-semibold uppercase tracking-wider text-neutral-400 block">E-mail</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-neutral-500">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </span>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder="exemplo@blackout.com" 
                            required 
                            class="pl-11 pr-4 py-3.5 w-full rounded-2xl bg-black/60 border border-neutral-900 text-white placeholder-neutral-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all duration-200 text-sm font-light font-barlow"
                        />
                    </div>
                </div>

                <!-- Campo de Senha -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label for="password" class="text-xs font-semibold uppercase tracking-wider text-neutral-400 block">Senha</label>
                        <span class="text-[10px] text-neutral-600 font-medium">Acesso Restrito</span>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-neutral-500">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </span>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="••••••••••••" 
                            required 
                            class="pl-11 pr-4 py-3.5 w-full rounded-2xl bg-black/60 border border-neutral-900 text-white placeholder-neutral-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all duration-200 text-sm font-light font-barlow"
                        />
                    </div>
                </div>

                <!-- Bloco de Erro Customizado -->
                <?php if (isset($erro)): ?>
                    <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 flex items-start gap-3 animate-shake">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-rose-400 shrink-0 mt-0.5"></i>
                        <div class="text-xs text-rose-400 leading-normal font-medium">
                            <?= $erro ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Botão de Login Primário -->
                <button 
                    type="submit" 
                    class="w-full py-3.5 rounded-2xl bg-amber-500 hover:bg-amber-400 text-black font-bold tracking-wide uppercase transition duration-300 flex items-center justify-center gap-2 shadow-lg shadow-amber-500/10 hover:shadow-amber-500/20 text-xs"
                >
                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                    Acessar Painel
                </button>
            </form>

            <!-- Links Secundários e Retorno -->
            <div class="mt-8 pt-6 border-t border-neutral-900/60 text-center">
                <a href="index.php" class="inline-flex items-center gap-2 text-xs text-neutral-500 hover:text-white transition duration-200">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    Voltar para o Cardápio Público
                </a>
            </div>

        </div>
    </main>

    <!-- Rodapé Estético Muted -->
    <footer class="border-t border-neutral-900/60 bg-neutral-950/60 py-6 text-center text-xs text-neutral-700 relative z-10">
        <div class="container mx-auto px-6">
            <p>&copy; 2026 Blackout Café — Área de Autenticação Segura.</p>
        </div>
    </footer>

    <!-- Scripts essenciais para Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        // Inicialização limpa e assíncrona assim que o DOM for carregado
        window.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</body>
</html>