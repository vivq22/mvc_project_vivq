<?php

namespace Dao\Roles;

use Dao\Table;

class Roles extends Table{

    public static function getRoles(): array{
        $sqlstr = "SELECT * FROM roles;";
        return self::obtenerRegistros(
            $sqlstr,
            []
        );
    }

    public static function getRolById(int $id)
    {
        $sqlstr = "SELECT * from roles where id = :rolescod;";
        return self::obtenerUnRegistro($sqlstr, ["rolescod" => $id]);
    }

    public static  function insertRol(
        string $rolesest,
        string $rolesdsc
    ){
        $sqlstr = "INSERT INTO roles (rolesest, rolesdsc) VALUES (:rolesest, :rolesdsc);";
        $params = [
            "rolesest" => $rolesest,
            "rolesdsc" => $rolesdsc
        ];
        return self::executeNonQuery(
            $sqlstr,
            $params
        );
    }

    public static function updateRol(
        int $rolescod,
        string $rolesest,
        string $rolesdsc
    ){
        $sqlstr = "UPDATE roles SET rolesest = :rolesest, rolesdsc = :rolesdsc WHERE id = :rolescod;";
        $params = [
            "rolescod" => $rolescod,
            "rolesest" => $rolesest,
            "rolesdsc" => $rolesdsc
        ];
        return self::executeNonQuery(
            $sqlstr,
            $params
        );
    }
    public static function deleteRol(int $id)
    {
        $sqlstr = "DELETE FROM roles WHERE id = :rolescod;";
        return self::executeNonQuery($sqlstr, ["rolescod" => $id]);
    }
}