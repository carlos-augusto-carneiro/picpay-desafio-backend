#!/bin/bash

# Espera o MySQL ficar pronto
echo "Aguardando o banco de dados..."
until php -r "try { new PDO('mysql:host=db;port=3306', 'carlos', 'carlos123!'); exit(0); } catch (Exception \$e) { exit(1); }"; do
  sleep 2
done
echo "Criando swagger..."
php artisan l5-swagger:generate
echo "Banco de dados conectado! Rodando migrations..."
php artisan migrate
php artisan cache:clear
php artisan config:clear

# Executa o comando principal (php-fpm ou o worker)
exec "$@"
