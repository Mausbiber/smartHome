import json

from tinkerforge.bricklet_temperature import Temperature
from tinkerforge.bricklet_humidity import Humidity
from tinkerforge.bricklet_ambient_light import AmbientLight
from tinkerforge.bricklet_barometer import Barometer
from tinkerforge.bricklet_remote_switch import RemoteSwitch
from tinkerforge.bricklet_moisture import Moisture
from tinkerforge.bricklet_ptc import PTC
from tinkerforge.bricklet_dual_relay import DualRelay


# FERTIG
class BrickletTemperature:
    _QUOTIENT = 100.0

    def __init__(self, uid, connection, logging_daemon, queue, value=0.0, trigger_difference=0.1):
        self._bricklet = Temperature(uid, connection)
        self._value = value
        self._value_old = value
        self.trigger_difference = trigger_difference
        self._rising = False
        self._falling = False
        self.uid = uid
        self._logging_daemon = logging_daemon
        self._queue = queue
        self._logging_daemon.info('Tinkerforge ... Temperature-Bricklet UID "%s" initialisiert' % uid)

    def set_callback(self, timeframe=5000):
        self._bricklet.set_temperature_callback_period(timeframe)
        self._bricklet.register_callback(self._bricklet.CALLBACK_TEMPERATURE, self._changed)
        self._logging_daemon.debug('Tinkerforge ... Temperature-Bricklet UID "%s" Callback gesetzt' % self.uid)

    def read(self):
        return self._bricklet.get_temperature() / self._QUOTIENT

    def read_rising(self):
        return self._rising

    def read_falling(self):
        return self._falling

    def _changed(self, tmp_value):
        tmp_value = (tmp_value / self._QUOTIENT)
        if abs(self._value - tmp_value) >= self.trigger_difference:
            tmp_value = (tmp_value / self._QUOTIENT)
        if abs(self._value - tmp_value) >= self.trigger_difference:
            if tmp_value > self._value_old:
                self._rising = True
                self._falling = False
            elif tmp_value < self._value_old:
                self._rising = False
                self._falling = True
            self._logging_daemon.debug(
                'Tinkerforge ... Temperature-Bricklet UID "%s" , Neu = %f , Alt = %f , rising = %s , falling = %s' % (
                    self.uid, tmp_value, self._value_old, self._rising, self._falling))
            self._value_old = tmp_value
            self._value = tmp_value
            tmp_json = json.dumps(["send_changed_data", self.uid, "sensor", "temperature", self._value])
            for consumer in self._queue:
                consumer(tmp_json)
                self._logging_daemon.info(
                    'Tinkerforge ... Temperature-Bricklet UID "%s" , neuer Wert %f -> SocketServer Warteschlange ' % (
                        self.uid, self._value))

    temperature = property(read)
    rising = property(read_rising)
    falling = property(read_falling)


# FERTIG
class BrickletHumidity:
    _QUOTIENT = 10.0

    def __init__(self, uid, connection, logging_daemon, queue, value=0.0, trigger_difference=0.1):
        self.bricklet = Humidity(uid, connection)
        self._value = value
        self._value_old = value
        self.trigger_difference = trigger_difference
        self._rising = False
        self._falling = False
        self.uid = uid
        self._logging_daemon = logging_daemon
        self._queue = queue
        self._logging_daemon.info('Tinkerforge ... Humidity-Bricklet UID "%s" initialisiert' % uid)

    def set_callback(self, timeframe=5000):
        self.bricklet.set_humidity_callback_period(timeframe)
        self.bricklet.register_callback(self.bricklet.CALLBACK_HUMIDITY, self._changed)
        self._logging_daemon.debug('Tinkerforge ... Humidity-Bricklet UID "%s" Callback gesetzt' % self.uid)

    def read(self):
        return self.bricklet.get_humidity() / self._QUOTIENT

    def read_rising(self):
        return self._rising

    def read_falling(self):
        return self._falling

    def _changed(self, tmp_value):
        tmp_value = (tmp_value / self._QUOTIENT)
        if abs(self._value - tmp_value) >= self.trigger_difference:
            if tmp_value > self._value_old:
                self._rising = True
                self._falling = False
            elif tmp_value < self._value_old:
                self._rising = False
                self._falling = True
            self._logging_daemon.debug(
                'Tinkerforge ... Humidity-Bricklet UID "%s" , Neu = %f , Alt = %f , rising = %s , falling = %s' % (
                    self.uid, tmp_value, self._value_old, self._rising, self._falling))
            self._value_old = tmp_value
            self._value = tmp_value
            tmp_json = json.dumps(["send_changed_data", self.uid, "sensor", "humidity", self._value])
            for consumer in self._queue:
                consumer(tmp_json)
                self._logging_daemon.info(
                    'Tinkerforge ... Humidity-Bricklet UID "%s" , neuer Wert %f -> SocketServer Warteschlange ' % (
                        self.uid, self._value))

    humidity = property(read)
    rising = property(read_rising)
    falling = property(read_falling)


