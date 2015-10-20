import asyncio
from asyncio.queues import Queue
import logging
from datetime import datetime

from tinkerforge.ip_connection import IPConnection
import pymysql
import websockets
import json

import RPi.GPIO as GPIO
import lib_sispm
from lib_tinkerforge import BrickletRemote, BrickletDualRelay
from lib_timerswitch import TimerSwitch

machine_ip = "192.168.127.99"
machine_port = 5555
consumers_gui = []


def action(title, scheduler_id, switch_to, date_stop, switch_typ, arg_a, arg_b, arg_c, arg_d):
    check_date_stop(scheduler_id, switch_to, date_stop)
    if switch_typ == "tf_remote":
        action_tf_remote(title, switch_to, arg_a, arg_b, int(arg_c))
    elif switch_typ == "tf_dual":
        action_tf_dual_relay(title, switch_to, arg_a)
    elif switch_typ == "usb_socket":
        action_usb_socket(title, switch_to, int(arg_a))
    elif switch_typ == "gpio":
        action_gpio(title, switch_to, int(arg_a))


def action_tf_remote(title, switch_to, remote_type, remote_code_a, remote_code_b):
    logger.info('Timerswitch ... %s (tf_remote) : %s' % (title, switch_to))
    switch_to = 1 if switch_to else 0
    bricklet_tf_remote.send(remote_type, remote_code_a, remote_code_b, switch_to)


def action_tf_dual_relay(title, switch_to, relay):
    logger.info('Timerswitch ... %s (tf_dual) : %s' % (title, switch_to))
    switch_to = 1 if switch_to else 0
    if relay == "a":
        bricklet_dual_relay.a = switch_to
    elif relay == "b":
        bricklet_dual_relay.b = switch_to


def action_usb_socket(title, switch_to, number):
    logger.info('Timerswitch ... %s (usb_socket) : %s' % (title, switch_to))
    usb_steckdose.set_outlet_enabled(number, switch_to)


def action_gpio(title, switch_to, gpio_pin):
    logger.info('Timerswitch ... %s (gpio) : %s' % (title, switch_to))
    GPIO.setup(gpio_pin, GPIO.OUT)
    if switch_to:
        GPIO.output(gpio_pin, GPIO.HIGH)
    else:
        GPIO.output(gpio_pin, GPIO.LOW)


def check_date_stop(scheduler_id, switch_to, date_stop):
    logger.debug('Timerswitch ... id = %s  switch_to = %s  date_stop = %s  date_now = %s ' % (
    scheduler_id, switch_to, date_stop, datetime.now()))
    if (not switch_to) and (date_stop is not None):
        if datetime.now() >= date_stop:
            timer.delete_db(scheduler_id)


@asyncio.coroutine
def sending_loop_gui(websocket):
    # create sending-queue
    loop = asyncio.get_event_loop()
    sending_queue_gui = Queue()
    logger.info('websockets .... GUI Queue startet')

    def changed(tmp):
        loop.call_soon_threadsafe(sending_queue_gui.put_nowait, tmp)

    try:
        consumers_gui.append(changed)
        logger.info('websockets .... ein GUI-Client wurde in die Queue aufgenommen')

        while True:
            data = yield from sending_queue_gui.get()
            yield from websocket.send(data)
            logger.debug('websockets .... Sende json Daten -> GUI : %s' % data)

    finally:
        consumers_gui.remove(changed)
        logger.info('websockets .... ein GUI-Client wurde aus der Queue entfernt')


@asyncio.coroutine
def socket_handler_gui(websocket, path):
    # set up sending-queue
    task = asyncio.async(sending_loop_gui(websocket))

    while True:
        message = yield from websocket.recv()

        if message is None:
            break

        logger.debug('websockets .... Empfange json Daten von GUI-Client : %s' % message)
        gui_message_handler(message)

    task.cancel()


def gui_message_handler(message):
    # decode JSON String
    message = json.loads(message)

    # extract variables from json
    command = message[0]
    scheduler_id = message[1]
    logger.debug(
        'websockets .... GUI -> Nachricht: %s ID =  %s' % (command, scheduler_id))

    if command == "new":
        timer.load(scheduler_id)
    elif command == "update":
        timer.reload(scheduler_id)
    elif command == "delete":
        timer.delete_job(scheduler_id)


def set_logging():
    console_handler = logging.StreamHandler()
    formatter = logging.Formatter('%(asctime)s : %(message)s', '%Y-%m-%d %H:%M:%S')
    console_handler.setFormatter(formatter)
    logger.addHandler(console_handler)


if __name__ == '__main__':
    #
    # set up Logging Deamon
    #
    logger = logging.getLogger('Timerswitch')
    logger.setLevel(logging.DEBUG)
    set_logging()
    logger.info('Timerswitch ... startet')
    #
    # set up MySQL Connection
    #
    mysql_connection = pymysql.connect(host="192.168.127.10", port=3306, user="smartHome", passwd="smartHome",
                                       db="smartHome",
                                       autocommit=True)
    mysql_cursor = mysql_connection.cursor(pymysql.cursors.DictCursor)
    logger.info('mySQL ......... Verbindung online')
    #
    # set up Tinkerforge
    #
    tinkerforge_connection = IPConnection()
    bricklet_dual_relay = BrickletDualRelay("ryE", tinkerforge_connection, logger, consumers_gui)
    bricklet_tf_remote = BrickletRemote("jMj", tinkerforge_connection, logger, consumers_gui)
    tinkerforge_connection.connect(machine_ip, 4223)
    logger.info('Tinkerforge ... System online')
    #
    GPIO.setmode(GPIO.BOARD)
    usb_steckdose = lib_sispm.Sispm()
    #
    # set up TimerSwitch
    #
    timer = TimerSwitch(logger, mysql_cursor, action)
    #
    # set up WebSocktes
    #
    gui_server = websockets.serve(socket_handler_gui, machine_ip, machine_port)
    asyncio.get_event_loop().run_until_complete(gui_server)
    logger.info('websockets .... System online')
    asyncio.get_event_loop().run_forever()
