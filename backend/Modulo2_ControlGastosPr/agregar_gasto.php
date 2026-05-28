<?php

// conexion en la base de datos
include(__DIR__ . "/../conexion.php");
session_start();

// se comprueba que el usuario tenga sesion para evitar accesos no autorizados
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../../frontend/Modulo1_Login/index.php");
    exit;
}

// se sacaron los datos de sesion
$usuario_id = $_SESSION["usuario_id"];
$usuario_nombre = $_SESSION["usuario_nombre"];
$iniciales = strtoupper(substr($usuario_nombre, 0, 1));

$mensaje_error = "";

// se inicializan las variables del formulario con valores vacios
$id_gasto = 0;
$nombre_gasto_val = "";
$monto_val = "";
$categoria_id_val = 0;
$beca_id_val = 0;
$es_edicion = false;

// se consultaron las categorias para el selector
$cat_query = $conexion->query("SELECT id, nombre_categoria, nivel_prioridad FROM categorias ORDER BY nivel_prioridad, nombre_categoria");
$categorias = $cat_query ? $cat_query->fetch_all(MYSQLI_ASSOC) : array();

// se consultan las becas del usuario para mostrar el selector
$consulta_becas_form = $conexion->prepare(
    "SELECT b.id, b.nombre_beca, b.monto,
            COALESCE(SUM(g.monto), 0) as gastado
     FROM becas b
     LEFT JOIN gastos g ON g.beca_id = b.id
     WHERE b.usuario_id = ?
     GROUP BY b.id"
);
$consulta_becas_form->bind_param("i", $usuario_id);
$consulta_becas_form->execute();
$becas_usuario = $consulta_becas_form->get_result()->fetch_all(MYSQLI_ASSOC);
$consulta_becas_form->close();
$tiene_beca = count($becas_usuario) > 0;

// se verifica si se recibio un id por GET para modo edicion
if (isset($_GET["id"])) {
    $id_gasto = intval($_GET["id"]);

    // se carga el gasto existente del usuario
    $consulta_edit = $conexion->prepare(
        "SELECT id, nombre_gasto, monto, categoria_id, beca_id FROM gastos WHERE id = ? AND usuario_id = ?"
    );
    $consulta_edit->bind_param("ii", $id_gasto, $usuario_id);
    $consulta_edit->execute();
    $gasto_edit = $consulta_edit->get_result()->fetch_assoc();
    $consulta_edit->close();

    // se llenan las variables del formulario si el gasto existe y pertenece al usuario
    if ($gasto_edit) {
        $nombre_gasto_val = $gasto_edit["nombre_gasto"];
        $monto_val = $gasto_edit["monto"];
        $categoria_id_val = $gasto_edit["categoria_id"];
        $beca_id_val = $gasto_edit["beca_id"] ?? 0;
        $es_edicion = true;
    }
}

