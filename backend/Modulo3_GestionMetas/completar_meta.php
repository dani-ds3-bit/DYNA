<?php
include(__DIR__ . "/../conexion.php");
session_start();

// se verifica la sesion antes de hacer cualquier cambio
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../../frontend/Modulo1_Login/index.php");
    exit;
}

$usuario_id = $_SESSION["usuario_id"];

// se verifica que se recibio un id valido
if (!isset($_GET["id"]) || intval($_GET["id"]) <= 0) {
    header("Location: ../../frontend/Modulo3_GestionMetas/metas.php");
    exit;
}

$id_meta = intval($_GET["id"]);

// se consulta la meta para verificar que pertenece al usuario y obtener su nombre
$consulta = $conexion->prepare("SELECT nombre_meta FROM metas WHERE id = ? AND usuario_id = ?");
$consulta->bind_param("ii", $id_meta, $usuario_id);
$consulta->execute();
$meta = $consulta->get_result()->fetch_assoc();
$consulta->close();

if (!$meta) {
    // la meta no existe o no pertenece al usuario
    header("Location: ../../frontend/Modulo3_GestionMetas/metas.php");
    exit;
}

// se registra en la bitacora que la meta fue completada
$nombre_meta = $meta["nombre_meta"];
$desc = "Meta completada exitosamente: " . $nombre_meta;
$bit = $conexion->prepare("INSERT INTO bitacora (tipo_evento, descripcion, usuario_id) VALUES ('Meta Completada', ?, ?)");
$bit->bind_param("si", $desc, $usuario_id);
$bit->execute();
$bit->close();

// se elimina la meta de la base de datos
$eliminar = $conexion->prepare("DELETE FROM metas WHERE id = ? AND usuario_id = ?");
$eliminar->bind_param("ii", $id_meta, $usuario_id);
$eliminar->execute();
$eliminar->close();

// se regresa a la lista de metas
header("Location: ../../frontend/Modulo3_GestionMetas/metas.php");
exit;
?>