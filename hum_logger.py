#!/usr/bin/python
import am2320
import mysql.connector
import datetime as dt

hum_sensor = am2320.AM2320(1)
_, humidity = hum_sensor.readSensor()

with open("/home/pi/balloonS/sensor_logs/hum_log", mode = "a+") as hum_log:	
	time_stamp = dt.datetime.now().strftime('%Y-%m-%d %H:%M:%S')
	hum_log.write(time_stamp + " h=" + str(humidity) + "%\n")
	
mydb = mysql.connector.connect(
	host = "localhost",
	user = "root",
	passwd = "balloonSroot",
	database = "balloonS"
)

mycursor = mydb.cursor()
sql = "INSERT INTO hum_log(log_time, log_val, unit) VALUES (%s, %s, %s)"
val = (time_stamp, str(humidity), "hPa")
mycursor.execute(sql, val)
mydb.commit()