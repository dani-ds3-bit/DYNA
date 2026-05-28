<?php
// conexion a la base de datos
include(__DIR__ . "/../conexion.php");
session_start();

// se verifico que el usuario tenga sesion antes de mostrar detalles
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../../frontend/Modulo1_Login/index.php");
    exit;
}

// se extrajeron los datos de sesion para el encabezado de la vista
$usuario_id = $_SESSION["usuario_id"];
$usuario_nombre = $_SESSION["usuario_nombre"];
$iniciales = strtoupper(substr($usuario_nombre, 0, 1));

// se leyo el id de la meta y se convirtio a entero
$id_meta = 0;
if (isset($_GET["id"])) {
    $id_meta = intval($_GET["id"]);
}

// si no se recibio un id valido, se dirige a la lista de proyecciones
if ($id_meta <= 0) {
    header("Location: ../../frontend/Modulo4_Proyecciones/proyecciones.php");
    exit;
}

// se busco la meta en la base de datos para saber si pertenece al usuario
$consulta = $conexion->prepare("SELECT * FROM metas WHERE id = ? AND usuario_id = ?");
$consulta->bind_param("ii", $id_meta, $usuario_id);
$consulta->execute();
$meta = $consulta->get_result()->fetch_assoc();
$consulta->close();

// si la meta no existe o no es del usuario, se redirige para evitar errores
if (!$meta) {
    header("Location: ../../frontend/Modulo4_Proyecciones/proyecciones.php");
    exit;
}

// se extrajeron los montos y se calculo cuanto dinero falta para alcanzar el objetivo
$monto_objetivo = floatval($meta["monto_objetivo"]);
$monto_acumulado = floatval($meta["monto_acumulado"]);
$monto_faltante = $monto_objetivo - $monto_acumulado;
if ($monto_faltante < 0) {
    $monto_faltante = 0;
}

// se inicializaron las variables de sugerencia
$fecha_meta = "Sin fecha";
$tiene_limite = false;
$ahorro_diario_sugerido = 0;
$ahorro_semanal_sugerido = 0;
$ahorro_mensual_sugerido = 0;
$dias_totales = 0;
$dias_restantes = 0;

// se calcularon las sugerencias solo si la meta tiene fecha limite definida
if (isset($meta["fecha_limite"]) && $meta["fecha_limite"] != "" && $meta["fecha_limite"] != null) {
    $fecha_meta = date("d/m/Y", strtotime($meta["fecha_limite"]));
    $tiene_limite = true;

    $fecha_ini = strtotime($meta["fecha_inicio"]);
    $fecha_fin = strtotime($meta["fecha_limite"]);
    $hoy = strtotime(date("Y-m-d"));
    $segundos_totales = $fecha_fin - $fecha_ini;
    $dias_totales = max(1, round($segundos_totales / 86400));
    $dias_restantes = max(0, round(($fecha_fin - $hoy) / 86400));

    if ($dias_totales > 0 && $monto_faltante > 0) {
        $ahorro_diario_sugerido = $monto_faltante / $dias_totales;
        $semanas_totales = max(1, round($dias_totales / 7));
        $ahorro_semanal_sugerido = $monto_faltante / $semanas_totales;
        $meses_totales = max(1, round($dias_totales / 30));
        $ahorro_mensual_sugerido = $monto_faltante / $meses_totales;
    }
}

// ============================================================
// DATOS PARA LAS GRÁFICAS
// ============================================================

// GRÁFICA 1: Progreso acumulado de la meta (área suavizada)
// Se construyen los puntos: monto_inicial en fecha_inicio y monto_acumulado hoy
// y se proyecta la línea ideal hasta fecha_limite

$puntos_reales = []; // [[label, valor], ...]
$puntos_proyeccion = [];
$puntos_ideales = [];

// punto de inicio
$puntos_reales[] = [date("d/m", strtotime($meta["fecha_inicio"])), floatval($meta["monto_inicial"])];

// se buscan los abonos registrados en la bitacora para construir el historial
// como la bitacora guarda texto, usamos el monto_acumulado actual como unico punto real disponible
// y construimos una linea simple inicio → hoy (real) + hoy → fecha_limite (proyeccion)

$hoy_label = date("d/m");
$puntos_reales[] = [$hoy_label, $monto_acumulado];

// GRÁFICA SEMANAL: 7 días de la semana actual (Lunes a Domingo)
// esto da contexto de cuánto abona cada día de la semana
$semanas_labels = [];
$semanas_montos = [];

$inicio_semana = strtotime("monday this week");

