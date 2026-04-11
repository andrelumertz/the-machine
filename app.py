import os
import chromadb
import gradio as gr
from llama_index.core import VectorStoreIndex, StorageContext, SimpleDirectoryReader
from llama_index.core.node_parser import SentenceSplitter
from llama_index.embeddings.huggingface import HuggingFaceEmbedding
from llama_index.vector_stores.chroma import ChromaVectorStore
from llama_index.llms.groq import Groq
from llama_index.core.memory import ChatSummaryMemoryBuffer
from chromadb import EmbeddingFunction, Documents, Embeddings

# 1. Definições de Classe (Leve)
class ChromaEmbeddingWrapper(EmbeddingFunction):
    def __init__(self, model_name):
        self.model = HuggingFaceEmbedding(model_name=model_name)
    def __call__(self, input: Documents) -> Embeddings:
        return [self.model.get_text_embedding(text) for text in input]

# 2. Variáveis Globais (Vazias no início)
chat_engine = None

def inicializar_sistema():
    """Toda a carga pesada acontece aqui, apenas uma vez."""
    global chat_engine
    if chat_engine is None:
        print(">>> Iniciando 'The Machine': Carregando modelos e documentos...")
        
        # Embeddings
        embed_model = HuggingFaceEmbedding(model_name="sentence-transformers/paraphrase-multilingual-MiniLM-L12-v2")
        embed_model_chroma = ChromaEmbeddingWrapper(model_name="sentence-transformers/paraphrase-multilingual-MiniLM-L12-v2")

        # Documentos
        documentos = SimpleDirectoryReader(input_dir="documents").load_data()
        node_parser = SentenceSplitter(chunk_size=1200)
        nodes = node_parser.get_nodes_from_documents(documentos)

        # ChromaDB
        db = chromadb.PersistentClient(path="./chroma_db")
        chroma_collection = db.get_or_create_collection(
            name="documentos_serenatto",
            embedding_function=embed_model_chroma
        )
        vector_store = ChromaVectorStore(chroma_collection=chroma_collection)
        storage_context = StorageContext.from_defaults(vector_store=vector_store)
        index = VectorStoreIndex(nodes, storage_context=storage_context, embed_model=embed_model)

        # LLM e Chat Engine
        llm = Groq(model="llama-3.3-70b-versatile", api_key=os.environ.get("GROQ_API"))
        memory = ChatSummaryMemoryBuffer(llm=llm, token_limit=256)
        chat_engine = index.as_chat_engine(
            chat_mode="context",
            llm=llm,
            memory=memory,
            system_prompt="Você é especialista em cafés especiais da Serenatto..."
        )
    return chat_engine

# 3. Funções da Interface
def converse_com_bot(message, chat_history):
    # O sistema só carrega quando você clica em enviar a primeira vez
    engine = inicializar_sistema()
    response = engine.chat(message)
    
    chat_history = chat_history or []
    chat_history.append({"role": "user", "content": message})
    chat_history.append({"role": "assistant", "content": response.response})
    return "", chat_history

def resetar_chat():
    global chat_engine
    if chat_engine:
        chat_engine.reset()
    return []

# 4. Interface Gradio (Inicia rápido!)
# CSS atualizado para alinhar tudo e remover elementos desnecessários, mantendo o foco no chat
css = """
/* 1. Reset Total de Fundo e Bordas */
footer {display: none !important;}
.gradio-container {background-color: #0a0a0a !important; border: none !important;}
#chatbot {background-color: #0a0a0a !important; border: none !important; box-shadow: none !important;}

/* 2. Estilo das Mensagens (Minimalista) */
.message {background: transparent !important; border: none !important; padding: 12px !important; color: white !important;}
.message.bot {border-left: 2px solid #58664d !important;}
.message.user {color: #8c774c !important;}

/* 3. ALINHAMENTO: Colocando input e botão lado a lado */
.row-input {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    gap: 10px !important;
    background-color: #0a0a0a !important;
    padding: 10px !important;
}

/* 4. Estilo do Botão de Envio (Circular e Verde) */
#send_btn {
    background-color: #58664d !important;
    border: none !important;
    max-width: 45px !important;
    min-width: 45px !important;
    height: 45px !important;
    border-radius: 50% !important;
    cursor: pointer !important;
}

/* 5. Estilo do Campo de Texto */
#msg_input {
    background-color: #1a1a1a !important;
    border: 1px solid #333 !important;
    color: white !important;
    border-radius: 20px !important;
}

/* Esconde a barra de rolagem lateral branca que apareceu no seu print */
.gradio-container { overflow: hidden !important; }
"""

with gr.Blocks(css=css) as demo:
    # O chatbot ocupa a maior parte do espaço
    chatbot = gr.Chatbot(show_label=False, height=450)
    
    # Criamos uma Row específica para o input e a setinha
    with gr.Row(elem_classes="row-input"):
        msg = gr.Textbox(
            show_label=False,
            placeholder="Type your message...",
            container=False,
            elem_id="msg_input",
            scale=8
        )
        submit_btn = gr.Button("➤", elem_id="send_btn", variant="primary", scale=1)
            
    # Botão de limpar mais discreto no fundo
    limpar = gr.Button("Limpar Histórico", size="sm", variant="secondary")

    # Ações
    msg.submit(converse_com_bot, [msg, chatbot], [msg, chatbot])
    submit_btn.click(converse_com_bot, [msg, chatbot], [msg, chatbot])
    limpar.click(resetar_chat, None, chatbot, queue=False)

demo.launch(theme=gr.themes.Soft())