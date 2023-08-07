from flask import Flask, render_template, request, redirect, url_for
import cv2
import base_de_datos
import reconocimiento_facial

app = Flask(__name__)

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/registro', methods=['GET', 'POST'])
def registro():
    if request.method == 'POST':
        nombre = request.form['nombre']
        registrar_usuario(nombre)
        return redirect(url_for('login'))
    return render_template('registro.html')

@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        nombre = request.form['nombre']
        if verificar_usuario(nombre):
            return 'Acceso permitido'
        else:
            return 'Acceso denegado'
    return render_template('login.html')

def registrar_usuario(nombre):
    cap = cv2.VideoCapture(0)
    # Lógica para capturar y guardar el reconocimiento facial
    cap.release()

    base_de_datos.insertar_usuario(nombre)

def verificar_usuario(nombre):
    # Lógica para verificar si un usuario está registrado en la base de datos y reconocer su rostro
    return reconocimiento_facial.verificar_rostro(nombre)

if __name__ == '__main__':
    app.run(debug=True)
