using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;
using System.IO;
using System.Diagnostics;
using System.Security.Cryptography;
//using System.Security.Permissions;

namespace ProyectoEncriptacion
{
    public partial class Form1 : Form
    {
        public Form1()
        {
            InitializeComponent();
        }

        private void label1_Click(object sender, EventArgs e)
        {

        }

        private void textBox1_TextChanged(object sender, EventArgs e)
        {

        }

        private void textBox3_TextChanged(object sender, EventArgs e)
        {

        }

        private void button1_Click(object sender, EventArgs e)
        {
            if(openFileDialog1.ShowDialog() == DialogResult.OK)
            {
                //Abre una ventana mostrando el contenido del archivo
                /*string archivo = openFileDialog1.FileName;
                Process proceso = new Process();
                proceso.StartInfo.FileName = archivo;
                proceso.Start();*/
               
                textBox2.Text = openFileDialog1.FileName;
            }
        }

        private void button2_Click(object sender, EventArgs e)
        {
            if (saveFileDialog1.ShowDialog() == DialogResult.OK)
            {
                string sourceFilePath = openFileDialog1.FileName;
                string destinationFilePath = saveFileDialog1.FileName;

                //Obtencion de la extension del archivo seleccionado
                string fileExtension = Path.GetExtension(sourceFilePath);

                //Agregacion de la extension al archivo destino
                string destinationFileWithExtension = Path.ChangeExtension(destinationFilePath, fileExtension);
                
                // Copia el archivo seleccionado a la ubicación de destino
                //File.Copy(sourceFilePath, destinationFileWithExtension); 
               // MessageBox.Show("El archivo se guardó exitosamente.");
                
                textBox3.Text = destinationFileWithExtension;
            }
        }

        private void button3_Click(object sender, EventArgs e)
        {
            string inputFile = openFileDialog1.FileName;
            string outputFile = saveFileDialog1.FileName;
            string fileExtension = Path.GetExtension(inputFile);
            string outputFileWithExtension = Path.ChangeExtension(outputFile, fileExtension);

            //saveFileDialog1.OverwritePrompt = true;

            string password = "";
            EncryptFile(inputFile, outputFileWithExtension, password);

            MessageBox.Show("Archivo encriptado y guardado exitosamente.");
            
        }

        public static void EncryptFile(string inputFile, string outputFileWithExtension, string password)
        {
            byte[] passwordBytes = new Rfc2898DeriveBytes(password, salt: new byte[8], iterations: 1000).GetBytes(32);

            using (Aes aes = Aes.Create())
            {
                aes.Key = passwordBytes;
                aes.GenerateIV();

                using (FileStream inputFileStream = new FileStream(inputFile, FileMode.Open))
                {
                    using (FileStream outputFileWithExtensionStream = new FileStream(outputFileWithExtension, FileMode.Create))
                    {
                        outputFileWithExtensionStream.Write(aes.IV, 0, aes.IV.Length);

                        using (CryptoStream cryptoStream = new CryptoStream(outputFileWithExtensionStream, aes.CreateEncryptor(), CryptoStreamMode.Write))
                        using (StreamWriter writer = new StreamWriter(cryptoStream, Encoding.UTF8))
                        {
                            byte[] buffer = new byte[4096];
                            int bytesRead;

                            while ((bytesRead = inputFileStream.Read(buffer, 0, buffer.Length)) > 0)
                            {
                                cryptoStream.Write(buffer, 0, bytesRead);
                            } 
                        }
                    }
                }
            }
        }


        private void button4_Click(object sender, EventArgs e)
        {

        }
    }
}
