# balloonS

A remote control and sensor data visualization for Raspberry Pi based high altitude balloon.
  
[TOC]

## Introduction
### Brief description
The system provides location, temperature, humidity, pressure and altitude logging with camera streaming and additional GPIO remote controls with a web server hosted on a mobile platform.
### Detailed description
The goal of this project is to make and build a microsystem that will be then placed inside of capsule carried by a high altitude balloon. The system will be powered by Raspberry Pi and gather information about conditions in different parts of atmosphere. All the data will be presented and accessible via web server hosted by the Pi and backed up evenly.

The Internet Technologies part of this program is crucial because of three reasons:

1. The balloon has to be recovered after landing, therefore the system is about to provide location for the landing zone, so the balloon can be easily found. This is achieved by three developements:
 
 	* GPS logging and presenting it by Google maps on hosted website
 	* Providing a camera livestream
 	* Activating buzzer accessed via the hosted website
 	
1. The gathered data should be backed up and remotely accessible in case of the recovery being not viable. In this scenario even if the hardware is lost the data will be gained.
1. The gathered data should be presented in a readible form, easy to process and analyze. Therefore the hosted server ought to provide user-friendly interface with interactive charts and neatly formatted tables.


### The initial status of the project during startup
The project has been started before the beginning of the Internet Technologies subject. At the startup the system has fully $inlin$ functioning sensor hardware and contains:

	* $I^2C$ based humidity and pressure sensors as well as RTC module
	* GPS connected via *UART*
	* Two thermometers connected via *1-wire*
	* Web server based on Apache2, with MySQL user base
	* Webcam streaming video using motion
	* Functioning remote GPIO controlls accessed by the web page
	* Web page presenting the data in a non interactive way, charts are images and logs are row data
	* Embedded Google Maps window with route markers

Since the sensors hardware and offline back-end (made in Python) are not the subject of Internet Technologies they will not be mentioned again. The progress of this part of project, not related to web based services, can be tracked on GitHub.

### The enhancements that will be implemented during the Internet Technologies project
The aim of the enhancements is to make web page more interactive and provide easier way to analyze data. The logging system will also be upgraded. During Internet Technologies project we are about to:

1. Create a system for registering new users. A person who will be in need of having an account will be able to register. It will cause sending an e-mail to the administrator who will have to confirm the registration.
1. Gather the sensor logs in MySQL database rather than inside text files, the tables presenting data should be nicely embedded on the web page.
1. Generate interactive log charts on client's side by a JavaScript library.
1. Make the site adaptive to mobile devices.

### Planned schedule of work:

|    date    | task number |                              planned task description                             |
|:----------:|:-----------:|:---------------------------------------------------------------------------------:|
| 2018-10-10 |      W1     | schedule and goals determination                                                  |
| 2018-10-17 |      W2     | new user registration with admin e-mail confirmation                              |
| 2018-10-24 |      W3     | storing data in both MySQL and text files                                         |
| 2018-10-31 |      W4     | presenting the data in tables embedded on the website (data stored in MySQL only) |
| 2018-11-07 |      W5     | interactive log charts on client's site                                           |
| 2018-11-14 |      W6     | tables, charts CSS styling, desktop version polishing                             |
| 2018-11-21 |      W7     | mobile adaptation - mobile version of the web page 1                              |
| 2018-11-28 |      W8     | mobile adaptation - mobile version of the web page 2                              |
| 2018-12-05 |      W9     | extra time(for unpredicted delays)                                                |
