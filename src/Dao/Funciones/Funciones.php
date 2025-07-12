<?php

namespace Dao\Funciones;

use Dao\Table;

class Funciones extends Table{

    public static function getFunciones(): array{
        $sqlstr = "SELECT * FROM funciones;";
        return self::obtenerRegistros(
            $sqlstr,
            []
        );
    }

    public static function getFuncionById(int $id)
    {
        $sqlstr = "SELECT * from funciones where id = :fncod;";
        return self::obtenerUnRegistro($sqlstr, ["fncod" => $id]);
    }

    public static  function insertFuncion(
        string $fncod,
        string $fndsc,
        string $fnest,
        string $fntyp
    ){
        $sqlstr = "INSERT INTO funciones (fncod, fndsc, fnest, fntyp) VALUES (:fncod, :fndsc, :fnest, :fntyp);";
        $params = [
            "fncod" => $fncod,
            "fndsc" => $fndsc,
            "fnest" => $fnest,
            "fntyp" => $fntyp
        ];
        return self::executeNonQuery(
            $sqlstr,
            $params
        );
    }

    public static function updateFuncion(
        string $fncod,
        string $fnest,
        string $fndsc,
        string $fntyp
    ){
        $sqlstr = "UPDATE funciones SET fnest = :fnest, fndsc = :fndsc, fntyp = :fntyp WHERE id = :fncod;";
        $params = [
            "fncod" => $fncod,
            "fnest" => $fnest,
            "fndsc" => $fndsc,
            "fntyp" => $fntyp
        ];
        return self::executeNonQuery(
            $sqlstr,
            $params
        );
    }
    public static function deleteFuncion(int $id)
    {
        $sqlstr = "DELETE FROM funciones WHERE id = :fncod;";
        return self::executeNonQuery($sqlstr, ["fncod" => $id]);
    }
}