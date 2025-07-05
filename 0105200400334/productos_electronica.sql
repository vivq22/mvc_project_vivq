CREATE TABLE ProductosElectronica (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    tipo VARCHAR(50),
    precio DECIMAL(10, 2),
    marca VARCHAR(50),
    fecha_lanzamiento DATE
);