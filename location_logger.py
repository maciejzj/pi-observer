#!/usr/bin/python
from gps import gps

gps = gps()

with open("/home/pi/balloonS/sensor_logs/loc_log", mode = "a+") as loc_log:	
	loc_log.write(gps.make_gps_log_entry() + "\n")
	


