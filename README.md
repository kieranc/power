power
=====

Simple electricity meter monitoring for Raspberry Pi.

# Requirements
* Raspberry Pi
* Photoresistor (Light Dependent Resistor)
* 10k Ohm Resistor
* 1uF Capacitor
* Relevant Cables/Connectors
* Modern Electricity Meter

Modern electricity meters have a blinking/flashing LED, often with small text that reads 800 Imp/kWh. The two important things here are that you have a blinking LED, and that you know the number e.g. 800. Without these, this project will not work for you.

You will need to wire up a simple circuit, as shown in the diagram, more details about this circuit can be found on the [Adafruit](http://learn.adafruit.com/basic-resistor-sensor-reading-on-raspberry-pi/overview) website; it makes for a great educational set up as well.

**Note:** I have had no problem running this circuit over 10 metres of cheap but reliable 4-core telephone cable. Your mileage may vary, though I am aware of someone else having experimented with 30 metres of cat5 cable without encountering any problems.

# Software Installation
On your Raspberry Pi, you will need to ensure that you have certain Python related files installed. To make sure, type the following commands...
```bash
sudo apt-get install sqlite3 libsqlite3-dev
sudo apt-get install python-dev python-pip
sudo pip install rpi.gpio
```

The above installs SQLite3 and the necessary files for Python to interact with the Raspberry Pi GPIO pins. Now you will want to download the files from this github repository. To do so, type the following commands...
```bash
sudo apt-get install git
git clone https://github.com/yfory/power.git && cd power
```

The file named power-monitor is used to automatically start the data logging process on boot and stop on shutdown. For testing purposes, you do not need this script. However, you should make use of it if you are setting up a more permanent solution.
```bash
sudo cp power-monitor /etc/init.d/
sudo chmod a+x /etc/init.d/power-monitor
sudo update-rc.d power-monitor defaults
```
**Note:** Be sure to check the power-monitor file to make sure that the path to the Python application, power.py, matches with the path on your system. For example, /home/pi/power/power.py

Next, move your database file to a more suitable location...
```bash
sudo mkdir /var/db
sudo cp power.db /var/db/
```

A final sanity check; on my set up, 90000 seemed a reasonable number to use for pulse checking. You may need to modify the power.py file to change this number for your system. It is all dependent on how well you black-taped the photoresistor, how much cable you are using, etc. *Hint: add a __print__ command to the Python code near __while True:__ to check the values.*

Once you have your number in place, you can start the data logging process...
```bash
sudo /etc/init.d/power-monitor start
```

# Setting up a Web Interface
If you already have a web server running on your Raspberry Pi, with PHP enabled, simply copy the contents of the www directory to the relevant location on your Raspberry Pi.

To set up a simple web webserver with PHP, type the following commands:
```bash
sudo apt-get install lighttpd
sudo apt-get install php5-cgi
sudo lighty-enable-mod fastcgi
sudo lighty-enable-mod fastcgi-php
sudo service lighttpd force-reload
```

Now you can copy the PHP file to /var/www, e.g. _cp -r www/* /var/www/_

Finally, edit the *config.php* file located in */var/www* and set the IMPKWH value to match your system (the default is 800).

To view your electricity consumption, open a web browser on your desktop computer and navigate to your Raspberry Pi, for example by typing: _http://192.168.0.3/_

# License

Copyright (c) 2012 Edward O'Regan

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

