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
footer {display: none !important;}
.gradio-container {background-color: transparent !important; border: none !important;}
/* Remove o contorno azul ao clicar e deixa o chat liso */
#chatbot {border: none !important; box-shadow: none !important;}
/* Estiliza o botão de envio para ser um círculo ou ícone limpo */
#send_btn {
    max-width: 50px !important;
    min-width: 50px !important;
    border-radius: 50% !important;
}
"""

with gr.Blocks(css=css, theme=gr.themes.Soft()) as demo:
    with gr.Column(elem_id="col-container"):
        chatbot = gr.Chatbot(
            show_label=False, 
            bubble_full_width=False, 
            height=430,
            show_share_button=False #Remove o ícone de compartilhar para limpar o UI
        )
        
        
        with gr.Row(variant="compact"):
            msg = gr.Textbox(
                show_label=False,
                placeholder="Type your message...",
                container=False,
                scale=10 # Dá mais espaço para o texto
            )
            
            submit_btn = gr.Button("➤", elem_id="send_btn", variant="primary")
            
        with gr.Row():
            limpar = gr.Button("Limpar Histórico", size="sm", variant="secondary")

    
    msg.submit(converse_com_bot, [msg, chatbot], [msg, chatbot])
    submit_btn.click(converse_com_bot, [msg, chatbot], [msg, chatbot])
    
    limpar.click(resetar_chat, None, chatbot, queue=False)

demo.launch()