#!/usr/bin/env python3

'''
Read pressure from BME280 sensor and calculate altitude, then log readouts to
database.
'''

import datetime as dt
from math import log

import bme280
import smbus2
import mysql.connector


def read_altitude_sensor(sensor_addr):
    ''' Read pressure and temperature form BME280 sensor. '''
    bus_num = 1
    bus = smbus2.SMBus(bus_num)
    sensor_data = bme280.sample(bus, sensor_addr)
    return sensor_data


def calculate_altitude(temp, press):
    ''' Estimate altitude based on temperature and pressure. '''
    press_0 = 1013.25  # Pressure at sea level (hPa)
    u = 0.0289644  # Molar mas of air
    g = 9.8101  # Standard acceleration
    R = 8.314458948  # Universal gas constant

    alt = -(R * (float(temp) + 273) * log(press / press_0) / (u * g))
    return alt


def insert_press_alt_to_db(press, alt):
    ''' Insert pressure and altitude to database. '''
    mydb = mysql.connector.connect(
        host='localhost',
        user='pi_observer_root',
        passwd='piobserverroot',
        database='pi_observer_data_logs'
    )

    time_stamp = dt.datetime.now().strftime('%Y-%m-%d %H:%M:%S')

    mycursor = mydb.cursor()
    sql = 'INSERT INTO press_log(time, value, unit) VALUES (%s, %s, %s)'
    val = (time_stamp, round(press, 4), 'hPa')
    mycursor.execute(sql, val)
    mydb.commit()

    mycursor = mydb.cursor()
    sql = 'INSERT INTO alt_log(time, value, unit) VALUES (%s, %s, %s)'
    val = (time_stamp, str(round(alt, 4)), 'm')
    mycursor.execute(sql, val)
    mydb.commit()


def log_press_alt():
    ''' Read and log pressure and altitude. '''
    sensor_addr = 0x76
    sensor_data = read_altitude_sensor(sensor_addr)
    temp = sensor_data.temperature
    press = sensor_data.pressure
    alt = calculate_altitude(temp, press)
    insert_press_alt_to_db(press, alt)


if __name__ == '__main__':
    log_press_alt()
