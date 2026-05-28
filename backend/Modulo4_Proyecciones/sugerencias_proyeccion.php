<?php
// conexio para consultar la base de datos
include(__DIR__ . "/../conexion.php");
session_start();

// se verifico que el usuario tenga sesion activa antes de mostrar datos
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../../frontend/Modulo1_Login/index.php");
    exit;
}

// se extrajeron los datos de sesion para el encabezado de la vista
$usuario_id = $_SESSION["usuario_id"];
$usuario_nombre = $_SESSION["usuario_nombre"];
$iniciales = strtoupper(substr($usuario_nombre, 0, 1));

// se leyo el id de la meta y se convirtio a entero, con la finalidad de evitar inyeccion sql
$id_meta = 0;
if (isset($_GET["id"])) {
    $id_meta = intval($_GET["id"]);
    // echo "id meta: " . $id_meta;
}

// si no llego un id valido, se dirieige  a proyecciones 
if ($id_meta <= 0) {
    header("Location: ../../frontend/proyecciones.php");
    exit;
}

// se busco la meta en la base de datos que pertenezca al usuario en sesion
$consulta = $conexion->prepare("SELECT * FROM metas WHERE id = ? AND usuario_id = ?");
$consulta->bind_param("ii", $id_meta, $usuario_id);
$consulta->execute();
$meta = $consulta->get_result()->fetch_assoc();
$consulta->close();

// si la meta no existe se redirecciona sin mostrar error al usuario
if (!$meta) {
    header("Location: ../../frontend/proyecciones.php");
    exit;
}







// CALCULOS 

// se convirtieron los montos a float para hacer calculos en decimal 
$monto_objetivo = floatval($meta["monto_objetivo"]);
$monto_acumulado = floatval($meta["monto_acumulado"]);
$monto_inicial = floatval($meta["monto_inicial"]);

// se calculo el porcentaje dividiendo el acumulado entre el objetivo
$porcentaje = 0;
if ($monto_objetivo > 0) {
    $porcentaje = ($monto_acumulado / $monto_objetivo) * 100;
}

// se limito el porcentaje a 100 para que la barra no se salga 
if ($porcentaje > 100) {
    $porcentaje = 100;
}

// se inicializaron las variables de sugerencia con ceros por si no hay fecha limite
$fecha_meta = "Sin fecha";
$tiene_limite = false;
$ahorro_diario_sugerido = 0;
$ahorro_semanal_sugerido = 0;
$ahorro_mensual_sugerido = 0;

// se realizaron todos los calculos de tiempo solo si la meta tiene fecha limite
if (isset($meta["fecha_limite"]) && $meta["fecha_limite"] != "") {
    $fecha_meta = date("d/m/Y", strtotime($meta["fecha_limite"]));
    $tiene_limite = true;

    // aqui se convirtieron las fechas a timestamps para poder restarlas en segundos
    $fecha_ini = strtotime($meta["fecha_inicio"]);
    $fecha_fin = strtotime($meta["fecha_limite"]);

    // aqui se calculo la duracion total de la meta en dias usando 86400 seg = 1 dia
    $segundos_totales = $fecha_fin - $fecha_ini;
    $dias_totales = round($segundos_totales / 86400);

    // y aqui se determino cuanto falta por ahorrar restando lo acumulado al objetivo
    $monto_faltante = $monto_objetivo - $monto_acumulado;

    // se puso en cero para el caso en que ya se supero el objetivo
    if ($monto_faltante < 0) {
        $monto_faltante = 0;
    }

    // se dividio el faltante entre los distintos periodos para generar los planes
    if ($dias_totales > 0 && $monto_faltante > 0) {
        // plan diario: se dividio el faltante entre el total de dias
        $ahorro_diario_sugerido = $monto_faltante / $dias_totales;

        // plan semanal: se usaron semanas de 7 dias con minimo de 1
        $semanas_totales = round($dias_totales / 7);
        if ($semanas_totales < 1) {
            $semanas_totales = 1;
        }
        $ahorro_semanal_sugerido = $monto_faltante / $semanas_totales;

        // plan mensual: se usaron meses de 30 dias con minimo de 1
        $meses_totales = round($dias_totales / 30);
        if ($meses_totales < 1) {
            $meses_totales = 1;
        }
        $ahorro_mensual_sugerido = $monto_faltante / $meses_totales;
    }
}

// datos para la grafica de progreso
$chart_labels = [];
$chart_ideal = [];
$chart_real = [];

if ($tiene_limite && $dias_totales > 0) {
    $fecha_ini_ts = strtotime($meta["fecha_inicio"]);
    $hoy_ts = strtotime(date("Y-m-d"));
    $dias_transcurridos = max(0, round(($hoy_ts - $fecha_ini_ts) / 86400));
    $puntos = min(8, $dias_totales);

    $inserted_hoy = false;
    for ($i = 0; $i <= $puntos; $i++) {
        $fraccion = $i / $puntos;
        $dias_offset = round($fraccion * $dias_totales);

        // Si este intervalo ya superó 'hoy', e 'hoy' no ha sido insertado
        if (!$inserted_hoy && $dias_offset > $dias_transcurridos) {
            $chart_labels[] = date("d/m"); // hoy
            $chart_ideal[] = round(($dias_transcurridos / $dias_totales) * $monto_objetivo, 2);
            $chart_real[] = round($monto_acumulado, 2);
            $inserted_hoy = true;
        }

        // Siempre agregamos el label del intervalo
        $chart_labels[] = date("d/m", strtotime($meta["fecha_inicio"] . " + {$dias_offset} days"));
        $chart_ideal[] = round($fraccion * $monto_objetivo, 2);

        if ($dias_offset == 0) {
            // El inicio SIEMPRE es el monto_inicial
            $chart_real[] = round($monto_inicial, 2);
            
            // Si HOY mismo es el día cero, tenemos que inyectar el acumulado como un segundo punto visible
            if ($dias_transcurridos == 0) {
                $chart_labels[] = "Hoy";
                $chart_ideal[] = 0;
                $chart_real[] = round($monto_acumulado, 2);
                $inserted_hoy = true;
            }
        } else if ($dias_offset < $dias_transcurridos) {
            $chart_real[] = round($monto_inicial + ($monto_acumulado - $monto_inicial) * ($dias_offset / max(1, $dias_transcurridos)), 2);
        } else if ($dias_offset == $dias_transcurridos && !$inserted_hoy) {
            // Si el intervalo cae exactamente hoy
            $chart_real[] = round($monto_acumulado, 2);
            $inserted_hoy = true;
        } else {
            $chart_real[] = null;
        }
    }
} else {
    $chart_labels = [date("d/m", strtotime($meta["fecha_inicio"])), date("d/m")];
    $chart_ideal = [0, $monto_objetivo];
    $chart_real = [$monto_inicial, $monto_acumulado];
}

$json_chart_labels = json_encode($chart_labels);
$json_chart_ideal = json_encode($chart_ideal);
$json_chart_real = json_encode($chart_real);
?>