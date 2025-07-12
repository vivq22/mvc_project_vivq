<?php
/**
 * CRUD Generator
 * 
 * Genera automáticamente:
 * - Modelo de datos
 * - Controladores (lista y formulario)
 * - Vistas (lista y formulario)
 * 
 * @version 1.1
 * @date 2025-07-08
 */

// ==============================================
// CONFIGURACIÓN INICIAL
// ==============================================

// Mostrar todos los errores para desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar si se ejecuta desde CLI
if (php_sapi_name() !== 'cli') {
    die("❌ Este script debe ejecutarse desde la línea de comandos (CLI).");
}

// Paso 1: Cargar configuración desde parameters.env
function parseEnv($filePath) {
    $config = [];
    if (file_exists($filePath)) {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Saltar comentarios
            if (strpos(trim($line), '#') === 0) continue;
            
            // Manejar líneas con = correctamente
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);
                
                // Eliminar comillas si existen
                if (preg_match('/^(["\'])(.*)\1$/', $value, $matches)) {
                    $value = $matches[2];
                }
                
                $config[$key] = $value;
            }
        }
    }
    return $config;
}

// Mapear tipos SQL a PHP
function getPHPTypes($sqlType) {
    $sqlType = strtolower($sqlType);
    
    if (strpos($sqlType, 'int') !== false) return 'int';
    if (strpos($sqlType, 'decimal') !== false || strpos($sqlType, 'float') !== false || strpos($sqlType, 'double') !== false) return 'float';
    if (strpos($sqlType, 'bool') !== false) return 'bool';
    if (strpos($sqlType, 'date') !== false || strpos($sqlType, 'time') !== false) return 'string'; // DateTime podría ser mejor
    return 'string';
}

// Mapear tipos SQL a HTML input types
function getHTMLInputType($sqlType) {
    $sqlType = strtolower($sqlType);
    
    if (strpos($sqlType, 'int') !== false) return 'number';
    if (strpos($sqlType, 'decimal') !== false || strpos($sqlType, 'float') !== false) return 'number';
    if (strpos($sqlType, 'date') !== false) return 'date';
    if (strpos($sqlType, 'time') !== false) return 'time';
    if (strpos($sqlType, 'datetime') !== false || strpos($sqlType, 'timestamp') !== false) return 'datetime-local';
    if (strpos($sqlType, 'text') !== false || strpos($sqlType, 'longtext') !== false) return 'textarea';
    return 'text';
}

// Cargar configuración
$envConfig = parseEnv(__DIR__ . '/parameters.env');
if (empty($envConfig)) {
    die("❌ Error: No se pudo cargar el archivo parameters.env o está vacío.");
}

// Definir constantes de conexión
define('DB_HOST', $envConfig['DB_SERVER'] ?? 'localhost');
define('DB_USER', $envConfig['DB_USER'] ?? 'root');
define('DB_PASS', $envConfig['DB_PSWD'] ?? '');
define('DB_NAME', $envConfig['DB_DATABASE'] ?? '');
define('DB_PORT', $envConfig['DB_PORT'] ?? '3306');

// ==============================================
// INTERACCIÓN CON EL USUARIO
// ==============================================

// Paso 2: Pedir el nombre de la tabla
echo "🔍 Ingresa el nombre de la tabla (ej: productos): ";
$tableName = trim(fgets(STDIN));

if (empty($tableName)) {
    die("❌ Error: Debes proporcionar un nombre de tabla.");
}

