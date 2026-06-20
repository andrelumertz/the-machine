<?php
require "src/conexao-bd.php";
require "src/Modelo/Produto.php";
require "src/Modelo/Repositorio/produtoRepositorio.php";

$produtosRepositorio = new produtoRepositorio($pdo);
$dadosCafe = $produtosRepositorio->opcoesCafe();
$dadosAlmoco = $produtosRepositorio->opcoesAlmoco();
?>

<!doctype html>
<html lang="pt-br" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="img/logo-Blackout.png" type="image/x-icon">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700&display=swap" rel="stylesheet">
    
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

    <style>
        .chat-fab {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 56px;
            height: 56px;
            background: #0e0e0e;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            box-shadow:
                0 8px 32px rgba(0, 0, 0, 0.4),
                0 0 0 1px rgba(255, 255, 255, 0.04);
            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease,
                background 0.2s ease;
        }
        .chat-fab:hover {
            transform: scale(1.08);
            background: #1a1a1a;
            box-shadow:
                0 12px 40px rgba(0, 0, 0, 0.5),
                0 0 0 1px rgba(255, 255, 255, 0.08);
        }
        .chat-fab svg {
            width: 22px;
            height: 22px;
            color: #e8e8e8;
            transition: opacity 0.2s ease;
        }
        .chat-fab .icon-close { display: none; }
        .chat-fab.open .icon-chat { display: none; }
        .chat-fab.open .icon-close { display: block; }

        /* Tooltip */
        .chat-fab::before {
            content: "Assistente Blackout";
            position: absolute;
            right: calc(100% + 12px);
            background: #0e0e0e;
            color: #e8e8e8;
            font-family: "Barlow", sans-serif;
            font-size: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.03em;
            padding: 6px 12px;
            border-radius: 6px;
            white-space: nowrap;
            border: 1px solid rgba(255, 255, 255, 0.08);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }
        .chat-fab:hover::before { opacity: 1; }

        /* Overlay */
        .chat-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 9990;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .chat-overlay.visible {
            opacity: 1;
            pointer-events: all;
        }

        /* Modal do chat (widget flutuante) */
        .chat-modal {
            position: fixed;
            bottom: 6rem;
            right: 2rem;
            width: 400px;
            height: 580px;
            background: #0a0a0a;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            z-index: 9995;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow:
                0 24px 64px rgba(0, 0, 0, 0.6),
                0 0 0 1px rgba(255, 255, 255, 0.04),
                inset 0 1px 0 rgba(255, 255, 255, 0.06);
            transform: translateY(16px) scale(0.97);
            opacity: 0;
            pointer-events: none;
            transition:
                transform 0.3s cubic-bezier(0.16, 1, 0.3, 1),
                opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .chat-modal.visible {
            transform: translateY(0) scale(1);
            opacity: 1;
            pointer-events: all;
        }

        /* Header da modal */
        .chat-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
            background: #0e0e0e;
            flex-shrink: 0;
        }
        .chat-modal-header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .chat-status-dot {
            width: 8px;
            height: 8px;
            background: #4ade80;
            border-radius: 50%;
            box-shadow: 0 0 8px rgba(74, 222, 128, 0.6);
            animation: pulse-dot 2s infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .chat-modal-title {
            font-family: "Barlow", sans-serif;
            font-size: 0.85rem;
            font-weight: 600;
            color: #e8e8e8;
            letter-spacing: 0.02em;
        }
        .chat-modal-subtitle {
            font-family: "Barlow", sans-serif;
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.35);
            letter-spacing: 0.03em;
            margin-top: 1px;
        }
        .chat-modal-close {
            width: 28px;
            height: 28px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s ease;
        }
        .chat-modal-close:hover { background: rgba(255, 255, 255, 0.1); }
        .chat-modal-close svg {
            width: 14px;
            height: 14px;
            color: rgba(255, 255, 255, 0.5);
        }

        /* Custom Scrollbar Interno para UX fluida */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.01); }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); border-radius: 999px; }

        @media (max-width: 480px) {
            .chat-modal {
                right: 0;
                bottom: 0;
                width: 100%;
                height: 75vh;
                border-radius: 16px 16px 0 0;
            }
            .chat-fab {
                bottom: 1.25rem;
                right: 1.25rem;
            }
        }

        /* ===================================================== */
        /* SEÇÃO DEDICADA DO ASSISTENTE (full page, estilo "app") */
        /* ===================================================== */
        .assistente-section {
            background: radial-gradient(ellipse 80% 60% at 50% 0%, rgba(245, 158, 11, 0.06), transparent 70%), #050505;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .assistente-conversa {
            background: #0a0a0a;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            box-shadow:
                0 32px 80px rgba(0, 0, 0, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.05);
        }

        .assistente-mensagens {
            min-height: 420px;
            max-height: 560px;
        }

        .assistente-mensagens::-webkit-scrollbar { width: 6px; }
        .assistente-mensagens::-webkit-scrollbar-track { background: transparent; }
        .assistente-mensagens::-webkit-scrollbar-thumb {
            background: rgba(245, 158, 11, 0.25);
            border-radius: 999px;
        }

        .assistente-bolha-bot {
            background: #131316;
            border: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 1rem;
            line-height: 1.6;
        }

        .assistente-bolha-user {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            font-size: 1rem;
            line-height: 1.6;
        }

        .assistente-input {
            font-size: 1rem;
        }

        .assistente-chip {
            transition: all 0.15s ease;
        }
        .assistente-chip:hover {
            background: rgba(245, 158, 11, 0.12);
            border-color: rgba(245, 158, 11, 0.4);
            color: #fbbf24;
        }

        /* Cards de produto sugeridos pelo bot dentro da conversa */
        .produto-card-chat {
            background: #131316;
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 14px;
            overflow: hidden;
            cursor: pointer;
            flex-shrink: 0;
            width: 168px;
            transition: border-color 0.15s ease, transform 0.15s ease;
        }
        .produto-card-chat:hover {
            border-color: rgba(245, 158, 11, 0.45);
            transform: translateY(-2px);
        }
        .produto-card-chat img {
            width: 100%;
            height: 88px;
            object-fit: cover;
        }
        .produto-card-row {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 4px;
        }
        .produto-card-row::-webkit-scrollbar { height: 4px; }
        .produto-card-row::-webkit-scrollbar-thumb {
            background: rgba(245, 158, 11, 0.25);
            border-radius: 999px;
        }
        @keyframes destaque-produto {
            0%, 100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
            30% { box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.35); }
        }
        .produto-destacado {
            animation: destaque-produto 1.1s ease;
        }

        @media (max-width: 640px) {
            .assistente-mensagens {
                min-height: 320px;
                max-height: 440px;
            }
        }
    </style>
    <title>Blackout - Cardápio</title>
