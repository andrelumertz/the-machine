import os
import chromadb
import gradio as gr
from llama_index.core import VectorStoreIndex, StorageContext
from llama_index.core.node_parser import SentenceSplitter
from llama_index.core import SimpleDirectoryReader
from llama_index.embeddings.huggingface import HuggingFaceEmbedding
from llama_index.vector_stores.chroma import ChromaVectorStore
from llama_index.llms.groq import Groq
from llama_index.core.memory import ChatSummaryMemoryBuffer
from chromadb import EmbeddingFunction, Documents, Embeddings

class ChromaEmbeddingWrapper(EmbeddingFunction):
    def __init__(self, model_name):
        self.model = HuggingFaceEmbedding(model_name=model_name)
    def __call__(self, input: Documents) -> Embeddings:
        return [self.model.get_text_embedding(text) for text in input]

# Configurando embeddings
embed_model = HuggingFaceEmbedding(model_name="intfloat/multilingual-e5-large")
embed_model_chroma = ChromaEmbeddingWrapper(model_name="intfloat/multilingual-e5-large")

# Carregando documentos
documentos = SimpleDirectoryReader(input_dir="documents").load_data()

# Criando nodes
node_parser = SentenceSplitter(chunk_size=1200)
nodes = node_parser.get_nodes_from_documents(documentos)

# Configurando ChromaDB
db = chromadb.PersistentClient(path="./chroma_db")
chroma_collection = db.get_or_create_collection(
    name="documentos_serenatto",
    embedding_function=embed_model_chroma
)
vector_store = ChromaVectorStore(chroma_collection=chroma_collection)
storage_context = StorageContext.from_defaults(vector_store=vector_store)
index = VectorStoreIndex(nodes, storage_context=storage_context, embed_model=embed_model)

# Configurando LLM
llm = Groq(
    model="llama-3.3-70b-versatile",
    api_key=os.environ.get("GROQ_API")
)

# Configurando memória e chat engine
memory = ChatSummaryMemoryBuffer(llm=llm, token_limit=256)
chat_engine = index.as_chat_engine(
    chat_mode="context",
    llm=llm,
    memory=memory,
    system_prompt="""Você é especialista em cafés especiais da Serenatto, uma loja online que vende grãos de cafés torrados.
    Sua função é tirar dúvidas de forma simpática e natural sobre os grãos disponíveis"""
)

# Interface Gradio
def converse_com_bot(message, chat_history):
    response = chat_engine.chat(message)
    if chat_history is None:
        chat_history = []
    chat_history.append({"role": "user", "content": message})
    chat_history.append({"role": "assistant", "content": response.response})
    return "", chat_history

def resetar_chat():
    chat_engine.reset()
    return []

with gr.Blocks() as demo:
    gr.Markdown("# Chatbot da Serenatto")
    chatbot = gr.Chatbot(type="messages")
    msg = gr.Textbox(label="Digite a sua mensagem")
    limpar = gr.Button("Limpar")
    msg.submit(converse_com_bot, [msg, chatbot], [msg, chatbot])
    limpar.click(resetar_chat, None, chatbot, queue=False)

demo.launch()