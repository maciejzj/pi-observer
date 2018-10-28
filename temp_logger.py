#!/usr/bin/python
import sys
import re
import mysql.connector
import datetime as dt
	
therm_dev_name = {'ext' : "28-000005945f57", 'int' : "28-00000a418b77"}
therm_addr = {'ext' : "/sys/bus/w1/devices/" + therm_dev_name['ext'] + "/w1_slave",
				"int" : "/sys/bus/w1/devices/" + therm_dev_name['int'] + "/w1_slave"
				}
therm_log_name = {'ext': "temp_log", 'int': "int_temp_log"}

mydb = mysql.connector.connect(
	host = "localhost",
	user = "root",
	passwd = "balloonSroot",
	database = "balloonS"
)

time_stamp = dt.datetime.now().strftime('%Y-%m-%d %H:%M:%S')

for key in therm_addr.viewkeys() & therm_log_name.viewkeys():
	with open(therm_addr[key], mode = "r") as therm:	
		lines = therm.readlines()
		for line in lines:
			match = re.search(r'(?<=t=)[0-9]*', line)
			if(match):
				temp = float(match.group()) / 1000
	
	with open("/home/pi/balloonS/sensor_logs/" + therm_log_name[key], mode = "a+") as temp_log:	
		temp_log.write(time_stamp + " t=" + str(temp) + "C\n")
		
	mycursor = mydb.cursor()
	sql = "INSERT INTO " + therm_log_name[key] + "(log_time, log_val, unit) VALUES (%s, %s, %s)"
	val = (time_stamp, temp, "C")
	mycursor.execute(sql, val)
	mydb.commit()