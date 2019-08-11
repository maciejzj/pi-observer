#!/usr/bin/python3
import mysql.connector
from gps import gps

gps = gps()
record = gps.get_decimal_degrees_record()

mydb = mysql.connector.connect(
	host = "localhost",
	user = "pi_observer_root",
	passwd = "piobserverroot",
	database = "pi_observer_data_logs"
)

print(record)

mycursor = mydb.cursor()
sql = "INSERT INTO loc_log(time, status, latitude, longitude, velocity, course) VALUES (%s, %s, %s, %s, %s, %s)"
val = (record['timestamp'], record['status'], record['latitude'], record['longitude'], record['velocity'], record['course'])
mycursor.execute(sql, val)
mydb.commit()