for ($d = 0; $d < 7; $d++) {
    $fecha_dia = date("Y-m-d", strtotime("+$d days", $inicio_semana));
    $label_dia = date("d/m", strtotime("+$d days", $inicio_semana));
    
    $q = $conexion->prepare(
        "SELECT COALESCE(SUM(CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(LOWER(descripcion), ' a la meta ', 1), 'se abono $', -1) AS DECIMAL(10,2))), 0) as total 
         FROM bitacora
         WHERE usuario_id = ? 
           AND tipo_evento = 'Abono a Meta' 
           AND LOWER(descripcion) LIKE ? 
           AND DATE(fecha_hora) = ?"
    );
    $like_nombre = "% a la meta " . $meta['nombre_meta'];
    $q->bind_param("iss", $usuario_id, $like_nombre, $fecha_dia);
    $q->execute();
    $row = $q->get_result()->fetch_assoc();
    $q->close();

    $semanas_labels[] = $label_dia;
    $semanas_montos[] = round(floatval($row["total"]), 2);
}

// GRÁFICA MENSUAL: gastos de los últimos 6 meses
$meses_labels = [];
$meses_montos = [];

$nombres_meses = ["01" => "Ene", "02" => "Feb", "03" => "Mar", "04" => "Abr", "05" => "May", "06" => "Jun", 
                  "07" => "Jul", "08" => "Ago", "09" => "Sep", "10" => "Oct", "11" => "Nov", "12" => "Dic"];

for ($i = 5; $i >= 0; $i--) {
    $anio = date("Y", strtotime("-{$i} months"));
    $mes = date("m", strtotime("-{$i} months"));
    $label_mes = $nombres_meses[$mes] . " " . $anio;

    $q = $conexion->prepare(
        "SELECT COALESCE(SUM(CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(LOWER(descripcion), ' a la meta ', 1), 'se abono $', -1) AS DECIMAL(10,2))), 0) as total 
         FROM bitacora
         WHERE usuario_id = ? 
           AND tipo_evento = 'Abono a Meta' 
           AND LOWER(descripcion) LIKE ? 
           AND YEAR(fecha_hora) = ? 
           AND MONTH(fecha_hora) = ?"
    );
    $like_nombre = "% a la meta " . $meta['nombre_meta'];
    $q->bind_param("isii", $usuario_id, $like_nombre, $anio, $mes);
    $q->execute();
    $row = $q->get_result()->fetch_assoc();
    $q->close();

    $meses_labels[] = $label_mes;
    $meses_montos[] = round(floatval($row["total"]), 2);
}

// GRÁFICA DE PROGRESO DE META: línea ideal vs real
// Línea ideal: divide el objetivo en puntos equidistantes desde inicio hasta fin
$labels_meta = [];
$ideal_meta = [];
$real_meta = [];

if ($tiene_limite && $dias_totales > 0) {
    $puntos = min(8, $dias_totales); // máximo 8 puntos en el eje X
    for ($i = 0; $i <= $puntos; $i++) {
        $fraccion = $i / $puntos;
        $dia_offset = round($fraccion * $dias_totales);
        $fecha_punto = date("d/m", strtotime($meta["fecha_inicio"] . " + {$dia_offset} days"));
        $labels_meta[] = $fecha_punto;
        $ideal_meta[] = round($fraccion * $monto_objetivo, 2);
        // la linea real solo llega hasta hoy; el resto se deja null para que la grafica lo corte
        $dias_al_punto = round($fraccion * $dias_totales);
        $dias_transcurridos = max(0, round((strtotime("now") - strtotime($meta["fecha_inicio"])) / 86400));
        $real_meta[] = ($dias_al_punto <= $dias_transcurridos) ? round(min($monto_objetivo, $monto_acumulado * ($dias_al_punto / max(1, $dias_transcurridos))), 2) : null;
    }
} else {
    // sin fecha limite: mostrar solo inicio y ahora
    $labels_meta = [date("d/m", strtotime($meta["fecha_inicio"])), date("d/m")];
    $ideal_meta = [0, $monto_objetivo];
    $real_meta = [floatval($meta["monto_inicial"]), $monto_acumulado];
}

// se codifican todos los arreglos como JSON para pasarlos al JS
$json_semanas_labels = json_encode($semanas_labels);
$json_semanas_montos = json_encode($semanas_montos);
$json_meses_labels = json_encode($meses_labels);
$json_meses_montos = json_encode($meses_montos);
$json_labels_meta = json_encode($labels_meta);
$json_ideal_meta = json_encode($ideal_meta);
$json_real_meta = json_encode($real_meta);
?>