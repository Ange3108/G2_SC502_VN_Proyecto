/*Se crea la base de datos */
CREATE DATABASE IF NOT EXISTS la_crocherita;
USE la_crocherita;

/*Se crea la tabla Usuarios*/
CREATE TABLE Usuarios (
  id_usuario INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  correo VARCHAR(100) NOT NULL UNIQUE,
  contraseña VARCHAR(255) NOT NULL
);

/*Se crea la tabla Patrones - debe estar antes que Proyectos*/
CREATE TABLE Patrones (
  id_patron INT AUTO_INCREMENT PRIMARY KEY,
  nombre_patron VARCHAR(100) NOT NULL,
  descripcion TEXT,
  imagen_url VARCHAR(255),
  nivel_dificultad ENUM('principiante', 'intermedio', 'avanzado'),
  puntos_utilizados VARCHAR(500),
  materiales VARCHAR(1000),
  pdf_url VARCHAR(255)
);

/*Se crea la tabla Proyectos - sin puntos_utilizados y materiales*/
CREATE TABLE Proyectos (
  id_proyecto INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT,
  id_patron INT,
  nombre_proyecto VARCHAR(100) NOT NULL,
  descripcion TEXT,
  estado ENUM('en proceso', 'terminado') DEFAULT 'en proceso',
  imagen_url VARCHAR(255),
  FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario), 
  FOREIGN KEY (id_patron) REFERENCES Patrones(id_patron)
);


/*Se crea la tabla Clases*/
CREATE TABLE Clases (
  id_clase INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT,
  nombre_clase VARCHAR(100) NOT NULL,
  dia_semana VARCHAR(20) NOT NULL,
  hora_inicio TIME NOT NULL,
  hora_fin TIME NOT NULL,
  FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario)
);

CREATE TABLE Asistencia (
  id_asistencia INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT,
  id_clase INT,
  fecha DATE NOT NULL,
  estado ENUM('asistió', 'faltó') DEFAULT 'asistió',
  FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario),
  FOREIGN KEY (id_clase) REFERENCES Clases(id_clase)
);

CREATE TABLE Eventos (
  id_evento INT AUTO_INCREMENT PRIMARY KEY,
  nombre_evento VARCHAR(100) NOT NULL,
  descripcion TEXT,
  fecha_evento DATE NOT NULL
);

CREATE TABLE Favoritos_Patrones (
  id_usuario INT,
  id_patron INT,
  PRIMARY KEY (id_usuario, id_patron),
  FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario),
  FOREIGN KEY (id_patron) REFERENCES Patrones(id_patron)
);

-- Insertar usuario de ejemplo
INSERT INTO Usuarios (nombre, correo, contraseña) VALUES
('Usuario Demo', 'demo@ejemplo.com', '$2y$10$example_hash_password');

INSERT INTO Patrones (nombre_patron, descripcion, imagen_url, nivel_dificultad, puntos_utilizados, materiales) VALUES
('Oso de Peluche', 'Adorable oso de peluche perfecto para regalar', '../img/oso.jpg', 'intermedio', 'Punto bajo, punto alto, anillo mágico', 'Hilo acrílico, aguja 4mm, relleno, ojos de seguridad'),
('Ratoncito', 'Pequeño ratón tejido muy fácil de hacer', '../img/ratoncito.jpg', 'principiante', 'Punto bajo, aumentos, disminuciones', 'Hilo algodón, aguja 3.5mm, relleno'),
('Tulipán', 'Hermosa flor tulipán para decorar', '../img/Tulipan.jpg', 'intermedio', 'Punto alto, punto bajo, cadenas', 'Hilo algodón de colores, aguja 3mm, alambre floral');