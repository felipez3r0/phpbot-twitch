import RPi.GPIO as GPIO

import time
GPIO.setwarnings(False)
GPIO.setmode (GPIO.BOARD)
GPIO.setup (12,GPIO.OUT)

p = GPIO.PWM(12, 50)
duty = 0

p.start(duty)
for change_duty in range(0,101,10):
    p.ChangeDutyCycle(change_duty)
    time.sleep(0.1)
for change_duty in range(100, -1, -10):
    p.ChangeDutyCycle(change_duty)
    time.sleep(0.1)
p.stop()

