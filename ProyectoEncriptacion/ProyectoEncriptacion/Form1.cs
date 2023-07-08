﻿using System;
using System.IO;
using System.Security.Cryptography;
using System.Text;
using System.Windows.Forms;

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
            if (openFileDialog1.ShowDialog() == DialogResult.OK)
            {
                textBox2.Text = openFileDialog1.FileName;
            }
        }

        private void button2_Click(object sender, EventArgs e)
        {
            if (saveFileDialog1.ShowDialog() == DialogResult.OK)
            {
                textBox3.Text = saveFileDialog1.FileName;
            }
        }

        private void button3_Click(object sender, EventArgs e)
        {
            string inputFile = openFileDialog1.FileName;
            string outputFile = saveFileDialog1.FileName;
            string fileExtension = Path.GetExtension(inputFile);
            string outputFileWithExtension = Path.ChangeExtension(outputFile, fileExtension);

            string password = "";
            EncryptFile(inputFile, outputFileWithExtension, password);

            MessageBox.Show("Archivo encriptado y guardado exitosamente.");
        }

        private void button4_Click(object sender, EventArgs e)
        {
            string inputFile = openFileDialog1.FileName;
            string outputFile = saveFileDialog1.FileName;
            string fileExtension = Path.GetExtension(inputFile);
            string outputFileWithExtension = Path.ChangeExtension(outputFile, fileExtension);

            string password = "";
            DecryptFile(inputFile, outputFileWithExtension, password);

            MessageBox.Show("Archivo desencriptado y guardado exitosamente.");
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

        public static void DecryptFile(string inputFileWithExtension, string outputFile, string password)
        {
            byte[] passwordBytes = new Rfc2898DeriveBytes(password, salt: new byte[8], iterations: 1000).GetBytes(32);

            using (Aes aes = Aes.Create())
            {
                aes.Key = passwordBytes;

                using (FileStream inputFileStream = new FileStream(inputFileWithExtension, FileMode.Open))
                {
                    byte[] iv = new byte[aes.IV.Length];
                    inputFileStream.Read(iv, 0, iv.Length);
                    aes.IV = iv;

                    using (FileStream outputFileStream = new FileStream(outputFile, FileMode.Create))
                    {
                        using (CryptoStream cryptoStream = new CryptoStream(inputFileStream, aes.CreateDecryptor(), CryptoStreamMode.Read))
                        {
                            byte[] buffer = new byte[4096];
                            int bytesRead;

                            while ((bytesRead = cryptoStream.Read(buffer, 0, buffer.Length)) > 0)
                            {
                                outputFileStream.Write(buffer, 0, bytesRead);
                            }
                        }
                    }
                }
            }
        }
    }
}
