#!/usr/bin/python3
import datetime as dt
import matplotlib.pyplot as plt
import matplotlib.dates as md
from matplotlib.pyplot import figure
import re

log_names = ['temp_log', 'int_temp_log', 'press_log', 'alt_log', 'hum_log']
y_labels = ['External emperature (Celcius)',
        'Internal temperature (Celcius)',
        'Pressure (hPa)',
        'Altitude (m)',
        'humidity (%)']

for log_name, y_label in zip(log_names, y_labels):
    times_stamps_x = []
    log_vals_y = []

    with open('/var/log/pi_observer/' + log_name, mode = 'r') as logFile:
        line = 1
        while line:
            line = logFile.readline()

            y_value_match = re.search(r'(?<=[thpa]=)[0-9\.]*', line)
            if(y_value_match):
                log_vals_y.append(float(y_value_match.group()))
            
            x_value_match = re.search(r'(?<=\[)[^\]]*', line)
            if(x_value_match):	
                times_stamps_x.append(
                    dt.datetime.strptime(
                        x_value_match.group(), '%Y-%m-%d %H:%M:%S'))

    # Chart plotting
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

    plt.savefig('/var/log/pi_observer/' + log_name +'.png')
