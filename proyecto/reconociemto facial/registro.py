import cv2
import base_de_datos

def registrar_usuario(nombre):
    cap = cv2.VideoCapture(0)
    # Lógica para capturar y guardar el reconocimiento facial
    cap.release()

    base_de_datos.insertar_usuario(nombre)
