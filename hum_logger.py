#!/usr/bin/python
import am2320
import datetime as dt

hum_sensor = am2320.AM2320(1)
_, humidity = hum_sensor.readSensor()

with open("/home/pi/balloonS/sensor_logs/hum_log", mode = "a+") as hum_log:	
	time_stamp = dt.datetime.now().strftime('[%Y-%m-%d %H:%M:%S]')
	hum_log.write(time_stamp + " h=" + str(humidity) + "%\n")