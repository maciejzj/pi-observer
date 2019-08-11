#!/usr/bin/python3
import os
from gps import gps
import sys

gps = gps()
try:
	if (gps.get_gps_time() == 1):
		print("Failed to set time from GPS")
		sys.exit(1)
	else:
		os.system("sudo date +[%Y-%m-%d]\\ %H:%M:%S -s " + "\"" + gps.get_gps_time() + "\" --utc")
		sys.exit(0)
except Exception as e:
	sys.exit(1)

