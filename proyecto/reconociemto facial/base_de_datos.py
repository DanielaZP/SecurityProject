import psycopg2

class BaseDeDatos:
    def __init__(self):
        self.conn = psycopg2.connect(
            host='localhost',
            database='security',
            user='postgres',
            password='password'
        )
        self.cursor = self.conn.cursor()

    def insertar_usuario(self, nombre, apellido):
        query = "INSERT INTO usuariosid (nombre, apellido) VALUES (%s, %s)"
        values = (nombre, apellido)
        self.cursor.execute(query, values)
        self.conn.commit()

    def verificar_usuario(self, nombre, apellido):
        query = "SELECT * FROM usuarios WHERE nombre = %s AND apellido = %s"
        values = (nombre, apellido)
        self.cursor.execute(query, values)
        return self.cursor.fetchone() is not None

    def cerrar_conexion(self):
        self.cursor.close()
        self.conn.close()
