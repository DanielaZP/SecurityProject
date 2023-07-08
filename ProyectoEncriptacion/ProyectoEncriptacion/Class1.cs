using System;
using Npgsql;

namespace ProyectoEncriptacion
{
    public class ConexionBD
    {
        private string connectionString;

        public ConexionBD()
        {
            // Configura la cadena de conexión
            connectionString = "Server=localhost;Port=5432;Database=encriptacion;User Id=postgres;Password=Pincholin7;"; 
        }

        public void Conectar()
        {
            using (NpgsqlConnection connection = new NpgsqlConnection(connectionString))
            {
                try
                {
                    connection.Open();
                    Console.WriteLine("Conexión exitosa");

                    // Realiza operaciones en la base de datos aquí

                    connection.Close();
                }
                catch (NpgsqlException ex)
                {
                    Console.WriteLine("Error al conectar con la base de datos: " + ex.Message);
                }
            }
        }
    }
}





