#!/usr/bin/python
import mysql.connector
from gps import gps

gps = gps()
record = gps.get_decimal_degrees_record()

mydb = mysql.connector.connect(
	host = "localhost",
	user = "root",
	passwd = "balloonSroot",
	database = "balloonS"
)

mycursor = mydb.cursor()
sql = "INSERT INTO loc_log(log_time, latitude, longitude, velocity, course) VALUES (%s, %s, %s, %s, %s)"
val = (record['timestamp'], record['latitude'], record['longitude'], record['velocity'], record['course'])
mycursor.execute(sql, val)
mydb.commit()