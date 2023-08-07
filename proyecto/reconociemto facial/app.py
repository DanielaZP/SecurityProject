from flask import Flask, render_template, request, redirect, url_for
from reconocimiento_facial import ReconocimientoFacial
from base_de_datos import BaseDeDatos

app = Flask(__name__)

class Aplicacion:
    def __init__(self):
        self.reconocimiento_facial = ReconocimientoFacial()
        self.base_de_datos = BaseDeDatos()

    def registrar_usuario(self, nombre, apellido):
        self.base_de_datos.insertar_usuario(nombre, apellido)

    def verificar_usuario(self, nombre, apellido):
        return self.base_de_datos.verificar_usuario(nombre, apellido)

app_obj = Aplicacion()

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/registro', methods=['GET', 'POST'])
def registro():
    if request.method == 'POST':
        nombre = request.form['nombre']
        apellido = request.form['apellido']
        app_obj.registrar_usuario(nombre, apellido)
        return 'Usuario registrado exitosamente'
    return render_template('registro.html')

@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        nombre = request.form['nombre']
        apellido = request.form['apellido']
        if app_obj.verificar_usuario(nombre, apellido):
            app_obj.reconocimiento_facial.reconocer_rostro()
            return 'Inicio de sesión exitoso'
        else:
            return 'Usuario no encontrado'
    return render_template('login.html')

if __name__ == '__main__':
    app.run(debug=True)
