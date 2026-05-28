<?php
// se obtienen las metas del usuario que tienen notificaciones activadas
$consulta_notif = $conexion->prepare("SELECT id, nombre_meta, monto_objetivo, monto_acumulado, fecha_limite FROM metas WHERE usuario_id = ? AND notificaciones_activas = 1 AND estado != 'Completada'");
$consulta_notif->bind_param("i", $usuario_id);
$consulta_notif->execute();
$notificaciones_metas = $consulta_notif->get_result()->fetch_all(MYSQLI_ASSOC);
$consulta_notif->close();
?>