</head>
<body class="bg-[#050505] text-gray-200 font-sans selection:bg-gray-800 antialiased overflow-x-hidden">

    <header class="border-b border-white/[0.06] bg-[#070707]/90 backdrop-blur-md sticky top-0 z-50">
        <nav class="container mx-auto px-6 py-4 flex items-center justify-between">
            <span class="flex items-center text-center gap-4">
                <img src="./img/logo-blackout.png" class="w-20 h-20" alt="Logo">
                <a href="#" class="text-xl font-bold tracking-widest text-white font-barlow">BLACKOUT CAFÉ</a>
            </span>
            
            <div class="hidden md:flex items-center gap-6">
                <a href="#" class="text-sm font-medium text-white border-b-2 border-white pb-1">cardápio</a>
                <a href="#assistente" class="text-sm font-medium text-gray-400 hover:text-white transition">assistente ia</a>
                <a href="./sobre.php" class="text-sm font-medium text-gray-400 hover:text-white transition">sobre</a>
                <a href="#" class="text-sm font-medium text-gray-400 hover:text-white transition">reservas</a>
                <a href="#" class="text-sm font-medium text-gray-400 hover:text-white transition">pedidos</a>
            </div>
            <button class="rounded-2xl px-5 py-2.5 text-xs bg-amber-500 hover:bg-amber-400 text-black font-bold tracking-wide uppercase transition">pedir agora</button>
        </nav>
    </header>
    
    <section class="relative min-h-[500px] flex items-center overflow-hidden bg-black py-20 border-b border-white/[0.06]">
        <video autoplay loop muted playsinline class="absolute inset-0 bg-gradient-to-r from-gray-950 via-gray-950/80 to-transparent z-10">
            <source src="./img/video-coffe.mp4" type="video/mp4">
        </video>
        <div class="absolute inset-0 bg-gradient-to-r from-black via-black/6 to-transparent z-10"></div>
        
        <div class="container mx-auto px-6 grid grid-cols-1 lg:grid-cols-12 gap-12 relative z-20 items-center">
            <div class="lg:col-span-7 max-w-xl">
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-gray-400 mb-3">PORTO ALEGRE — DESDE 2024</p>
                <h1 class="text-4xl md:text-5xl font-extrabold leading-[1.15] tracking-tight text-amber-400 mb-6 font-barlow">
                    Café de verdade,<br><span class="text-white">sem pressa.</span>
                </h1>
                <p class="text-base text-gray-400 leading-relaxed mb-8">
                    Grãos selecionados, ambiente tranquilo e um cardápio digital que respeita cada detalhe do seu ritual diário de bem-estar.
                </p>
                <div class="flex flex-wrap items-center gap-4">
                    <a href="#cafe" class="px-6 py-3 rounded-full border border-white/10 bg-white/5 text-sm text-white font-medium hover:bg-white/10 hover:border-white/20 transition flex items-center gap-2.5">
                        <i data-lucide="book-open" class="w-4 h-4"></i> ver cardápio
                    </a>
                    <a href="#" class="px-6 py-3 rounded-full border border-white/10 bg-white/5 text-sm text-white font-medium hover:bg-white/10 hover:border-white/20 transition flex items-center gap-2.5">
                        <i data-lucide="calendar" class="w-4 h-4"></i> fazer reserva
                    </a>
                </div>
            </div>
            
            <div class="lg:col-span-5 w-full">
                <div class="bg-[#0b0b0c]/90 border border-white/[0.08] p-6 rounded-2xl flex gap-6 items-center backdrop-blur-md">
                    <div class="flex-1">
                        <span class="inline-block px-2.5 py-0.5 text-[10px] font-semibold tracking-wider rounded bg-amber-500/10 border border-amber-500/30 text-amber-400 mb-3">DESTAQUE</span>
                        <h3 class="text-lg font-bold text-white mb-1">Cold Brew Especial</h3>
                        <p class="text-xs text-gray-400 mb-4 leading-relaxed">Infusão fria de 18h, notas de chocolate e caramelo.</p>
                        <div class="text-2xl font-bold text-white mb-2">R$ 18</div>
                        <div class="flex items-center gap-1.5 text-xs text-green-400">
                            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> Disponível agora
                        </div>
                    </div>
                    <div class="flex-1 w-28 h-36 rounded-lg overflow-hidden border border-white/10 flex-shrink-0">
                        <img src="https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=300&q=80" alt="Cold Brew" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <nav class="border-b border-white/[0.06] bg-[#070707] py-4 sticky top-[73px] z-40">
        <div class="container mx-auto px-6 flex items-center justify-center gap-10 text-xs uppercase tracking-wider font-semibold">
            <a href="#cafe" class="flex items-center gap-2 text-white transition border-b border-white pb-1">
                <i data-lucide="coffee" class="w-4 h-4"></i> Cafés
            </a>
            <a href="#almoco" class="flex items-center gap-2 text-gray-400 hover:text-white transition">
                <i data-lucide="utensils-crosshair" class="w-4 h-4"></i> Almoço
            </a>
        </div>
    </nav>
    
    <main class="container mx-auto px-6 py-16 flex flex-col gap-20">
        <section id="cafe" class="scroll-mt-32">
            <div class="mb-10">
                <h2 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight flex items-center gap-3 font-barlow">
                    <i data-lucide="coffee" class="w-7 h-7 text-white/60"></i> OPÇÕES PARA O CAFÉ
                </h2>
                <div class="w-16 h-[2px] bg-white/20 mt-3"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($dadosCafe as $cafe): ?>
                    <article data-produto="<?= htmlspecialchars($cafe->getNome(), ENT_QUOTES, 'UTF-8') ?>" class="bg-[#0b0b0c] border border-white/[0.06] rounded-xl overflow-hidden group hover:border-white/25 transition-all duration-300 flex flex-col scroll-mt-32">
                        <div class="w-full h-56 overflow-hidden relative">
                            <img src="<?= $cafe->getImagemDiretorio() ?>" alt="<?= $cafe->getNome() ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 brightness-[0.90]">
                        </div>
                        <div class="p-6 flex flex-col flex-grow gap-4">
                            <div class="flex items-start justify-between gap-4">
                                <h3 class="text-lg font-bold text-white font-barlow tracking-wide group-hover:text-white transition"><?= $cafe->getNome() ?></h3>
                                <div class="text-lg font-extrabold text-white font-barlow shrink-0"><?= $cafe->getPrecoFormatado() ?></div>
                            </div>
                            <p class="text-xs text-gray-400 leading-relaxed flex-grow"><?= $cafe->getDescricao() ?></p>
                            <button class="w-full py-2.5 text-xs uppercase tracking-wider rounded-lg bg-amber-500 hover:bg-amber-400 text-black font-bold transition">pedir item</button>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
        
        <section id="almoco" class="scroll-mt-32">
            <div class="mb-10">
                <h2 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight flex items-center gap-3 font-barlow">
                    <i data-lucide="utensils" class="w-7 h-7 text-white/60"></i> OPÇÕES PARA O ALMOÇO
                </h2>
                <div class="w-16 h-[2px] bg-white/20 mt-3"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($dadosAlmoco as $almoco): ?>
                    <article data-produto="<?= htmlspecialchars($almoco->getNome(), ENT_QUOTES, 'UTF-8') ?>" class="bg-[#0b0b0c] border border-white/[0.06] rounded-xl overflow-hidden group hover:border-white/25 transition-all duration-300 flex flex-col scroll-mt-32">
                        <div class="w-full h-56 overflow-hidden relative">
                            <img src="<?= $almoco->getImagemDiretorio() ?>" alt="<?= $almoco->getNome() ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 brightness-[0.90]">
                        </div>
                        <div class="p-6 flex flex-col flex-grow gap-4">
                            <div class="flex items-start justify-between gap-4">
                                <h3 class="text-lg font-bold text-white font-barlow tracking-wide group-hover:text-white transition"><?= $almoco->getNome() ?></h3>
                                <div class="text-lg font-extrabold text-white font-barlow shrink-0"><?= $almoco->getPrecoFormatado() ?></div>
                            </div>
                            <p class="text-xs text-gray-400 leading-relaxed flex-grow"><?= $almoco->getDescricao() ?></p>
                            <button class="w-full py-2.5 text-xs uppercase tracking-wider rounded-lg bg-amber-500 hover:bg-amber-400 text-black font-bold transition">pedir item</button>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <!-- ===================================================== -->
    <!-- SEÇÃO DEDICADA DO ASSISTENTE BLACKOUT (full page)       -->
    <!-- ===================================================== -->
    <section id="assistente" class="assistente-section scroll-mt-24 py-24">
        <div class="container mx-auto px-6">

            <div class="max-w-2xl mx-auto text-center mb-12">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/25 text-amber-400 text-xs font-semibold tracking-wider uppercase mb-5">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                    Powered by IA &amp; RAG
                </span>
                <h2 class="text-3xl md:text-5xl font-extrabold text-white tracking-tight font-barlow leading-[1.1] mb-4">
                    Converse com a <span class="text-amber-400">Blackout</span>
                </h2>
                <p class="text-base md:text-lg text-gray-400 leading-relaxed">
                    Nossa inteligência artificial conhece cada item do cardápio. Pergunte sobre grãos, harmonizações ou peça uma recomendação personalizada.
                </p>
            </div>

            <div class="max-w-3xl mx-auto">
                <div class="assistente-conversa flex flex-col overflow-hidden">

                    <div class="flex items-center gap-3 px-6 py-5 border-b border-white/[0.06]">
                    <div class="relative w-10 h-10 shrink-0">
                        <div class="w-10 h-10 rounded-full overflow-hidden border border-amber-500/20">
                            <img src="./img/logo-blackout.png" alt="Blackout" class="w-full h-full object-cover">
                        </div>
                        <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-green-500 ring-2 ring-[#0a0a0a]"></span>
                    </div>
                        <div>
                            <div class="text-sm font-semibold text-white font-barlow tracking-wide">Assistente Blackout</div>
                            <div class="flex items-center gap-1.5 text-xs text-gray-500">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> online agora
                            </div>
                        </div>
                    </div>

                    <div id="chat-messages-full" class="assistente-mensagens flex-1 overflow-y-auto p-6 space-y-5">
                        <div class="flex gap-3 max-w-[90%]">
                            <div class="w-9 h-9 rounded-full overflow-hidden border border-amber-500/20 shrink-0">
                                <img src="./img/logo-blackout.png" alt="Blackout" class="w-full h-full object-cover">
                            </div>
                            <div class="assistente-bolha-bot text-gray-200 p-4 rounded-2xl rounded-tl-none">
                                Olá! Sou o Assistente da Blackout Café. Posso te ajudar a escolher um grão, sugerir harmonizações ou tirar dúvidas sobre o cardápio. O que deseja hoje?
                            </div>
                        </div>
                    </div>

                    <div class="px-6 pb-4 flex flex-wrap gap-2">
                        <button type="button" class="assistente-chip-full assistente-chip text-xs text-gray-300 border border-white/10 rounded-full px-3.5 py-1.5">Qual café vocês recomendam?</button>
                        <button type="button" class="assistente-chip-full assistente-chip text-xs text-gray-300 border border-white/10 rounded-full px-3.5 py-1.5">Tem opção sem lactose?</button>
                        <button type="button" class="assistente-chip-full assistente-chip text-xs text-gray-300 border border-white/10 rounded-full px-3.5 py-1.5">Quero um café forte</button>
                    </div>

                    <div class="p-4 md:p-5 bg-[#0d0d0f] border-t border-white/[0.05]">
                        <form id="chat-form-full" class="flex gap-3 relative items-center">
                            <input 
                                type="text" 
                                id="user-input-full" 
                                placeholder="Pergunte sobre nosso cardápio..." 
                                autocomplete="off"
                                class="assistente-input w-full bg-[#16161a] border border-white/[0.08] rounded-2xl pl-5 pr-14 py-4 focus:outline-none focus:border-amber-500/50 text-gray-200 placeholder-gray-600 transition-all"
                            >
                            <button 
                                type="submit" 
                                class="absolute right-2 bg-amber-500 hover:bg-amber-400 text-black w-10 h-10 rounded-xl flex items-center justify-center transition-colors cursor-pointer"
                            >
                                <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                            </button>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </section>
    
    <footer class="border-t border-white/[0.06] bg-[#070707] py-12 mt-12">
    <div class="container mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-6 text-xs text-gray-500">
        <p class="font-medium font-barlow tracking-widest">&copy; 2026 BLACKOUT CAFÉ. TODOS OS DIREITOS RESERVADOS.</p>
        <div class="flex items-center gap-6">
            <a href="#" class="hover:text-white transition">termos de uso</a>
            <a href="#" class="hover:text-white transition">privacidade</a>
            <a href="#" class="hover:text-white transition">contato</a>
            
            <span class="text-white/[0.06] select-none">|</span>
            <a href="/login.php" class="text-gray-700 hover:text-amber-500 transition flex items-center gap-1 font-medium tracking-wider" title="Área Restrita">
                <span>painel</span>
            </a>
        </div>
    </div>
</footer>
    
    <div class="chat-overlay" id="chatOverlay" onclick="toggleChat()"></div>

    <div class="chat-modal" id="chatModal">
        <div class="chat-modal-header">
            <div class="chat-modal-header-left">
                <div class="relative w-8 h-8 shrink-0">
                    <div class="w-8 h-8 rounded-full overflow-hidden border border-amber-500/20">
                        <img src="./img/logo-blackout.png" alt="Blackout" class="w-full h-full object-cover">
                    </div>
                    <span class="chat-status-dot absolute -bottom-0.5 -right-0.5 ring-2 ring-[#0e0e0e]"></span>
                </div>
                <div>
                    <div class="chat-modal-title">Assistente Blackout</div>
                    <div class="chat-modal-subtitle">Especialista em cafés especiais</div>
                </div>
            </div>
            <button class="chat-modal-close" onclick="toggleChat()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12" /></svg>
            </button>
        </div>
        
        <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar bg-[#09090a]">
            <div class="flex gap-2.5 max-w-[85%]">
                <div class="w-7 h-7 rounded-full overflow-hidden border border-amber-500/20 shrink-0">
                    <img src="./img/logo-blackout.png" alt="Blackout" class="w-full h-full object-cover">
                </div>
                <div class="bg-[#121214] border border-white/[0.04] p-3 rounded-2xl rounded-tl-none text-xs text-gray-300 leading-relaxed">
                    Olá! Sou o Assistente da Blackout Café. Posso te ajudar a escolher um grão, sugerir harmonizações ou tirar dúvidas sobre o cardápio. O que deseja hoje?
                </div>
            </div>
        </div>

        <div class="p-3 bg-[#0d0d0f] border-t border-white/[0.05]">
            <form id="chat-form" class="flex gap-2 relative items-center">
                <input 
                    type="text" 
                    id="user-input" 
                    placeholder="Pergunte sobre nosso cardápio..." 
                    autocomplete="off"
                    class="w-full bg-[#16161a] border border-white/[0.06] rounded-xl pl-4 pr-10 py-2.5 text-xs focus:outline-none focus:border-amber-500/50 text-gray-200 placeholder-gray-600 transition-all"
                >
                <button 
                    type="submit" 
                    class="absolute right-1.5 bg-amber-500 hover:bg-amber-400 text-black w-7 h-7 rounded-lg flex items-center justify-center transition-colors cursor-pointer"
                >
                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                </button>
            </form>
        </div>
    </div>

    <button class="chat-fab" id="chatFab" onclick="toggleChat()" aria-label="Abrir assistente">
        <svg class="icon-chat" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
        </svg>
        <svg class="icon-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 6L6 18M6 6l12 12" />
        </svg>
    </button>
    
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>

    <script>
        let isOpen = false;

        function toggleChat() {
            isOpen = !isOpen;
            const modal = document.getElementById("chatModal");
            const overlay = document.getElementById("chatOverlay");
            const fab = document.getElementById("chatFab");

            if (isOpen) {
                modal.classList.add("visible");
                overlay.classList.add("visible");
                fab.classList.add("open");
                document.getElementById("user-input").focus();
            } else {
                modal.classList.remove("visible");
                overlay.classList.remove("visible");
                fab.classList.remove("open");
            }
        }

        /**
         * Fábrica de instâncias de chat. Permite ter múltiplos chats na
         * mesma página (o widget flutuante + a seção dedicada) sem
         * duplicar lógica nem conflitar IDs.
         *
         * @param {Object} cfg
         * @param {string} cfg.formId
         * @param {string} cfg.inputId
         * @param {string} cfg.messagesId
         * @param {string} cfg.userBubbleClass   classes extras na bolha do usuário
         * @param {string} cfg.botBubbleClass    classes extras na bolha do bot
         * @param {boolean} cfg.compact          usa o estilo compacto (widget) ou o estilo grande (seção)
         */
        function criarChatInstance(cfg) {
            const form = document.getElementById(cfg.formId);
            const input = document.getElementById(cfg.inputId);
            const messagesContainer = document.getElementById(cfg.messagesId);

            if (!form || !input || !messagesContainer) return;

            function scrollToBottom() {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }

            function appendMessage(sender, text) {
                const wrapper = document.createElement("div");

                if (cfg.compact) {
                    wrapper.classList.add("flex", "gap-2.5", "max-w-[85%]");
                    if (sender === "user") {
                        wrapper.classList.add("ml-auto", "flex-row-reverse");
                        wrapper.innerHTML = `
                            <div class="w-7 h-7 rounded-full bg-gray-800 border border-white/[0.05] text-gray-300 flex items-center justify-center shrink-0 text-[10px] font-bold">U</div>
                            <div class="bg-amber-500 text-black p-3 rounded-2xl rounded-tr-none text-xs font-medium leading-relaxed shadow-lg shadow-amber-500/5">
                                ${text}
                            </div>
                        `;
                    } else {
                        wrapper.innerHTML = `
                            <div class="w-7 h-7 rounded-full overflow-hidden border border-amber-500/20 shrink-0">
                                <img src="./img/logo-blackout.png" alt="Blackout" class="w-full h-full object-cover">
                            </div>
                            <div class="bg-[#121214] border border-white/[0.04] p-3 rounded-2xl rounded-tl-none text-xs text-gray-300 leading-relaxed">
                                ${text}
                            </div>
                        `;
                    }
                } else {
                    wrapper.classList.add("flex", "gap-3", "max-w-[90%]");
                    if (sender === "user") {
                        wrapper.classList.add("ml-auto", "flex-row-reverse");
                        wrapper.innerHTML = `
                            <div class="w-9 h-9 rounded-full bg-gray-800 border border-white/[0.05] text-gray-300 flex items-center justify-center shrink-0 text-xs font-bold">U</div>
                            <div class="assistente-bolha-user text-black p-4 rounded-2xl rounded-tr-none font-medium">
                                ${text}
                            </div>
                        `;
                    } else {
                        wrapper.innerHTML = `
                            <div class="w-9 h-9 rounded-full overflow-hidden border border-amber-500/20 shrink-0">
                                <img src="./img/logo-blackout.png" alt="Blackout" class="w-full h-full object-cover">
                            </div>
                            <div class="assistente-bolha-bot text-gray-200 p-4 rounded-2xl rounded-tl-none">
                                ${text}
                            </div>
                        `;
                    }
                }

                messagesContainer.appendChild(wrapper);
                scrollToBottom();
            }

            function showTypingIndicator() {
                const indicator = document.createElement("div");
                indicator.classList.add(cfg.typingId);
                indicator.classList.add("flex", "gap-2.5", cfg.compact ? "max-w-[85%]" : "max-w-[90%]");
                const avatarSize = cfg.compact ? "w-7 h-7" : "w-9 h-9";
                const bubbleClass = cfg.compact
                    ? "bg-[#121214] border border-white/[0.04] px-4 py-3 rounded-2xl rounded-tl-none"
                    : "assistente-bolha-bot px-5 py-4 rounded-2xl rounded-tl-none";
                indicator.innerHTML = `
                    <div class="${avatarSize} rounded-full overflow-hidden border border-amber-500/20 shrink-0">
                        <img src="./img/logo-blackout.png" alt="Blackout" class="w-full h-full object-cover">
                    </div>
                    <div class="${bubbleClass} flex items-center gap-1">
                        <span class="w-1.5 h-1.5 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                        <span class="w-1.5 h-1.5 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                        <span class="w-1.5 h-1.5 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                    </div>
                `;
                messagesContainer.appendChild(indicator);
                scrollToBottom();
                return indicator;
            }

            function scrollAteProduto(nome) {
                const alvo = document.querySelector(`[data-produto="${CSS.escape(nome)}"]`);
                if (!alvo) return;
                alvo.scrollIntoView({ behavior: "smooth", block: "center" });
                alvo.classList.add("produto-destacado");
                setTimeout(() => alvo.classList.remove("produto-destacado"), 1200);
            }

            function appendProductCards(produtos) {
                if (!produtos || produtos.length === 0) return;

                const offset = cfg.compact ? "pl-[38px]" : "pl-[48px]";
                const wrapper = document.createElement("div");
                wrapper.className = offset;

                const row = document.createElement("div");
                row.className = "produto-card-row";

                produtos.forEach((produto) => {
                    const card = document.createElement("div");
                    card.className = "produto-card-chat";
                    card.innerHTML = `
                        <img src="${produto.imagem}" alt="${produto.nome}">
                        <div class="p-2.5">
                            <div class="text-[11px] font-semibold text-white leading-snug line-clamp-2">${produto.nome}</div>
                            <div class="text-xs font-bold text-amber-400 mt-1">${produto.preco}</div>
                        </div>
                    `;
                    card.addEventListener("click", () => scrollAteProduto(produto.nome));
                    row.appendChild(card);
                });

                wrapper.appendChild(row);
                messagesContainer.appendChild(wrapper);
                scrollToBottom();
            }

            async function enviarMensagem(text) {
                if (!text) return;

                input.value = "";
                appendMessage("user", text);
                const indicator = showTypingIndicator();

                try {
                    const response = await fetch("src/Controlador/chat-bridge.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ message: text })
                    });

                    const data = await response.json();
                    indicator.remove();

                    if (data.success) {
                        appendMessage("bot", data.response);
                        appendProductCards(data.produtos);
                    } else {
                        appendMessage("bot", "Houve um imprevisto técnico ao processar sua resposta. Pode tentar novamente?");
                    }
                } catch (error) {
                    indicator.remove();
                    appendMessage("bot", "Estou com dificuldades para me conectar ao servidor de café. Verifique sua conexão.");
                    console.error("Erro no chat:", error);
                }
            }

            form.addEventListener("submit", (e) => {
                e.preventDefault();
                enviarMensagem(input.value.trim());
            });

            return { enviarMensagem };
        }

        // Instância do widget flutuante (compacto)
        criarChatInstance({
            formId: "chat-form",
            inputId: "user-input",
            messagesId: "chat-messages",
            compact: true,
            typingId: "typing-indicator-widget"
        });

        // Instância da seção dedicada (estilo grande)
        const chatSecaoCompleta = criarChatInstance({
            formId: "chat-form-full",
            inputId: "user-input-full",
            messagesId: "chat-messages-full",
            compact: false,
            typingId: "typing-indicator-full"
        });

        // Chips de sugestão de pergunta rápida, na seção dedicada
        document.querySelectorAll(".assistente-chip-full").forEach((chip) => {
            chip.addEventListener("click", () => {
                if (chatSecaoCompleta) {
                    chatSecaoCompleta.enviarMensagem(chip.textContent.trim());
                }
            });
        });
    </script>
</body>
</html>