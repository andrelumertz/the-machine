<?php 
    
    session_start();
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: login.php");
        exit();
    }

    require "src/conexao-bd.php";
    require "src/Modelo/Produto.php";
    require "src/Modelo/Repositorio/produtoRepositorio.php";

    $produtoRepositorio = new produtoRepositorio($pdo);
    $produtos = $produtoRepositorio->buscarTodos();
?>

<!doctype html>
<html lang="pt-br" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blackout Café - Painel Administrativo</title>
    
    <!-- Tailwind CSS via CDN para desenvolvimento rápido -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Configuração do Tailwind para fontes customizadas e suporte dark -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                        barlow: ['Barlow', 'sans-serif'],
                        inter: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- Importação de fontes adicionais -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&family=Barlow:wght@400;500;600;700&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="icon" href="img/logo-Blackout.png" type="image/x-icon">
</head>
<body class="bg-black text-neutral-200 font-sans selection:bg-neutral-800 antialiased min-h-screen flex flex-col justify-between">

    <div>
        <!-- Cabeçalho Administrativo Fixo -->
        <header class="border-b border-neutral-900 bg-black/90 backdrop-blur sticky top-0 z-50">
            <nav class="container mx-auto px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-xl font-bold tracking-wider text-white">BLACKOUT CAFÉ</span>
                    <span class="px-2.5 py-0.5 text-[10px] font-bold tracking-widest bg-amber-500/10 text-amber-400 border border-amber-500/20 rounded-full uppercase">
                        Admin Panel
                    </span>
                </div>
                <div class="flex items-center gap-6">
                    <a href="index.php" target="_blank" class="text-sm text-neutral-400 hover:text-white transition flex items-center gap-2">
                        <i data-lucide="external-link" class="w-4 h-4 text-neutral-500"></i>
                        Ver Cardápio Público
                    </a>
                    <a href="logout.php" class="px-4 py-2 text-sm rounded-full bg-neutral-900 border border-neutral-800 text-neutral-300 hover:text-rose-400 hover:border-rose-500/20 hover:bg-rose-500/5 transition flex items-center gap-2">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        Sair
                    </a>
                </div>
            </nav>
        </header>

        <!-- Área de Conteúdo Principal -->
        <main class="container mx-auto px-6 py-12 max-w-7xl">
            
            <!-- Título da Seção e Ações Rápidas -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10 pb-6 border-b border-neutral-900">
                <div>
                    <h1 class="text-3xl font-extrabold text-white tracking-tight flex items-center gap-3">
                        <i data-lucide="sliders" class="w-8 h-8 text-amber-500"></i>
                        Administração de Produtos
                    </h1>
                    <p class="text-sm text-neutral-500 mt-1">Gerencie os itens do cardápio digital do Blackout Café em tempo real.</p>
                </div>
                
                <!-- Botões Primários -->
                <div class="flex flex-wrap items-center gap-3">
                    <form action="gerador-pdf.php" method="post">
                        <button type="submit" class="px-5 py-2.5 text-sm rounded-full border border-neutral-800 text-neutral-300 font-medium hover:bg-neutral-950 hover:border-neutral-700 transition flex items-center gap-2">
                            <i data-lucide="file-text" class="w-4 h-4 text-neutral-400"></i>
                            Baixar Relatório PDF
                        </button>
                    </form>
                    <a href="cadastrar-produto.php" class="px-5 py-2.5 text-sm rounded-full bg-amber-500 text-black font-semibold hover:bg-amber-400 transition flex items-center gap-2">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        Cadastrar Novo Produto
                    </a>
                </div>
            </div>

            <!-- Tabela de Produtos Formatada -->
            <div class="bg-neutral-950 border border-neutral-900 rounded-2xl overflow-hidden shadow-2xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-neutral-900 bg-neutral-900/20 text-xs font-mono text-neutral-400 tracking-wider uppercase">
                                <th class="px-6 py-4">Produto</th>
                                <th class="px-6 py-4">Tipo</th>
                                <th class="px-6 py-4">Descrição</th>
                                <th class="px-6 py-4 text-right">Valor</th>
                                <th class="px-6 py-4 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-900">
                            <?php foreach($produtos as $produto): ?>
                                <tr class="hover:bg-neutral-900/30 transition duration-150">
                                    <!-- Nome do Produto -->
                                    <td class="px-6 py-5">
                                        <div class="font-bold text-white text-sm tracking-tight"><?= $produto->getNome() ?></div>
                                    </td>
                                    
                                    <!-- Tipo (Badge Customizado Dinâmico) -->
                                    <td class="px-6 py-5">
                                        <?php 
                                            // Define a cor da badge de acordo com o tipo
                                            $tipo = $produto->getTipo();
                                            $badgeClasses = ($tipo === "Café") 
                                                ? "bg-amber-500/10 text-amber-400 border-amber-500/20" 
                                                : "bg-emerald-500/10 text-emerald-400 border-emerald-500/20";
                                        ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold tracking-wide uppercase border <?= $badgeClasses ?>">
                                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                            <?= $tipo ?>
                                        </span>
                                    </td>
                                    
                                    <!-- Descrição (Truncada para não quebrar a largura da tela) -->
                                    <td class="px-6 py-5 max-w-sm">
                                        <p class="text-xs text-neutral-400 leading-relaxed truncate font-light" title="<?= $produto->getDescricao() ?>">
                                            <?= $produto->getDescricao() ?>
                                        </p>
                                    </td>
                                    
                                    <!-- Preço destacado em Dourado -->
                                    <td class="px-6 py-5 text-right font-mono font-bold text-sm text-amber-500">
                                        <?= $produto->getPrecoFormatado() ?>
                                    </td>
                                    
                                    <!-- Coluna de Ações -->
                                    <td class="px-6 py-5">
                                        <div class="flex items-center justify-center gap-3">
                                            <!-- Editar -->
                                            <a href="editar-produto.php?id=<?= $produto->getId() ?>" 
                                               class="p-2 text-neutral-400 hover:text-white hover:bg-neutral-900 border border-transparent hover:border-neutral-800 rounded-lg transition-all flex items-center gap-1 text-xs font-semibold" 
                                               title="Editar Produto">
                                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                                                Editar
                                            </a>
                                            
                                            <!-- Excluir (Formulário) -->
                                            <form action="excluir-produto.php" method="post" onsubmit="return confirm('Tem certeza que deseja excluir este produto?');">
                                                <input type="hidden" name="id" value="<?= $produto->getId() ?>">
                                                <button type="submit" 
                                                        class="p-2 text-neutral-500 hover:text-rose-400 hover:bg-rose-950/10 border border-transparent hover:border-rose-500/20 rounded-lg transition-all flex items-center gap-1 text-xs font-semibold">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                    Excluir
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <!-- Rodapé Estético -->
    <footer class="border-t border-neutral-900 bg-black py-8 mt-20">
        <div class="container mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-neutral-600">
            <p>&copy; 2026 Blackout Café — Painel de Controle Administrativo.</p>
            <p>Desenvolvido com ❤️ por <a href="https://github.com/andrelumertz" target="_blank" class="text-amber-500 hover:text-amber-400 transition">André Lumertz</a></p>
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