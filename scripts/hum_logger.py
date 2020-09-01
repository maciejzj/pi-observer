#!/usr/bin/env python3

'''
Read humidity from AM2320 sensor and log readout to database.
'''

import datetime as dt

import am2320
import mysql.connector


def read_hum():
    ''' Read humidity from am2320 sensor. '''
    hum_sensor = am2320.AM2320()
    _, humidity = hum_sensor.readSensor()
    return humidity


def insert_hum_to_db(hum, table_name):
    ''' Insert humidity readout to database. '''
    mydb = mysql.connector.connect(
        host='localhost',
        user='pi_observer_root',
        passwd='piobserverroot',
        database='pi_observer_data_logs'
    )

    time_stamp = dt.datetime.now().strftime('%Y-%m-%d %H:%M:%S')

    mycursor = mydb.cursor()
    sql = 'INSERT INTO ' + table_name + '(time, value, unit) VALUES (%s, %s, %s)'
    val = (time_stamp, hum, '%')
    mycursor.execute(sql, val)
    mydb.commit()


def log_hum():
    ''' Read and log humidity. '''
    hum = read_hum()
    insert_hum_to_db(hum, 'hum_log')


if __name__ == '__main__':
    log_hum()
