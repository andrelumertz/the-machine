FROM php:8.2-apache

# Instala extensões do PHP necessárias para o banco de dados (PDO MySQL)
RUN docker-php-ext-install pdo pdo_mysql

# Instala o Python 3 e o pip de forma direta e limpa
RUN apt-get update && apt-get install -y --no-install-recommends \
    python3 \
    python3-pip \
    && rm -rf /var/lib/apt/lists/*

# Define o diretório do servidor Apache
WORKDIR /var/www/html

# Copia o arquivo de requisitos leve
COPY requirements.txt ./

# Instala as dependências Python leves (vai passar voando agora)
RUN pip3 install --no-cache-dir -r requirements.txt --break-system-packages

# Copia o restante de todos os arquivos do seu projeto
COPY . .

# Dá permissão para o Apache ler e gravar os arquivos
RUN chown -R www-data:www-data /var/www/html/

# Expõe a porta padrão do Render
EXPOSE 80

# COMANDO HÍBRIDO MÁGICO (Inicia o Uvicorn na porta 8001, espera 3 segundos e abre o Apache)
CMD uvicorn app:app --host 127.0.0.1 --port 8001 & sleep 3 && apache2-foreground