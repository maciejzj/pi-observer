sudo mysql --user=root
DROP USER 'root'@'localhost';
CREATE USER 'root'@'localhost' IDENTIFIED BY 'balloonSroot';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost'

CREATE DATABASE balloonS;
USE balloonS;

CREATE TABLE users(id int, login varchar(25), pass varchar(25));

CREATE TABLE press_log(num int NOT NULL AUTO_INCREMENT, log_time timestamp, log_val real, unit varchar(6), PRIMARY KEY (num));
);