# FERTIG
class BrickletAmbientLight:
    _QUOTIENT = 10.0

    def __init__(self, uid, connection, logging_daemon, queue, value=0.0, trigger_difference=0.1):
        self.bricklet = AmbientLight(uid, connection)
        self._value = value
        self._value_old = value
        self.trigger_difference = trigger_difference
        self._rising = False
        self._falling = False
        self.uid = uid
        self._logging_daemon = logging_daemon
        self._queue = queue
        self._logging_daemon.info('Tinkerforge ... AmbientLight-Bricklet UID "%s" initialisiert' % uid)

    def set_callback(self, timeframe=5000):
        self.bricklet.set_illuminance_callback_period(timeframe)
        self.bricklet.register_callback(self.bricklet.CALLBACK_ILLUMINANCE, self._changed)
        self._logging_daemon.debug('Tinkerforge ... AmbientLight-Bricklet UID "%s" Callback gesetzt' % self.uid)

    def read(self):
        return self.bricklet.get_illuminance() / self._QUOTIENT

    def read_rising(self):
        return self._rising

    def read_falling(self):
        return self._falling

    def _changed(self, tmp_value):
        tmp_value = (tmp_value / self._QUOTIENT)
        if abs(self._value - tmp_value) >= self.trigger_difference:
            if tmp_value > self._value_old:
                self._rising = True
                self._falling = False
            elif tmp_value < self._value_old:
                self._rising = False
                self._falling = True
            self._logging_daemon.debug(
                'Tinkerforge ... AmbientLight-Bricklet UID "%s" , Neu = %f , Alt = %f , rising = %s , falling = %s' % (
                    self.uid, tmp_value, self._value_old, self._rising, self._falling))
            self._value_old = tmp_value
            self._value = tmp_value
            tmp_json = json.dumps(["send_changed_data", self.uid, "sensor", "ambient_light", self._value])
            for consumer in self._queue:
                consumer(tmp_json)
                self._logging_daemon.info(
                    'Tinkerforge ... AmbientLight-Bricklet UID "%s" , neuer Wert %f -> SocketServer Warteschlange ' % (
                        self.uid, self._value))

    ambient_light = property(read)
    rising = property(read_rising)
    falling = property(read_falling)


