<?php

// conexion a la base de datos
include(__DIR__ . "/../conexion.php");
session_start();

// se verifico que haya sesion activa antes de procesar cualquier abono
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../../frontend/Modulo1_Login/index.php");
    exit;
}

// se extrajeron los datos de sesion para la vista y para las consultas
$usuario_id = $_SESSION["usuario_id"];
$usuario_nombre = $_SESSION["usuario_nombre"];
$iniciales = strtoupper(substr($usuario_nombre, 0, 1));

$mensaje_error = "";
$mensaje_exito = "";

// se leyo el id de la meta para saber a cual se le abona
$id_meta = 0;
if (isset($_GET["id"])) {
    $id_meta = intval($_GET["id"]);
}

// se consulto la meta para estar seguro del nombre y verificar que pertenece al usuario
$consulta = $conexion->prepare("SELECT nombre_meta, monto_acumulado, monto_objetivo FROM metas WHERE id = ? AND usuario_id = ?");
$consulta->bind_param("ii", $id_meta, $usuario_id);
$consulta->execute();
$meta = $consulta->get_result()->fetch_assoc();
$consulta->close();

// si la meta no existe o no es del usuario, se redirecciona
if (!$meta) {
    header("Location: ../../frontend/metas.php");
    exit;
}

// se proceso el formulario de abono cuando se envia
if ($_SERVER["REQUEST_METHOD"] == "POST") { //aqui se envia
    $monto_abono = 0;
    if (isset($_POST["monto_abono"])) {
        $monto_abono = floatval($_POST["monto_abono"]);
    }

    // se valida que el monto sea mayor a cero para evitar abonos incorrectos
    if ($monto_abono <= 0) {
        $mensaje_error = "Por favor, la cantidad no tiene que quedar en cero.";
    } else {
        // se suma el abono al monto ya acumulado para obtener el nuevo total
        $nuevo_acumulado = $meta['monto_acumulado'] + $monto_abono;


        // se actualizo la columna monto acumulado en la base de datos con el nuevo valor
        $actualizar = $conexion->prepare(
            "UPDATE metas SET monto_acumulado = ? WHERE id = ? AND usuario_id = ?"
        );
        $actualizar->bind_param("dii", $nuevo_acumulado, $id_meta, $usuario_id);

        if ($actualizar->execute()) {



            // se guardo el evento de abono en la bitacora con el nombre de la meta
            $bit = $conexion->prepare(
                "INSERT INTO bitacora (tipo_evento, descripcion, usuario_id) VALUES ('Abono a Meta', CONCAT('se abono $', ?, ' a la meta ', ?), ?)"
            );
            $bit->bind_param("dsi", $monto_abono, $meta['nombre_meta'], $usuario_id);
            $bit->execute();
            $bit->close();

            // se dirige a la lista de metas despues del abono exitoso
            header("Location: ../../frontend/Modulo3_GestionMetas/metas.php");
            exit;
        } else {
            $mensaje_error = "Error al guardar tu abono.";
        }
        $actualizar->close();
    }
}
?>