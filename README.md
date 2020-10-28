# Raspberry Pi GPIO Web Control

This project enables you to simple, fast and easy control your GPIOs on a Raspberry Pi or other Wiringpi-compatible board running a linux os. The example-configuration is for a 8 relais module with low-triggered io like [this one](https://amzn.to/3jAYWzl) (paid link).

<img src="https://images-na.ssl-images-amazon.com/images/I/71AdP68p7hL._SL1500_.jpg" height="200px" alt="Sample image of a relais board" />

## Prerequisites

- [Wiringpi](http://wiringpi.com/) compatible board
- Linux OS with apache2 and php (recommended: Debian or derivate)

## 1-2-3 Installation

1. Get the install script: `wget https://raw.githubusercontent.com/pascaltippelt/raspberrypiGPIOWeb/main/install.sh`
2. Make the script executeable: `chmod +x install.sh`
3. Execute the installer as root: `sudo ./install.sh`

## Usage

After the installation you may access your GPIO control via http://[IP of your Board]:8080 . E.g.: http://192.168.0.100:8080
