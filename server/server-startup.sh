#!/bin/sh
# chkconfig: 123456 90 10
#
workdir=/smartHome/server

start() {
    cd ${workdir}
    /usr/local/bin/python3.4 /smartHome/server/sh-server.py &
    echo "Server started."
}

stop() {
    pid=`ps -ef | grep '[p]ython3.4 /smartHome/server/sh-server.py' | awk '{ print $2 }'`
    echo ${pid}
    kill ${pid}
    sleep 2
    echo "Server killed."
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
    echo "Usage: /etc/init.d/sh-server-startup {start|stop|restart}"
    exit 1
esac
exit 0