// Verificar si la tabla existe y obtener campos
try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $db = new PDO($dsn, DB_USER, DB_PASS, $options);
    
    // Obtener estructura de la tabla
    $query = $db->prepare("DESCRIBE $tableName");
    $query->execute();
    $fields = $query->fetchAll();
    
    if (empty($fields)) {
        die("❌ La tabla '$tableName' no existe o no tiene campos.");
    }
    
    // Identificar campo clave primaria
    $primaryKey = '';
    foreach ($fields as $field) {
        if ($field['Key'] == 'PRI') {
            $primaryKey = $field['Field'];
            break;
        }
    }
    
    if (empty($primaryKey)) {
        die("❌ La tabla '$tableName' no tiene una clave primaria definida.");
    }
    
    // Mostrar información de la tabla
    echo "✅ Tabla '$tableName' encontrada. Campos:\n";
    foreach ($fields as $field) {
        echo sprintf(
            "- %-20s %-20s %-10s %s\n",
            $field['Field'],
            $field['Type'],
            ($field['Null'] == 'YES' ? 'NULL' : 'NOT NULL'),
            ($field['Field'] == $primaryKey ? "[PRIMARY KEY]" : "")
        );
    }
    
    // Obtener comentario de la tabla si existe
    $query = $db->prepare("SELECT table_comment FROM information_schema.tables WHERE table_schema = ? AND table_name = ?");
    $query->execute([DB_NAME, $tableName]);
    $tableComment = $query->fetchColumn();
    
} catch (PDOException $e) {
    die("❌ Error de conexión: " . $e->getMessage());
}

// ==============================================
// GENERACIÓN DEL MODELO
// ==============================================

$modelContent = "<?php\n";
$modelContent .= "/**\n";
$modelContent .= " * Modelo para la tabla $tableName\n";
if (!empty($tableComment)) {
    $modelContent .= " * $tableComment\n";
}
$modelContent .= " * \n";
$modelContent .= " * @generated " . date('Y-m-d H:i:s') . "\n";
$modelContent .= " */\n\n";
$modelContent .= "namespace Dao\\" . ucfirst($tableName) . "s;\n\n";
$modelContent .= "use Dao\\Table;\n\n";
$modelContent .= "class " . ucfirst($tableName) . " extends Table\n";
$modelContent .= "{\n";
$modelContent .= "    /**\n";
$modelContent .= "     * Obtiene todos los registros de la tabla\n";
$modelContent .= "     * \n";
$modelContent .= "     * @return array\n";
$modelContent .= "     */\n";
$modelContent .= "    public static function getAll()\n";
$modelContent .= "    {\n";
$modelContent .= "        \$sqlstr = \"SELECT * FROM $tableName;\";\n";
$modelContent .= "        return self::obtenerRegistros(\$sqlstr, []);\n";
$modelContent .= "    }\n\n";

// Método getById
$pkType = getPHPTypes($fields[array_search($primaryKey, array_column($fields, 'Field'))]['Type']);
$modelContent .= "    /**\n";
$modelContent .= "     * Obtiene un registro por su clave primaria\n";
$modelContent .= "     * \n";
$modelContent .= "     * @param $pkType \$$primaryKey\n";
$modelContent .= "     * @return array\n";
$modelContent .= "     */\n";
$modelContent .= "    public static function getById($pkType \$$primaryKey)\n";
$modelContent .= "    {\n";
$modelContent .= "        \$sqlstr = \"SELECT * FROM $tableName WHERE $primaryKey = :$primaryKey;\";\n";
$modelContent .= "        return self::obtenerUnRegistro(\$sqlstr, [\"$primaryKey\" => \$$primaryKey]);\n";
$modelContent .= "    }\n\n";

// Método insert
$modelContent .= "    /**\n";
$modelContent .= "     * Inserta un nuevo registro\n";
$modelContent .= "     * \n";
$modelContent .= "     * @return int Número de filas afectadas\n";
$modelContent .= "     */\n";
$modelContent .= "    public static function insert(\n";
foreach ($fields as $field) {
    if ($field['Field'] == $primaryKey) continue;
    $phpType = getPHPTypes($field['Type']);
    $fieldName = $field['Field'];
    $modelContent .= "        $phpType \$$fieldName" . ($field['Null'] == 'YES' ? " = null" : "") . ",\n";
}
$modelContent = rtrim($modelContent, ",\n") . "\n";
$modelContent .= "    ) {\n";
$modelContent .= "        \$sqlstr = \"INSERT INTO $tableName (\n";
$modelContent .= "            " . implode(",\n            ", array_map(function($f) { 
    return $f['Field']; 
}, array_filter($fields, function($f) use ($primaryKey) { 
    return $f['Field'] != $primaryKey; 
}))) . "\n";
$modelContent .= "        ) VALUES (\n";
$modelContent .= "            " . implode(",\n            ", array_map(function($f) { 
    return ":" . $f['Field']; 
}, array_filter($fields, function($f) use ($primaryKey) { 
    return $f['Field'] != $primaryKey; 
}))) . "\n";
$modelContent .= "        )\";\n\n";
$modelContent .= "        \$params = [\n";
foreach ($fields as $field) {
    if ($field['Field'] == $primaryKey) continue;
    $modelContent .= "            \"" . $field['Field'] . "\" => \$" . $field['Field'] . ",\n";
}
$modelContent .= "        ];\n\n";
$modelContent .= "        return self::executeNonQuery(\$sqlstr, \$params);\n";
$modelContent .= "    }\n\n";

