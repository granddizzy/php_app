# Dockerfile для контейнера базы данных
FROM mariadb:latest

# Установка клиента MariaDB
RUN apt-get update && \
    apt-get install -y default-mysql-client && \
    apt-get clean && rm -rf /var/lib/apt/lists/*
