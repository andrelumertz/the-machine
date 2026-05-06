<?php

    require "src/conexao-bd.php";
    require "src/Modelo/Produto.php";
    require "src/Modelo/Repositorio/produtoRepositorio.php";

    $produtosRepositorio = new produtoRepositorio($pdo);
    $dadosCafe = $produtosRepositorio->opcoesCafe();
    $dadosAlmoco = $produtosRepositorio->opcoesAlmoco();

    
?>



<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="icon" href="img/logo-Blackout.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>Blackout - Cardápio</title>

    <style>
    /* ========== CHATBOT FLUTUANTE ========== */
      /* Botão flutuante */
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
      .chat-fab .icon-close {
        display: none;
      }
      .chat-fab.open .icon-chat {
        display: none;
      }
      .chat-fab.open .icon-close {
        display: block;
      }

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
      .chat-fab:hover::before {
        opacity: 1;
      }

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

      /* Modal do chat */
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
        0%,
        100% {
          opacity: 1;
        }
        50% {
          opacity: 0.5;
        }
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

      /* iframe container */
      .chat-iframe-wrapper {
        flex: 1;
        overflow: hidden;
        position: relative;
        background-color: #0a0a0a; /* Fundo preto para não dar "flash" branco */
      }
      .chat-iframe-wrapper iframe {
        width: 100%;
        height: 100%;
        border: none;
        display: block;
      }

      /* Responsivo mobile */
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
<body>
    <main>
        <section class="container-banner">
            <div class="container-texto-banner">
                <img src="img/logo-blackout-horizontal.jpg" class="logo" alt="logo-Blackout">
            </div>
        </section>
        <h2>Cardápio Digital</h2>
        <section class="container-cafe-manha">
            <div class="container-cafe-manha-titulo">
                <h3>Opções para o Café</h3>
                <img class= "ornaments" src="img/ornaments-coffee.png" alt="ornaments">
            </div>
            <div class="container-cafe-manha-produtos">
                <?php foreach ($dadosCafe as $cafe):?>
                    <div class="container-produto">
                        <div class="container-foto">
                            <img src="<?= $cafe->getImagemDiretorio() ?>">
                        </div>
                        <p><?= $cafe->getNome() ?></p>
                        <p><?= $cafe->getDescricao() ?></p>
                        <p><?= $cafe->getPrecoFormatado()  ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <section class="container-almoco">
            <div class="container-almoco-titulo">
                <h3>Opções para o Almoço</h3>
                <img class= "ornaments" src="img/ornaments-coffee.png" alt="ornaments">
            </div>
            <div class="container-almoco-produtos">
                <?php foreach ($dadosAlmoco as $almoco):?>
                    <div class="container-produto">
                        <div class="container-foto">
                            <img src="<?= $almoco->getImagemDiretorio() ?>">
                        </div>
                        <p><?= $almoco->getNome() ?></p>
                        <p><?= $almoco->getDescricao()  ?></p>
                        <p><?= $almoco->getPrecoFormatado() ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

        </section>
    </main>
    <!-- Overlay -->
    <div class="chat-overlay" id="chatOverlay" onclick="toggleChat()"></div>

    <!-- Modal do Chatbot -->
    <div class="chat-modal" id="chatModal">
      <div class="chat-modal-header">
        <div class="chat-modal-header-left">
          <div class="chat-status-dot"></div>
          <div>
            <div class="chat-modal-title">Assistente Blackout</div>
            <div class="chat-modal-subtitle">
              Especialista em cafés especiais
            </div>
          </div>
        </div>
        <button class="chat-modal-close" onclick="toggleChat()">
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
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

    <!-- Botão flutuante -->
    <button
      class="chat-fab"
      id="chatFab"
      onclick="toggleChat()"
      aria-label="Abrir assistente"
    >
      <!-- Ícone chat -->
      <svg
        class="icon-chat"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.75"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <path
          d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"
        />
      </svg>
      <!-- Ícone fechar -->
      <svg
        class="icon-close"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.75"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <path d="M18 6L6 18M6 6l12 12" />
      </svg>
    </button>

    <script>
      let isOpen = false;

      function toggleChat() {
        isOpen = !isOpen;
        const modal = document.getElementById("chatModal");
        const overlay = document.getElementById("chatOverlay");
        const fab = document.getElementById("chatFab");
        const iframe = document.getElementById("chatIframe");

        if (isOpen) {
          // Lazy load do iframe apenas quando abrir pela primeira vez
          if (!iframe.src || iframe.src === window.location.href) {
            iframe.src = iframe.dataset.src;
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