#!/usr/bin/python
from Adafruit_BME280 import *
from math import log
import datetime as dt
import mysql.connector
import re

sensor = BME280(t_mode=BME280_OSAMPLE_8, p_mode=BME280_OSAMPLE_8, h_mode=BME280_OSAMPLE_8)

# Pressure logging
degrees = sensor.read_temperature()
pascals = sensor.read_pressure()
hectopascals = pascals / 100

with open("/home/pi/balloonS/sensor_logs/press_log", mode = "a+") as press_log:	
	time_stamp = dt.datetime.now().strftime('%Y-%m-%d %H:%M:%S')
	press_log.write(time_stamp + " p=" + str(hectopascals) + "hPa\n")

mydb = mysql.connector.connect(
	host = "localhost",
	user = "root",
	passwd = "balloonSroot",
	database = "balloonS"
)

mycursor = mydb.cursor()
sql = "INSERT INTO press_log(log_time, log_val, unit) VALUES (%s, %s, %s)"
val = (time_stamp, hectopascals, "hPa")
mycursor.execute(sql, val)
mydb.commit()

# Altitude logging
PRESS0 = 1013.25	# Pressure at sea level (hPa)
u = 0.0289644		# Molar mas of air
g = 9.8101			# Standard acceleration
R = 8.314458948		# Universal gas constant

with open("/home/pi/balloonS/sensor_logs/temp_log", mode = "r") as temp_log:
	temp_log.seek(2)
	line = temp_log.readline()
	temp = re.search(r'(?<=t=)[0-9]*', line).group()

# Calculate altitude
alt = - (R * (float(temp) + 273) * log(hectopascals / PRESS0) / (u * g))

with open("/home/pi/balloonS/sensor_logs/alt_log", mode = "a+") as alt_log:	
	time_stamp = dt.datetime.now().strftime('[%Y-%m-%d %H:%M:%S]')
	alt_log.write(time_stamp + " a=" + str(alt) + "m\n")