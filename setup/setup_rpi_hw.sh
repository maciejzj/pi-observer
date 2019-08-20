#!/bin/bash
# Thanks to: http://www.uugear.com/portfolio/a-single-script-to-setup-i2c-on-your-raspberry-pi/

function setup_i2c()
{
	if grep -q "i2c-bcm2708" /etc/modules; then
	  echo -e "${RED}Seems i2c-bcm2708 module already exists, skip this step.${NOCOLOR}"
	else
	  echo -e "i2c-bcm2708" >> /etc/modules
	fi
	if grep -q "i2c-dev" /etc/modules; then
	  echo -e "${RED}Seems i2c-dev module already exists, skip this step.${NOCOLOR}"
	else
	  echo -e "i2c-dev" >> /etc/modules
	fi
	if grep -q "dtparam=i2c1=on" /boot/config.txt; then
	  echo -e "${RED}Seems i2c1 parameter already set, skip this step.${NOCOLOR}"
	else
	  echo -e "dtparam=i2c1=on" >> /boot/config.txt
	fi
	if grep -q "dtparam=i2c_arm=on" /boot/config.txt; then
	  echo -e "${RED}Seems i2c_arm parameter already set, skip this step.${NOCOLOR}"
	else
	  echo -e "dtparam=i2c_arm=on" >> /boot/config.txt
	fi
}

function setup_hum_sensor()
{
	git clone -q https://github.com/Gozem/am2320.git
	head -n -5 am2320/am2320.py > /usr/lib/python3.7/am2320.py
	rm -rf am2320
}

function setup_one_wire()
{
	if grep -q "dtoverlay=w1-gpio,gpiopin=4" /boot/config.txt; then
	  echo -e "${RED}Seems 1 wire parameter already set, skip this step.${NOCOLOR}"
	else
	  echo -e "dtoverlay=w1-gpio,gpiopin=4" >> /boot/config.txt
	fi

	if grep -q "dtoverlay=w1-gpio,gpiopin=17" /boot/config.txt; then
	  echo -e "${RED}Seems 1 wire parameter already set, skip this step.${NOCOLOR}"
	else
	  echo -e "dtoverlay=w1-gpio,gpiopin=17" >> /boot/config.txt
	fi
}

function setup_rtc()
{
	if grep -q "rtc-ds1307" /etc/modules; then
	  echo -e "${RED}Seems rtc-ds1307 module already exists, skip this step.${NOCOLOR}"
	else
	  echo -e "rtc-ds1307" >> /etc/modules
	fi
	sed -ie '/if \[ -e \/run\/systemd\/system \] ; then/,+2 s/^/#/' /lib/udev/hwclock-set
}

function setup_gpio()
{
	wget -q https://unicorn.drogon.net/wiringpi-2.46-1.deb -P /tmp
	sudo dpkg -i /tmp/wiringpi-2.46-1.deb
}

