<?php
// conexion compartida para interactuar con la base de detos 
include(__DIR__ . "/../conexion.php");
session_start();

// se verifico que el usuario tuviera sesion antes de permitir cambios
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../../frontend/Modulo1_Login/index.php");
    exit;
}

// se extrajeron los datos de sesion necesarios para la vista
$usuario_id = $_SESSION["usuario_id"];
$usuario_nombre = $_SESSION["usuario_nombre"];
$iniciales = strtoupper(substr($usuario_nombre, 0, 1));

$mensaje_error = "";
$mensaje_exito = "";

// se inicializaron las variables con valores vacios para el formulario de nueva meta
$id_meta = 0;
$nombre_meta = "";
$monto_objetivo = "";
$monto_inicial = 0;
$fecha_inicio = date("Y-m-d");
$fecha_limite = "";
$notificaciones = 1;
$es_edicion = false;

// se verifico si se recibio un id por GET para determinar si es edicion o alta nueva
if (isset($_GET["id"])) {
    $id_meta = intval($_GET["id"]);

    // se consulto la meta en la BD para llenar el formulario con sus datos actuales
    $consulta = $conexion->prepare("SELECT * FROM metas WHERE id = ? AND usuario_id = ?");
    $consulta->bind_param("ii", $id_meta, $usuario_id);
    $consulta->execute();
    $meta = $consulta->get_result()->fetch_assoc();
    $consulta->close();

    // se llenaron las variables de formulario si la meta existe y pertenece al usuario
    if ($meta) {
        $nombre_meta = $meta["nombre_meta"];
        $monto_objetivo = $meta["monto_objetivo"];
        $monto_inicial = $meta["monto_inicial"];
        $fecha_inicio = $meta["fecha_inicio"];
        $fecha_limite = $meta["fecha_limite"];
        $notificaciones = $meta["notificaciones_activas"];
        $es_edicion = true;
    }
}

// se proceso el formulario solo cuando se envio con POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // se leyo el id oculto del formulario para saber si es edicion o alta
    $id_meta = 0;
    if (isset($_POST["id_meta"])) {
        $id_meta = intval($_POST["id_meta"]);
    }

    // se limpiaron y convirtieron los campos enviados por el usuario
    $nombre_meta = trim($_POST["nombre_meta"]);
    $monto_objetivo = floatval($_POST["monto_objetivo"]);

    $monto_inicial = 0;
    if (isset($_POST["monto_inicial"])) {
        $monto_inicial = floatval($_POST["monto_inicial"]);
    }

    $fecha_inicio = date("Y-m-d");
    if (isset($_POST["fecha_inicio"])) {
        $fecha_inicio = $_POST["fecha_inicio"];
    }

    // se acepto null como fecha limite si el campo se dejo vacio
    $fecha_limite = null;
    if (isset($_POST["fecha_limite"]) && $_POST["fecha_limite"] != "") {
        $fecha_limite = $_POST["fecha_limite"];
    }

    // se determino si las notificaciones quedaron activadas o no
    $notificaciones = 0;
    if (isset($_POST["notificaciones"])) {
        $notificaciones = 1;
    }

    // se valido que los campos minimos obligatorios estuvieran llenos
    if ($nombre_meta == "" || $monto_objetivo <= 0) {
        $mensaje_error = "Tu nombre y el costo final no pueden quedar en blanco .";
    } else {
        if ($id_meta > 0) {
            // se ejecuto un UPDATE porque el id indica que la meta ya existe
            $actualizar = $conexion->prepare(
                "UPDATE metas SET nombre_meta=?, monto_objetivo=?, monto_inicial=?, fecha_inicio=?, fecha_limite=?, notificaciones_activas=? WHERE id=? AND usuario_id=?"
            );
            $actualizar->bind_param("sddssiii", $nombre_meta, $monto_objetivo, $monto_inicial, $fecha_inicio, $fecha_limite, $notificaciones, $id_meta, $usuario_id);
            if ($actualizar->execute()) {
                $mensaje_exito = "Tu meta fue guardada correctamente.";
                $es_edicion = true;
            } else {
                $mensaje_error = "error al guardar la meta.";
            }
            $actualizar->close();
        } else {
            // se ejecuto un INSERT porque no se recibio un id de meta existente
            // se uso el monto inicial tambien como monto acumulado de arranque
            $insertar = $conexion->prepare(
                "INSERT INTO metas (usuario_id, nombre_meta, monto_objetivo, monto_inicial, monto_acumulado, fecha_inicio, fecha_limite, notificaciones_activas)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $insertar->bind_param("isdddssi", $usuario_id, $nombre_meta, $monto_objetivo, $monto_inicial, $monto_inicial, $fecha_inicio, $fecha_limite, $notificaciones);

            if ($insertar->execute()) {

                // se armo la descripcion y se guardo en la bitacora para tener historial
                $desc = "Tu nueva meta fue creada: " . $nombre_meta . " (objetivo: $" . number_format($monto_objetivo, 2) . ")";
                $bit = $conexion->prepare("INSERT INTO bitacora (tipo_evento, descripcion, usuario_id) VALUES ('Meta', ?, ?)");
                $bit->bind_param("si", $desc, $usuario_id);
                $bit->execute();
                $bit->close();

                // se dirigio a la lista de metas una vez guardada
                header("Location: ../../frontend/Modulo3_GestionMetas/metas.php");
                exit;
            } else {
                $mensaje_error = "Error al guardar. Por favor intenta de nuevo.";
            }
            $insertar->close();
        }
    }
}
?>