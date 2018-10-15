#!/usr/bin/python
import serial
import var
import datetime as dt
import sys

class gps:
	def __init__(self, port = "/dev/serial0"):
		# Initializes serial connection for gps communication
		try:
			self.__ser = serial.Serial(port)
		except Exception, e:
			sys.exit("Can not connect with GPS using uart" + str(e));
		
	def get_record(self):
		# For 50 times tries to read GPRMC record from gps	
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
				self._velocity = data[7]		# Velocity in knots
				self._course = data[8]			# True course
				self._date = data[9][4:6] + "-" + data[9][2:4] + "-" + data[9][0:2]
				return 0
			else:
				self._time = dt.datetime.now().strftime('%H:%M:%S')
				self._latitude = "GPS signal lost"
				self._hemisphere_NS = "GPS signal lost"
				self._longitude = "GPS signal lost"
				self._hemisphere_WE = "GPS signal lost"
				self._velocity = "GPS signal lost"
				self._course = "GPS signal lost"
				self._date = dt.datetime.now().strftime('%Y-%m-%d')
				return 1
		else:
			self._time = dt.datetime.now().strftime('%H:%M:%S')
			self._latitude = "GPS"
			self._hemisphere_NS = "GPS error"
			self._longitude = "GPS error"
			self._hemisphere_WE = "GPS error"
			self._velocity = "GPS error"
			self._course = "GPS error"
			self._date = dt.datetime.now().strftime('%Y-%m-%d')
			return 1
		
	def _decode(self, coord):
		#Converts DDDMM.MMMMM to DD deg MM.MMMMM min
		tmp = coord.split(".")
		deg = tmp[0][0:-2]
		mins = tmp[0][-2:]
		return deg + " deg " + mins + "." + tmp[1] + " min"
		
	def get_gps_time(self):
		# Returns date and time or 1 if fails to obtain them
		if (self.get_record()):
			return 1
		else:
			return self._date + " " + self._time
	
	def get_decimal_degrees_record(self):	
		# Read from GPS and get current location parameters dictionary in decimal_degrees
		self.get_record()

		hemi_NE_sign = "+" if self._hemisphere_NS == "N" else "-"
		hemi_WE_sign = "+" if self._hemisphere_WE == "E" else "-"
		
		pos = self._latitude.find('.')
		lat_deg = self._latitude[:pos-2]
		lat_mins = self._latitude[pos-2:pos] + self._latitude[pos+1:]
		lat_mins = str(float(lat_mins) / 60.0)

		pos = self._longitude.find('.')
		lng_deg = self._longitude[:pos-2]
		lng_mins = self._longitude[pos-2:pos] + self._longitude[pos+1:]
		lng_mins = str(float(lng_mins) / 60.0)
		
		return {
			'timestamp' : self.get_gps_time(),
			'latitude' : hemi_NE_sign + lat_deg + "." + lat_mins,
			'longitude' : hemi_WE_sign + lng_deg + "." + lng_mins,
			'velocity' : self._velocity,
			'course' : self._course }
	
	def get_location_message(self):
		# Read from GPS and get current location in a easily readible string
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