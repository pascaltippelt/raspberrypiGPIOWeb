# Documentation

## GPIO pre-boot-states

The gpio are set to "OUTPUT, PULLUP, DRIVE HIGH" right at the beginning before the os boots. This is achieved by an entry in the /boot/config.txt file on the small FAT partition on the SD card. This enshures no illegal states while booting, especially with those common relais-boards from ebay / amazon.

```
#Add GPIO pre-boot-states
echo "Configuring pre-boot environment."
echo "#GPIO pre-boot-states for Raspberry Pi GPIO Web" >> /boot/config.txt
echo "gpio=5,6,9,10,11,13,17,22,27=op,pu,dh" >> /boot/config.txt
echo "#More info at https://www.raspberrypi.org/documentation/configuration/config-txt/gpio.md" >> /boot/config.txt
```

## Apache2 configuration

Apache will is configured to listen to port 8080. 

```
Listen 8080
<VirtualHost *:8080>
    DocumentRoot "/var/www/html/gpio"
</VirtualHost>
```
