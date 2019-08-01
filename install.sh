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
	 
# Load setup functions
. ./setup/setup_rpi_hw.sh

# Enable i2c bus on Rpi
setup_i2c

# Install apt apps from pkglist 
echo -e "${BLUE}==> Installing apt packages${NOCOLOR}"
sudo apt -y install `cat setup/pkglist`
echo -e "${GREEN}Apt packages installation done${NOCOLOR}"

# Install pip modules from requirements.txt
echo -e "${BLUE}==> Installing python modules${NOCOLOR}"
pip3 install -r setup/requirements.txt
echo -e "${GREEN}Python modules installation done${NOCOLOR}"

echo -e "${BLUE}==> Setting up database${NOCOLOR}"
mysql --user=root < setup/db_setup.sql
echo -e "${GREEN}Database setup done${NOCOLOR}"

