import pymysql


class DB:
    conn = None

    def __init__(self, db_host, db_port, db_user, db_pass, db_name):
        self.db_host = db_host
        self.db_port = db_port
        self.db_user = db_user
        self.db_pass = db_pass
        self.db_name = db_name
        self.connect()

    def connect(self):
        self.conn = pymysql.connect(host=self.db_host, port=self.db_port, user=self.db_user, passwd=self.db_pass, db=self.db_name, autocommit=True)

    def query(self, sql, *arguments):
        try:
            cursor = self.conn.cursor(pymysql.cursors.DictCursor)
            cursor.execute(sql, arguments)
        except (AttributeError, pymysql.err.OperationalError):
            self.connect()
            cursor = self.conn.cursor(pymysql.cursors.DictCursor)
            cursor.execute(sql, arguments)
        return cursor
