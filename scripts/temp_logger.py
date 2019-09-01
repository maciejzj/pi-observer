#!/usr/bin/python3.7
import sys
import re
import mysql.connector
import datetime as dt

# Linux devices files name in dict.
therm_dev_name = {'ext' : "28-000005945f57", 'int' : "28-00000a418b77"}
therm_addr = {'ext' : "/sys/bus/w1/devices/" + therm_dev_name['ext'] + "/w1_slave",
              "int" : "/sys/bus/w1/devices/" + therm_dev_name['int'] + "/w1_slave"}

# Names for database tables
therm_log_name = {'ext': "temp_log", 'int': "int_temp_log"}

mydb = mysql.connector.connect(
    host = "localhost",
    user = "pi_observer_root",
    passwd = "piobserverroot",
    database = "pi_observer_data_logs")

# Get timestamp
time_stamp = dt.datetime.now().strftime('%Y-%m-%d %H:%M:%S')

# Iterate through therms addresses and dev names simultaneously.
for key in therm_addr.keys() & therm_log_name.keys():
    with open(therm_addr[key], mode = "r") as therm:
        lines = therm.readlines()
        # Read the Linux device file and find temp value line with regex.
        for line in lines:
            match = re.search(r'(?<=t=)[0-9]*', line)
            if(match):
                temp = float(match.group()) / 1000

    # Write log to txt file
    with open("/var/log/pi_observer/" + therm_log_name[key], \
            mode = "a+") as temp_log:
        temp_log.write("[" + time_stamp + "] " + " t=" + str(temp) + "C\n")

    # Write log to database
    mycursor = mydb.cursor()
    sql = "INSERT INTO " + therm_log_name[key] \
        + "(time, value, unit) VALUES (%s, %s, %s)"
    val = (time_stamp, temp, "C")
    mycursor.execute(sql, val)
    mydb.commit()
