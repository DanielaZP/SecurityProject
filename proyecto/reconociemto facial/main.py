from flask import Flask, render_template, request, redirect, url_for
from registro import registrar_usuario
from reconocimiento import reconocer_usuario

app = Flask(__name__)

@app.route('/')
def inicio():
    return render_template('login.html')

@app.route('/registro', methods=['GET', 'POST'])
def registro():
    if request.method == 'POST':
        nombre = request.form['nombre']
        # Lógica para registrar el usuario y el reconocimiento facial
        registrar_usuario(nombre)
        return redirect(url_for('inicio'))
    return render_template('registro.html')

@app.route('/reconocimiento', methods=['GET', 'POST'])
def reconocimiento():
    if request.method == 'POST':
        # Lógica para el reconocimiento facial
        resultado = reconocer_usuario()
        return render_template('reconocimiento.html', resultado=resultado)
    return render_template('reconocimiento.html', resultado=None)

if __name__ == '__main__':
    app.run(debug=True)