// Método update
$modelContent .= "    /**\n";
$modelContent .= "     * Actualiza un registro existente\n";
$modelContent .= "     * \n";
$modelContent .= "     * @return int Número de filas afectadas\n";
$modelContent .= "     */\n";
$modelContent .= "    public static function update(\n";
$modelContent .= "        $pkType \$$primaryKey,\n";
foreach ($fields as $field) {
    if ($field['Field'] == $primaryKey) continue;
    $phpType = getPHPTypes($field['Type']);
    $fieldName = $field['Field'];
    $modelContent .= "        $phpType \$$fieldName" . ($field['Null'] == 'YES' ? " = null" : "") . ",\n";
}
$modelContent = rtrim($modelContent, ",\n") . "\n";
$modelContent .= "    ) {\n";
$modelContent .= "        \$sqlstr = \"UPDATE $tableName SET \n";
foreach ($fields as $field) {
    if ($field['Field'] == $primaryKey) continue;
    $modelContent .= "            " . $field['Field'] . " = :" . $field['Field'] . ",\n";
}
$modelContent = rtrim($modelContent, ",\n") . "\n";
$modelContent .= "        WHERE $primaryKey = :$primaryKey\";\n\n";
$modelContent .= "        \$params = [\n";
$modelContent .= "            \"$primaryKey\" => \$$primaryKey,\n";
foreach ($fields as $field) {
    if ($field['Field'] == $primaryKey) continue;
    $modelContent .= "            \"" . $field['Field'] . "\" => \$" . $field['Field'] . ",\n";
}
$modelContent .= "        ];\n\n";
$modelContent .= "        return self::executeNonQuery(\$sqlstr, \$params);\n";
$modelContent .= "    }\n\n";

// Método delete
$modelContent .= "    /**\n";
$modelContent .= "     * Elimina un registro\n";
$modelContent .= "     * \n";
$modelContent .= "     * @param $pkType \$$primaryKey\n";
$modelContent .= "     * @return int Número de filas afectadas\n";
$modelContent .= "     */\n";
$modelContent .= "    public static function delete($pkType \$$primaryKey)\n";
$modelContent .= "    {\n";
$modelContent .= "        \$sqlstr = \"DELETE FROM $tableName WHERE $primaryKey = :$primaryKey\";\n";
$modelContent .= "        \$params = [\"$primaryKey\" => \$$primaryKey];\n";
$modelContent .= "        return self::executeNonQuery(\$sqlstr, \$params);\n";
$modelContent .= "    }\n";
$modelContent .= "}\n";

// Guardar el modelo
$modelDir = __DIR__ . '/src/Dao/' . ucfirst($tableName) . 's';
if (!file_exists($modelDir)) {
    mkdir($modelDir, 0777, true);
}

$modelPath = "$modelDir/" . ucfirst($tableName) . ".php";
if (file_put_contents($modelPath, $modelContent) === false) {
    die("❌ Error al guardar el modelo en: $modelPath");
}
echo "✅ Modelo generado en: $modelPath\n";

// ==============================================
// GENERACIÓN DE CONTROLADORES
// ==============================================

