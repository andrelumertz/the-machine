<?php
// Página pública - Não requer sessão ativa de administrador
?>

<!doctype html>
<html lang="pt-br" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nós - Blackout Café</title>
    
    <!-- Tailwind CSS via CDN -->
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
    
    <!-- Fontes do Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&family=Barlow:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="img/logo-Blackout.png" type="image/x-icon">

    <!-- Estilo customizado para o seu Botão do Chatbot (Mantendo o seu botão idêntico) -->
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
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.04);
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }
        .chat-fab:hover {
            transform: scale(1.08);
            background: #1a1a1a;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.08);
        }
        .chat-fab svg {
            width: 22px;
            height: 22px;
            color: #e8e8e8;
        }
        .chat-fab .icon-close {
            display: none;
        }
        .chat-fab.open .icon-chat {
            display: none;
        }
        .chat-fab.open .icon-close {
            display: block;
        }
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
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.04), inset 0 1px 0 rgba(255, 255, 255, 0.06);
            transform: translateY(16px) scale(0.97);
            opacity: 0;
            pointer-events: none;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .chat-modal.visible {
            transform: translateY(0) scale(1);
            opacity: 1;
            pointer-events: all;
        }
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
        .chat-modal-close:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        .chat-modal-close svg {
            width: 14px;
            height: 14px;
            color: rgba(255, 255, 255, 0.5);
        }
        .chat-iframe-wrapper {
            flex: 1;
            overflow: hidden;
            position: relative;
            background-color: #0a0a0a;
        }
        .chat-iframe-wrapper iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }
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
    </style>