// se verifico que el formulario fue enviado con POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // se lee el id oculto del formulario para saber si es edicion o alta nueva
    $id_gasto = 0;
    if (isset($_POST["id_gasto"])) {
        $id_gasto = intval($_POST["id_gasto"]);
    }

    // se extrajeron los campos del formulario
    $nombre_gasto = "";
    if (isset($_POST["nombre_gasto"])) {
        $nombre_gasto = trim($_POST["nombre_gasto"]);
    }

    $monto = 0;
    if (isset($_POST["monto"])) {
        $monto = floatval($_POST["monto"]);
    }

    $categoria_id = 0;
    if (isset($_POST["categoria_id"])) {
        $categoria_id = intval($_POST["categoria_id"]);
    }

    // verifica si el checkbox de beca estaba activo y obtiene el beca_id
    $beca_id = 0;
    if (isset($_POST["es_de_beca"]) && $_POST["es_de_beca"] == "1") {
        if (isset($_POST["beca_id"]) && intval($_POST["beca_id"]) > 0) {
            $beca_id = intval($_POST["beca_id"]);
        } else {
            // si solo hay 1 beca, tomar el id de la unica beca del usuario
            if (count($becas_usuario) == 1) {
                $beca_id = $becas_usuario[0]["id"];
            }
        }
    }

    // se consulta la prioridad de la categoria elegida para asignarla al gasto
    $prioridad = "Media";
    if ($categoria_id > 0) {
        $consulta_pri = $conexion->prepare("SELECT nivel_prioridad FROM categorias WHERE id = ?");
        $consulta_pri->bind_param("i", $categoria_id);
        $consulta_pri->execute();
        $res_pri = $consulta_pri->get_result()->fetch_assoc();
        $consulta_pri->close();

        if ($res_pri) {
            if ($res_pri['nivel_prioridad'] == 1) {
                $prioridad = "Alta";
            } elseif ($res_pri['nivel_prioridad'] == 2) {
                $prioridad = "Media";
            } elseif ($res_pri['nivel_prioridad'] == 3) {
                $prioridad = "Baja";
            }
        }
    }

    // se establece es_de_beca segun si se selecciono beca
    $es_de_beca = ($beca_id > 0) ? 1 : 0;

    // se tomo la fecha y hora actuales para el registro
    $fecha = date("Y-m-d");
    $hora = date("H:i:s");

    // se valido que los campos obligatorios tuvieran valores
    if ($nombre_gasto == "" || $monto <= 0 || $categoria_id <= 0) {
        $mensaje_error = "El nombre, monto y categoria no pueden quedar en blanco.";
        $nombre_gasto_val = $nombre_gasto;
        $monto_val = $monto;
        $categoria_id_val = $categoria_id;
        $beca_id_val = $beca_id;
        if ($id_gasto > 0) {
            $es_edicion = true;
        }
    } else {

        if ($id_gasto > 0) {
            // se ejecuta UPDATE porque el id indica que el gasto ya existe
            $actualizar = $conexion->prepare(
                "UPDATE gastos SET nombre_gasto=?, monto=?, categoria_id=?, prioridad=?, es_de_beca=?, beca_id=?
                 WHERE id=? AND usuario_id=?"
            );
            $beca_id_null = $beca_id > 0 ? $beca_id : null;
            $actualizar->bind_param(
                "sdisiiii",
                $nombre_gasto,
                $monto,
                $categoria_id,
                $prioridad,
                $es_de_beca,
                $beca_id_null,
                $id_gasto,
                $usuario_id
            );
            if ($actualizar->execute()) {
                header("Location: ../../frontend/Modulo2_ControlGastosPr/gastos.php");
                exit;
            } else {
                $mensaje_error = "Error al actualizar el gasto.";
            }
            $actualizar->close();
        } else {
            // se establece el metodo de registro como manual
            $metodo_registro = "Manual";

            // se prepara la sentencia para insertar el gasto nuevo
            $insertar = $conexion->prepare(
                "INSERT INTO gastos (monto, nombre_gasto, categoria_id, prioridad, fecha_creacion, hora_creacion, usuario_id, metodo_registro, es_de_beca, beca_id)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $beca_id_null = $beca_id > 0 ? $beca_id : null;
            $insertar->bind_param(
                "dsisssisii",
                $monto,
                $nombre_gasto,
                $categoria_id,
                $prioridad,
                $fecha,
                $hora,
                $usuario_id,
                $metodo_registro,
                $es_de_beca,
                $beca_id_null
            );

            if ($insertar->execute()) {
                // se registro el evento en la bitacora
                $bit = $conexion->prepare(
                    "INSERT INTO bitacora (tipo_evento, descripcion, usuario_id) VALUES ('Nuevo Gasto', CONCAT('se registro gasto de $', ?), ?)"
                );
                $bit->bind_param("di", $monto, $usuario_id);
                $bit->execute();
                $bit->close();

                header("Location: ../../frontend/Modulo2_ControlGastosPr/gastos.php");
                exit;
            } else {
                $mensaje_error = "Error al guardar el gasto.";
            }
            $insertar->close();
        }
    }
}
?>