<?php

namespace Dao\Restaurants;

use Dao\Table;

class Restaurants extends Table
{

    public static function getRestaurants(): array
    {
        $sqlstr = "SELECT * FROM datosrestaurantes;";
        return self::obtenerRegistros(
            $sqlstr,
            []
        );
    }

    public static function getRestaurantsById(int $id)
    {
        $sqlstr = "SELECT * from datosrestaurantes where id_restaurante = :id_restaurante;";
        return self::obtenerUnRegistro($sqlstr, ["id_restaurante" => $id]);
    }

    public static function newRestaurant(string $nombre, string $tipo_cocina, string $ubicacion, float $calificacion, int $capacidad_comensales)
    {
        $sqlstr = "INSERT INTO datosrestaurantes (nombre, tipo_cocina, ubicacion, calificacion, capacidad_comensales) values (:nombre, :tipo_cocina, :ubicacion, :calificacion, :capacidad_comensales);";
        return self::executeNonQuery(
            $sqlstr,
            [
                "nombre" => $nombre,
                "tipo_cocina" => $tipo_cocina,
                "ubicacion" => $ubicacion,
                "calificacion" => $calificacion,
                "capacidad_comensales" => $capacidad_comensales
            ]
        );
    }

    public static function updateRestaurant(int $id, string $nombre, string $tipo_cocina, string $ubicacion, float $calificacion, int $capacidad_comensales)
    {
$sqlstr = "UPDATE datosrestaurantes SET nombre = :nombre, tipo_cocina = :tipo_cocina, ubicacion = :ubicacion, calificacion = :calificacion, capacidad_comensales = :capacidad_comensales WHERE id_restaurante = :id_restaurante;";
        return self::executeNonQuery(
            $sqlstr,
            [
                "nombre" => $nombre,
                "tipo_cocina" => $tipo_cocina,
                "ubicacion" => $ubicacion,
                "calificacion" => $calificacion,
                "capacidad_comensales" => $capacidad_comensales,
                "id_restaurante" => $id
            ]
        );
    }
    public static function deleteRestaurant(int $id)
    {
$sqlstr = "DELETE FROM datosrestaurantes WHERE id_restaurante = :id_restaurante;";
        return self::executeNonQuery(
            $sqlstr,
            [
                "id_restaurante" => $id
            ]
        );
    }
}