import serial
import datetime as dt
import sys

class gps:
    def __init__(self, port = "/dev/serial0"):
        # Initializes serial connection for gps communication
        try:
            self.__ser = serial.Serial(port)
        except Exception as e:
                        sys.exit("Can not connect with GPS using uart: " + str(e))
        
    def get_record(self):
        # For 50 times tries to read GPRMC record from gps in form of strings
        got_record = False
        for _ in range(50):
            gps_record = self.__ser.readline().decode('UTF-8')
            if gps_record[0:6] == "$GPRMC":
                got_record = True
                break

        if got_record == True:
            data = gps_record.split(",")
            if data[2] == 'A':
                self._status = "Correct"
                
                # GMT time
                if is_number(data[1][0:2]) and is_number(data[1][2:4]) and is_number(data[1][4:6]):
                    self._time = data[1][0:2] + ":" + data[1][2:4] + ":" + data[1][4:6]
                else:
                    self._time = dt.datetime.now().strftime('[%H:%M:%S]')
                    self._status = "Corrupted data"
            
                # Latitude
                if (is_number(data[3])):
                    self._latitude = data[3]
                else:
                    self._status = "Corrupted data"
                
                # Latitude direction N/S
                self._hemisphere_NS = data[4]
                
                # Longitude
                if (is_number(data[5])):
                    self._longitude = data[5]
                else:
                    self._status = "Corrupted data"	

                # Longitude direction W/E
                self._hemisphere_WE = data[6]	
                
                # Velocity in knots
                if (is_number(data[7])):
                    self._velocity = data[7]
                else:
                    self._status = "Corrupted data"

                # True course
                if (is_number(data[8])):
                    self._course = data[8]
                elif data[8] == '':
                    self._course = 0;
                else:
                    self._status = "Corrupted data"	
                
                # Date
                if is_number(data[9][4:6]) and is_number(data[9][2:4]) and is_number(data[9][0:2]):
                    self._date = data[9][4:6] + "-" + data[9][2:4] + "-" + data[9][0:2]
                else:
                    self._status = "Corrupted data"	

                if self._status == "Correct":
                    return 0
                else:
                    return 1
            else:
                self._status = "Signal lost"
                self._time = dt.datetime.now().strftime('%H:%M:%S')
                self._date = dt.datetime.now().strftime('%Y-%m-%d')

                return 1
        else:
            self._status = "Connection error"
            self._time = dt.datetime.now().strftime('%H:%M:%S')
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
        if (self.get_record() == 0):
            hemi_NE_sign = "+" if self._hemisphere_NS == "N" else "-"
            hemi_WE_sign = "+" if self._hemisphere_WE == "E" else "-"
            
            pos = self._latitude.find('.')
            lat_deg = self._latitude[:pos-2]
            lat_mins = self._latitude[pos-2:pos] + self._latitude[pos+1:]
            lat_mins = str(round(float(lat_mins) / 60.0))

            pos = self._longitude.find('.')
            lng_deg = self._longitude[:pos-2]
            lng_mins = self._longitude[pos-2:pos] + self._longitude[pos+1:]
            lng_mins = str(round(float(lng_mins) / 60.0))

            return {
                'timestamp' : self.get_gps_time(),
                'status' : self._status,
                'latitude' : float(hemi_NE_sign + lat_deg + "." + lat_mins),
                'longitude' : float(hemi_WE_sign + lng_deg + "." + lng_mins),
                'velocity' : float(self._velocity),
                'course' : float(self._course) }
        else:
            return {
                'timestamp' : self._date + " " + self._time,
                'status' : self._status,
                'latitude' : 0,
                'longitude' : 0,
                'velocity' : 0,
                'course' : 0 }
    
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
            
def is_number(s):
    try:
        float(s)
        return True
    except ValueError:
        return False
