#!/usr/bin/python

from gps import gps

gps = gps()

with open("/home/pi/balloonS/sensor_logs/google_maps_log", mode = "a+") as g_m_log:	
	g_m_log.write(gps.make_gogole_maps_marker_entry() + "\n")
	