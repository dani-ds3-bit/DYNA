<?php
// conexion para acceder a la base de datos
include(__DIR__ . "/../conexion.php");
session_start();

// se verifico que el usuario tenga sesion activa
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../../frontend/Modulo1_Login/index.php");
    exit;
}

// se extrajeron los datos de sesion 
$usuario_id = $_SESSION["usuario_id"];
$usuario_nombre = $_SESSION["usuario_nombre"];
$iniciales = strtoupper(substr($usuario_nombre, 0, 1));

$mensaje_error = "";

// se consultan las categorias para el selector y para la deteccion por voz
$cat_query = $conexion->query("SELECT id, nombre_categoria, nivel_prioridad FROM categorias ORDER BY nivel_prioridad, nombre_categoria");
$categorias = $cat_query ? $cat_query->fetch_all(MYSQLI_ASSOC) : array();

// se consultan las becas del usuario para mostrar el checkbox y selector
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

//parte del formulario voz
// si el formulario de confirmacion de voz fue enviado con POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // se limpiaron los datos que el usuario confirmo en el panel de revision
    $nombre_gasto = trim($_POST["nombre_gasto"]);
    $monto = floatval($_POST["monto"]);
    $categoria_id = intval($_POST["categoria_id"]);

    // se busco la prioridad de la categoria seleccionada para asignarla al gasto
    $prioridad = "Media";
    if ($categoria_id > 0) {
        $consulta_pri = $conexion->prepare("SELECT nivel_prioridad FROM categorias WHERE id = ?");
        $consulta_pri->bind_param("i", $categoria_id);
        $consulta_pri->execute();
        $res_pri = $consulta_pri->get_result()->fetch_assoc();
        $consulta_pri->close();

        // se convirtio el nivel numerico en texto para guardarlo en la base de datos
        if ($res_pri) {
            if ($res_pri['nivel_prioridad'] == 1) {
                $prioridad = "Alta";
            } else if ($res_pri['nivel_prioridad'] == 2) {
                $prioridad = "Media";
            } else if ($res_pri['nivel_prioridad'] == 3) {
                $prioridad = "Baja";
            }
        }
    }

    // se tomo la fecha y hora del sistema para registrar cuando ocurrio el gasto
    $fecha = date("Y-m-d");
    $hora = date("H:i:s");

    // se marco el metodo como Voz en el historial
    $metodo_registro = "Voz";

    // verifica si el checkbox de beca estaba activo y obtiene el beca_id
    $beca_id = 0;
    if (isset($_POST["es_de_beca"]) && $_POST["es_de_beca"] == "1") {
        // si se envio un beca_id especifico (hay mas de 1 beca)
        if (isset($_POST["beca_id"]) && intval($_POST["beca_id"]) > 0) {
            $beca_id = intval($_POST["beca_id"]);
        } else {
            // si solo hay 1 beca, tomar el id de la unica beca del usuario
            if (count($becas_usuario) == 1) {
                $beca_id = $becas_usuario[0]["id"];
            }
        }
    }

    $es_de_beca = ($beca_id > 0) ? 1 : 0;

    // se valida que el nombre, monto y categoria no estuvieran vacios
    if ($nombre_gasto == "" || $monto <= 0 || $categoria_id <= 0) {
        $mensaje_error = "Por favor, revisa que el nombre, monto y categoria esten completos.";
    } else {
        // se verifica que la categoria seleccionada exista en la BD antes de intentar insertar
        // (evita el error de FK si el ID auto-seleccionado ya no existe tras limpiar duplicados)
        $verif_cat = $conexion->prepare("SELECT id FROM categorias WHERE id = ?");
        $verif_cat->bind_param("i", $categoria_id);
        $verif_cat->execute();
        $verif_cat->store_result();
        $cat_existe = ($verif_cat->num_rows > 0);
        $verif_cat->close();

        if (!$cat_existe) {
            $mensaje_error = "La categoria seleccionada no existe. Selecciona una de la lista y vuelve a guardar.";
        }
    }

    if ($mensaje_error == "" && $nombre_gasto != "" && $monto > 0 && $categoria_id > 0) {

        // se preparo e inserto el gasto con todos los campos en la base de datos
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
            $insertar->close();

            // descripcion con el nombre y monto para registrarla en la bitacora
            $desc = "Gasto por voz: " . $nombre_gasto . " por $" . number_format($monto, 2);
            $bit = $conexion->prepare("INSERT INTO bitacora (tipo_evento, descripcion, usuario_id) VALUES ('Gasto', ?, ?)");
            $bit->bind_param("si", $desc, $usuario_id);
            $bit->execute();
            $bit->close();

            // se dirige a gastos una vez que se guardo correctamente
            header("Location: ../../frontend/Modulo2_ControlGastosPr/gastos.php");
            exit;
        } else {
            $mensaje_error = "Error al guardar, intenta nuevamente.";
            $insertar->close();
        }
    }
}
?>