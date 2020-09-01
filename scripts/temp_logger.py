#!/usr/bin/env python3

'''
Read temperature from 1-Wire based thermometers and log readouts to database.
'''

import datetime as dt
import re

import mysql.connector


def dev_path_from_dev_name(dev_names):
    '''
    Build one 1-Wire Linux device driver file paths from device names.
    Takes a dictionary with arbitary chosen nicknames as keys and device names
    as values.
    '''
    dev_paths = {}
    for dev_nickname in dev_names:
        dev_paths[dev_nickname] = \
            '/sys/bus/w1/devices/' + dev_names[dev_nickname] + '/w1_slave'

    return dev_paths


def read_temp(therm_devs):
    '''
    Read temperature from 1-Wire Linux device.
    Takes a dictionary with arbitary chosen nicknames as keys and device driver
    paths as values.
    '''
    temps = {}
    for dev_nickname in therm_devs:
        with open(therm_devs[dev_nickname], mode='r') as therm:
            lines = therm.readlines()
            for line in lines:
                match = re.search(r'(?<=t=)[0-9]*', line)
                if match:
                    temp = float(match.group()) / 1000
                    temps[dev_nickname] = temp
                    break
    return temps


def insert_temp_to_db(temps, temp_db_table_names):
    '''
    Insert temperature to logs database.
    Takes two dictionaries wirh arbitrary chosen nicknames as keys, first dict
    with temperatures as values and second as table names for each nickname.
    '''
    mydb = mysql.connector.connect(
        host='localhost',
        user='pi_observer_root',
        passwd='piobserverroot',
        database='pi_observer_data_logs'
    )

    time_stamp = dt.datetime.now().strftime('%Y-%m-%d %H:%M:%S')

    for therm_nickname in temps.keys() & temp_db_table_names.keys():
        mycursor = mydb.cursor()
        sql = 'INSERT INTO ' + temp_db_table_names[therm_nickname] \
            + '(time, value, unit) VALUES (%s, %s, %s)'
        vals = (time_stamp, temps[therm_nickname], 'C')
        mycursor.execute(sql, vals)
        mydb.commit()


def log_temp():
    ''' Read and log temperature form external and internal thermometers. '''
    therm_dev_names = {'ext': '28-000005945f57', 'int': '28-00000a418b77'}
    therm_dev_paths = dev_path_from_dev_name(therm_dev_names)
    temps = read_temp(therm_dev_paths)

    temp_db_table_names = {'ext': 'temp_log', 'int': 'int_temp_log'}
    insert_temp_to_db(temps, temp_db_table_names)


if __name__ == '__main__':
    log_temp()
