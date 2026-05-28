<?php

//  conexion para hacer las consultas
include(__DIR__ . "/../conexion.php");

$mensaje_error = "";
$mensaje_ok = "";

// se verifica que el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // se extrajeron y limpiaron los valores
    $nombre_completo = "";
    if (isset($_POST["nombre"])) {
        $nombre_completo = trim($_POST["nombre"]);
    }

    $correo = "";
    if (isset($_POST["correo"])) {
        $correo = trim($_POST["correo"]);
    }

    $contraseña = "";
    if (isset($_POST["contraseña"])) {
        $contraseña = trim($_POST["contraseña"]);
    }

    // se valida que todos los campos llegaran con contenido
    if ($nombre_completo == "" || $correo == "" || $contraseña == "") {
        $mensaje_error = "Por favor de rellenar todos los campos.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        // se comprueba que el correo tenga formato valido antes de guardarlo
        $mensaje_error = "El correo no tiene un formato valido.";
    } elseif (strlen($contraseña) < 6) {
        // se verifica que la contraseña tuviera al menos 6 caracteres
        $mensaje_error = "La contraseña debe tener al menos 6 caracteres.";
    } else {

        // se consulto si ya existe una cuenta registrada con ese mismo correo
        $verificar = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ?");
        $verificar->bind_param("s", $correo);
        $verificar->execute();
        $verificar->store_result();

        if ($verificar->num_rows > 0) {
            // se notifico que el correo ya estaba en uso
            $mensaje_error = "Ya existe una cuenta con ese correo electronico.";
        } else {







            // cifrado de contraseña
            $contraseña_cifrada = password_hash($contraseña, PASSWORD_DEFAULT);

            // se inserta el nuevo usuario usando la contraseña cifrada
            $insertar = $conexion->prepare(
                "INSERT INTO usuarios (nombre_completo, correo, contraseña) VALUES (?, ?, ?)"
            );
            $insertar->bind_param("sss", $nombre_completo, $correo, $contraseña_cifrada);

            if ($insertar->execute()) {

                // se obtiene el id del nuevo usuario para vincular las becas
                $nuevo_usuario_id = $conexion->insert_id;

                // se procesan las becas si el usuario indicó que tiene
                $tiene_beca = isset($_POST["tiene_beca"]) && $_POST["tiene_beca"] == "1";
                if ($tiene_beca) {
                    $nombres_becas = isset($_POST["nombre_beca"]) ? $_POST["nombre_beca"] : [];
                    $montos_becas = isset($_POST["monto_beca"]) ? $_POST["monto_beca"] : [];

                    $insertar_beca = $conexion->prepare(
                        "INSERT INTO becas (usuario_id, nombre_beca, monto) VALUES (?, ?, ?)"
                    );
                    for ($i = 0; $i < count($nombres_becas); $i++) {
                        $nombre_b = trim($nombres_becas[$i] ?? "");
                        $monto_b = floatval($montos_becas[$i] ?? 0);
                        if ($nombre_b != "" && $monto_b > 0) {
                            $insertar_beca->bind_param("isd", $nuevo_usuario_id, $nombre_b, $monto_b);
                            $insertar_beca->execute();
                        }
                    }
                    $insertar_beca->close();
                }

                $mensaje_ok = "Cuenta creada correctamente. Ya puedes iniciar sesion.";
            } else {
                $mensaje_error = "Error al guardar. Intenta nuevamente.";
            }

            $insertar->close();
        }

        $verificar->close();
    }
}
?>