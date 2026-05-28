<?php
// se incluyo la conexion compartida para consultar la tabla bitacora
include(__DIR__ . "/../conexion.php");
session_start();

// se verifico que el usuario tenga sesion activa antes de mostrar el historial
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../../frontend/Modulo1_Login/index.php");
    exit;
}

// se extrajeron el nombre e iniciales del usuario para mostrarlos en la vista
$nombre    = $_SESSION["usuario_nombre"];
$iniciales = strtoupper(substr($nombre, 0, 1));

// se preparo la consulta para traer los ultimos 50 eventos del usuario
// se uso LIMIT 50 por ahora para no sobrecargar la pagina con demasiados datos 
$consulta = $conexion->prepare(
    "SELECT tipo_evento, descripcion, fecha_hora FROM bitacora WHERE usuario_id = ? ORDER BY fecha_hora DESC LIMIT 50"
);
$consulta->bind_param("i", $_SESSION["usuario_id"]);
$consulta->execute();
$eventos = $consulta->get_result()->fetch_all(MYSQLI_ASSOC);
$consulta->close();

// aqui el lenguaje me las da en ingles, po ende las transcribi a español 
// y la interfaz los necesita en espanol para el usuario
$meses = array(
    "January"   => "Enero",   "February"  => "Febrero",
    "March"     => "Marzo",   "April"     => "Abril",
    "May"       => "Mayo",    "June"      => "Junio",
    "July"      => "Julio",   "August"    => "Agosto",
    "September" => "Septiembre", "October" => "Octubre",
    "November"  => "Noviembre",  "December" => "Diciembre"
);

// se recorrio el arreglo de eventos y se agruparon por etiqueta de dia en español
// esto permite mostrar los eventos bajo un encabezado de fecha en la bitacora
$por_dia = array();
foreach ($eventos as $ev) {
    $tiempo = strtotime($ev["fecha_hora"]);
    $mes    = $meses[date("F", $tiempo)];
    $dia    = date("d", $tiempo) . " de " . $mes . " de " . date("Y", $tiempo);
    $por_dia[$dia][] = $ev;
}
?>
