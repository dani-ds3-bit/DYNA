<?php

// conexion para poder ejecutar y borrar el gasto
include(__DIR__ . "/../conexion.php");
session_start();

// se confirmo que el usuario tenga sesion
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../../frontend/Modulo1_Login/index.php");
    exit;
}

$usuario_id = $_SESSION["usuario_id"];

// se leyo el id del gasto 
$id_gasto = 0;
if (isset($_GET["id"])) {
    $id_gasto = intval($_GET["id"]);
}

// se ejecuto el DELETE solo si se recibio un id valido mayor a cero
// se incluye el usuario id en la condicion para que no se borre un gasto ajeno
// se verifica que lo que se va a elminar realmente sea del usuario
if ($id_gasto > 0) {
    $eliminar = $conexion->prepare("DELETE FROM gastos WHERE id = ? AND usuario_id = ?");
    $eliminar->bind_param("ii", $id_gasto, $usuario_id);
    $eliminar->execute();
    $eliminar->close();
}

// se redirecciona a la lista de gastos una vez terminada 
header("Location: ../../frontend/Modulo2_ControlGastosPr/gastos.php");
exit;
?>