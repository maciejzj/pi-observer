#!/usr/bin/python
import datetime as dt
import matplotlib.pyplot as plt
import matplotlib.dates as md
from matplotlib.pyplot import figure
import re

log_names = ["temp_log", "int_temp_log", "press_log", "alt_log", "hum_log"]
y_labels = ["temperature in Celcius degrees",
		"internal temperature in Celcius degrees",
		"pressure in hPa",
		"altitude in meters",
		"humidity percentage"]

for log_name, y_label in zip(log_names, y_labels):
	times_stamps_x = []
	log_vals_y = []

	with open("/home/pi/balloonS/sensor_logs/" + log_name, mode = "r") as logFile:
		line = 1
		while line:
			line = logFile.readline()

			# Find temperature value in line and append it to
			# temperature array 
			temp = re.search(r'(?<=[thpa]=)[0-9\.]*', line)
			if(temp):
				log_vals_y.append(float(temp.group()))
			
			temp = re.search(r'(?<=\[)[^\]]*', line)
			if(temp):	
				times_stamps_x.append(
					dt.datetime.strptime(temp.group(), '%Y-%m-%d %H:%M:%S'))

	# Plot temperature chart
	figure(num=None, facecolor='w', edgecolor='k')
	plt.plot(times_stamps_x, log_vals_y)
	ax = plt.gca()
	# Set x date formater
	xfmt = md.DateFormatter('[%m-%d] %H:%M')
	ax.xaxis.set_major_formatter(xfmt)
	# Auto allign ticks
	plt.gcf().autofmt_xdate()
	ax.get_yaxis().get_major_formatter().set_useOffset(False)
	plt.xlabel('time')
	plt.ylabel(y_label)
	plt.grid()
	# Save
	plt.savefig('/home/pi/balloonS/sensor_logs/' + log_name +'.png')