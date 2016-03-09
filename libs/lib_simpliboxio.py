import json
import usb.core, sys


Vendor_ID = 0x16C0   # VUSB VID
Product_ID = 0x05DF  # VUSB PID for HID


class SimpliBoxIO:
    def __init__(self, switch_id, ip, logging_daemon, queue):
        self.switch_id = switch_id
        self.ip = ip
        self._logging_daemon = logging_daemon
        self._queue = queue
        self.interface = 0
        self.dev = usb.core.find(idVendor = Vendor_ID,idProduct = Product_ID)
        if self.dev is None:
            self._logging_daemon.info('SimpliBoxIO.... Device not found')
        else:
            if sys.platform != "win32":
                if self.dev.is_kernel_driver_active(self.interface) is True:
                    self.dev.detach_kernel_driver(self.interface)
                    usb.util.claim_interface(self.dev, self.interface)
                    self.dev = usb.core.find(idVendor=Vendor_ID,idProduct=Product_ID)
            self.dev.set_configuration()
            self._logging_daemon.info('SimpliBoxIO.... initialisiert')

    def status(self, bit_number):
        tmp = self.dev.ctrl_transfer(0xA1, 0x01, 0x03, 0, 3)
        return ((tmp[1] & (1 << int(bit_number))) != 0)

    def set_switch(self, switch_to, arg_a, arg_b, arg_c, arg_d):
        tmp = self.dev.ctrl_transfer(0xA1, 0x01, 0x03, 0, 3)
        if ((tmp[1] & (1 << int(arg_a))) != 0) != switch_to:
            bit = 1 << int(arg_a)
            datapack= 0x01, 0x00, tmp[1] ^ bit
            tmp = self.dev.ctrl_transfer(0x21, 0x09, 0x03, 0, datapack)
        self._logging_daemon.debug(
            'SimpliBoxIO.... geschaltet Relais %s , SOLL = %s , IST = %s' % (arg_a, switch_to, self.status(arg_a)))
        tmp_json = json.dumps(["switch_changed_status", self.ip, self.switch_id, switch_to])
        for consumer in self._queue:
            consumer(tmp_json)
            self._logging_daemon.info(
                'SimpliBoxIO.... Relais %s , send %s -> SocketServer Warteschlange ' % (arg_a, self.status(arg_a)))
