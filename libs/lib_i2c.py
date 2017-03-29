import json
#import RPi.GPIO as GPIO
import smbus
import time

class RaspiI2C:
    def __init__(self, switch_id, ip, logging_daemon, queue):
        self.switch_id = switch_id
        self.ip = ip
        self._logging_daemon = logging_daemon
        self._queue = queue
        self._logging_daemon.info('I2C BUS ..... initialisiert')

    @staticmethod
    def status(arg_a, arg_b):
        # GPIO.setmode(GPIO.BOARD)
        # GPIO.setup(int(number), GPIO.IN)
        # return GPIO.input(int(number))
        bus = smbus.SMBus(1)
        value_read = bus.read_byte(int(arg_b, 0))
        return (value_read & 2**(int(arg_a)-1)) == 0
        #return 0

    def set_switch(self, switch_to, arg_a, arg_b, arg_c, arg_d):
        self._logging_daemon.info('I2C BUS (%s) ..... im File' % (arg_b))
        
        #print("Type of arg_a: " + str(type(arg_a)))
        #print("Type of arg_b: " + str(type(arg_b)))
        
        #GPIO.setmode(GPIO.BOARD)
        #GPIO.setup(int(arg_a), GPIO.OUT)
        bus = smbus.SMBus(1)
        value_read = bus.read_byte(int(arg_b, 0))
        if switch_to:
            #GPIO.output(int(arg_a), GPIO.HIGH)
            value_write = bus.write_byte(int(arg_b, 0), (value_read & ~2**(int(arg_a)-1))) # bitwise a or b
        else:
            #GPIO.output(int(arg_a), GPIO.LOW)
            value_write = bus.write_byte(int(arg_b, 0), (value_read | 2**(int(arg_a)-1))) # bitwise a and complement of b
        self._logging_daemon.debug(
            'I2C Bus (%s) ..... geschaltet Pin %s , SOLL = %s , IST = %s' % (arg_b, arg_a, switch_to, self.status(arg_a, arg_b)))
        tmp_json = json.dumps({
            "usage": "switch_changed_status",
            "ip": self.ip,
            "id": self.switch_id,
            "value": switch_to
        })
        for consumer in self._queue:
            consumer(tmp_json)
            self._logging_daemon.info(
                'I2C Bus (%s) ..... Pin %s , send %s -> SocketServer Warteschlange ' % (arg_b, arg_a, self.status(arg_a, arg_b)))
