<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

require "src/conexao-bd.php";
require "src/Modelo/Produto.php";
require "src/Modelo/Repositorio/produtoRepositorio.php";

if (isset($_POST['cadastro'])) {
    $produto = new Produto(
        null, 
        $_POST['tipo'],
        $_POST['nome'],
        $_POST['descricao'],
        $_POST['preco']
    );

    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
        $produto->setImagem(uniqid() . $_FILES['imagem']['name']);
        move_uploaded_file($_FILES['imagem']['tmp_name'], $produto->getImagemDiretorio());
    }
    
    $produtoRepositorio = new produtoRepositorio($pdo);
    $produtoRepositorio->salvar($produto);
    
    header("Location: admin.php");
    exit();
}
?>

<!doctype html>
<html lang="pt-br" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="img/logo-Blackout.png" type="image/x-icon">
    
    <!-- Tailwind CSS via CDN para desenvolvimento rápido -->
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&family=Barlow:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>Blackout - Cadastrar Produto</title>
</head>
<body class="bg-neutral-950 text-neutral-200 font-sans selection:bg-neutral-800 antialiased min-h-screen flex flex-col justify-between relative overflow-x-hidden">

    <!-- Brilho de fundo sutil imitando luz de filamento de carbono -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[500px] h-[500px] bg-amber-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>

    <!-- Cabeçalho Administrativo Fixo -->
    <header class="border-b border-neutral-900 bg-black/90 backdrop-blur sticky top-0 z-50">
        <nav class="container mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-xl font-bold tracking-wider text-white">BLACKOUT CAFÉ</span>
                <span class="px-2.5 py-0.5 text-[10px] font-bold tracking-widest bg-amber-500/10 text-amber-400 border border-amber-500/20 rounded-full uppercase">
                    Admin Panel
                </span>
            </div>
            <a href="admin.php" class="px-4 py-2 text-sm rounded-full bg-neutral-900 border border-neutral-800 text-neutral-300 hover:text-white hover:border-neutral-700 transition flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Voltar ao Painel
            </a>
        </nav>
    </header>

    <!-- Formulário de Cadastro Centralizado -->
    <main class="flex-grow flex items-center justify-center px-6 py-12 relative z-10">
        <div class="w-full max-w-lg bg-neutral-900/40 border border-neutral-900/85 rounded-3xl p-8 backdrop-blur-xl shadow-2xl">
            
            <!-- Cabeçalho do Card -->
            <div class="flex flex-col items-center text-center mb-8">
                <div class="w-12 h-12 mb-3 rounded-2xl bg-neutral-950 border border-neutral-800 flex items-center justify-center text-amber-500 shadow-md">
                    <i data-lucide="plus" class="w-6 h-6"></i>
                </div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight">CADASTRAR PRODUTO</h1>
                <p class="text-xs text-neutral-500 mt-1">Preencha os campos para adicionar o item ao cardápio</p>
            </div>

            <!-- Form -->
            <form action="cadastrar-produto.php" method="post" enctype="multipart/form-data" class="space-y-6">

                <!-- Seletor Dinâmico de Categoria (Substitui os botões de rádio nativos) -->
                <div class="space-y-2">
                    <label class="text-xs font-semibold uppercase tracking-wider text-neutral-400 block">Categoria</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative block cursor-pointer select-none">
                            <input type="radio" id="cafe" name="tipo" value="Café" checked class="peer sr-only">
                            <div class="flex items-center justify-center gap-2 px-4 py-3.5 rounded-2xl bg-black/60 border border-neutral-900 text-sm text-neutral-400 peer-checked:border-amber-500 peer-checked:bg-amber-500/10 peer-checked:text-amber-400 hover:border-neutral-800 transition-all font-medium">
                                <i data-lucide="coffee" class="w-4 h-4"></i>
                                Café
                            </div>
                        </label>
                        <label class="relative block cursor-pointer select-none">
                            <input type="radio" id="almoco" name="tipo" value="Almoço" class="peer sr-only">
                            <div class="flex items-center justify-center gap-2 px-4 py-3.5 rounded-2xl bg-black/60 border border-neutral-900 text-sm text-neutral-400 peer-checked:border-amber-500 peer-checked:bg-amber-500/10 peer-checked:text-amber-400 hover:border-neutral-800 transition-all font-medium">
                                <i data-lucide="utensils" class="w-4 h-4"></i>
                                Almoço
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Campo: Nome do Produto -->
                <div class="space-y-2">
                    <label for="nome" class="text-xs font-semibold uppercase tracking-wider text-neutral-400 block">Nome do Produto</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-neutral-500">
                            <i data-lucide="tag" class="w-4 h-4"></i>
                        </span>
                        <input 
                            type="text" 
                            id="nome" 
                            name="nome" 
                            placeholder="Ex: Espresso Duplo" 
                            required 
                            class="pl-11 pr-4 py-3.5 w-full rounded-2xl bg-black/60 border border-neutral-900 text-white placeholder-neutral-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all duration-200 text-sm font-light font-barlow"
                        />
                    </div>
                </div>

                <!-- Campo: Preço do Produto (Com Prefixo R$) -->
                <div class="space-y-2">
                    <label for="preco" class="text-xs font-semibold uppercase tracking-wider text-neutral-400 block">Preço de Venda</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-amber-500 font-mono text-sm font-semibold">
                            R$
                        </span>
                        <input 
                            type="text" 
                            id="preco" 
                            name="preco" 
                            placeholder="0,00" 
                            required 
                            class="pl-12 pr-4 py-3.5 w-full rounded-2xl bg-black/60 border border-neutral-900 text-white placeholder-neutral-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all duration-200 text-sm font-mono font-semibold"
                        />
                    </div>
                </div>

                <!-- Campo: Descrição do Produto -->
                <div class="space-y-2">
                    <label for="descricao" class="text-xs font-semibold uppercase tracking-wider text-neutral-400 block">Descrição Detalhada</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-neutral-500">
                            <i data-lucide="align-left" class="w-4 h-4"></i>
                        </span>
                        <input 
                            type="text" 
                            id="descricao" 
                            name="descricao" 
                            placeholder="Descreva sabores, grãos, tamanho..." 
                            required 
                            class="pl-11 pr-4 py-3.5 w-full rounded-2xl bg-black/60 border border-neutral-900 text-white placeholder-neutral-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all duration-200 text-sm font-light font-barlow"
                        />
                    </div>
                </div>

                <!-- Campo Customizado de Upload de Imagem (Premium File Uploader) -->
                <div class="space-y-2">
                    <label class="text-xs font-semibold uppercase tracking-wider text-neutral-400 block">Foto Representativa</label>
                    <label for="imagem" class="group flex flex-col items-center justify-center gap-3 px-6 py-8 border border-dashed border-neutral-800 rounded-2xl bg-black/40 hover:border-amber-500/40 cursor-pointer transition-all">
                        <div class="w-10 h-10 rounded-full bg-neutral-950 border border-neutral-800 flex items-center justify-center text-neutral-500 group-hover:text-amber-400 transition-all" id="upload-icon-wrapper">
                            <i data-lucide="upload-cloud" class="w-5 h-5" id="upload-icon"></i>
                        </div>
                        <div class="text-xs text-neutral-400 text-center" id="upload-text">
                            <span class="font-semibold text-amber-500 group-hover:text-amber-400">Clique para enviar</span> ou arraste o arquivo
                        </div>
                        <div class="text-[10px] text-neutral-600" id="upload-limits">PNG, JPG ou WEBP (Max. 5MB)</div>
                        <input 
                            type="file" 
                            name="imagem" 
                            accept="image/*" 
                            id="imagem" 
                            class="hidden"
                        />
                    </label>
                </div>

                <!-- Botão de Cadastro Primário -->
                <button 
                    type="submit" 
                    name="cadastro" 
                    class="w-full py-4 rounded-2xl bg-amber-500 hover:bg-amber-400 text-black font-bold tracking-wide uppercase transition duration-300 flex items-center justify-center gap-2 shadow-lg shadow-amber-500/10 hover:shadow-amber-500/20 text-xs"
                >
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    Cadastrar Novo Produto
                </button>
            </form>

        </div>
    </main>

    <!-- Rodapé Estético -->
    <footer class="border-t border-neutral-900 bg-neutral-950/60 py-6 text-center text-xs text-neutral-700 relative z-10">
        <div class="container mx-auto px-6">
            <p>&copy; 2026 Blackout Café — Painel Administrativo de Produtos.</p>
        </div>
    </footer>

    <!-- Scripts essenciais para Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Scripts JQuery e Máscara de Moedas mantidos idênticos do seu antigo -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-maskmoney/3.0.2/jquery.maskMoney.min.js" integrity="sha512-Rdk63VC+1UYzGSgd3u2iadi0joUrcwX0IWp2rTh6KXFoAmgOjRS99Vynz1lJPT8dLjvo6JZOqpAHJyfCEZ5KoA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="js/index.js"></script>

    <script>
        // Inicialização de componentes e efeitos do formulário
        window.addEventListener('DOMContentLoaded', () => {
            // Inicializa ícones lucide
            lucide.createIcons();

            // Script visual para mostrar o nome da foto selecionada
            const fileInput = document.getElementById('imagem');
            const uploadText = document.getElementById('upload-text');
            const uploadLimits = document.getElementById('upload-limits');
            const iconWrapper = document.getElementById('upload-icon-wrapper');

            fileInput.addEventListener('change', (e) => {
                if(e.target.files.length > 0) {
                    const fileName = e.target.files[0].name;
                    uploadText.innerHTML = `Arquivo: <span class="font-semibold text-emerald-400">${fileName}</span>`;
                    uploadLimits.textContent = "Excelente! Clique no botão abaixo para concluir o cadastro.";
                    iconWrapper.className = "w-10 h-10 rounded-full bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400";
                    // Troca dinamicamente o ícone de upload por um checkmark verde
                    iconWrapper.innerHTML = `<i data-lucide="check" class="w-5 h-5"></i>`;
                    lucide.createIcons();
                }
            });
        });
    </script>
</body>
</html>