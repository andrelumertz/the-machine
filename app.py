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
            system_prompt="Você é a assistente virtual da Blackout. Seu objetivo é ajudar clientes com dúvidas sobre nosso cardápio de cafés especiais. Use apenas as informações do contexto fornecido. Se encontrar o nome da cafeteria diferente em qualquer documento, trate-o como um erro e refira-se sempre à empresa como Blackout."
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

css = """
footer {display: none !important;}
/* Força o preto em todas as camadas possíveis */
.gradio-container, .main, .wrap, .inner-wrap, #col-container placeholder-content.svelte-1rn3hyj .bubble-wrap.svelte-kpz1 {
    background-color: #0a0a0a !important; 
    border: none !important;
}

#chatbot {
    background-color: #0a0a0a !important; 
    border: none !important;
}

/* A classe que vai alinhar o input e a setinha */
.row-input {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    gap: 8px !important;
    padding: 10px !important;
    background-color: #0a0a0a !important;
}

#msg_input {
    background-color: #1a1a1a !important;
    border: 1px solid #333 !important;
    color: white !important;
    border-radius: 20px !important;
}

#send_btn {
    background-color: #58664d !important;
    border: none !important;
    min-width: 45px !important;
    max-width: 45px !important;
    height: 45px !important;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

/* termina a barra de rolagem branca lateral */
.gradio-container { overflow: hidden !important; }
.message-wrap::-webkit-scrollbar { width: 0px !important; }
"""

with gr.Blocks() as demo:
    with gr.Column(elem_id="col-container"):
        # Reduzi um pouco o height para garantir que não transborde na modal
        chatbot = gr.Chatbot(show_label=False, height=400)
        
        # O PULO DO GATO: Aplicando a classe CSS na Row
        with gr.Row(elem_classes=["row-input"]):
            msg = gr.Textbox(
                show_label=False,
                placeholder="Type your message...",
                container=False,
                elem_id="msg_input",
                scale=9 # Ocupa a maior parte da linha
            )
            submit_btn = gr.Button("➤", elem_id="send_btn", scale=1)
            
        limpar = gr.Button("Limpar Histórico", size="sm", variant="secondary")

    # Ações
    msg.submit(converse_com_bot, [msg, chatbot], [msg, chatbot])
    submit_btn.click(converse_com_bot, [msg, chatbot], [msg, chatbot])
    limpar.click(resetar_chat, None, chatbot, queue=False)

demo.launch(
    share=True,
    theme=gr.themes.Soft(),
    css=css
)