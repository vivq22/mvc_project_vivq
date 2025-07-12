<?php

namespace Dao\Usuarios;

use Dao\Table;

class Usuarios extends Table{
    
    public static function getUsuarios(): array{
        $sqlstr = "SELECT * FROM usuario;";
        return self::obtenerRegistros(
            $sqlstr,
            []
        );
    }

    public static function getUsuarioById(int $id)
    {
        $sqlstr = "SELECT * from usuario where id = :usercod;";
        return self::obtenerUnRegistro($sqlstr, ["usercod" => $id]);
    }

    public static  function insertUsuario(
        string $useremail,
        string $username,
        string $userpswd,
        date $userfching,
        string $userpswdest,
        date $userpswdexp,
        string $useractcod,
        string $userpswdchg,
        string $usertipo
    ){
        $sqlstr = "INSERT INTO usuario (useremail, username, userpswd, userfching, userpswdest, userpswdexp, useractcod, userpswdchg, usertipo) 
                   VALUES (:useremail, :username, :userpswd, :userfching, :userpswdest, :userpswdexp, :useractcod, :userpswdchg, :usertipo);";
        $params = [
            "useremail" => $useremail,
            "username" => $username,
            "userpswd" => $userpswd,
            "userfching" => $userfching,
            "userpswdest" => $userpswdest,
            "userpswdexp" => $userpswdexp,
            "useractcod" => $useractcod,
            "userpswdchg" => $userpswdchg,
            "usertipo" => $usertipo
        ];
        return self::executeNonQuery(
            $sqlstr,
            $params
        );
    }

    public static function updateUsuario(
        int $usercod,
        string $useremail,
        string $username,
        string $userpswd,
        date $userfching,
        string $userpswdest,
        date $userpswdexp,
        string $useractcod,
        string $userpswdchg,
        string $usertipo
    ){
        $sqlstr = "UPDATE usuario SET useremail = :useremail, username = :username, userpswd = :userpswd, userfching = :userfching, userpswdest = :userpswdest, userpswdexp = :userpswdexp, useractcod = :useractcod, userpswdchg = :userpswdchg, usertipo = :usertipo WHERE id = :usercod;";
        $params = [
            "usercod" => $usercod,
            "useremail" => $useremail,
            "username" => $username,
            "userpswd" => $userpswd,
            "userfching" => $userfching,
            "userpswdest" => $userpswdest,
            "userpswdexp" => $userpswdexp,
            "useractcod" => $useractcod,
            "userpswdchg" => $userpswdchg,
            "usertipo" => $usertipo
        ];
        return self::executeNonQuery(
            $sqlstr,
            $params
        );
    }

    public static function deleteUsuario(int $id)
    {
        $sqlstr = "DELETE FROM usuario WHERE id = :usercod;";
        return self::executeNonQuery($sqlstr, ["usercod" => $id]);
    }
}