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
        print(">>> Iniciando Blackout Coffee...")
        embed_model = HuggingFaceEmbedding(model_name="sentence-transformers/paraphrase-multilingual-MiniLM-L12-v2")
        embed_model_chroma = ChromaEmbeddingWrapper(model_name="sentence-transformers/paraphrase-multilingual-MiniLM-L12-v2")
        documentos = SimpleDirectoryReader(input_dir="documents").load_data()
        node_parser = SentenceSplitter(chunk_size=1200)
        nodes = node_parser.get_nodes_from_documents(documentos)
        db = chromadb.PersistentClient(path="/tmp/chroma_db")
        chroma_collection = db.get_or_create_collection(name="docs_blackout", embedding_function=embed_model_chroma)
        vector_store = ChromaVectorStore(chroma_collection=chroma_collection)
        storage_context = StorageContext.from_defaults(vector_store=vector_store)
        index = VectorStoreIndex(nodes, storage_context=storage_context, embed_model=embed_model)
        api_key = os.environ.get("GROQ_API") or os.environ.get("GROQ_API_KEY")
        llm = Groq(model="llama-3.3-70b-versatile", api_key=api_key)
        memory = ChatSummaryMemoryBuffer(llm=llm, token_limit=256)
        chat_engine = index.as_chat_engine(chat_mode="context", llm=llm, memory=memory, system_prompt="Você é a Blackout.")
    return chat_engine

# 3. Funções da Interface
def converse_com_bot(message, chat_history):
    if chat_history is None: chat_history = []
    engine = inicializar_sistema()
    response = engine.chat(message)
    chat_history.append((message, response.response))
    return "", chat_history

def resetar_chat():
    global chat_engine
    if chat_engine: chat_engine.reset()
    return []

# Interface Vanilla (Sem CSS para não bugar o Jinja2)
with gr.Blocks() as demo:
    gr.Markdown("# Blackout Coffee")
    chatbot = gr.Chatbot(height=400)
    msg = gr.Textbox(placeholder="Digite aqui...")
    with gr.Row():
        btn = gr.Button("Enviar")
        clear = gr.Button("Limpar")

    msg.submit(converse_com_bot, [msg, chatbot], [msg, chatbot], api_name=False)
    btn.click(converse_com_bot, [msg, chatbot], [msg, chatbot], api_name=False)
    clear.click(resetar_chat, None, chatbot, api_name=False)

# Launch Direto
demo.launch(server_name="0.0.0.0", server_port=7860, show_api=False)