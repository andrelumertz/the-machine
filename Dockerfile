FROM php:8.2-apache

# 1. Traz o Composer oficial para dentro do container
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 2. Instala os pacotes do sistema necessários (Python + dependências para o Composer funcionar rápido)
RUN apt-get update && apt-get install -y --no-install-recommends \
    python3 \
    python3-pip \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# 3. Instala extensões do PHP necessárias para o banco de dados (PDO MySQL)
RUN docker-php-ext-install pdo pdo_mysql

# Define o diretório do servidor Apache
WORKDIR /var/www/html

# Copia o arquivo de requisitos leve do Python
COPY requirements.txt ./

# Instala as dependências Python
RUN pip3 install --no-cache-dir -r requirements.txt --break-system-packages

# Copia o restante de todos os arquivos do projeto (incluindo o composer.json e o composer.lock)
COPY . .

# 4. A MÁGICA ACONTECE AQUI: Roda o Composer para criar a pasta vendor com o Dompdf
RUN composer install --no-dev --optimize-autoloader

# Dá permissão para o Apache ler e gravar os arquivos
RUN chown -R www-data:www-data /var/www/html/

# Expõe a porta padrão do Render
EXPOSE 80

# COMANDO HÍBRIDO MÁGICO (Inicia o Uvicorn na porta 8001, espera 3 segundos e abre o Apache)
CMD uvicorn app:app --host 127.0.0.1 --port 8001 & sleep 3 && apache2-foreground