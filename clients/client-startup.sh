#!/bin/sh
# chkconfig: 123456 90 10
#
workdir=/smartHome/clients

start() {
    cd ${workdir}
    /usr/local/bin/python3.4 /smartHome/clients/sh-client.py &
    echo "Client started."
}

stop() {
    pid=`ps -ef | grep '[p]ython3.4 /smartHome/clients/sh-client.py' | awk '{ print $2 }'`
    echo ${pid}
    kill ${pid}
    sleep 2
    echo "Client killed."
}

case "$1" in
  start)
    start
    ;;
  stop)
    stop
    ;;
  restart)
    stop
    start
    ;;
  *)
    echo "Usage: /etc/init.d/sh-client-startup {start|stop|restart}"
    exit 1
esac
exit 0
