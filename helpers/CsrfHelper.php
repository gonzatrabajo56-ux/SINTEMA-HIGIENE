<?php
/**
 * Helper para protección CSRF
 */

class CsrfHelper {
    
    /**
     * Genera un token CSRF y lo guarda en sesión
     */
    public static function generateToken(): string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verifica si el token CSRF es válido
     */
    public static function validateToken(string $token): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Genera un campo oculto con el token CSRF
     */
    public static function tokenField(): string {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
    
    /**
     * Regenera el token CSRF (para después de validación exitosa)
     */
    public static function regenerateToken(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}
