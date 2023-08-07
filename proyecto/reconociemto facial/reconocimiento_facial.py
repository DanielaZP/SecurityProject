import cv2

class ReconocimientoFacial:
    def __init__(self):
        self.cascade_path = 'haarcascade_frontalface_default.xml'
        self.face_cascade = cv2.CascadeClassifier(cv2.data.haarcascades + self.cascade_path)

    def reconocer_rostro(self):
        cap = cv2.VideoCapture(0)
        while True:
            ret, frame = cap.read()
            gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
            faces = self.face_cascade.detectMultiScale(gray, scaleFactor=1.3, minNeighbors=5)
            
            for (x, y, w, h) in faces:
                cv2.rectangle(frame, (x, y), (x + w, y + h), (255, 0, 0), 2)
            
            cv2.imshow('Reconocimiento Facial', frame)
            
            if cv2.waitKey(1) & 0xFF == ord('q'):
                break

        cap.release()
        cv2.destroyAllWindows()

    def guardar_rostro(self, nombre, apellido):
        cap = cv2.VideoCapture(0)
        while True:
            ret, frame = cap.read()
            gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
            faces = self.face_cascade.detectMultiScale(gray, scaleFactor=1.3, minNeighbors=5)
            
            for (x, y, w, h) in faces:
                cv2.rectangle(frame, (x, y), (x + w, y + h), (255, 0, 0), 2)
                rostro = gray[y:y+h, x:x+w]
                
                # Lógica para guardar el rostro en la base de datos
                # Utiliza la función correspondiente de la clase BaseDeDatos

            cv2.imshow('Captura de Rostro', frame)
            
            if cv2.waitKey(1) & 0xFF == ord('q'):
                break

        cap.release()
        cv2.destroyAllWindows()
