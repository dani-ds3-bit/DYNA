<?php
include(__DIR__ . "/../conexion.php");
session_start();

// se verifica que hay sesion activa
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../../frontend/Modulo1_Login/index.php?error=sesion_expirada");
    exit;
}

$usuario_id = $_SESSION["usuario_id"];

// se elimina el usuario; el ON DELETE CASCADE de las FK
// borra automaticamente sus gastos, metas, becas y bitacora
$eliminar = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
$eliminar->bind_param("i", $usuario_id);
$eliminar->execute();
$eliminar->close();

// se destruye la sesion despues de eliminar la cuenta
session_unset();
session_destroy();

// se redirige al login con un mensaje de confirmacion 
header("Location: ../../frontend/Modulo1_Login/index.php?cuenta_eliminada=1");
exit;
?>