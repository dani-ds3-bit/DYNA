<?php
// conexion con la base de datos de dyna
//

// se configuraron los encabezados 
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Función para cargar variables de entorno desde un archivo .env si existe
function cargarEnv($ruta) {
    if (!file_exists($ruta)) {
        return;
    }

    $lineas = @file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lineas === false) {
        return;
    }

    foreach ($lineas as $linea) {
        // Ignorar comentarios
        if (strpos(trim($linea), '#') === 0) {
            continue;
        }

        $posIgual = strpos($linea, '=');
        if ($posIgual !== false) {
            $clave = trim(substr($linea, 0, $posIgual));
            $valor = trim(substr($linea, $posIgual + 1));
            
            // Quitar comillas si existen
            $valor = trim($valor, "\"'");
            
            if (!empty($clave)) {
                $_ENV[$clave] = $valor;
                $_SERVER[$clave] = $valor;
                if (function_exists('putenv')) {
                    @putenv("$clave=$valor");
                }
            }
        }
    }
}

// Función segura para obtener variables de entorno con fallback
function obtenerEnv($clave, $default = null) {
    if (isset($_ENV[$clave])) {
        return $_ENV[$clave];
    }
    if (isset($_SERVER[$clave])) {
        return $_SERVER[$clave];
    }
    if (function_exists('getenv')) {
        $val = @getenv($clave);
        if ($val !== false) {
            return $val;
        }
    }
    return $default;
}

// Cargar el archivo de configuración .env en la raíz del proyecto
cargarEnv(__DIR__ . '/../.env');

// se declaran las credenciales de base de datos con fallback seguro
$servidor = obtenerEnv('DB_SERVER', 'localhost');
$usuario = obtenerEnv('DB_USER', 'root');
$clave = obtenerEnv('DB_PASS', 'Mayonesa#12');
$base_datos = obtenerEnv('DB_NAME', 'dyna_db');

// se creo la conexion usando 
$conexion = new mysqli($servidor, $usuario, $clave, $base_datos);

// se verifico si la conexion fallo antes de continuar con cualquier consulta
if ($conexion->connect_error) {
    die("<b>Error:</b> error de conexion en la base de datos.<br>
         verificar que XAMPP y el script
         <b>dyna_db.sql</b> se haya ejecutado en phpMyAdmin.<br><br>
         detalle del error: " . $conexion->connect_error);
}



if (!$conexion->set_charset("utf8mb4")) {
    die("error al establecer charset: " . $conexion->error);
}
?>