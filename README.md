# ☕ The Machine — Blackout Gerenciamento de Sistema & IA

> **Trabalho de Conclusão de Curso (TCC)**
> Curso: Análise e Desenvolvimento de Sistemas (ADS)
> Desenvolvedor: André Lumertz Martins

O **The Machine** é um ecossistema digital integrado desenvolvido para o **Blackout Café** (Porto Alegre/RS). A aplicação une um sistema de gestão administrativa tradicional (CRUD de produtos e relatórios) a um motor de Inteligência Artificial Generativa baseado em **RAG (Retrieval-Augmented Generation)**, atuando como um atendente especializado no cardápio e em cafés especiais.

---

## 🎯 Sobre o Projeto

Este projeto demonstra a viabilidade de integrar sistemas comerciais em **PHP** com serviços modernos de IA em **Python (FastAPI)** dentro de um único ambiente conteinerizado, otimizando o uso de recursos em nuvem e oferecendo inferência de baixíssima latência para o usuário final.

---

## 🚀 Funcionalidades Principais

* **☕ Sommelier de Café (IA):** Chatbot especialista integrado que consome o PDF do cardápio técnico para tirar dúvidas, sugerir harmonizações e métodos de preparo.
* **🛡️ Filtro de Intenções Furtivo:** Camada de segurança no front-end e back-end que bloqueia escopos fora de contexto (programação, política) e redireciona automaticamente intenções de compra/pagamento para canais humanos (WhatsApp/E-mail).
* **💻 Painel Administrativo (Dashboard):** Sistema completo para gerenciamento da operação da cafeteria e controle de produtos (CRUD) com tratamento flexível de tipagem nula para ativos visuais.
* **📄 Relatórios Gerenciais:** Módulo de exportação de dados administrativos estruturados em formato PDF via Composer e biblioteca Dompdf.
* **⚡ Inferência em Tempo Real:** Motor de IA alimentado pela infraestrutura da Groq LPU, respondendo em milissegundos.

---

## 🛠️ Tecnologias e Arquitetura

### Core & Painel Administrativo

