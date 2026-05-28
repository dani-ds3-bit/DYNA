<?php
session_start();
include(__DIR__ . "/../conexion.php");

// validar que el usuario inicio sesion
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../../frontend/Modulo1_Login/login.php");
    exit;
}

$usuario_id = $_SESSION["usuario_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_beca = trim($_POST["nombre_beca"] ?? "");
    $monto_beca = floatval($_POST["monto_beca"] ?? 0);

    if ($nombre_beca != "" && $monto_beca > 0) {
        $insertar = $conexion->prepare(
            "INSERT INTO becas (usuario_id, nombre_beca, monto) VALUES (?, ?, ?)"
        );
        $insertar->bind_param("isd", $usuario_id, $nombre_beca, $monto_beca);

        if ($insertar->execute()) {
            // se inserta un log en la bitacora
            $bit = $conexion->prepare(
                "INSERT INTO bitacora (tipo_evento, descripcion, usuario_id) VALUES ('Nueva Beca', CONCAT('Se registro la beca exitosamente', ?), ?)"
            );
            $bit->bind_param("si", $nombre_beca, $usuario_id);
            $bit->execute();
            $bit->close();
        }
        $insertar->close();
    }
}

// se redirige de vuelta a la pagina de gastos
header("Location: ../../frontend/Modulo2_ControlGastosPr/gastos.php");
exit;
?>