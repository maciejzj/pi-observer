#!/bin/bash
# Thanks to: http://www.uugear.com/portfolio/a-single-script-to-setup-i2c-on-your-raspberry-pi/

function setup_i2c()
{
	# Enable I2C on Raspberry Pi
	echo -e "${BLUE}==> Enabling I2C${NOCOLOR}"
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
	 
	# Install i2c-tools
	echo -e "${BLUE}==> Install i2c-tools${NOCOLOR}"
	if hash i2cget 2>/dev/null; then
	  echo -e "${RED}Seems i2c-tools is installed already, skip this step.${NOCOLOR}"
	else
	  apt-get install -y i2c-tools
	fi
	echo -e "${GREEN}Done, i2c is set up${NOCOLOR}"
}

