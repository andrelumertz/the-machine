# ☕ Chatbot Blackout Coffee

> Atendente inteligente 24h especializado em cafés especiais, utilizando arquitetura RAG para consultas precisas e alta performance com Groq LPU.

[![Railway](https://img.shields.io/badge/Railway-0b0d0e?style=for-the-badge&logo=railway&logoColor=white)](https://railway.app)
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Composer](https://img.shields.io/badge/Composer-885630?style=for-the-badge&logo=composer&logoColor=white)](https://getcomposer.org/)
[![Groq](https://img.shields.io/badge/Groq-f3d03e?style=for-the-badge&logo=cachet&logoColor=black)](https://groq.com)

## 🎯 Sobre o Projeto

Este projeto é o **Trabalho de Conclusão de Curso (TCC)** para o curso de Análise e Desenvolvimento de Sistemas (ADS). O foco é a criação de um ecossistema digital para a **Blackout Coffee**, unindo gestão administrativa e inteligência artificial generativa.

O sistema permite a gestão de produtos via painel administrativo, geração de documentos gerenciais e oferece um chatbot especialista que utiliza **RAG (Retrieval-Augmented Generation)**.

## 🚀 Funcionalidades Principais

- [x] **Sommelier de Café:** Consultas técnicas sobre grãos e métodos via IA.
- [x] **Painel Administrativo:** Gestão completa da operação da cafeteria.
- [x] **Relatórios em PDF:** Exportação de dados administrativos para relatórios em PDF (via Composer).
- [x] **Alta Performance:** Respostas em tempo real utilizando Groq LPU.

## 🛠️ Tecnologias e Arquitetura

### **Core & Admin**
* **PHP & MySQL:** Lógica de negócio e persistência de dados.
* **Composer:** Gerenciamento de dependências (Dompdf/FPDF para relatórios).
* **Railway:** Hospedagem da aplicação e do banco de dados.

### **Inteligência Artificial**
* **Groq LPU:** Inferência de baixíssima latência.
* **RAG:** Base de conhecimento alimentada via PDF técnico.
