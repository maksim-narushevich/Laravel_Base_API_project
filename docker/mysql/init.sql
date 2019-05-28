INSERT INTO mysql.user (Host, User, Password) VALUES ('%', 'root', password('laraapi'));
GRANT ALL ON *.* TO 'root'@'%' WITH GRANT OPTION;

CREATE DATABASE IF NOT EXISTS laravel_api_db;
