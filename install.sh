#!/bin/bash

GREEN='\033[1;32m'
RED='\033[1;31m'
BLUE='\033[1m\033[34m'
NOCOLOR='\033[0m'

# Check if sudo is used
if [ "$(id -u)" != 0 ]; then
  echo -e "${RED}Installation script must be run with root permissions${NOCOLOR}"
  exit 1
fi
	 
# Install apt apps from pkglist 
echo -e "${BLUE}==> Installing apt packages${NOCOLOR}"
sudo apt -y install `cat setup/pkglist`
echo -e "${GREEN}Apt packages installation done${NOCOLOR}"

# Install pip modules from requirements.txt
echo -e "${BLUE}==> Installing python modules${NOCOLOR}"
pip3 install -r setup/requirements.txt
echo -e "${GREEN}Python modules installation done${NOCOLOR}"
# Load setup functions
. ./setup/setup_rpi_hw.sh

# Enable i2c bus on Rpi
echo -e "${BLUE}==> Setting up hardware buses${NOCOLOR}"
setup_i2c
setup_one_wire
setup_hum_sensor
setup_rtc
echo -e "${GREEN}Hardware setup done${NOCOLOR}"

echo -e "${BLUE}==> Setting up database${NOCOLOR}"
mysql --user=root < setup/db_setup.sql
echo -e "${GREEN}Database setup done${NOCOLOR}"

# Create directories
echo -e "${BLUE}==> Setting up required directories${NOCOLOR}"
[ ! -d /var/log/pi_observer ] && mkdir /var/log/pi_observer
echo -e "${GREEN}Setup done${NOCOLOR}"

# Install system scripts
install ./scripts/* /usr/bin

