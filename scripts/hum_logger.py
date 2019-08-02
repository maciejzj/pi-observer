#!/usr/bin/python3
import am2320
import mysql.connector
import datetime as dt

# Read humidity dorm sensor, ignore temp.
hum_sensor = am2320.AM2320(1)
_, humidity = hum_sensor.readSensor()

# Write to log text file.
with open("/var/log/pi_observer/hum_log", mode = "a+") as hum_log:	
	time_stamp = dt.datetime.now().strftime('%Y-%m-%d %H:%M:%S')
	hum_log.write(time_stamp + " h=" + str(humidity) + "%\n")
	
mydb = mysql.connector.connect(
	host = "localhost",
	user = "pi_observer_root",
	passwd = "piobserverroot",
	database = "pi_observer_data_logs"
)

# Write to log database.
mycursor = mydb.cursor()
sql = "INSERT INTO hum_log(time, value, unit) VALUES (%s, %s, %s)"
val = (time_stamp, str(humidity), "hPa")
mycursor.execute(sql, val)
mydb.commit()