// --- Controlador de Lista ---
$controllerListContent = "<?php\n";
$controllerListContent .= "/**\n";
$controllerListContent .= " * Controlador para listar registros de $tableName\n";
$controllerListContent .= " * \n";
$controllerListContent .= " * @generated " . date('Y-m-d H:i:s') . "\n";
$controllerListContent .= " */\n\n";
$controllerListContent .= "namespace Controllers\\" . ucfirst($tableName) . "s;\n\n";
$controllerListContent .= "use Controllers\\PublicController;\n";
$controllerListContent .= "use Dao\\" . ucfirst($tableName) . "s\\" . ucfirst($tableName) . " as " . ucfirst($tableName) . "DAO;\n";
$controllerListContent .= "use Views\\Renderer;\n\n";
$controllerListContent .= "class " . ucfirst($tableName) . "s extends PublicController\n";
$controllerListContent .= "{\n";
$controllerListContent .= "    private array \$viewData;\n\n";
$controllerListContent .= "    public function __construct()\n";
$controllerListContent .= "    {\n";
$controllerListContent .= "        \$this->viewData = [];\n";
$controllerListContent .= "    }\n\n";
$controllerListContent .= "    public function run(): void\n";
$controllerListContent .= "    {\n";
$controllerListContent .= "        // Obtener parámetros del DAO\n";
$controllerListContent .= "        \$this->viewData = [\"" . strtolower($tableName) . "\" => " . ucfirst($tableName) . "DAO::getAll()];\n";
$controllerListContent .= "        Renderer::render('" . strtolower($tableName) . "s/" . strtolower($tableName) . "s', \$this->viewData);\n";
$controllerListContent .= "    }\n";
$controllerListContent .= "}\n";
// Guardar el controlador de lista
$controllerListDir = __DIR__ . '/src/Controllers/' . ucfirst($tableName
) . 's';
if (!file_exists($controllerListDir)) {
    mkdir($controllerListDir, 0777, true);
}
$controllerListPath = "$controllerListDir/" . ucfirst($tableName) . "s.php";
if (file_put_contents($controllerListPath, $controllerListContent) === false) {
    die("❌ Error al guardar el controlador de lista en: $controllerListPath");
}
echo "✅ Controlador de lista generado en: $controllerListPath\n";

// --- Controlador de Formulario ---
$controllerFormContent = "<?php\n";
$controllerFormContent .= "/**\n";
$controllerFormContent .= " * Controlador para el formulario de $tableName\n";
$controllerFormContent .= " * \n";
$controllerFormContent .= " * @generated " . date('Y-m-d H:i:s') . "\n";
$controllerFormContent .= " */\n\n";
$controllerFormContent .= "namespace Controllers\\" . ucfirst($tableName) . "s;\n\n";
$controllerFormContent .= "use Controllers\\PublicController;\n";
$controllerFormContent .= "use Dao\\" . ucfirst($tableName) . "s\\" . ucfirst($tableName) . " as " . ucfirst($tableName) . "DAO;\n";
$controllerFormContent .= "use Views\\Renderer;\n\n";
$controllerFormContent .= "class " . ucfirst($tableName) . "Form extends PublicController\n";
$controllerFormContent .= "{\n";
$controllerFormContent .= "    private array \$viewData;\n\n";  
$controllerFormContent .= "    public function __construct()\n";
$controllerFormContent .= "    {\n";
$controllerFormContent .= "        \$this->viewData = [];\n";
$controllerFormContent .= "    }\n\n";
$controllerFormContent .= "    public function run(): void\n";
$controllerFormContent .= "    {\n";
$controllerFormContent .= "        // Obtener parámetros del DAO\n";
$controllerFormContent .= "        \$this->viewData = [\"" . strtolower($tableName) . "\" => " . ucfirst($tableName) . "DAO::getAll()];\n";
$controllerFormContent .= "        Renderer::render('" . strtolower($tableName) . "s/" . strtolower($tableName) . "s', \$this->viewData);\n";
$controllerFormContent .= "    }\n";
$controllerFormContent .= "}\n";
// Guardar el controlador de formulario
$controllerFormDir = __DIR__ . '/src/Controllers/' . ucfirst($tableName) . 's';
if (!file_exists($controllerFormDir)) {
    mkdir($controllerFormDir, 0777, true);
}
$controllerFormPath = "$controllerFormDir/" . ucfirst($tableName) . "Form.php";
if (file_put_contents($controllerFormPath, $controllerFormContent) === false) {
    die("❌ Error al guardar el controlador de formulario en: $controllerFormPath");
}

