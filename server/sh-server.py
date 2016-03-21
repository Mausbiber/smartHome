# !/usr/bin/env python3

import asyncio
import socket
from asyncio.queues import Queue
import logging
from datetime import datetime
import pymysql
import websockets
import json
from lib_timerswitch import TimerSwitch
import sys
import signal
from config import *

SERVER_IP = [l for l in (
    [ip for ip in socket.gethostbyname_ex(socket.gethostname())[2] if not ip.startswith("127.")][:1], [
        [(s.connect(('8.8.8.8', 53)), s.getsockname()[0], s.close()) for s in
         [socket.socket(socket.AF_INET, socket.SOCK_DGRAM)]][0][1]]) if l][0][0]

if STANDALONE:
    MYSQL_HOST = SERVER_IP

consumers_clients = []
consumers_gui = []


def set_exit_handler(func):
    signal.signal(signal.SIGTERM, func)


def on_exit(sig, func=None):
    logger.error('SYSTEM ........ exit handler triggered')
    sys.exit(1)


def action(scheduler_id, switch_id, switch_ip, switch_to, date_stop):
    check_date_stop(scheduler_id, switch_to, date_stop)
    send_to_clients(json.dumps({"usage": "switch_turn", "ip": switch_ip, "id": switch_id, "value": switch_to}))


def check_date_stop(scheduler_id, switch_to, date_stop):
    logger.debug('Timerswitch ... schedulers.id = %s  switch_to = %s  date_stop = %s  date_now = %s ' % (
        scheduler_id, switch_to, date_stop, datetime.now()))
    if (not switch_to) and (date_stop is not None):
        if datetime.now() >= date_stop:
            timer.delete_db(scheduler_id)


@asyncio.coroutine
def sending_loop_clients(websocket):
    # create sending-queue
    loop = asyncio.get_event_loop()
    sending_queue_sensors = Queue()
    logger.info('websockets .... smartHome Queue startet')

    def changed(tmp):
        loop.call_soon_threadsafe(sending_queue_sensors.put_nowait, tmp)

    try:
        consumers_clients.append(changed)
        logger.info('websockets .... ein neuer smartHome-Client wurde in die Queue aufgenommen')

        while True:
            tmp_data = yield from sending_queue_sensors.get()
            yield from websocket.send(tmp_data)
            logger.debug('websockets .... Sende json Daten -> smartHome-Client : %s' % tmp_data)

    finally:
        consumers_clients.remove(changed)
        logger.info('websockets .... ein smartHome-Client wurde aus der Queue entfernt')


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
            tmp_data = yield from sending_queue_gui.get()
            yield from websocket.send(tmp_data)
            logger.debug('websockets .... Sende json Daten -> GUI : %s' % tmp_data)

    finally:
        consumers_gui.remove(changed)
        logger.info('websockets .... ein GUI-Client wurde aus der Queue entfernt')


@asyncio.coroutine
def socket_handler_clients(websocket, path):
    # set up sending-queue
    task = asyncio.async(sending_loop_clients(websocket))

    while True:

        try:
            # get message from client
            message_rec = yield from websocket.recv()
        except websockets.exceptions.ConnectionClosed:
            logger.debug('websockets .... ! GUI connection unexpected closed !')
            break

        # leave if client is disconnect
        if message_rec is None:
            break

        # switch to different tasks
        logger.debug('websockets .... Empfange json Daten von Clients : %s' % message_rec)
        tmp = clients_message_handler(message_rec)
        if tmp is not False:
            yield from websocket.send(tmp)

    # close sending-queue if client discconect
    task.cancel()


@asyncio.coroutine
def socket_handler_gui(websocket, path):
    # set up sending-queue
    task = asyncio.async(sending_loop_gui(websocket))

    while True:

        try:
            # get message from client
            message_rec = yield from websocket.recv()
        except websockets.exceptions.ConnectionClosed:
            logger.debug('websockets .... ! CLIENT connection unexpected closed !')
            break

        # leave if client is disconnect
        if message_rec is None:
            break

        # switch to different tasks
        logger.debug('websockets .... Empfange json Daten von GUI-Client : %s' % message_rec)
        tmp = gui_message_handler(message_rec)
        if tmp is not False:
            yield from websocket.send(tmp)

    # close sending-queue if client discconect
    task.cancel()


