sudo mysql --user=root
DROP USER 'root'@'localhost';
CREATE USER 'root'@'localhost' IDENTIFIED BY 'balloonSroot';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost'

CREATE DATABASE balloonS
USE balloonS

CREATE TABLE users(id int, login varchar(25), pass varchar(25));