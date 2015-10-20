# dev=sispm.Sispm()                # Get first available device.
# dev.set_outlet_enabled(0,True)   # Switch first outlet on.
# dev.set_outlet_enabled(1,False)  # Switch second outlet off.
# print dev.get_outlet_enabled(2)  # Print status of third outlet.

import usb, struct, sys

VENDOR_ID=0x04B4
PRODUCT_ID_SISPM=0xFD11
PRODUCT_ID_MSISPM_OLD=0xFD10
PRODUCT_ID_MSISPM_FLASH=0xFD12
PRODUCT_ID_MSISPM_FLASH_NEW=0xFD13

class SispmException(Exception):
        pass

def get_num_devices():
        cnt=0

        busses=usb.busses()
        for bus in busses:
                for device in bus.devices:
                        if device.idVendor==VENDOR_ID \
                                        and device.idProduct in [PRODUCT_ID_SISPM,
                                        PRODUCT_ID_MSISPM_OLD,PRODUCT_ID_MSISPM_FLASH,
                                        PRODUCT_ID_MSISPM_FLASH_NEW]:
                                cnt+=1

        return cnt
class Sispm:

        def __init__(self, num=0):
                cnt=0

                busses=usb.busses()
                for bus in busses:
                        for device in bus.devices:
                                if device.idVendor==VENDOR_ID \
                                                and device.idProduct in [PRODUCT_ID_SISPM,
                                                PRODUCT_ID_MSISPM_OLD,PRODUCT_ID_MSISPM_FLASH,
                                                PRODUCT_ID_MSISPM_FLASH_NEW]:

                                        if num==cnt:
                                                self.device=device
                                                self.deviceHandle=self.device.open()
                                                return

                                        else:
                                                cnt+=1

                raise SispmException("Sispm device not found.")

        def _usb_command(self, b1, b2, dir_in=False):
                """ Send a usb command. """
                if dir_in:
                        req=0x01
                        reqtype=0x21|0x80;
                        buf=2

                else:
                        req=0x09
                        reqtype=0x21 # USB_DIR_OUT+USB_TYPE_CLASS+USB_RECIP_INTERFACE
                        buf=struct.pack("BB",b1,b2)

                buf=self.deviceHandle.controlMsg(reqtype,req,buf,(0x03<<8)|b1,0,500)

                if dir_in:
                        return buf[1]!=0

        def _check_outlet(self, num):
                if num<0 or num>=self.get_num_outlets():
                        raise SispmException("Outlet %d doesn't exist on this device."%num)

        def set_outlet_enabled(self, outlet, onoff):
                self._check_outlet(outlet)
                self._usb_command(3*(outlet+1),{False: 0x00, True: 0x03}[onoff])

        def get_outlet_enabled(self, outlet):
                self._check_outlet(outlet)
                return self._usb_command(3*(outlet+1), 0x03, True)

        def set_buzzer_enabled(self, onoff):
                self._usb_command(1,{False: 0x00, True: 0x03}[onoff])

        def get_num_outlets(self):
                if self.device.idProduct in \
                                [PRODUCT_ID_MSISPM_OLD,PRODUCT_ID_MSISPM_FLASH]:
                        return 1

                elif self.device.idProduct in \
                                [PRODUCT_ID_SISPM,PRODUCT_ID_MSISPM_FLASH_NEW]:
                        return 4

                else:
                        return None

if __name__=="__main__":
        arg=1

        if len(sys.argv)<=1:
                sys.exit(1)

        dev=None

        while arg<len(sys.argv):
                if sys.argv[arg]=="-list":
                        for i in range(0,get_num_devices()):
                                dev=Sispm(i)
                                print('%d: Gembird device with %s outlet(s). USB device %d.',i ,dev.get_num_outlets() ,dev.device.filename)
                        sys.exit(0)
                elif sys.argv[arg]=="-dev" and arg<len(sys.argv)-1:
                        dev=Sispm(int(sys.argv[arg+1]))
                        arg+=2
                elif sys.argv[arg]=="-on" and arg<len(sys.argv)-1:
                        if dev is None: dev=Sispm()
                        dev.set_outlet_enabled(int(sys.argv[arg+1]),True)
                        arg+=2
                elif sys.argv[arg]=="-off" and arg<len(sys.argv)-1:
                        if dev is None: dev=Sispm()
                        dev.set_outlet_enabled(int(sys.argv[arg+1]),False)
                        arg+=2

                elif sys.argv[arg]=="-status" and arg<len(sys.argv)-1:
                        if dev is None: dev=Sispm()
                        outlet=int(sys.argv[arg+1])
                        print('outlet %d is %s',outlet,{False: "off", True: "on"}[dev.get_outlet_enabled(outlet)])
                        arg+=2
                else:
                        sys.exit(1)

