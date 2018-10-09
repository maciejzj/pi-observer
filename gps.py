#!/usr/bin/python
import serial
import var
import datetime as dt
import sys

class gps:	
	__ser = None
	
	def __init__(self, port = "/dev/serial0"):
		try:
			self.__ser = serial.Serial(port, timeout = 0.2)
		except:
			sys.exit("Can not connect with GPS using uart");
		
	def get_record(self):		
		got_record = False
		for _ in range(50):
			gps_record = self.__ser.readline()
			if gps_record[0:6] == "$GPRMC":
				got_record = True
				break

		if got_record == True:
			data = gps_record.split(",")
			if data[2] == 'A':
				self._time = data[1][0:2] + ":" + data[1][2:4] + ":" + data[1][4:6]
				self._latitude = data[3]
				self._hemisphere_NS = data[4]	# Latitude direction N/S
				self._longitude = data[5]
				self._hemisphere_WE = data[6]	# Longitude direction W/E
				self._velocity = data[7]
				self._course = data[8]		# True course
				self._date = data[9][4:6] + "-" + data[9][2:4] + "-" + data[9][0:2]
				return 0
			else:
				self._time = dt.datetime.now().strftime('%H:%M:%S')
				self._latitude = "GPS signal lost"
				self._hemisphere_NS = "GPS signal lost"	# Latitude direction N/S
				self._longitude = "GPS signal lost"
				self._hemisphere_WE = "GPS signal lost"	# Longitude direction W/E
				self._velocity = "GPS signal lost"		# Velocity in knots
				self._course = "GPS signal lost"		# True course
				self._date = dt.datetime.now().strftime('%Y-%m-%d')
				return 1
		else:
			self._time = dt.datetime.now().strftime('%H:%M:%S')
			self._latitude = "GPS"
			self._hemisphere_NS = "GPS error"	# Latitude direction N/S
			self._longitude = "GPS error"
			self._hemisphere_WE = "GPS error"	# Longitude direction W/E
			self._velocity = "GPS error"		# Velocity in knots
			self._course = "GPS error"		# True course
			self._date = dt.datetime.now().strftime('%Y-%m-%d')
			return 1
				
	def _decode(self, coord):
		#Converts DDDMM.MMMMM to DD deg MM.MMMMM min
		tmp = coord.split(".")
		deg = tmp[0][0:-2]
		mins = tmp[0][-2:]
		return deg + " deg " + mins + "." + tmp[1] + " min"
		
	def get_gps_time(self):
		if (self.get_record()):
			return 1
		else:
			return self._date + " " + self._time
		
	def make_gps_log_entry(self):
		self.get_record()
		time_stamp = dt.datetime.now().strftime('[%Y-%m-%d %H:%M:%S]')
		
		return "%s latitude: %s(%s), longitude: %s(%s), velocity: %s, True Course: %s" %  (
			time_stamp,
		 	self._decode(self._latitude),
			self._hemisphere_NS,
			self._decode(self._longitude),
			self._hemisphere_NS,
			self._velocity,
			self._course)
		
	def make_gogole_maps_marker_entry(self):
		self.get_record()
		time_stamp = dt.datetime.now().strftime('[%Y-%m-%d %H:%M:%S]')
		
		hemi_NE_sign = "+" if self._hemisphere_NS == "N" else "-"
		hemi_WE_sign = "+" if self._hemisphere_WE == "E" else "-"
		
		pos = self._latitude.find('.')
		latitude = self._latitude[:pos-2] + "." + self._latitude[pos-2:pos] + self._latitude[pos+1:]
		pos = self._longitude.find('.')
		longitude = self._longitude[:pos-2] + "." + self._longitude[pos-2:pos] + self._longitude[pos+1:]

		return "%s,%s%s,%s%s" % (
			time_stamp,
			hemi_NE_sign, 
			latitude, 
			hemi_WE_sign, 
			longitude) 