#!/bin/bash
if [ "$(id -u)" != 0 ]; then
	echo "Start script must be run with root permissions" 
	exit 1
fi

if ! grep -q "pi_observe" /etc/rc.local; then
	sed -ri '/^exit 0/i\/usr/bin/pi_observe.sh' /etc/rc.local
fi

echo "ds1307 0x68" > /sys/class/i2c-adapter/i2c-1/new_device 2> /dev/null
/usr/bin/set_time_gps.py

if [ $? == "0" ]; then
	echo "`date`: time set from GPS" >> /var/log/pi_observer/rtc_log
	hwclock -w
else
	echo "`date`: time set from RTC" >> /var/log/pi_observer/rtc_log
	hwclock -s
fi

#echo "$(echo '* * * * * /usr/bin/temp_logger.py'; crontab -l)" | crontab -
#echo "$(echo '* * * * * /usr/bin/press_alt_logger.py'; crontab -l)" | crontab -
#echo "$(echo '* * * * * /usr/bin/hum_logger.py'; crontab -l)" | crontab -
#echo "$(echo '* * * * * /usr/bin/location_logger.py'; crontab -l)" | crontab -
#echo "$(echo '*/30 * * * * /usr/bin/chart_drawer.py'; crontab -l)" | crontab -