# FERTIG
class BrickletAirPressure:
    _QUOTIENT = 1000.0

    def __init__(self, uid, connection, logging_daemon, queue, value=0.0, trigger_difference=0.5):
        self.bricklet = Barometer(uid, connection)
        self._value = value
        self._value_old = value
        self.trigger_difference = trigger_difference
        self._rising = False
        self._falling = False
        self.uid = uid
        self._logging_daemon = logging_daemon
        self._queue = queue
        self._logging_daemon.info('Tinkerforge ... Barometer-Bricklet UID "%s" initialisiert' % uid)

    def set_callback(self, timeframe=5000):
        self.bricklet.set_air_pressure_callback_period(timeframe)
        self.bricklet.register_callback(self.bricklet.CALLBACK_AIR_PRESSURE, self._changed)
        self._logging_daemon.debug('Tinkerforge ... Barometer-Bricklet UID "%s" Callback gesetzt' % self.uid)

    def read(self):
        return self.bricklet.get_air_pressure() / self._QUOTIENT

    def read_rising(self):
        return self._rising

    def read_falling(self):
        return self._falling

    def _changed(self, tmp_value):
        tmp_value = (tmp_value / self._QUOTIENT)
        if abs(self._value - tmp_value) >= self.trigger_difference:
            if tmp_value > self._value_old:
                self._rising = True
                self._falling = False
            elif tmp_value < self._value_old:
                self._rising = False
                self._falling = True
            self._logging_daemon.debug(
                'Tinkerforge ... Barometer-Bricklet UID "%s" , Neu = %f , Alt = %f , rising = %s , falling = %s' % (
                    self.uid, tmp_value, self._value_old, self._rising, self._falling))
            self._value_old = tmp_value
            self._value = tmp_value
            tmp_json = json.dumps(["send_changed_data", self.uid, "sensor", "air_pressure", self._value])
            for consumer in self._queue:
                consumer(tmp_json)
                self._logging_daemon.info(
                    'Tinkerforge ... Barometer-Bricklet UID "%s" , neuer Wert %f -> SocketServer Warteschlange ' % (
                        self.uid, self._value))

    air_pressure = property(read)
    rising = property(read_rising)
    falling = property(read_falling)


# FERTIG
class BrickletPTC:
    _QUOTIENT = 100.0

    def __init__(self, uid, connection, logging_daemon, queue, value=0.0, trigger_difference=0.1):
        self._bricklet = PTC(uid, connection)
        self._value = value
        self._value_old = value
        self.trigger_difference = trigger_difference
        self.uid = uid
        self._rising = False
        self._falling = False
        self._logging_daemon = logging_daemon
        self._queue = queue
        self._logging_daemon.info('Tinkerforge ... PTC-Bricklet UID "%s" initialisiert' % uid)

    def set_callback(self, timeframe=5000):
        self._bricklet.set_temperature_callback_period(timeframe)
        self._bricklet.register_callback(self._bricklet.CALLBACK_TEMPERATURE, self._changed)
        self._logging_daemon.debug('Tinkerforge ... PTC-Bricklet UID "%s" Callback gesetzt' % self.uid)

    def read(self):
        return self._bricklet.get_temperature() / self._QUOTIENT

    def read_rising(self):
        return self._rising

    def read_falling(self):
        return self._falling

    def _changed(self, tmp_value):
        tmp_value = (tmp_value / self._QUOTIENT)
        if abs(self._value - tmp_value) >= self.trigger_difference:
            if tmp_value > self._value_old:
                self._rising = True
                self._falling = False
            elif tmp_value < self._value_old:
                self._rising = False
                self._falling = True
            self._logging_daemon.debug(
                'Tinkerforge ... PTC-Bricklet UID "%s" , Neu = %f , Alt = %f , rising = %s , falling = %s' % (
                    self.uid, tmp_value, self._value_old, self._rising, self._falling))
            self._value_old = tmp_value
            self._value = tmp_value
            tmp_json = json.dumps(["send_changed_data", self.uid, "sensor", "ptc", self._value])
            for consumer in self._queue:
                consumer(tmp_json)
                self._logging_daemon.info(
                    'Tinkerforge ... PTC-Bricklet UID "%s" , neuer Wert %f -> SocketServer Warteschlange ' % (
                        self.uid, self._value))

    ptc = property(read)
    rising = property(read_rising)
    falling = property(read_falling)


