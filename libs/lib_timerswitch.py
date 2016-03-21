from apscheduler.schedulers.asyncio import AsyncIOScheduler
import datetime


class TimerSwitch:
    UNIT2KEYWORD = {
        'Minuten': 'minutes',
        'Stunden': 'hours',
        'Tage': 'days',
        'Wochen': 'weeks'
    }
    WEEK2WEEK = [
        ('mon', 'weekly_monday'),
        ('tue', 'weekly_tuesday'),
        ('wed', 'weekly_wednesday'),
        ('thu', 'weekly_thursday'),
        ('fri', 'weekly_friday'),
        ('sat', 'weekly_saturday'),
        ('sun', 'weekly_sunday'),
    ]

    def __init__(self, logging_daemon, db_connection, method):
        self._scheduler = AsyncIOScheduler()
        self._method = method
        self._logging_daemon = logging_daemon
        self._db_connection = db_connection
        self._scheduler.start()
        self.load()
        self._logging_daemon.info('TimerSwitch ... initialisiert')

    def load(self, scheduler_id=None):
        sql = """SELECT
              schedulers.id AS scheduler_id,
              schedulers.title AS scheduler_title,
              schedulers.date_start_on,
              schedulers.date_start_off,
              schedulers.date_stop,
              schedulers.date_stop_on,
              schedulers.date_stop_off,
              schedulers.duration,
              schedulers.interval_number,
              schedulers.interval_unit,
              schedulers.weekly_monday,
              schedulers.weekly_tuesday,
              schedulers.weekly_wednesday,
              schedulers.weekly_thursday,
              schedulers.weekly_friday,
              schedulers.weekly_saturday,
              schedulers.weekly_sunday,
              switches.id AS switches_id,
              switches.title AS switches_title,
              switches.argA,
              switches.argB,
              switches.argC,
              switches.argD,
              switch_types.title AS switches_typ,
              clients.ip AS switches_ip
              FROM schedulers, switches, switch_types, clients
              WHERE schedulers.switches_id = switches.id
              AND switches.switch_types_id = switch_types.id
              AND switches.clients_id = clients.id"""
        if isinstance(scheduler_id, int):
            sql += " AND schedulers.id = %s"
            self._db_connection.execute(sql, scheduler_id)
        else:
            self._db_connection.execute(sql)
        results = self._db_connection.fetchall()
        for result in results:
            self._add(result)

    def _add(self, dataset):
        scheduler_id = dataset['scheduler_id']
        title = dataset['scheduler_title']
        date_start_on = dataset['date_start_on']
        date_start_off = dataset['date_start_off']
        date_stop_on = dataset['date_stop_on']
        date_stop_off = dataset['date_stop_off']
        duration = dataset['duration']

        week = ','.join(
            abr for abr, full in self.WEEK2WEEK
            if dataset[full]
        )

        if duration == 'einmalig':
            scheduler_type = 'date'
            args_on = dict(run_date=date_start_on)
            args_off = dict(run_date=date_start_off)
            date_stop_off = date_start_off
        elif duration == 'intervall':
            scheduler_type = 'interval'
            interval_argument = {self.UNIT2KEYWORD[dataset['interval_unit']]: dataset['interval_number']}
            args_on = dict(interval_argument, start_date=date_start_on, end_date=date_stop_on)
            args_off = dict(interval_argument, start_date=date_start_off, end_date=date_stop_off)
        elif duration == 'wochentag':
            scheduler_type = 'cron'
            args_on = dict(
                day_of_week=week, hour=date_start_on.hour, minute=date_start_on.minute,
                start_date=date_start_on, end_date=date_stop_on)
            args_off = dict(
                day_of_week=week, hour=date_start_off.hour, minute=date_start_off.minute,
                start_date=date_start_off, end_date=date_stop_off)

        self._scheduler.add_job(self._method, scheduler_type,
                                args=[scheduler_id, dataset['switches_id'], dataset['switches_ip'], True, None],
                                id='%son' % scheduler_id, **args_on)
        self._scheduler.add_job(self._method, scheduler_type,
                                args=[scheduler_id, dataset['switches_id'], dataset['switches_ip'], False,
                                      date_stop_off], id='%soff' % scheduler_id, **args_off)

        self._logging_daemon.info('Timerswitch ... add_job "%s" (id = %s)' % (title, scheduler_id))
        self._logging_daemon.debug(
            'Timerswitch ... self._scheduler.add_job(%s, %s, args=[%s, %s, True, None], id=%soff, %s' % (
                self._method, scheduler_type, title, scheduler_id, scheduler_id, args_on))

    def reload(self, scheduler_id):
        self.delete_job(scheduler_id)
        self.load(scheduler_id)
        self._logging_daemon.info('Timerswitch ... Reload       ID = %s' % scheduler_id)

    def delete_job(self, scheduler_id):
        self._scheduler.remove_job(str(scheduler_id) + 'on')
        self._scheduler.remove_job(str(scheduler_id) + 'off')
        self._logging_daemon.info('Timerswitch ... Delete Job   ID = %s' % scheduler_id)

    def delete_db(self, scheduler_id):
        if isinstance(scheduler_id, int):
            scheduler_id = int(scheduler_id)
            self._db_connection.execute("DELETE FROM schedulers WHERE id = %s", scheduler_id)
            self._logging_daemon.info('Timerswitch ... Delete DB    ID = %s' % scheduler_id)

    def restart(self):
        self._logging_daemon.info('TimerSwitch ... stopping for restart')
        self._scheduler.shutdown(0)
        self._scheduler = AsyncIOScheduler()
        self._scheduler.start()
        self.load()
        self._logging_daemon.info('TimerSwitch ... neu initialisiert')
