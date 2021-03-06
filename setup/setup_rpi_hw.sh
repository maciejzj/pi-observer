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
	# Disable automatic clock updates from web
	sed -ie '/if \[ -e \/run\/systemd\/system \] ; then/,+2 s/^/#/' /lib/udev/hwclock-set
}

function setup_camera()
{
	if grep -q "start_x=1" /boot/config.txt; then
		echo -e "${RED}Seems camera parameter already set, skip this step.${NOCOLOR}"
	else
		echo -e "start_x=1" >> /boot/config.txt
	fi
	if grep -q "gpu_mem" /boot/config.txt; then
		sed -i 's/gpu_mem=[0-9]*/gpu_mem=128/' /boot/config.txt
	else
		echo -e "gpu_mem=128" >> /boot/config.txt
	fi
	if grep -q "bcm2835-v412" /etc/modules; then
		echo -e "${RED}Seems camera module already exists, skip this step.${NOCOLOR}"
	else
		echo -e "bcm2835-v4l2" >> /etc/modules
	fi
	sed -i 's/start_motion_daemon=no/start_motion_daemon=yes/' /etc/default/motion
	cp setup/motion.conf /etc/motion/motion.conf
}

