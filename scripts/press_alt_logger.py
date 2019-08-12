#!/usr/bin/python3
import smbus2
import bme280
from math import log
import datetime as dt
import mysql.connector
import re

# BME280 i2c settings
address = 0x76
bus = smbus2.SMBus(1)
compensation_params = bme280.load_calibration_params(bus, address)

# Pressure logging
sensor_data = bme280.sample(bus, address)
temp = sensor_data.temperature
pressure = sensor_data.pressure

with open("/var/log/pi_observer/press_log", mode = "a+") as press_log:	
	time_stamp = dt.datetime.now().strftime('%Y-%m-%d %H:%M:%S')
	press_log.write("[" + time_stamp + "] " + " p=" + str(round(pressure, 4)) + "hPa\n")

mydb = mysql.connector.connect(
    host = "localhost",
    user = "pi_observer_root",
    passwd = "piobserverroot",
    database = "pi_observer_data_logs"
)

mycursor = mydb.cursor()
sql = "INSERT INTO press_log(time, value, unit) VALUES (%s, %s, %s)"
val = (time_stamp, round(pressure, 4), "hPa")
mycursor.execute(sql, val)
mydb.commit()

# Altitude logging
PRESS0 = 1013.25	# Pressure at sea level (hPa)
u = 0.0289644		# Molar mas of air
g = 9.8101		# Standard acceleration
R = 8.314458948		# Universal gas constant

# Calculate altitude
alt = - (R * (float(temp) + 273) * log(pressure / PRESS0) / (u * g))

with open("/var/log/pi_observer/alt_log", mode = "a+") as alt_log:	
	time_stamp = dt.datetime.now().strftime('%Y-%m-%d %H:%M:%S')
	alt_log.write("[" + time_stamp + "] " + " a=" + str(round(alt, 4)) + "m\n")

mycursor = mydb.cursor()
sql = "INSERT INTO alt_log(time, value, unit) VALUES (%s, %s, %s)"
val = (time_stamp, str(round(alt, 4)), "m")
mycursor.execute(sql, val)
mydb.commit()

