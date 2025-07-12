<?php

namespace Controllers\Crud;

use Controllers\PublicController;
use Views\Renderer;
use Dao\Crud\Generator as DaoGenerator;
use Utilities\Context;
/**
 * Clase para generar CRUDs automáticamente.
 */


class Generator extends PublicController {

    public function run(): void {
    $tablesData = DaoGenerator::getTables();
    $selectedTable = $_POST['table'] ?? '';
    $generated = false;
    $dao = '';
    $controllerList = '';
    $controllerForm = '';
    $viewList = '';
    $viewForm = '';

    //Si el boton de generar fue presionado y hay una tabla en el input se crea el CRUD
if ($this->isPostBack() && !empty($selectedTable)) {
        $generated = true;
        $dao = self::generateDao($selectedTable);
        $controllerList = self::generateControllerList($selectedTable);
        $controllerForm = self::generateControllerForm($selectedTable);
        $viewList = self::generateViewList($selectedTable);
        $viewForm = self::generateViewForm($selectedTable);
    } else {
        // Si no se ha seleccionado una tabla, se muestra un mensaje de error
        if (empty($selectedTable)) {
            Renderer::render("crud/generator", [
                'tables' => implode(", ", $tablesData), 
                'generated' => $generated,
                'error' => 'Por favor, selecciona una tabla para generar el CRUD.'
            ]);
            return;
        }
        
    }

    Renderer::render("crud/generator", [
        'tables' => implode(", ", $tablesData), 
        'generated' => $generated,
        'table' => $selectedTable,
        'codeSections' => [
            [
                'title' => '📁 DAO (Modelo de Datos)',
                'id' => 'code-dao',
                'code' => $dao
            ],
            [
                'title' => '📂 Controlador - Lista',
                'id' => 'code-controller-list',
                'code' => $controllerList
            ],
            [
                'title' => '📂 Controlador - Formulario',
                'id' => 'code-controller-form',
                'code' => $controllerForm
            ],
            [
                'title' => '🖥️ Vista - Lista',
                'id' => 'code-view-list',
                'code' => $viewList
            ],
            [
                'title' => '🖥️ Vista - Formulario',
                'id' => 'code-view-form',
                'code' => $viewForm
            ]
        ]
    ]);
}

public static function generateDao(string $table): string {
    $fields = DaoGenerator::getFields($table);
    $fieldTypes = DaoGenerator::getFieldTypes($table);
    $primaryKey = DaoGenerator::getPrimaryKey($table);

    $daoCode = "<?php\n\nnamespace Dao\\{$table};\n\nuse Dao\\Table;\n\nclass {$table} extends Table {\n";
    $daoCode .= "    public static function getAll(): array {\n";
    $daoCode .= "        \$sqlstr = \"SELECT * FROM `{$table}`;\";\n";
    $daoCode .= "        return self::obtenerRegistros(\$sqlstr, []);\n";
    $daoCode .= "    }\n\n";

    // Add methods for each field
    foreach ($fields as $field) {
        $daoCode .= "    public static function get{$field}(): string {\n";
        $daoCode .= "        return self::getFieldType('{$field}');\n";
        $daoCode .= "    }\n\n";
    }

    if ($primaryKey) {
        $daoCode .= "    public static function getPrimaryKey(): string {\n";
        $daoCode .= "        return '{$primaryKey}';\n";
        $daoCode .= "    }\n\n";
    }

    $daoCode .= "}\n";

    return $daoCode;
}

public static function generateControllerList(string $table): string {
    $controllerCodeList = "<?php\n\nnamespace Controllers\\{$table};\n\nuse Controllers\\PublicController;\nuse Views\\Renderer;\nuse Dao\\{$table}\\{$table} as {$table}Dao;\n\nclass {$table}List extends PublicController {\n";
    $controllerCodeList .= "    public function run(): void {\n";
    $controllerCodeList .= "        \$data = {$table}Dao::getAll();\n";
    $controllerCodeList .= "        Renderer::render('{$table}/list', ['data' => \$data]);\n";
    $controllerCodeList .= "    }\n";
    $controllerCodeList .= "}\n";

    return $controllerCodeList;
}

public static function generateControllerForm(string $table): string {
    $controllerCodeForm = "<?php\n\nnamespace Controllers\\{$table};\n\nuse Controllers\\PublicController;\nuse Views\\Renderer;\nuse Dao\\{$table}\\{$table} as {$table}Dao;\n\nclass {$table}Form extends PublicController {\n";
    $controllerCodeForm .= "    public function run(): void {\n";
    $controllerCodeForm .= "        \$mode = Context::get('mode', 'INS');\n";
    $controllerCodeForm .= "        \$data = [];\n";
    $controllerCodeForm .= "        if (\$mode !== 'INS') {\n";
    $controllerCodeForm .= "            \$id = Context::get('id');\n";
    $controllerCodeForm .= "            \$data = {$table}Dao::getById(\$id);\n";

    $controllerCodeForm .= "        }\n";
    $controllerCodeForm .= "        Renderer::render('{$table}/form', ['data' => \$data, 'mode' => \$mode]);\n";
    $controllerCodeForm .= "    }\n";
    $controllerCodeForm .= "}\n";
    return $controllerCodeForm;
}

public static function generateViewList(string $table): string {
    $viewCodeList = "<!-- Vista de lista para {$table} -->\n";
    $viewCodeList .= "<h1>Lista de {$table}</h1>\n";
    $viewCodeList .= "<table>\n";
    $viewCodeList .= "    <thead>\n";
    $viewCodeList .= "        <tr>\n";
    $viewCodeList .= "            <th>ID</th>\n";
    $viewCodeList .= "            <th>Nombre</th>\n";
    // Add headers
    $fields = DaoGenerator::getFields($table);
    foreach ($fields as $field) {
        $viewCodeList .= "            <th>{$field}</th>\n";
    }
    
    $viewCodeList .= "            <th>Acciones</th>\n";
    $viewCodeList .= "        </tr>\n";
    $viewCodeList .= "    </thead>\n";
    $viewCodeList .= "    <tbody>\n";
    
    // Placeholder for data rows
    $viewCodeList .= "        <!-- Aquí van los datos -->\n";
    
    $viewCodeList .= "    </tbody>\n";
    $viewCodeList .= "</table>\n";

    return $viewCodeList;
}

public static function generateViewForm(string $table): string {
    $viewCodeForm = "<!-- Vista de formulario para {$table} -->\n";
    $viewCodeForm .= "<h1>Formulario de {$table}</h1>\n";
    $viewCodeForm .= "<form method='post'>\n";

    // Add fields
    $fields = DaoGenerator::getFields($table);
    foreach ($fields as $field) {
        $viewCodeForm .= "    <label for='{$field}'>{$field}</label>\n";
        $viewCodeForm .= "    <input type='text' id='{$field}' name='{$field}' required>\n";
    }

    $viewCodeForm .= "    <button type='submit'>Guardar</button>\n";
    $viewCodeForm .= "</form>\n";

    return $viewCodeForm;
}

public function isPostBack(): bool {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

}
