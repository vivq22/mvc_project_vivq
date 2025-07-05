<?php

namespace Dao\Examen;

use Dao\Table;

class Productos extends Table
{
    public static function getProductos()
    {
        $sqlstr = "SELECT * FROM ProductosElectronica;";
        return self::obtenerRegistros($sqlstr, []);
    }

    public static function getProductoById(int $productoId)
    {
        $sqlstr = "SELECT * FROM ProductosElectronica WHERE id_producto = :id;";
        return self::obtenerUnRegistro($sqlstr, ["id" => $productoId]);
    }

    public static function nuevoProducto(
        string $nombre,
        string $tipo,
        float $precio,
        string $marca,
        string $fecha_lanzamiento
    ) {
        $sqlstr = "INSERT INTO ProductosElectronica (nombre, tipo, precio, marca, fecha_lanzamiento) VALUES (:nombre, :tipo, :precio, :marca, :fecha_lanzamiento);";

        return self::executeNonQuery(
            $sqlstr,
            [
                "nombre" => $nombre,
                "tipo" => $tipo,
                "precio" => $precio,
                "marca" => $marca,
                "fecha_lanzamiento" => $fecha_lanzamiento
            ]
        );
    }

    public static function actualizarProducto(
        int $id,
        string $nombre,
        string $tipo,
        float $precio,
        string $marca,
        string $fecha_lanzamiento
    ): int {
        $sqlstr = "UPDATE ProductosElectronica 
                    SET nombre = :nombre,
                        tipo = :tipo,
                        precio = :precio,
                        marca = :marca,
                        fecha_lanzamiento = :fecha_lanzamiento
                    WHERE id_producto = :id;";

        return self::executeNonQuery(
            $sqlstr,
            [
                "nombre" => $nombre,
                "tipo" => $tipo,
                "precio" => $precio,
                "marca" => $marca,
                "fecha_lanzamiento" => $fecha_lanzamiento,
                "id" => $id
            ]
        );
    }

    public static function eliminarProducto(int $id): int
    {
        $sqlstr = "DELETE FROM ProductosElectronica WHERE id_producto = :id;";
        return self::executeNonQuery(
            $sqlstr,
            ["id" => $id]
        );
    }
}
