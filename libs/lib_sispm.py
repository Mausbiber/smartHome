# dev=sispm.Sispm()             # Get first available device.
# dev.set_switch(1,False)       # Switch second outlet off.
# print dev.status(2)           # Print status of third outlet.

import json
import usb, struct, sys

VENDOR_ID = 0x04B4
PRODUCT_ID_SISPM = 0xFD11
PRODUCT_ID_MSISPM_OLD = 0xFD10
PRODUCT_ID_MSISPM_FLASH = 0xFD12
PRODUCT_ID_MSISPM_FLASH_NEW = 0xFD13


class SispmException(Exception):
    pass


class Sispm:
    def __init__(self, switch_id, ip, logging_daemon, queue, num=0):
        self.switch_id = switch_id
        self.ip = ip
        self._logging_daemon = logging_daemon
        self._queue = queue

        cnt = 0
        busses = usb.busses()
        for bus in busses:
            for device in bus.devices:
                if device.idVendor == VENDOR_ID \
                        and device.idProduct in [PRODUCT_ID_SISPM,
                                                 PRODUCT_ID_MSISPM_OLD, PRODUCT_ID_MSISPM_FLASH,
                                                 PRODUCT_ID_MSISPM_FLASH_NEW]:

                    if num == cnt:
                        self.device = device
                        self.deviceHandle = self.device.open()
                        return

                    else:
                        cnt += 1

        self._logging_daemon.info('SIS USB ....... initialisiert')
        raise SispmException("Sispm device not found.")

    def _usb_command(self, b1, b2, dir_in=False):
        """ Send a usb command. """
        if dir_in:
            req = 0x01
            reqtype = 0x21 | 0x80;
            buf = 2

        else:
            req = 0x09
            reqtype = 0x21  # USB_DIR_OUT+USB_TYPE_CLASS+USB_RECIP_INTERFACE
            buf = struct.pack("BB", b1, b2)

        buf = self.deviceHandle.controlMsg(reqtype, req, buf, (0x03 << 8) | b1, 0, 500)

        if dir_in:
            return buf[1] != 0

    def _check_outlet(self, num):
        if num < 0 or num >= self.get_num_outlets():
            raise SispmException("Outlet %d doesn't exist on this device." % num)

    def set_switch(self, switch_to, arg_a, arg_b, arg_c, arg_d):
        self._check_outlet(int(arg_a))
        self._usb_command(3 * (int(arg_a) + 1), {False: 0x00, True: 0x03}[switch_to])
        self._logging_daemon.debug(
            'SIS USB ....... geschaltet Relais %s , SOLL = %s , IST = %s' % (arg_a, switch_to, self.status(arg_a)))
        tmp_json = json.dumps({
            "usage": "switch_changed_status",
            "ip": self.ip,
            "id": self.switch_id,
            "value": switch_to
        })
        for consumer in self._queue:
            consumer(tmp_json)
            self._logging_daemon.info(
                'SIS USB ....... Relais %s , send %s -> SocketServer Warteschlange ' % (arg_a, self.status(arg_a)))

    def status(self, number):
        self._check_outlet(int(number))
        return self._usb_command(3 * (int(number) + 1), 0x03, True)

    def set_buzzer_enabled(self, onoff):
        self._usb_command(1, {False: 0x00, True: 0x03}[onoff])

    def get_num_outlets(self):
        if self.device.idProduct in \
                [PRODUCT_ID_MSISPM_OLD, PRODUCT_ID_MSISPM_FLASH]:
            return 1

        elif self.device.idProduct in \
                [PRODUCT_ID_SISPM, PRODUCT_ID_MSISPM_FLASH_NEW]:
            return 4

        else:
            return None
