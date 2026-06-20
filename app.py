import nest_asyncio
nest_asyncio.apply()

from dotenv import load_dotenv
load_dotenv()

from fastapi.middleware.cors import CORSMiddleware
import os
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import uvicorn

# LlamaIndex Core imports
from llama_index.core import VectorStoreIndex, SimpleDirectoryReader, Settings
from llama_index.core.node_parser import SentenceSplitter
from llama_index.llms.groq import Groq

# Embedding REAL e leve via API de Inferência do Hugging Face
from llama_index.embeddings.huggingface_api import HuggingFaceInferenceAPIEmbedding

import logging
logging.basicConfig(level=logging.INFO)

# 1. Framework API
app = FastAPI(title="Blackout Coffee AI Engine", version="1.0")

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"], 
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# 2. Modelos Pydantic
class ChatRequest(BaseModel):
    message: str

class ChatResponse(BaseModel):
    success: bool
    response: str

# 2.1 Filtro de intenção de pedido/pagamento
EXPRESSOES_PEDIDO = [
    "fechar pedido", "fechar a compra", "fechar minha compra",
    "finalizar pedido", "finalizar compra", "finalizar a compra",
    "confirmar pedido", "confirmar compra", "confirmar pagamento",
    "quero pagar", "posso pagar", "como pago", "vou pagar",
    "fazer o pedido", "fazer meu pedido", "realizar o pedido",
    "processar pagamento", "processar o pedido",
    "quero comprar", "vou comprar", "como comprar",
    "fechar a conta", "fechar conta",
]

MENSAGEM_REDIRECIONAMENTO = (
    "Para fechar seu pedido ou finalizar o pagamento, fale diretamente com um de "
    "nossos atendentes! 😊\n\n"
    "📱 WhatsApp: (11) 99999-9999\n"
    "📧 E-mail: contato@blackout.com.br\n\n"
    "Eles vão te ajudar rapidinho a finalizar tudo. Posso te ajudar com mais alguma "
    "dúvida sobre o cardápio enquanto isso?"
)

def contem_intencao_pedido(mensagem: str) -> bool:
    texto = mensagem.lower()
    return any(expressao in texto for expressao in EXPRESSOES_PEDIDO)

# 3. Variáveis Globais & Inicialização Eficiente
chat_engine = None

def inicializar_sistema():
    global chat_engine
    if chat_engine is None:
        print(">>> [SERVER] Iniciando Blackout Coffee Engine com RAG Real...")
        
        # CHAVE DA API DA GROQ
        groq_api_key = os.environ.get("GROQ_API") or os.environ.get("GROQ_API_KEY")
        if not groq_api_key:
            print(">>> [AVISO] GROQ_API não encontrada nas variáveis de ambiente!")

        # DEFINE O EMBEDDING REAL E AUTÊNTICO VIA HUGGING FACE (BGE-SMALL)
        # Lido via variável de ambiente para respeitar as diretrizes de segurança (Push Protection)
        hf_token = os.environ.get("HF_TOKEN")
        if not hf_token:
            print(">>> [AVISO] HF_TOKEN não encontrado nas variáveis de ambiente!")

        Settings.embed_model = HuggingFaceInferenceAPIEmbedding(
            model_name="BAAI/bge-small-en-v1.5",
            token=hf_token
        )
            
        # Define o LLM da Groq globalmente (geração de texto rápido)
        llm = Groq(model="llama-3.3-70b-versatile", api_key=groq_api_key)
        Settings.llm = llm
        
        # Define o LLM da Groq globalmente (geração de texto rápido)
        llm = Groq(model="llama-3.3-70b-versatile", api_key=groq_api_key)
        Settings.llm = llm
        
        # AQUI ESTÁ A CORREÇÃO: Força o LlamaIndex a saber que o Groq tem espaço de sobra
        Settings.context_window = 8000
        Settings.num_output = 1000
        
        # Garante que o diretório de documentos exista
        if not os.path.exists("documents"):
            os.makedirs("documents")
            
        # Carrega e parseia o PDF do cardápio
        documentos = SimpleDirectoryReader(input_dir="documents").load_data()
        node_parser = SentenceSplitter(chunk_size=1000, chunk_overlap=150)
        nodes = node_parser.get_nodes_from_documents(documentos)
        
        # Cria o índice de vetores na memória RAM através de embeddings legítimos
        index = VectorStoreIndex(nodes)
        
        system_prompt = (
            "Você é a Blackout, a inteligência artificial simpática do Blackout Café em Porto Alegre. "
            "Seu objetivo é ajudar os clientes a entenderem o cardápio, sugerir harmonizações de café "
            "e responder dúvidas cordialmente baseando-se nos documentos fornecidos.\n\n"
            "REGRAS IMPORTANTES:\n"
            "1. Você NUNCA deve processar, confirmar, simular ou finalizar pedidos e pagamentos. "
            "Você não tem capacidade real de registrar pedidos ou cobrar pagamentos, então jamais "
            "deve agir como se estivesse fazendo isso.\n"
            "2. Se o cliente quiser fazer um pedido, fechar uma compra, pagar, ou tiver dúvidas sobre "
            "um pedido já feito, NÃO tente resolver isso pelo chat. Em vez disso, oriente educadamente "
            "o cliente a entrar em contato com um atendente humano pelo WhatsApp (11) 99999-9999 "
            "ou pelo e-mail contato@blackout.com.br para finalizar a compra.\n"
            "3. Se o cliente perguntar sobre assuntos totalmente fora do tema do café/cardápio "
            "(matemática, programação, política, etc), recuse educadamente e redirecione a conversa "
            "de volta para o tema do café.\n"
            "4. Se a resposta não estiver nos documentos fornecidos, diga claramente que não tem essa "
            "informação no cardápio, em vez de inventar uma resposta."
        )
        
        # O chat_mode="context" agora vai funcionar perfeitamente, varrendo os vetores reais criados pelo BGE
        chat_engine = index.as_chat_engine(chat_mode="context", llm=llm, system_prompt=system_prompt)
    return chat_engine

# Executa a inicialização no startup da API
@app.on_event("startup")
async def startup_event():
    try:
        inicializar_sistema()
        print(">>> [SERVER] Engine de IA carregada com RAG legítimo e pronta.")
    except Exception as e:
        print(f">>> [ERRO CRÍTICO NO STARTUP] Falha ao inicializar: {str(e)}")

# 4. Endpoints da API REST
@app.post("/api/chat", response_model=ChatResponse)
async def converse_com_bot(payload: ChatRequest):
    try:
        if contem_intencao_pedido(payload.message):
            return ChatResponse(success=True, response=MENSAGEM_REDIRECIONAMENTO)

        engine = inicializar_sistema()
        response = engine.chat(payload.message)
        return ChatResponse(success=True, response=str(response))
    except Exception as e:
        print(f">>> [ERRO] Falha no processamento: {str(e)}")
        raise HTTPException(status_code=500, detail=f"Erro interno no motor de IA: {str(e)}")

@app.post("/api/chat/reset")
async def resetar_chat():
    global chat_engine
    if chat_engine:
        chat_engine.reset()
    return {"success": True, "message": "Histórico do chat limpo com sucesso."}

if __name__ == "__main__":
    uvicorn.run("app:app", host="127.0.0.1", port=8001, reload=True)