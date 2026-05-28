<?php
session_start();
include(__DIR__ . "/../conexion.php");

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../../frontend/Modulo1_Login/login.php");
    exit;
}

$usuario_id = $_SESSION["usuario_id"];
$id_beca = intval($_GET["id"] ?? 0);

if ($id_beca > 0) {

    // primero quitamos los gastos asociados a esta beca para no perder el historial de gastos
    $desvincular = $conexion->prepare(
        "UPDATE gastos SET es_de_beca = 0, beca_id = NULL WHERE beca_id = ? AND usuario_id = ?"
    );
    $desvincular->bind_param("ii", $id_beca, $usuario_id);
    $desvincular->execute();
    $desvincular->close();

    // luego se elimina la beca la beca
    $eliminar = $conexion->prepare(
        "DELETE FROM becas WHERE id = ? AND usuario_id = ?"
    );
    $eliminar->bind_param("ii", $id_beca, $usuario_id);

    if ($eliminar->execute()) {
        $bit = $conexion->prepare(
            "INSERT INTO bitacora (tipo_evento, descripcion, usuario_id) VALUES ('Beca Eliminada', 'se eliminó una beca de la cuenta', ?)"
        );
        $bit->bind_param("i", $usuario_id);
        $bit->execute();
        $bit->close();
    }
    $eliminar->close();
}

header("Location: ../../frontend/Modulo2_ControlGastosPr/gastos.php");
exit;
?>