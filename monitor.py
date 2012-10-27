#!/usr/bin/python

"""
	Power Monitor
	Logs power consumption to an SQLite database, based on the number
	of pulses of a light on an electricity meter.

	Copyright (c) 2012 Edward O'Regan

	Permission is hereby granted, free of charge, to any person obtaining a copy of
	this software and associated documentation files (the "Software"), to deal in
	the Software without restriction, including without limitation the rights to
	use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
	of the Software, and to permit persons to whom the Software is furnished to do
	so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.
"""

import RPi.GPIO as GPIO, sqlite3 as sqlite, time, os

GPIO.setmode(GPIO.BCM)
currentR=0
dbconn=None
pulsedb="/var/db/power.db"

def GetResistance(GPIOPin):
	resistance=0
	GPIO.setup(GPIOPin, GPIO.OUT)
	GPIO.output(GPIOPin, GPIO.LOW)
	time.sleep(0.1)

	GPIO.setup(GPIOPin, GPIO.IN)
	while(GPIO.input(GPIOPin)==GPIO.LOW):
		resistance+=1
	return resistance


def InsertPulse():
	try:
		dbconn=sqlite.connect(pulsedb)
		dbdata=dbconn.cursor()
		dbdata.execute("insert into pulse values(strftime('%s','now'))")
		dbconn.commit()
	except sqlite.Error,e:
		print e
		if dbconn:
			dbconn.rollback()
	finally:
		if dbconn:
			dbconn.close()


while True:
	previousR=currentR
	currentR=GetResistance(4)


	if currentR<90000:
		if previousR>=90000:
			InsertPulse()

