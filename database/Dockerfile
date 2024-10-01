# Dockerfile для контейнера базы данных
FROM mariadb:10.5

# Установка клиента MariaDB
RUN apt-get update && \
    apt-get install -y mariadb-client && \
    apt-get clean && rm -rf /var/lib/apt/lists/*
