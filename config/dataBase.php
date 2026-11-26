<?php

class Database {
    // PARAMETROS DE CONEXION
    private static string $host = 'localhost';
    private static string $db_name = 'sistema_db';
    private static string $username = 'root';
    private static string $password = ''; // Vacio por defecto en XAMPP
    private static string $charset = 'utf8mb4';
    private static ?PDO $connection = null;



    private function __construct() {
        
    }

    public static function getConnection():PDO{
        
        if (self::$connection === null) {
           
                $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=" . self::$charset;
                
                  // Opciones de configuración de PDO
            $options = [
                // 1. Lanza excepciones automáticas ante errores SQL (vital para try/catch)
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                
                // 2. Devuelve los resultados como arrays asociativos ['campo' => 'valor']
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                
                // 3. Desactiva emulación de consultas preparadas para máxima seguridad contra SQL Injection
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

             try {
                self::$connection = new PDO($dsn, self::$username, self::$password, $options);
            } catch (PDOException $e) {
                die("Error de conexion a la base de datos: " . $e->getMessage());
            }
        }
        return self::$connection;
    }

}
