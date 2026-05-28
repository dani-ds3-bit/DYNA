<?php

// conexion a las consultas a la base de datos
include(__DIR__ . "/../conexion.php");
session_start();

// se verifico que el usuario tuviera sesion activa antes de mostrar datos
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../../frontend/Modulo1_Login/index.php");
    exit;
}

// se extrajeron los datos de sesion
$nombre   = $_SESSION["usuario_nombre"];
$iniciales = strtoupper(substr($nombre, 0, 1));

// se preparo y ejecuto la consulta para obtener todos los gastos del usuario
$consulta = $conexion->prepare(
    "SELECT g.id, g.nombre_gasto, g.monto, g.prioridad, g.fecha_creacion, g.metodo_registro,
            c.nombre_categoria, g.es_de_beca, g.beca_id
     FROM gastos g LEFT JOIN categorias c ON g.categoria_id = c.id
     WHERE g.usuario_id = ? ORDER BY g.fecha_creacion DESC, g.hora_creacion DESC"
);
$consulta->bind_param("i", $_SESSION["usuario_id"]);
$consulta->execute();
$gastos = $consulta->get_result()->fetch_all(MYSQLI_ASSOC);
$consulta->close();

// las becas se consultan todas las becas del usuario con cuanto han gastado de cada una
$consulta_becas = $conexion->prepare(
    "SELECT b.id, b.nombre_beca, b.monto,
            COALESCE(SUM(g.monto), 0) as gastado
     FROM becas b
     LEFT JOIN gastos g ON g.beca_id = b.id
     WHERE b.usuario_id = ?
     GROUP BY b.id"
);
$consulta_becas->bind_param("i", $_SESSION["usuario_id"]);
$consulta_becas->execute();
$becas = $consulta_becas->get_result()->fetch_all(MYSQLI_ASSOC);
$consulta_becas->close();
$tiene_becas = count($becas) > 0;

// compatibilidad: calcular monto_beca total y gastado_beca total para el panel legacy
$monto_beca   = 0;
$gastado_beca = 0;
foreach ($becas as $b) {
    $monto_beca   += $b['monto'];
    $gastado_beca += $b['gastado'];
}

// se recorrio el arreglo de gastos y se sumaron los montos totales
$total = 0;
foreach ($gastos as $g) {
    $total += $g["monto"];
}

// se obtienen las notificaciones para la campanita
include(__DIR__ . "/../obtener_notificaciones.php");
?>