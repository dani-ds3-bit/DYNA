<?php
// conexion para actuaizar el perfil 
include(__DIR__ . "/../conexion.php");
session_start();

// se verifico que el usuario tenga sesion antes de mostrar la pantalla de ajustes
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../../frontend/Modulo1_Login/index.php?error=sesion_expirada");
    exit;
}

// se extrajeron nombre e iniciales para el encabezado de la vista
$nombre = $_SESSION["usuario_nombre"];
$iniciales = strtoupper(substr($nombre, 0, 1));

// se busco el perfil completo del usuario para llenar los campos del formulario
$buscar = $conexion->prepare("SELECT nombre_completo, correo, tema FROM usuarios WHERE id = ?");
$buscar->bind_param("i", $_SESSION["usuario_id"]);
$buscar->execute();
$usuario = $buscar->get_result()->fetch_assoc();
$buscar->close();

$mensaje_ok = "";
$mensaje_error = "";

// se proceso el formulario solo cuando fue enviado con POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // se limpio el nombre enviado para evitar espacios innecesarios
    $nuevo_nombre = "";
    if (isset($_POST["nombre_completo"])) {
        $nuevo_nombre = trim($_POST["nombre_completo"]);
    }

    $nueva_contraseña = "";
    if (isset($_POST["contraseña"])) {
        $nueva_contraseña = trim($_POST["contraseña"]);
    }

    $confirmar_contraseña = "";
    if (isset($_POST["confirmar_contraseña"])) {
        $confirmar_contraseña = trim($_POST["confirmar_contraseña"]);
    }

    // se valido que el campo nombre no llegara vacio antes de actualizar
    if ($nuevo_nombre == "") {
        $mensaje_error = "el nombre no puede estar vacio.";
    } elseif ($nueva_contraseña != "" && strlen($nueva_contraseña) < 6) {
        $mensaje_error = "la nueva contraseña debe tener al menos 6 caracteres.";
    } elseif ($nueva_contraseña != "" && $nueva_contraseña !== $confirmar_contraseña) {
        $mensaje_error = "las contraseñas no coinciden.";
    } else {
        // si hay una nueva contraseña
        if ($nueva_contraseña != "") {
            $nueva_contraseña_hash = password_hash($nueva_contraseña, PASSWORD_DEFAULT);
            $actualizar = $conexion->prepare("UPDATE usuarios SET nombre_completo = ?, contraseña = ? WHERE id = ?");
            $actualizar->bind_param("ssi", $nuevo_nombre, $nueva_contraseña_hash, $_SESSION["usuario_id"]);
        } else {
            // se preparo el UPDATE para guardar solo el nuevo nombre
            $actualizar = $conexion->prepare("UPDATE usuarios SET nombre_completo = ? WHERE id = ?");
            $actualizar->bind_param("si", $nuevo_nombre, $_SESSION["usuario_id"]);
        }

        if ($actualizar->execute()) {
            // se sincronizo la sesion con el nuevo nombre para que toda la app lo refleje
            $_SESSION["usuario_nombre"] = $nuevo_nombre;
            $nombre = $nuevo_nombre;
            $iniciales = strtoupper(substr($nombre, 0, 1));
            $usuario["nombre_completo"] = $nuevo_nombre;
            $mensaje_ok = "cambios guardados correctamente.";
        } else {
            $mensaje_error = "error al actualizar la configuracion.";
        }
        $actualizar->close();
    }
}
?>