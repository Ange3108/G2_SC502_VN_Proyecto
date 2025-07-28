/*Se crea la base de datos */
CREATE SCHEMA la_crocherita;
USE la_crocherita;

/*Se crea la tabla Usuarios*/
CREATE TABLE Usuarios (
  id_usuario INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  correo VARCHAR(100) NOT NULL UNIQUE,
  contraseña VARCHAR(255) NOT NULL
);

/*Se crea la tabla Proyectos*/
CREATE TABLE Proyectos (
  id_proyecto INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT,
  nombre_proyecto VARCHAR(100) NOT NULL,
  descripcion TEXT,
  tipo_reto ENUM('personal', 'reto') DEFAULT 'personal',
  nivel_dificultad ENUM('principiante', 'intermedio', 'avanzado'),
  estado ENUM('en proceso', 'terminado') DEFAULT 'en proceso',
  imagen_url VARCHAR(255),
  puntos_utilizados TEXT,
  FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario)
);


/*Se crea la tabla Clases*/
CREATE TABLE Clases (
  id_clase INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT,
  nombre_clase VARCHAR(100) NOT NULL,
  dia_semana VARCHAR(20) NOT NULL,
  hora_inicio TIME NOT NULL,
  hora_fin TIME NOT NULL,
  ubicacion VARCHAR(255),
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
  fecha_evento DATE NOT NULL,
  id_usuario INT,
  FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario)
);

CREATE TABLE Patrones (
  id_patron INT AUTO_INCREMENT PRIMARY KEY,
  nombre_patron VARCHAR(100),
  descripcion TEXT,
  imagen_url VARCHAR(255),
  nivel_dificultad ENUM('principiante', 'intermedio', 'avanzado')
);
ALTER TABLE Proyectos ADD COLUMN id_patron INT,
ADD FOREIGN KEY (id_patron) REFERENCES Patrones(id_patron);

CREATE TABLE Favoritos_Patrones (
  id_usuario INT,
  id_patron INT,
  PRIMARY KEY (id_usuario, id_patron),
  FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario),
  FOREIGN KEY (id_patron) REFERENCES Patrones(id_patron)
);