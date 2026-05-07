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

# 1. Definições de Classe
class ChromaEmbeddingWrapper(EmbeddingFunction):
    def __init__(self, model_name):
        self.model = HuggingFaceEmbedding(model_name=model_name)
    def __call__(self, input: Documents) -> Embeddings:
        return [self.model.get_text_embedding(text) for text in input]

# 2. Variáveis Globais
chat_engine = None

def inicializar_sistema():
    global chat_engine
    
    if chat_engine is None:
        print(">>> Iniciando sistema RAG: Blackout Coffee...")
        
        embed_model = HuggingFaceEmbedding(model_name="sentence-transformers/paraphrase-multilingual-MiniLM-L12-v2")
        embed_model_chroma = ChromaEmbeddingWrapper(model_name="sentence-transformers/paraphrase-multilingual-MiniLM-L12-v2")

        documentos = SimpleDirectoryReader(input_dir="documents").load_data()
        node_parser = SentenceSplitter(chunk_size=1200)
        nodes = node_parser.get_nodes_from_documents(documentos)

        # Usando /tmp para evitar erros de escrita em produção
        db = chromadb.PersistentClient(path="/tmp/chroma_db")
        chroma_collection = db.get_or_create_collection(
            name="documentos_blackout",
            embedding_function=embed_model_chroma
        )
        vector_store = ChromaVectorStore(chroma_collection=chroma_collection)
        storage_context = StorageContext.from_defaults(vector_store=vector_store)
        index = VectorStoreIndex(nodes, storage_context=storage_context, embed_model=embed_model)

        api_key = os.environ.get("GROQ_API") or os.environ.get("GROQ_API_KEY")
        llm = Groq(model="llama-3.3-70b-versatile", api_key=api_key)
        
        memory = ChatSummaryMemoryBuffer(llm=llm, token_limit=256)
        chat_engine = index.as_chat_engine(
            chat_mode="context",
            llm=llm,
            memory=memory,
            system_prompt="Você é a assistente virtual da Blackout. Seu objetivo é ajudar clientes com dúvidas sobre nosso cardápio de cafés especiais. Use apenas as informações do contexto fornecido. Refira-se sempre à empresa como Blackout."
        )
    return chat_engine

# 3. Funções da Interface
def converse_com_bot(message, chat_history):
    if chat_history is None:
        chat_history = []
    
    engine = inicializar_sistema()
    response = engine.chat(message)
    
    chat_history.append((message, response.response))
    return "", chat_history

def resetar_chat():
    global chat_engine
    if chat_engine:
        chat_engine.reset()
    return []

# CSS injetado via HTML para evitar conflitos com o motor de templates Jinja2
css_html = """
<style>
footer {display: none !important;}
.gradio-container, .main, .wrap, .inner-wrap { background-color: #0a0a0a !important; }
#chatbot { background-color: #0a0a0a !important; border: none !important; }
.row-input { display: flex !important; align-items: center !important; gap: 8px !important; padding: 10px !important; background-color: #0a0a0a !important; }
#msg_input { background-color: #1a1a1a !important; color: white !important; border-radius: 20px !important; }
#send_btn { background-color: #58664d !important; min-width: 45px !important; border-radius: 50% !important; }
</style>
"""

with gr.Blocks() as demo:
    gr.HTML(css_html) # Injeção segura do CSS
    with gr.Column(elem_id="col-container"):
        chatbot = gr.Chatbot(show_label=False, height=400)
        
        with gr.Row(elem_classes=["row-input"]):
            msg = gr.Textbox(
                show_label=False,
                placeholder="Como posso ajudar a Blackout hoje?",
                container=False,
                elem_id="msg_input",
                scale=9
            )
            submit_btn = gr.Button("➤", elem_id="send_btn", scale=1)
            
        limpar = gr.Button("Limpar Histórico", size="sm", variant="secondary")

    # Ações configuradas para não expor API
    msg.submit(converse_com_bot, [msg, chatbot], [msg, chatbot], api_name=False)
    submit_btn.click(converse_com_bot, [msg, chatbot], [msg, chatbot], api_name=False)
    limpar.click(resetar_chat, None, chatbot, api_name=False)

# Configuração robusta para Hugging Face Spaces
demo.queue()
demo.launch(
    server_name="0.0.0.0", 
    server_port=7860, 
    show_api=False,
    share=False
)