echo "✅ Controlador de formulario generado en: $controllerFormPath\n";
// ==============================================
// GENERACIÓN DE VISTAS
// ==============================================
// --- Vista de Lista ---
$viewListContent = "<!-- Vista de lista de $tableName -->\n";
$viewListContent .= "<h1>Lista de " . ucfirst($tableName) . "s</h1>\n";
$viewListContent .= "<table>\n";
$viewListContent .= "    <thead>\n";
$viewListContent .= "        <tr>\n";
foreach ($fields as $field) {
    $viewListContent .= "            <th>" . htmlspecialchars($field['Field']) . "</th>\n";
}
$viewListContent .= "            <th>Acciones</th>\n";
$viewListContent .= "        </tr>\n";
$viewListContent .= "    </thead>\n";
$viewListContent .= "    <tbody>\n";
$viewListContent .= "        <?php foreach (\$" . strtolower($tableName)
    . " as \$item): ?>\n";
$viewListContent .= "            <tr>\n";
foreach ($fields as $field) {
    $viewListContent .= "                <td>" . htmlspecialchars($item[$field['Field']]) . "</td>\n";
}
$viewListContent .= "                <td>\n";
$viewListContent .= "                    <a href=\"/{$tableName}/edit/<?php echo \$item['id']; ?>\">Editar</a>\n";
$viewListContent .= "                    <a href=\"/{$tableName}/delete/<?php echo \$item['id']; ?>\">Eliminar</a>\n";
$viewListContent .= "                </td>\n";
$viewListContent .= "            </tr>\n";
$viewListContent .= "        <?php endforeach; ?>\n";
$viewListContent .= "    </tbody>\n";
$viewListContent .= "</table>\n";
$viewListContent .= "<a href=\"/{$tableName}/create\">Crear nuevo " . ucfirst($tableName) . "</a>\n";
// Guardar la vista de lista
$viewListDir = __DIR__ . '/src/Views/templates/' . ucfirst($tableName) . 's';
if (!file_exists($viewListDir)) {
    mkdir($viewListDir, 0777, true);
}
$viewListPath = "$viewListDir/list.php";
if (file_put_contents($viewListPath, $viewListContent) === false) {
    die("❌ Error al guardar la vista de lista en: $viewListPath");
}

echo "✅ Vista de lista generada en: $viewListPath\n";  
// --- Vista de Formulario ---
$viewFormContent = "<!-- Vista de formulario de $tableName -->\n";
$viewFormContent .= "<h1>Formulario de " . ucfirst($tableName) . "</h1>\n";
$viewFormContent .= "<form method=\"post\" action=\"/{$tableName}/save
\">\n";
foreach ($fields as $field) {
    $inputType = getHTMLInputType($field['Type']);
    $fieldName = htmlspecialchars($field['Field']);
    $viewFormContent .= "    <label for=\"$fieldName\">" . ucfirst($fieldName) . ":</label>\n";
    
    if ($inputType == 'textarea') {
        $viewFormContent .= "    <textarea name=\"$fieldName\" id=\"$fieldName\"></textarea>\n";
    } else {
        $viewFormContent .= "    <input type=\"$inputType\" name=\"$fieldName\" id=\"$fieldName\" />\n";
    }
    
    $viewFormContent .= "    <br />\n";
}
$viewFormContent .= "    <button type=\"submit\">Guardar</button>\n";
$viewFormContent .= "</form>\n";
// Guardar la vista de formulario
$viewFormPath = "$viewListDir/form.php";
if (file_put_contents($viewFormPath, $viewFormContent) === false) {
    die("❌ Error al guardar la vista de formulario en: $viewFormPath");
}
echo "✅ Vista de formulario generada en: $viewFormPath\n";
// ==============================================
// MENSAJE FINAL
echo "✅ Generación de CRUD para la tabla '$tableName' completada.\n";