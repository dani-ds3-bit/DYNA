<?php
// conexion a la base de datos 
include(__DIR__ . "/../conexion.php");
session_start();

// se verifico que el usuario tenga sesion antes de mostrar sus metas
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../../frontend/Modulo1_Login/index.php");
    exit;
}

// se extrajeron los datos de sesion para el encabezado de la vista
$usuario_id     = $_SESSION["usuario_id"];
$usuario_nombre = $_SESSION["usuario_nombre"];
$iniciales      = strtoupper(substr($usuario_nombre, 0, 1));

// se preparÃ³ la consulta para traer todos los campos que la vista necesita mostrar
$consulta = $conexion->prepare(
    "SELECT id, nombre_meta, monto_objetivo, monto_inicial, monto_acumulado, fecha_inicio, fecha_limite, estado, notificaciones_activas
       FROM metas WHERE usuario_id = ? ORDER BY id DESC"
);
$consulta->bind_param("i", $usuario_id);
$consulta->execute();
$resultado = $consulta->get_result();

// se guardaron todas las metas en un arreglo para que la vista pueda iterarlo
$metas = $resultado->fetch_all(MYSQLI_ASSOC);
$consulta->close();

// se obtienen las notificaciones para la campanita
include(__DIR__ . "/../obtener_notificaciones.php");
?>