* **PHP 8.2 & MySQL:** Lógica de negócio, sessão de segurança do administrador e persistência dos dados dos produtos. Conta com um script de conexão inteligente (Environment-Aware) que alterna automaticamente entre o ambiente de nuvem (SSL) e o local.
* **TiDB Cloud:** Banco de dados em nuvem compatível com o protocolo MySQL, utilizado como banco de produção (ver seção [Hospedagem e Deploy](#-hospedagem-e-deploy)).
* **Tailwind CSS:** Interface responsiva com estética moderna, minimalista e *dark mode*.
* **Composer:** Gerenciamento de dependências e bibliotecas para geração dinâmica de arquivos PDF.

### Inteligência Artificial & RAG Otimizado

* **FastAPI & Uvicorn (Python 3):** Microserviço assíncrono responsável por expor a API do Chatbot na porta `8001`.
* **LlamaIndex Core:** Framework de orquestração do RAG. Configurado de forma eficiente para processar a base de conhecimento do PDF e gerar o contexto relevante de resposta.
* **Embeddings Reais:** Geração de vetores semânticos precisos para mapear o cardápio e as propriedades dos cafés, garantindo que a IA compreenda exatamente as nuances de cada método de preparo.
* **Groq LPU (Llama 3.3 70B):** Processamento cognitivo em nuvem por meio de chaves de API, garantindo performance extrema e respostas em milissegundos.

---

## 📁 Estrutura do Projeto

Abaixo está a organização dos arquivos e diretórios do ecossistema:

```text
THE_MACHINE/
├── css/                  # Estilizações globais da aplicação (Tailwind)
├── documents/            # Base de conhecimento do RAG
│   └── blackout_cafes_especiais.pdf  # PDF técnico consumido pela IA
├── img/                  # Ativos visuais (logos, mídias e backgrounds)
├── js/                   # Scripts de interatividade do front-end
├── src/
│   ├── Controlador/      # Pontes de integração (ex: chat-bridge.php)
│   ├── Modelo/           # Lógica de manipulação de dados e tipagem flexível
│   └── conexao-bd.php    # Configuração de persistência Híbrida (Nuvem/Local)
├── admin.php             # Dashboard principal do administrador
├── app.py                # Microserviço em Python (FastAPI + LlamaIndex)
├── cadastrar-produto.php # Módulo de inserção do CRUD
├── composer.json         # Dependências PHP (Dompdf)
├── conteudo-pdf.php      # Estruturação de dados para exportação
├── Dockerfile            # Receita de build híbrido (Python + PHP + Composer)
├── editar-produto.php    # Módulo de atualização do CRUD
├── excluir-produto.php   # Módulo de remoção do CRUD
├── gerador-pdf.php       # Script de disparo e download de relatórios
├── index.php             # Página institucional e interface do Chatbot
├── login.php             # Tela de autenticação restrita do painel
├── logout.php            # Encerramento seguro de sessão do admin
└── requirements.txt      # Dependências de IA para o ambiente Python
```

---

## ☁️ Hospedagem e Deploy

O sistema The Machine utiliza uma arquitetura moderna e otimizada para a nuvem. O banco de dados escolhido para o projeto foi o MySQL.

Para viabilizar a hospedagem 100% gratuita em produção, a aplicação foi dividida: o servidor web (PHP) e o motor de inteligência artificial (Python) ficam hospedados na plataforma **Render**. Como o Render não possui banco de dados MySQL nativo em seu plano gratuito, foi integrado o **TiDB Cloud**.

O TiDB Cloud é um banco de dados em nuvem de última geração que possui compatibilidade total (nativa) com o protocolo do MySQL.

A estratégia de desenvolvimento segue o padrão de mercado:

* **Ambiente de Desenvolvimento (Local):** É utilizado um banco MySQL local (XAMPP na porta 3307). O código identifica a ausência de variáveis de ambiente e se conecta sem SSL.
* **Ambiente de Produção (Nuvem/Online):** A aplicação do Render lê as variáveis de ambiente e conecta-se remotamente ao TiDB Cloud via porta 4000 exigindo certificado SSL, garantindo segurança total.

| Camada | Ambiente Local (Dev) | Ambiente de Produção |
|---|---|---|
| **Servidor Web (PHP)** | `php -S localhost:8000` | Render (Container Docker) |
| **Motor de IA (Python)** | `python app.py` (porta 8001) | Render (Microserviço Interno) |
| **Banco de Dados** | XAMPP (Porta 3307) | TiDB Cloud (Porta 4000 com SSL) |

---

## 📸 Demonstração do Sistema

**Interface do Chatbot Especialista (Dark Mode)**

O assistente integrado utiliza processamento cognitivo em nuvem para responder sobre grãos e métodos do cardápio em tempo real.

![Demonstração do Assistente Blackout respondendo sobre cafés fortes](./img/chat-assistente-blackout.png)

---

## 🔧 Como Executar o Projeto em Casa (Localhost)

Para rodar este projeto localmente, você pode escolher o método manual instalando os runtimes na sua máquina ou utilizar o Docker.

### 📋 Pré-requisitos

* **Chave de API da Groq:** Crie uma conta gratuita no Groq Cloud Console e gere uma API Key.
* Ambiente local com **PHP 8.2+**, **Composer** e **Python 3.10+** instalado **OU** o **Docker** configurado.
* **XAMPP/Servidor MySQL** (Configurado na porta 3307).

### 🚀 Método 1: Execução Manual (Sem Docker)

**Passo 1: Configurar a Inteligência Artificial (Python)**

Abra o terminal na raiz do projeto e instale as dependências da IA:

```bash
pip install -r requirements.txt
```

Crie um arquivo chamado `.env` na raiz do projeto e insira a sua chave da Groq:

```plaintext
GROQ_API_KEY=sua_chave_da_groq_aqui
```

Inicie o servidor do motor de IA:

```bash
python app.py
```

A API do chatbot estará rodando e aguardando requisições em `http://127.0.0.1:8001`.

**Passo 2: Configurar o Banco de Dados (MySQL / XAMPP)**

1. Inicie o serviço MySQL no XAMPP na porta 3307.
2. Utilize um cliente de banco de dados (ex: Beekeeper Studio) e crie um banco chamado `blackout_cafe`.
3. Importe a estrutura de tabelas ou rode o script de criação da tabela `produtos`.

**Passo 3: Configurar o Servidor Web (PHP)**

Instale as dependências de back-end (gerador de relatórios) utilizando o Composer:

```bash
composer install
```

Inicie o servidor embutido do PHP apontando para a raiz do projeto (abra um novo terminal para manter o processo do Python ativo):

```bash
php -S localhost:8000
```

Acesse `http://localhost:8000` no seu navegador para interagir com o site administrativo e o gerador de relatórios em PDF.

### 🐳 Método 2: Execução via Docker (Ambiente Completo)

O projeto já conta com um `Dockerfile` híbrido configurado para orquestrar o PHP, a instalação automática do Composer e a execução do Uvicorn (Python) simultaneamente.

Construa a imagem do contêiner local:

```bash
docker build -t the-machine .
```

Execute o contêiner passando a sua chave da Groq como variável de ambiente:

```bash
docker run -d -p 80:80 -e GROQ_API_KEY="sua_chave_da_groq_aqui" --name the-machine-app the-machine
```

Acesse `http://localhost` no seu navegador e o sistema estará operacional.
