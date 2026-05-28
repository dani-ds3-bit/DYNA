<?php
// se incluyo la conexion para poder consultar la tabla usuarios
include(__DIR__ . "/../conexion.php");

$mensaje_error = "";
$mensaje_ok = "";

// se proceso el formulario solo cuando se envio con POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // se leen los campos recibidos del formulario
    $correo = isset($_POST["correo"]) ? trim($_POST["correo"]) : "";
    $nueva = isset($_POST["nueva_contraseña"]) ? trim($_POST["nueva_contraseña"]) : "";
    $confirmar = isset($_POST["confirmar_contraseña"]) ? trim($_POST["confirmar_contraseña"]) : "";

    // validaciones 
    if ($correo == "" || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje_error = "Escribe un correo electronico valido.";
    } else if (strlen($nueva) < 6) {
        $mensaje_error = "La nueva contraseña debe tener al menos 6 caracteres.";
    } else if ($nueva !== $confirmar) {
        $mensaje_error = "Las contraseñas no coinciden. Intentalo de nuevo.";
    } else {
        // validar si el correo existe en la base de datos
        $buscar = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ?");
        $buscar->bind_param("s", $correo);
        $buscar->execute();
        $resultado = $buscar->get_result();

        if ($resultado->num_rows > 0) {
            $usuario = $resultado->fetch_assoc();
            $usuario_id = $usuario['id'];

            // encriptar la nueva contraseña
            $hash = password_hash($nueva, PASSWORD_DEFAULT);

            // actualizar la contraseña en la base de datos
            $actualizar = $conexion->prepare("UPDATE usuarios SET contraseña = ? WHERE id = ?");
            $actualizar->bind_param("si", $hash, $usuario_id);

            if ($actualizar->execute()) {
                $mensaje_ok = "Tu contraseña ha sido actualizada correctamente. Ya puedes iniciar sesion.";
            } else {
                $mensaje_error = "Ocurrio un error al actualizar la contraseña, por favor intenta mas tarde.";
            }
            $actualizar->close();
        } else {
            // si el correo no existe
            $mensaje_error = "No existe ninguna cuenta creada con este correo.";
        }
        $buscar->close();
    }
}
?>