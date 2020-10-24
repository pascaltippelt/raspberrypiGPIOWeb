#!/bin/bash
# Installer-script for Raspberry Pi GPIO Web

echo "Welcome to Raspberry Pi GPIO Web installer"
echo "Verifying root-access..."

#check root-access
if [ "$EUID" -ne 0 ]
  then echo "Please run as root. E.g.: sudo ./install.sh"
  exit
fi
echo "Root-access verified."

#Add GPIO pre-boot-states
echo "Configuring pre-boot environment."
echo "#GPIO pre-boot-states for Raspberry Pi GPIO Web" >> /boot/config.txt
echo "gpio=5,6,9,10,11,13,17,22,27=op,pu,dh" >> /boot/config.txt
echo "#More info at https://www.raspberrypi.org/documentation/configuration/config-txt/gpio.md" >> /boot/config.txt

#Install apache2 and php
echo "Installing apache2 and php. This may take a while."
apt update; apt upgrade
apt install apache2 php sudo

#Setting up Website
echo "Setting up php-files."
sudo -u www-data mkdir /var/www/html/gpio
sudo -u www-data wget [github-file] -O /var/www/html/gpio/index.php
