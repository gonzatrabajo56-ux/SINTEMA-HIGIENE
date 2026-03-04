<?php
/**
 * Helper para verificación de roles de usuario
 */

class RoleHelper {
    
    /**
     * Verifica si el usuario actual es administrador
     */
    public static function isAdmin(): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin';
    }

    /**
     * Verifica si el usuario tiene un rol específico
     */
    public static function hasRole(string $role): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === $role;
    }

    /**
     * Redirige a página de acceso denegado si no es admin
     */
    public static function requireAdmin(): void {
        if (!self::isAdmin()) {
            header("Location: index.php?page=dashboard&error=access_denied");
            exit();
        }
    }

    /**
     * Redirige al dashboard si ya está logueado
     */
    public static function requireGuest(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['usuario_id'])) {
            header("Location: index.php");
            exit();
        }
    }
}
