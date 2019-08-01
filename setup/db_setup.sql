USE mysql;
UPDATE user SET password=PASSWORD('piobserverroot') WHERE User='root' AND Host = 'localhost';
FLUSH PRIVILEGES;

DROP DATABASE IF EXISTS pi_observer_users;
CREATE DATABASE IF NOT EXISTS pi_observer_users;
USE pi_observer_users;

CREATE TABLE users(
	id int NOT NULL AUTO_INCREMENT, login varchar(25) NOT NULL, pass varchar(255) NOT NULL,
   	email varchar(255) NOT NULL, confirmed int(11), confirm_code int(11), PRIMARY KEY(id));

DROP DATABASE IF EXISTS pi_observer_data_logs;
CREATE DATABASE IF NOT EXISTS pi_observer_data_logs;
USE pi_observer_data_logs;

CREATE TABLE press_log(
	num int NOT NULL AUTO_INCREMENT, log_time timestamp,
	log_val real, unit varchar(6), PRIMARY KEY (num));

CREATE TABLE alt_log(
	num int NOT NULL AUTO_INCREMENT, log_time timestamp,
	log_val real, unit varchar(6), PRIMARY KEY (num));

CREATE TABLE temp_log(
	num int NOT NULL AUTO_INCREMENT, log_time timestamp,
	log_val real, unit varchar(6), PRIMARY KEY (num));

CREATE TABLE int_temp_log(
	num int NOT NULL AUTO_INCREMENT, log_time timestamp,
	log_val real, unit varchar(6), PRIMARY KEY (num));

CREATE TABLE hum_log(
	num int NOT NULL AUTO_INCREMENT, log_time timestamp,
	log_val real, unit varchar(6), PRIMARY KEY (num));

CREATE TABLE loc_log(
	num int NOT NULL AUTO_INCREMENT, log_time timestamp,
	status varchar(20), latitude real, longitude real,
	velocity real, course real, PRIMARY KEY (num));

