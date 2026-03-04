-- =====================================================
-- SISTEMA DE GESTIÓN DE INVENTARIO - SINTEMA HIGIENE
-- Base de datos completa para recrear el sistema
-- =====================================================

-- Crear la base de datos


-- =====================================================
-- TABLA: USUARIOS
-- =====================================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT(11) NOT NULL AUTO_INCREMENT,
    cedula VARCHAR(20) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    rol ENUM('admin', 'operador') DEFAULT 'operador',
    PRIMARY KEY (id),
    UNIQUE KEY cedula (cedula)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insertar usuarios de prueba
-- Contraseña: password (hash bcrypt)
INSERT INTO usuarios (cedula, nombre, password_hash, rol) VALUES 
('12345678', 'Administrador', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('28725251', 'Aurys Pinto', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'operador'),
('31077912', 'Gonzalo Diaz', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- =====================================================
-- TABLA: ÁREAS
-- =====================================================
CREATE TABLE IF NOT EXISTS areas (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    activa TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    UNIQUE KEY nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO areas (nombre, activa) VALUES 
('Almacén', 1),
('Cocina', 1),
('Oficina', 1),
('Baños', 1),
('Limpieza', 1);

-- =====================================================
-- TABLA: LOTES (Consumibles)
-- =====================================================
CREATE TABLE IF NOT EXISTS lotes (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nombre_producto VARCHAR(100) NOT NULL,
    cantidad_inicial DECIMAL(10,2) NOT NULL,
    cantidad_actual DECIMAL(10,2) NOT NULL,
    unidad ENUM('L', 'Kg', 'Pz') NOT NULL,
    estado ENUM('activo', 'agotado') DEFAULT 'activo',
    fecha_ingreso TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insertar lotes de ejemplo
INSERT INTO lotes (nombre_producto, cantidad_inicial, cantidad_actual, unidad, estado) VALUES 
('Cloro', 20.00, 18.50, 'L', 'activo'),
('Jabón líquido', 15.00, 12.30, 'L', 'activo'),
('Detergente', 10.00, 9.75, 'Kg', 'activo'),
('Escobas', 5.00, 3.00, 'Pz', 'activo'),
('Trapeadores', 4.00, 4.00, 'Pz', 'activo');

-- =====================================================
-- TABLA: BIENES NO PERECEDEROS (Activos Fijos)
-- =====================================================
CREATE TABLE IF NOT EXISTS bienes_no_perecederos (
    id INT(11) NOT NULL AUTO_INCREMENT,
    numero_bien VARCHAR(50) NOT NULL,
    descripcion TEXT NOT NULL,
    marca VARCHAR(100) DEFAULT NULL,
    modelo VARCHAR(100) DEFAULT NULL,
    color VARCHAR(50) DEFAULT NULL,
    serial VARCHAR(100) DEFAULT NULL,
    ubicacion_exacta VARCHAR(255) DEFAULT NULL,
    estado ENUM('disponible', 'asignado', 'mantenimiento', 'desactivado', 'baja') DEFAULT 'disponible',
    fecha_ingreso TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    area_asignada VARCHAR(100) DEFAULT NULL,
    responsable VARCHAR(100) DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY numero_bien (numero_bien),
    UNIQUE KEY serial (serial)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insertar bienes de ejemplo
INSERT INTO bienes_no_perecederos (numero_bien, descripcion, marca, modelo, color, serial, ubicacion_exacta, estado, area_asignada, responsable) VALUES 
('BNP-001', 'Computadora Desktop', 'Dell', 'Optiplex 7080', 'Negro', 'SN001234', 'Oficina Principal', 'asignado', 'Oficina', 'Gonzalo Diaz'),
('BNP-002', 'Impresora Laser', 'HP', 'LaserJet Pro', 'Blanco', 'SN002345', 'Oficina Principal', 'disponible', NULL, NULL),
('BNP-003', 'Aire Acondicionado', 'Split', '24000 BTU', 'Blanco', 'SN003456', 'Sala de Reuniones', 'asignado', 'Sala de Reuniones', 'Aurys Pinto'),
('BNP-004', 'Proyector', 'Epson', 'PowerLite', 'Gris', 'SN004567', 'Auditorio', 'disponible', NULL, NULL),
('BNP-005', 'Aspiradora', 'Karcher', 'WD 3', 'Amarillo', 'SN005678', 'Almacén', 'mantenimiento', 'Limpieza', NULL);

-- =====================================================
-- TABLA: MOVIMIENTOS (Consumibles)
-- =====================================================
CREATE TABLE IF NOT EXISTS movimientos (
    id INT(11) NOT NULL AUTO_INCREMENT,
    lote_id INT(11) NOT NULL,
    tipo_movimiento ENUM('entrada', 'salida') NOT NULL DEFAULT 'salida',
    cantidad_retirada DECIMAL(10,2) NOT NULL,
    area_destino VARCHAR(50) NOT NULL,
    responsable VARCHAR(100) NOT NULL,
    fecha_movimiento TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY fk_lote (lote_id),
    FOREIGN KEY (lote_id) REFERENCES lotes (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insertar movimientos de ejemplo
INSERT INTO movimientos (lote_id, tipo_movimiento, cantidad_retirada, area_destino, responsable, fecha_movimiento) VALUES 
(1, 'entrada', 20.00, 'Almacén', 'Sistema (Ingreso Inicial)', '2026-01-15 08:00:00'),
(1, 'salida', 1.00, 'Cocina', 'Gonzalo Diaz', '2026-01-20 10:30:00'),
(1, 'salida', 0.50, 'Baños', 'Aurys Pinto', '2026-01-25 14:15:00'),
(2, 'entrada', 15.00, 'Almacén', 'Sistema (Ingreso Inicial)', '2026-01-15 08:00:00'),
(2, 'salida', 2.00, 'Cocina', 'Gonzalo Diaz', '2026-01-22 09:00:00'),
(3, 'entrada', 10.00, 'Almacén', 'Sistema (Ingreso Inicial)', '2026-01-15 08:00:00'),
(3, 'salida', 0.25, 'Limpieza', 'Aurys Pinto', '2026-01-28 16:45:00');

-- =====================================================
-- TABLA: MOVIMIENTOS NO PERECEDEROS (Activos Fijos)
-- =====================================================
CREATE TABLE IF NOT EXISTS movimientos_no_perecederos (
    id INT(11) NOT NULL AUTO_INCREMENT,
    bien_id INT(11) NOT NULL,
    tipo_movimiento ENUM('asignacion', 'devolucion', 'mantenimiento', 'baja') NOT NULL,
    area_anterior VARCHAR(255) DEFAULT NULL,
    area_nueva VARCHAR(255) DEFAULT NULL,
    responsable_anterior VARCHAR(100) DEFAULT NULL,
    responsable_nuevo VARCHAR(100) DEFAULT NULL,
    fecha_movimiento TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    usuario_registro VARCHAR(100) DEFAULT NULL,
    observaciones TEXT DEFAULT NULL,
    PRIMARY KEY (id),
    KEY bien_id (bien_id),
    FOREIGN KEY (bien_id) REFERENCES bienes_no_perecederos (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insertar movimientos de activos
INSERT INTO movimientos_no_perecederos (bien_id, tipo_movimiento, area_anterior, area_nueva, responsable_anterior, responsable_nuevo, usuario_registro, observaciones) VALUES 
(1, 'asignacion', 'Almacén', 'Oficina', NULL, 'Gonzalo Diaz', 'Administrador', 'Asignación inicial'),
(3, 'asignacion', 'Almacén', 'Sala de Reuniones', NULL, 'Aurys Pinto', 'Administrador', 'Asignación para sala de reuniones'),
(5, 'mantenimiento', 'Limpieza', NULL, NULL, NULL, 'Administrador', 'Envío a mantenimiento preventivo');

-- =====================================================
-- MENSAJE DE ÉXITO
-- =====================================================
SELECT '✅ Base de datos creada correctamente' AS mensaje;
SELECT '✅ Tablas creadas: usuarios, areas, lotes, bienes_no_perecederos, movimientos, movimientos_no_perecederos' AS tablas;
SELECT '✅ Usuarios de prueba creados' AS usuarios;
SELECT '✅ Credenciales: Cédula: 12345678, Password: password' AS acceso;
