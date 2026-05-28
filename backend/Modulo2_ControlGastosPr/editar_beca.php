<?php
session_start();
include(__DIR__ . "/../conexion.php");

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../../frontend/Modulo1_Login/login.php");
    exit;
}

$usuario_id = $_SESSION["usuario_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_beca = intval($_POST["id_beca"] ?? 0);
    $nombre_beca = trim($_POST["nombre_beca"] ?? "");
    $monto_beca = floatval($_POST["monto_beca"] ?? 0);

    if ($id_beca > 0 && $nombre_beca != "" && $monto_beca > 0) {

        // verificar que la beca pertenezca al usuario antes de actualizar
        $actualizar = $conexion->prepare(
            "UPDATE becas SET nombre_beca = ?, monto = ? WHERE id = ? AND usuario_id = ?"
        );
        $actualizar->bind_param("sdii", $nombre_beca, $monto_beca, $id_beca, $usuario_id);

        if ($actualizar->execute()) {
            $bit = $conexion->prepare(
                "INSERT INTO bitacora (tipo_evento, descripcion, usuario_id) VALUES ('Edición de Beca', CONCAT('se actualizó la beca a ', ?), ?)"
            );
            $bit->bind_param("si", $nombre_beca, $usuario_id);
            $bit->execute();
            $bit->close();
        }
        $actualizar->close();
    }
}

header("Location: ../../frontend/Modulo2_ControlGastosPr/gastos.php");
exit;
?>