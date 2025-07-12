<?php

namespace Dao\Crud;

use Dao\Table;

class Generator extends Table
{
    /**
     * Obtiene todas las tablas disponibles en la base de datos actual.
     */
    public static function getTables(): array
    {
        $sqlstr = "SHOW TABLES;";
        $result = self::obtenerRegistros($sqlstr, []);
        return array_map(fn($row) => array_values($row)[0], $result);
    }

    /**
     * Obtiene el esquema de una tabla (columnas, tipo, null, etc.).
     */
    public static function getDescTable(string $table): array
    {
        $sqlstr = "DESCRIBE `{$table}`;";
        return self::obtenerRegistros($sqlstr, []);
    }

    /**
     * Obtiene la clave primaria de una tabla (si existe).
     */
    public static function getPrimaryKey(string $table): ?string
    {
        $sqlstr = "SHOW KEYS FROM `{$table}` WHERE Key_name = 'PRIMARY';";
        $keys = self::obtenerRegistros($sqlstr, []);
        return count($keys) > 0 ? $keys[0]["Column_name"] : null;
    }

    /**
     * Devuelve un arreglo plano con solo los nombres de los campos de la tabla.
     */
    public static function getFields(string $table): array
    {
        $desc = self::getDescTable($table);
        return array_map(fn($col) => $col['Field'], $desc);
    }

    /**
     * Retorna un arreglo con los tipos de datos por campo.
     */
    public static function getFieldTypes(string $table): array
    {
        $desc = self::getDescTable($table);
        $fields = [];
        foreach ($desc as $col) {
            $fields[$col['Field']] = $col['Type'];
        }
        return $fields;
    }
}
