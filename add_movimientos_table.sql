CREATE TABLE movimientos_no_perecederos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  bien_id INT NOT NULL,
  tipo_movimiento ENUM('asignacion', 'devolucion', 'mantenimiento', 'baja') NOT NULL,
  area_anterior VARCHAR(255),
  area_nueva VARCHAR(255),
  responsable_anterior VARCHAR(100),
  responsable_nuevo VARCHAR(100),
  fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  usuario_registro VARCHAR(100),
  observaciones TEXT,
  FOREIGN KEY (bien_id) REFERENCES bienes_no_perecederos(id) ON DELETE CASCADE
);