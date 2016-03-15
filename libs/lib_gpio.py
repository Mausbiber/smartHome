import json
import RPi.GPIO as GPIO


class RaspiGPIO:
    def __init__(self, switch_id, ip, logging_daemon, queue):
        self.switch_id = switch_id
        self.ip = ip
        self._logging_daemon = logging_daemon
        self._queue = queue
        self._logging_daemon.info('RaspiGPIO ..... initialisiert')

    @staticmethod
    def status(number):
        GPIO.setmode(GPIO.BOARD)
        # GPIO.setup(int(number), GPIO.IN)
        return GPIO.input(int(number))

    def set_switch(self, switch_to, arg_a, arg_b, arg_c, arg_d):
        GPIO.setmode(GPIO.BOARD)
        GPIO.setup(int(arg_a), GPIO.OUT)
        if switch_to:
            GPIO.output(int(arg_a), GPIO.HIGH)
        else:
            GPIO.output(int(arg_a), GPIO.LOW)
        self._logging_daemon.debug(
            'RaspiGPIO ..... geschaltet Pin %s , SOLL = %s , IST = %s' % (arg_a, switch_to, self.status(arg_a)))
        tmp_json = json.dumps({
            "usage": "switch_changed_status",
            "ip": self.ip,
            "id": self.switch_id,
            "value": switch_to
        })
        for consumer in self._queue:
            consumer(tmp_json)
            self._logging_daemon.info(
                'RaspiGPIO ..... Pin %s , send %s -> SocketServer Warteschlange ' % (arg_a, self.status(arg_a)))