# FERTIG
class BrickletMoisture:

    def __init__(self, uid, connection, logging_daemon, queue, value=0.0, trigger_difference=7.0):
        self._bricklet = Moisture(uid, connection)
        self._value = value
        self._value_old = value
        self.trigger_difference = trigger_difference
        self._rising = False
        self._falling = False
        self.uid = uid
        self._logging_daemon = logging_daemon
        self._queue = queue
        self._logging_daemon.info('Tinkerforge ... Moisture-Bricklet UID "%s" initialisiert' % uid)

    def set_callback(self, timeframe=20000):
        self._bricklet.set_moisture_callback_period(timeframe)
        self._bricklet.register_callback(self._bricklet.CALLBACK_MOISTURE, self._changed)
        self._logging_daemon.debug('Tinkerforge ... Moisture-Bricklet UID "%s" Callback gesetzt' % self.uid)

    def read(self):
        return self._bricklet.get_moisture_value()

    def read_rising(self):
        return self._rising

    def read_falling(self):
        return self._falling

    def _changed(self, tmp_value):
        if abs(self._value - tmp_value) >= self.trigger_difference:
            if tmp_value > self._value_old:
                self._rising = True
                self._falling = False
            elif tmp_value < self._value_old:
                self._rising = False
                self._falling = True
            self._logging_daemon.debug(
                'Tinkerforge ... Moisture-Bricklet UID "%s" , Neu = %f , Alt = %f , rising = %s , falling = %s' % (
                    self.uid, tmp_value, self._value_old, self._rising, self._falling))
            self._value_old = tmp_value
            self._value = tmp_value
            tmp_json = json.dumps(["send_changed_data", self.uid, "sensor", "moisture", self._value])
            for consumer in self._queue:
                consumer(tmp_json)
                self._logging_daemon.info(
                    'Tinkerforge ... Moisture-Bricklet UID "%s" , neuer Wert %f -> SocketServer Warteschlange ' % (
                        self.uid, self._value))

    moisture = property(read)
    rising = property(read_rising)
    falling = property(read_falling)


# FERTIG
class BrickletDualRelay:

    def __init__(self, uid, connection, logging_daemon, queue):
        self._bricklet = DualRelay(uid, connection)
        self.uid = uid
        self._logging_daemon = logging_daemon
        self._queue = queue
        self._logging_daemon.info('Tinkerforge ... DualRelay-Bricklet "%s" initialisiert' % uid)

    def _get_relay_a(self):
        _tmp = self._bricklet.get_state()
        return _tmp[0]

    def _set_relay_a(self, _tmp):
        self._bricklet.set_selected_state(1, _tmp)
        self._logging_daemon.debug(
            'Tinkerforge ... DualRelay-Bricklet UID "%s" , Relais A , Neu = %s' % (self.uid, _tmp))
        tmp_json = json.dumps(["send_changed_data", self.uid, "relay", "a", _tmp])
        for consumer in self._queue:
            consumer(tmp_json)
            self._logging_daemon.info(
                'Tinkerforge ... DualRelay-Bricklet UID "%s" Relais A , neuer Wert %s -> SocketServer Warteschlange ' % (
                    self.uid, self._get_relay_a))

    def _get_relay_b(self):
        _tmp = self._bricklet.get_state()
        return _tmp[1]

    def _set_relay_b(self, _tmp):
        self._bricklet.set_selected_state(2, _tmp)
        self._logging_daemon.info(
            'Tinkerforge ... DualRelay-Bricklet UID "%s" , Relais B , Neu = %s' % (self.uid, _tmp))
        tmp_json = json.dumps(["send_changed_data", self.uid, "relay", "b", _tmp])
        for consumer in self._queue:
            consumer(tmp_json)
            self._logging_daemon.info(
                'Tinkerforge ... DualRelay-Bricklet UID "%s" Relais B , neuer Wert %s -> SocketServer Warteschlange ' % (
                    self.uid, self._get_relay_b))

    a = property(_get_relay_a, _set_relay_a)
    b = property(_get_relay_b, _set_relay_b)


# 50/50 FERTIG
class BrickletRemote:

    def __init__(self, uid, connection, logging_daemon, queue):
        self.bricklet = RemoteSwitch(uid, connection)
        self.uid = uid
        self._logging_daemon = logging_daemon
        self._queue = queue
        self._logging_daemon.debug('Tinkerforge ... Remote-Bricklet "%s" initialisiert' % uid)

    def send(self, _type, _code_a, _code_b, _state):

        self.bricklet.set_repeats(5)
        if _type == "a":
            pass
        elif _type == "b":
            pass
        elif _type == "c":
            self.bricklet.switch_socket_c(_code_a, _code_b, _state)

        tmp_json = json.dumps(["send_changed_data", self.uid, "remote", _type, _state])
        for consumer in self._queue:
            consumer(tmp_json)
            self._logging_daemon.info(
                'Tinkerforge ... Remote-Bricklet UID "%s" send -> SocketServer Warteschlange ' % self.uid)

