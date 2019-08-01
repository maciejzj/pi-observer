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

