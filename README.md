power
=====

Power meter monitoring directly on the Pi using Open Energy Monitor and an LDR

I've forked this from yfory's power meter: https://github.com/yfory/power

His uses a capacitor/resistor for the LDR, I use a transistor based circuit instead to provide a digital on/off signal.
His version also writes values to an SQLite database and graphs them itself, this does not.

If these features are useful to you, I suggest you check his code.

# Requirements
* Raspberry Pi
* Photoresistor (Light Dependent Resistor)
* 10k Ohm resistor
* 1k resistors
* NPN Transistor - I used a BC547
* Relevant Cables/Connectors
* Modern Electricity Meter

Modern electricity meters have a blinking/flashing LED, often with small text that reads 1000 Imp/kWh. The two important things here are that you have a blinking LED, and that you know the number e.g. 800. Without these, this project will not work for you.

This code is set up for 1000 Imp/kWh, the value of 60 on line 65 sets this. 72 should work for 800 Imp/kWh or 30 for 2000 Imp/kWh.

This project uses the LDR as one half of a voltage divider to trigger a transistor which is connected to a GPIO pin on the Pi.

The circuit is documented here: http://pyevolve.sourceforge.net/wordpress/?p=2383

I used a 1k resistor between base and ground to make it more sensitive and later a 2k2 potentiometer in its place to provide some adjustment but I've not had to adjust it. 1k should be fine.

# Software Installation
On your Raspberry Pi, you will need to ensure that you have certain Python related files installed. To make sure, type the following commands...

```bash
sudo apt-get install python-dev python-pip
sudo pip install apscheduler
```

The above installs the advanced python scheduler used by the code.
Now you will want to download the files from this github repository. To do so, type the following commands...

```bash
sudo apt-get install git
git clone https://github.com/kieranc/power.git && cd power
```

The file named power-monitor is used to automatically start the data logging process on boot and stop on shutdown. For testing purposes, you do not need this script. However, you should make use of it if you are setting up a more permanent solution.

```bash
sudo cp power-monitor /etc/init.d/
sudo chmod a+x /etc/init.d/power-monitor
sudo update-rc.d power-monitor defaults
```

**Note:** Be sure to check the power-monitor file to make sure that the path to the Python application, monitor.py, matches with the path on your system. For example, /home/pi/power/power.py

Due to Python's inability to respond to an interrupt, I've used a very simple C app to listen for an interrupt triggered when the LDR detects a pulse. Monitor.py counts these pulses and each minute, creates a power reading in watts which it sends to EmonCMS' API.
I'm not much of a coder so a lot of the code is borrowed from other people, I've included all sources as far as I'm aware.
The C app came from Radek "Mrkva" Pilar on the raspberrypi.org forums: http://www.raspberrypi.org/phpBB3/viewtopic.php?f=44&t=7509
This app will need compiling like so:

```bash
gcc gpio-irq-demo.c -o gpio-irq
```

Put it somewhere accessible - I used /usr/local/bin, this will need modifying at the bottom of monitor.py if you put it somewhere else.

Once all this is done you can start the data logging process...

```bash
sudo /etc/init.d/power-monitor start
```

This script is configure only to submit its output to the EmonCMS API.
You can read how to set up EmonCMS on the Pi here: http://openenergymonitor.org/emon/emoncms/installing-ubuntu-debian-pi
Once you have set up EmonCMS you will need to get your API key and put it in monitor.py, line 69.
After that you'll need to tell EmonCMS what to do with the data - I'm not entirely clear on this bit myself yet but if you set it 
just to log to feed you can see easily if it's receiving data. Alternatively, check the web server access logs for API requests
which should happen every minute.


# License

Copyright (c) 2012 Edward O'Regan

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

