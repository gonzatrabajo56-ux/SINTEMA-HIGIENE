<?php
declare(strict_types=1);

class Database {
    private static ?PDO $instance = null;

    // Configuración (Idealmente esto vendría de un archivo .env)
    private const HOST = 'localhost';
    private const DB   = 'fundacion_limpieza';
    private const USER = 'root';
    private const PASS = '';
    private const CHARSET = 'utf8mb4';

    /**
     * Retorna la instancia única de PDO
     */
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            try {
                $dsn = "mysql:host=" . self::HOST . ";dbname=" . self::DB . ";charset=" . self::CHARSET;
                
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::ATTR_PERSISTENT         => false, // Cambiar a true solo si sabes que lo necesitas
                ];

                self::$instance = new PDO($dsn, self::USER, self::PASS, $options);
            } catch (PDOException $e) {
                // En producción, no muestres el mensaje de error directamente ($e->getMessage())
                // porque podría revelar datos sensibles. Loguéalo en su lugar.
                error_log("Error de Conexión: " . $e->getMessage());
                throw new Exception("Error interno al conectar con la base de datos.");
            }
        }
        return self::$instance;
    }

    // Evitar que se clone o se instancie manualmente
    private function __construct() {}
    private function __clone() {}
}