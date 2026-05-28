<?php
// se incluyo la conexion compartida para poder ejecutar el DELETE
include(__DIR__ . "/../conexion.php");
session_start();

// se comprobo que el usuario tenga sesion activa para evitar acceso no autorizado
if (!isset($_SESSION["usuario_id"]) || !isset($_GET["id"])) {
    header("Location: ../../frontend/Modulo3_GestionMetas/metas.php");
    exit;
}

// se convirtio el id a entero y se tomo el usuario de la sesion
$id         = intval($_GET["id"]);
$usuario_id = $_SESSION["usuario_id"];

// se consulto la meta para saber si pertenece al usuario
$consulta = $conexion->prepare("SELECT nombre_meta FROM metas WHERE id = ? AND usuario_id = ?");
$consulta->bind_param("ii", $id, $usuario_id);
$consulta->execute();
$meta = $consulta->get_result()->fetch_assoc();
$consulta->close();

// se realizo el DELETE si la meta pertenece al usuario
if ($meta) {
    $eliminar = $conexion->prepare("DELETE FROM metas WHERE id = ? AND usuario_id = ?");
    $eliminar->bind_param("ii", $id, $usuario_id);

    if ($eliminar->execute()) {

    
        // se construyos la descripcion del evento y se inserto en la bitacora
        $desc = "se elimino la meta: " . $meta['nombre_meta'];
        $bit  = $conexion->prepare("INSERT INTO bitacora (tipo_evento, descripcion, usuario_id) VALUES ('Eliminar Meta', ?, ?)");
        $bit->bind_param("si", $desc, $usuario_id);
        $bit->execute();
        $bit->close();
    }
    $eliminar->close();
}

// se dirigio la lista de metas al terminar
header("Location: ../../frontend/Modulo3_GestionMetas/metas.php");
exit;
?>
