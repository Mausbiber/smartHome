#!/usr/bin/env python3

import asyncio
from asyncio.queues import Queue
import logging
import sys
import signal
import json
import socket
from tinkerforge.ip_connection import IPConnection
from lib_tinkerforge import BrickletQuadRelay, BrickletRemote, BrickletDualRelay
import lib_sispm
import lib_simpliboxio
# import lib_gpio

import pymysql
import websockets
from config import *

DEVICE_IP = [l for l in (
    [ip for ip in socket.gethostbyname_ex(socket.gethostname())[2] if not ip.startswith("127.")][:1], [
        [(s.connect(('8.8.8.8', 53)), s.getsockname()[0], s.close()) for s in
         [socket.socket(socket.AF_INET, socket.SOCK_DGRAM)]][0][1]]) if l][0][0]

consumers = []
switches = {}
switches_info = {}


def set_exit_handler(func):
    signal.signal(signal.SIGTERM, func)


def on_exit(sig, func=None):
    logger.error('SYSTEM ........ exit handler triggered')
    sys.exit(1)


@asyncio.coroutine
def sending_loop(websocket):
    # create sending-queue
    loop = asyncio.get_event_loop()
    sending_queue = Queue()
    logger.info('websockets .... Queue startet')

    def changed(tmp):
        loop.call_soon_threadsafe(sending_queue.put_nowait, tmp)

    try:
        consumers.append(changed)
        logger.info('websockets .... consumers.append')

        while True:
            tmp_data = yield from sending_queue.get()
            yield from websocket.send(tmp_data)
            logger.debug('websockets .... yield from websocket.send : %s' % tmp_data)

    finally:
        consumers.remove(changed)
        logger.info('websockets .... consumers.remove')


@asyncio.coroutine
def client_handler():
    # connect to server
    while True:
        try:
            websocket = yield from websockets.connect('ws://' + SERVER_IP + ':' + SERVER_CLIENTS_PORT + '/')
            break
        except OSError:
            pass
    # set up sending-queue
    task = asyncio.async(sending_loop(websocket))
    logger.debug('websockets .... asyncio.async')

    while True:

        # get message from client
        message_received = yield from websocket.recv()

        # leave if client is disconnect
        if message_received is None:
            break

        # switch to different tasks
        logger.debug('websockets .... yield from websocket.recv -> %s' % message_received)
        message_handler(message_received)

    # close sending-queue if client discconect
    task.cancel()
    logger.info('websockets .... task.cancel')


def message_handler(_tmp_message):
    # decode JSON String
    message = json.loads(_tmp_message)

    # extract variables from json
    message_usage = message["usage"]
    message_ip = message["ip"]
    message_id = message["id"]
    message_value = message["value"]

    if message_ip == DEVICE_IP:
        if message_usage == "switch_turn":
            switches[switches_info[message_id, "index"]].set_switch(message_value, switches_info[message_id, "argA"],
                                                                    switches_info[message_id, "argB"],
                                                                    switches_info[message_id, "argC"],
                                                                    switches_info[message_id, "argD"])


def get_switches():
    tinkerforge_connection = IPConnection()
    tinkerforge = False
    #
    # get a list of all switches for this client
    #
    sql = """SELECT
          switches.id AS switches_id,
          switches.title AS switches_title,
          switches.argA,
          switches.argB,
          switches.argC,
          switches.argD,
          switch_types.title AS switches_typ
          FROM switches, switch_types, clients
          WHERE clients.ip = %s
          AND switches.switch_types_id = switch_types.id
          AND switches.clients_id = clients.id"""
    mysql_cursor.execute(sql, str(DEVICE_IP))
    results = mysql_cursor.fetchall()
    for result in results:
        switches_info[result['switches_id'], "title"] = result['switches_title']
        switches_info[result['switches_id'], "argA"] = result['argA']
        switches_info[result['switches_id'], "argB"] = result['argB']
        switches_info[result['switches_id'], "argC"] = result['argC']
        switches_info[result['switches_id'], "argD"] = result['argD']

        logger.debug(
            'get_switches... %s id=%s (%s)' % (result['switches_title'], result['switches_id'], result['switches_typ']))

        #
        # set up Tinkerforge switches
        #
        if result['switches_typ'] == "tf_ind_quad_relay":
            switches_info[result['switches_id'], "index"] = result['argA']

            switches[switches_info[result['switches_id'], "index"]] = BrickletQuadRelay(result['argA'],
                                                                                        result['switches_id'],
                                                                                        DEVICE_IP,
                                                                                        tinkerforge_connection, logger,
                                                                                        consumers)
            tinkerforge = True
        elif result['switches_typ'] == "tf_dual":
            switches_info[result['switches_id'], "index"] = result['argA']

            switches[switches_info[result['switches_id'], "index"]] = BrickletDualRelay(result['argA'],
                                                                                        result['switches_id'],
                                                                                        DEVICE_IP,
                                                                                        tinkerforge_connection, logger,
                                                                                        consumers)
            tinkerforge = True
        elif result['switches_typ'] == "tf_remote":
            switches_info[result['switches_id'], "index"] = result['argA']

            switches[switches_info[result['switches_id'], "index"]] = BrickletRemote(result['argA'],
                                                                                     result['switches_id'], DEVICE_IP,
                                                                                     tinkerforge_connection, logger,
                                                                                     consumers)
            tinkerforge = True

        #
        # set up SIS USB switch
        #
        elif result['switches_typ'] == "sis_usb_socket":
            switches_info[result['switches_id'], "index"] = "sis_usb_socket"
            switches[switches_info[result['switches_id'], "index"]] = lib_sispm.Sispm(result['switches_id'], DEVICE_IP,
                                                                                      logger, consumers)

        #
        # set up SimpliBoxIO
        #
        elif result['switches_typ'] == "simplibox_io_usb":
            switches_info[result['switches_id'], "index"] = "simplibox_io_usb"
            switches[switches_info[result['switches_id'], "index"]] = lib_simpliboxio.SimpliBoxIO(result['switches_id'],
                                                                                                  DEVICE_IP, logger,
                                                                                                  consumers)

        #
        # set up raspi gpio pins
        #
        elif result['switches_typ'] == "raspi_gpio":
            switches_info[result['switches_id'], "index"] = "raspi_gpio"
            switches[switches_info[result['switches_id'], "index"]] = lib_gpio.RaspiGPIO(result['switches_id'],
                                                                                         DEVICE_IP, logger, consumers)

    if tinkerforge:
        tinkerforge_connection.connect(DEVICE_IP, 4223)
        logger.info('Tinkerforge ... System online')


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


if __name__ == "__main__":
    set_exit_handler(on_exit)
    #
    # set up Logging Deamon
    #
    logger = logging.getLogger('raspi_server')
    logger.setLevel(logging.DEBUG)
    set_logging()
    logger.info('System startet')
    #
    # set up MySQL Connection
    #
    mysql_connection = pymysql.connect(host=MYSQL_HOST, port=MYSQL_PORT, user=MYSQL_USER, passwd=MYSQL_PW, db=MYSQL_DB,
                                       autocommit=True)
    mysql_cursor = mysql_connection.cursor(pymysql.cursors.DictCursor)
    logger.info('mySQL ......... Verbindung online')
    #
    # setup switches
    #
    get_switches()

    #
    # set up Websocket-Server
    #
    asyncio.get_event_loop().run_until_complete(client_handler())
