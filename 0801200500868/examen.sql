-- Tabla: Datos de Restaurantes
CREATE TABLE DatosRestaurantes (
    id_restaurante INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tipo_cocina VARCHAR(50),
    ubicacion VARCHAR(100),
    calificacion DECIMAL(3, 2),
    capacidad_comensales INT
);

INSERT INTO DatosRestaurantes (nombre, tipo_cocina, ubicacion, calificacion, capacidad_comensales) VALUES
('El Fogón Catracho', 'Comida Hondureña', 'Tegucigalpa, Honduras', 4.7, 60),
('Sakura Sushi Bar', 'Japonesa', 'San Pedro Sula, Honduras', 4.5, 40),
('La Parrilla del Valle', 'Parrillada', 'Valle de Ángeles, Honduras', 4.6, 80),
('Il Forno', 'Italiana', 'Tegucigalpa, Honduras', 4.3, 55),
('La Casa del Chef', 'Internacional', 'La Ceiba, Honduras', 4.8, 70),
('Taco Loco', 'Mexicana', 'Choluteca, Honduras', 4.2, 35),
('Wok & Roll', 'China', 'Comayagua, Honduras', 4.4, 50),
('Bistró del Mar', 'Mariscos', 'Roatán, Honduras', 4.9, 45),
('Café Colonial', 'Cafetería', 'Santa Rosa de Copán, Honduras', 4.1, 30),
('Sabores del Mundo', 'Fusión', 'Danlí, Honduras', 4.6, 65);
