<?php
// conexion  para consultar la base de datos
include(__DIR__ . "/../conexion.php");
session_start();

// se verifico que el usuario tenga sesion antes de mostrar datos
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../../frontend/Modulo1_Login/index.php");
    exit;
}

// se extrajeron los datos de sesion para el encabezado de la vista
$usuario_id = $_SESSION["usuario_id"];
$usuario_nombre = $_SESSION["usuario_nombre"];
$iniciales = strtoupper(substr($usuario_nombre, 0, 1));

// se traen las metas del usuario
$consulta = $conexion->prepare(
    "SELECT id, nombre_meta, monto_objetivo, monto_acumulado, monto_inicial,
            fecha_inicio, fecha_limite, estado
       FROM metas WHERE usuario_id = ? ORDER BY id DESC"
);
$consulta->bind_param("i", $usuario_id);
$consulta->execute();
$metas = $consulta->get_result()->fetch_all(MYSQLI_ASSOC);
$consulta->close();


?>