</head>
<body class="bg-black text-gray-200 font-sans selection:bg-gray-800 antialiased min-h-screen flex flex-col justify-between relative overflow-x-hidden">

    <!-- Brilho de fundo decorativo -->
    <div class="absolute top-[10%] left-1/4 w-[600px] h-[600px] bg-amber-500/5 rounded-full blur-[150px] pointer-events-none z-0"></div>
    <div class="absolute bottom-[20%] right-1/4 w-[500px] h-[500px] bg-neutral-800/10 rounded-full blur-[150px] pointer-events-none z-0"></div>

    <!-- Cabeçalho Fixo (Idêntico ao index.php) -->
    <header class="border-b border-neutral-900 bg-black/90 backdrop-blur sticky top-0 z-50">
        <nav class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="index.php" class="text-xl font-bold tracking-wider text-white">BLACKOUT CAFÉ</a>
            <div class="flex items-center gap-1">
                <a href="index.php" class="px-4 py-2 text-sm rounded-full text-gray-400 hover:text-white transition">Cardápio</a>
                <a href="sobre.php" class="px-4 py-2 text-sm rounded-full bg-neutral-900 text-white font-medium hover:bg-neutral-800 transition">Sobre</a>
                <a href="index.php#cafe" class="px-4 py-2 text-sm rounded-full text-gray-400 hover:text-white transition">Cafés</a>
            </div>
            <a href="index.php" class="px-5 py-2.5 text-sm rounded-full bg-white text-black font-semibold hover:bg-neutral-200 transition">Pedir Agora</a>
        </nav>
    </header>

    <!-- Conteúdo Principal -->
    <main class="relative z-10 flex-grow">

        <!-- Seção Hero: História e Introdução -->
        <section class="container mx-auto px-6 py-20 lg:py-32 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            
            <!-- Lado Esquerdo: Mensagem e Posicionamento -->
            <div class="space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 text-[10px] tracking-widest rounded-full bg-neutral-900 border border-neutral-800 text-amber-500 font-bold uppercase">
                    <i data-lucide="coffee" class="w-3.5 h-3.5"></i>
                    Desde 2024
                </div>
                <h1 class="text-4xl lg:text-5xl font-extrabold tracking-tight text-white leading-tight">
                    Um manifesto contra o ritmo acelerado das grandes cidades.
                </h1>
                <p class="text-neutral-400 text-lg leading-relaxed">
                    O Blackout Café surgiu de um desejo simples: criar um refúgio físico e mental no coração de Porto Alegre, onde as pessoas pudessem desacelerar, saborear o presente e experimentar o café de forma pura e meditativa.
                </p>
                <div class="h-px bg-neutral-900 w-1/2"></div>
                <div class="flex items-center gap-4 text-sm font-semibold font-barlow text-white">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    Grãos Estritamente Selecionados de Origem Única.
                </div>
            </div>

            <!-- Lado Direito: Foto Conceitual Minimalista -->
            <div class="relative group">
                <div class="absolute -inset-1 rounded-3xl bg-gradient-to-r from-amber-500/10 to-transparent blur-md group-hover:opacity-100 transition duration-500"></div>
                <div class="relative aspect-[4/3] rounded-3xl overflow-hidden border border-neutral-800 bg-neutral-950">
                    <img 
                        src="https://images.unsplash.com/photo-1763792273041-e85a12ab450f?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" 
                        alt="Nossos Baristas no balcão artesanal" 
                        class="w-full h-full object-cover grayscale opacity-90 group-hover:scale-105 group-hover:grayscale-0 transition duration-500"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent"></div>
                    <div class="absolute bottom-6 left-6 right-6">
                        <span class="text-[10px] uppercase tracking-widest text-neutral-500 font-bold font-barlow">O ambiente</span>
                        <h3 class="text-lg font-bold text-white mt-1">Um local seguro para todos</h3>
                    </div>
                </div>
            </div>

        </section>

        <!-- Seção: Nossos Três Pilares -->
        <section class="border-t border-neutral-900 bg-black/40 py-24">
            <div class="container mx-auto px-6">
                
                <!-- Título da Seção -->
                <div class="text-center max-w-xl mx-auto mb-20 space-y-3">
                    <h2 class="text-xs uppercase tracking-widest text-amber-500 font-bold">Nossa Filosofia</h2>
                    <h3 class="text-3xl font-extrabold text-white">Como criamos a xícara ideal</h3>
                    <p class="text-sm text-neutral-500 leading-relaxed">
                        Controlamos cuidadosamente cada elo da cadeia produtiva do café especial, trazendo transparência, ética e sabor refinado.
                    </p>
                </div>

                <!-- Cards dos Pilares -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    <!-- Pilar 1 -->
                    <div class="bg-neutral-950 border border-neutral-900 p-8 rounded-3xl space-y-6">
                        <div class="w-12 h-12 rounded-2xl bg-neutral-900 border border-neutral-800 flex items-center justify-center text-amber-500">
                            <i data-lucide="leaf" class="w-6 h-6"></i>
                        </div>
                        <h4 class="text-lg font-bold text-white font-barlow tracking-wide">1. ORIGEM RASTREÁVEL</h4>
                        <p class="text-neutral-400 text-sm leading-relaxed font-light">
                            Trabalhamos diretamente com pequenos produtores brasileiros de cafés especiais. Cada lote é colhido manualmente e possui rastreabilidade 100% garantida desde a lavoura.
                        </p>
                    </div>

                    <!-- Pilar 2 -->
                    <div class="bg-neutral-950 border border-neutral-900 p-8 rounded-3xl space-y-6">
                        <div class="w-12 h-12 rounded-2xl bg-neutral-900 border border-neutral-800 flex items-center justify-center text-amber-500">
                            <i data-lucide="flame" class="w-6 h-6"></i>
                        </div>
                        <h4 class="text-lg font-bold text-white font-barlow tracking-wide">2. TORRA CONTROLADA</h4>
                        <p class="text-neutral-400 text-sm leading-relaxed font-light">
                            Nossa torra é artesanal e precisa. Estudamos e desenhamos cada curva de temperatura para extrair o máximo do potencial sensorial do grão, preservando notas florais e frutadas.
                        </p>
                    </div>

                    <!-- Pilar 3 -->
                    <div class="bg-neutral-950 border border-neutral-900 p-8 rounded-3xl space-y-6">
                        <div class="w-12 h-12 rounded-2xl bg-neutral-900 border border-neutral-800 flex items-center justify-center text-amber-500">
                            <i data-lucide="home" class="w-6 h-6"></i>
                        </div>
                        <h4 class="text-lg font-bold text-white font-barlow tracking-wide">3. O ESPAÇO FÍSICO</h4>
                        <p class="text-neutral-400 text-sm leading-relaxed font-light">
                            Nossa cafeteria foi desenhada sob preceitos minimalistas. Isolamento acústico, luz suave e sem sinalizadores de urgência. O lugar perfeito para o seu foco ou descanso.
                        </p>
                    </div>

                </div>

            </div>
        </section>

        <!-- Seção: Frase de Efeito (Callout Estético) -->
        <section class="py-24 text-center max-w-4xl mx-auto px-6 space-y-8">
            <i data-lucide="quote" class="w-10 h-10 text-neutral-800 mx-auto"></i>
            <blockquote class="text-2xl md:text-3xl font-light italic leading-relaxed text-neutral-300">
                "Não vendemos apenas cafeína líquida para acelerar sua jornada. Oferecemos o ritual necessário para você se reconectar com o que realmente importa."
            </blockquote>
            <p class="text-xs uppercase tracking-widest text-amber-500 font-bold font-barlow">— Equipe Blackout Café, POA</p>
        </section>

    </main>

    <!-- Rodapé Estético (Idêntico ao index.php) -->
    <footer class="border-t border-neutral-900 bg-black mt-10">
        <div class="container mx-auto px-6 py-12 flex flex-col md:flex-row items-center justify-between gap-6 text-xs text-neutral-500">
            <p>&copy; 2026 Blackout Café. Todos os direitos reservados.</p>
            <div class="flex items-center gap-8">
                <a href="#" class="hover:text-white transition">Termos de Serviço</a>
                <a href="#" class="hover:text-white transition">Política de Privacidade</a>
                <a href="index.php" class="hover:text-white transition">Ver Cardápio</a>
            </div>
        </div>
    </footer>

    <!-- CHATBOT INTERATIVO COMPLETO -->
    <!-- Overlay do Chat -->
    <div class="chat-overlay" id="chatOverlay" onclick="toggleChat()"></div>

    <!-- Modal do Chatbot -->
    <div class="chat-modal" id="chatModal">
        <div class="chat-modal-header">
            <div class="chat-modal-header-left">
                <div class="chat-status-dot"></div>
                <div>
                    <div class="chat-modal-title">Assistente Blackout</div>
                    <div class="chat-modal-subtitle">Especialista em cafés especiais</div>
                </div>
            </div>
            <button class="chat-modal-close" onclick="toggleChat()" aria-label="Fechar chat">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6L6 18M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="chat-iframe-wrapper">
            <iframe 
                id="chatIframe" 
                src="" 
                data-src="https://decolumertz-chatbot-serenatto.hf.space?__theme=dark" 
                allow="microphone"
            ></iframe>
        </div>
    </div>

    <!-- Botão Flutuante (FAB) -->
    <button class="chat-fab" id="chatFab" onclick="toggleChat()" aria-label="Abrir assistente">
        <!-- Ícone Balão de Conversa (Chat) -->
        <svg class="icon-chat" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
        </svg>
        <!-- Ícone Fechar (X) -->
        <svg class="icon-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 6L6 18M6 6l12 12" />
        </svg>
    </button>

    <!-- Scripts do Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>

    <!-- Script de toggle do Chatbot -->
    <script>
        let isOpen = false;
        let iframeLoaded = false;

        function toggleChat() {
            isOpen = !isOpen;
            const modal = document.getElementById("chatModal");
            const overlay = document.getElementById("chatOverlay");
            const fab = document.getElementById("chatFab");
            const iframe = document.getElementById("chatIframe");

            if (isOpen) {
                if (!iframeLoaded) {
                    iframe.src = iframe.dataset.src;
                    iframeLoaded = true;
                }
                modal.classList.add("visible");
                overlay.classList.add("visible");
                fab.classList.add("open");
            } else {
                modal.classList.remove("visible");
                overlay.classList.remove("visible");
                fab.classList.remove("open");
            }
        }
    </script>
</body>
</html>