def gui_message_handler(_tmp_message):
    # decode JSON String
    message = json.loads(_tmp_message)

    # extract variables from json
    message_usage = message["usage"]
    message_ip = message["ip"]
    message_id = message["id"]
    message_value = message["value"]

    logger.debug(
            'websockets .... GUI -> Nachricht %s von %s -> %s : %s' % (
                message_usage, message_ip, message_id, message_value))

    # if json_usage == "get_values_setup" or json_usage == "get_value":
    #    for consumer in consumers_clients:
    #        consumer(_tmp)
    #    return False

    if message_usage == "timerswitch_new":
        timer.load(int(message_id))
        return json.dumps({"usage": "timerswitch_new", "ip": "", "id": message_id, "value": True})
    elif message_usage == "timerswitch_update":
        timer.reload(message_id)
        return json.dumps({"usage": "timerswitch_update", "ip": "", "id": message_id, "value": True})
    elif message_usage == "timerswitch_delete":
        timer.delete_job(message_id)
        return json.dumps({"usage": "timerswitch_delete", "ip": "", "id": message_id, "value": True})
    elif message_usage == "timerswitch_restart":
        timer.restart()
        return json.dumps({"usage": "timerswitch_restart", "ip": "", "id": message_id, "value": True})
    else:
        return False


def send_to_clients(_tmp):
    for consumer in consumers_clients:
        consumer(_tmp)
    return False


def clients_message_handler(_tmp_message):
    # decode JSON String
    message = json.loads(_tmp_message)

    # extract variables from json
    message_usage = message["usage"]
    message_ip = message["ip"]
    message_id = message["id"]
    message_value = message["value"]

    logger.debug(
            'websockets .... Clients -> Nachricht %s von %s -> %s : %s' % (
                message_usage, message_ip, message_id, message_value))

    if message_usage == "xyz":
        pass
    else:
        return send_to_gui(_tmp_message)


def send_to_gui(_tmp):
    for consumer in consumers_gui:
        consumer(_tmp)
    return False


def set_logging():
    # Logging auf Console
    if LOG_CONSOLE:
        console_handler = logging.StreamHandler()
        formatter = logging.Formatter('%(asctime)s : %(message)s', '%Y-%m-%d %H:%M:%S')
        console_handler.setFormatter(formatter)
        logger.addHandler(console_handler)

    # Logging in Datei
    if LOG_FILE:
        file_handler = logging.FileHandler(LOG_FILE_PATH, mode='w', encoding=None, delay=False)
        formatter = logging.Formatter('%(asctime)s : %(message)s', '%Y-%m-%d %H:%M:%S')
        file_handler.setFormatter(formatter)
        logger.addHandler(file_handler)


if __name__ == '__main__':
    #
    # set up Logging Deamon
    #
    logger = logging.getLogger('raspi_server')
    logger.setLevel(logging.DEBUG)
    set_logging()
    logger.info('smartHome Server startet')
    #
    # set up MySQL Connection
    #
    mysql_connection = pymysql.connect(host=MYSQL_HOST, port=MYSQL_PORT, user=MYSQL_USER, passwd=MYSQL_PW,
                                       db=MYSQL_DB, autocommit=True)
    mysql_cursor = mysql_connection.cursor(pymysql.cursors.DictCursor)
    logger.info('mySQL ......... Verbindung online')
    #
    # set up TimerSwitch
    #
    timer = TimerSwitch(logger, mysql_cursor, action)
    #
    # set up Websocket-Servers
    #
    sensor_server = websockets.serve(socket_handler_clients, SERVER_IP, SERVER_CLIENTS_PORT)
    gui_server = websockets.serve(socket_handler_gui, SERVER_IP, SERVER_GUI_PORT)
    # run Websocket-Servers
    asyncio.get_event_loop().run_until_complete(sensor_server)
    asyncio.get_event_loop().run_until_complete(gui_server)
    logger.info('websockets .... System online')
    asyncio.get_event_loop().run_forever()
