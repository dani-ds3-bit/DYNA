<?php
// ============================================================
// modulo 3 — agregar / editar meta (vista)
// se incluye el backend que decide si crear o actualizar la meta.
// el titulo y los botones cambian segun si es edicion o alta nueva.
// ============================================================

// se incluyo el modulo que carga los datos existentes si es edicion
include(__DIR__ . "/../../backend/Modulo3_GestionMetas/agregar_meta.php");
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- se cambió el titulo dinamicamente segun si es alta o edicion -->
  <title>Dyna - <?php if ($es_edicion) {
    echo "Editar";
  } else {
    echo "Crear";
  } ?> Meta</title>
  <link rel="stylesheet" href="../../estilos.css">
  <?php include(__DIR__ . "/../favicon.php"); ?>
</head>

<body>
  <div class="diseño-app">
    <aside class="barra-lateral">
      <div class="barra-lateral-marca">
        <div class="logo-menu-wrap">
          <img src="../../img/LOGO.png" alt="Logo Dyna">
        </div>
        <h2>Dyna</h2>
        <small>Finanzas Personales</small>
      </div>

      <!-- se marco metas como activo porque agregar y editar pertenecen al mismo modulo -->
      <nav class="menu-links">
        <a class="menu-link" href="../Modulo2_ControlGastosPr/gastos.php"><span class="icono-menu">G</span>Gastos</a>
        <a class="menu-link activo" href="metas.php"><span class="icono-menu">M</span>Metas</a>
        <a class="menu-link" href="../Modulo4_Proyecciones/proyecciones.php"><span
            class="icono-menu">P</span>Proyecciones</a>
        <a class="menu-link" href="../Modulo5_BitacorayNotificaciones/bitacora.php"><span
            class="icono-menu">B</span>Bitacora</a>
        <a class="menu-link" href="../Modulo6_Ajustes/ajustes.php"><span class="icono-menu">A</span>Ajustes</a>
      </nav>

      <div class="barra-lateral-pie">
        <span class="foto-usuario"><?php echo $iniciales; ?></span>
        <div>
          <strong><?php echo htmlspecialchars($usuario_nombre); ?></strong>
        </div>
      </div>
    </aside>

    <div class="cuerpo-app">
      <header class="barra-superior">
        <h2><?php if ($es_edicion) {
          echo "Editar Meta";
        } else {
          echo "Crear Meta";
        } ?></h2>
        <div class="barra-superior-derecha">
          <span class="foto-usuario"><?php echo $iniciales; ?></span>
        </div>
      </header>

      <main class="pagina-contenido">
        <section class="pagina-encabezado">
          <div>
            <h1><?php if ($es_edicion) {
              echo "Editar meta de ahorro";
            } else {
              echo "Nueva meta de ahorro";
            } ?></h1>
            <p>Define tu objetivo y ponle fecha.</p>
          </div>
          <div class="acciones">
            <a class="btn btn-plano" href="metas.php">Volver</a>
          </div>
        </section>

        <section class="panel">
          <!-- se mostro el error o el exito segun lo que regrese el backend -->
          <?php if ($mensaje_error != ""): ?>
            <p style="color:#d9534f; margin-bottom:12px; font-weight:bold;"><?php echo $mensaje_error; ?></p>
          <?php endif; ?>
          <?php if ($mensaje_exito != ""): ?>
            <p style="color:#28a745; margin-bottom:12px; font-weight:bold;"><?php echo $mensaje_exito; ?></p>
          <?php endif; ?>

          <!-- el id_meta oculto le dice al backend si ejecutar INSERT o UPDATE -->
          <form action="agregar_meta.php" method="POST" class="formulario-simple">
            <input type="hidden" name="id_meta" value="<?php echo $id_meta; ?>">

            <div class="grupo">
              <label for="nombre_meta">Que quieres lograr? (Ej. Viaje, Computadora)</label>
              <input type="text" id="nombre_meta" name="nombre_meta"
                value="<?php echo htmlspecialchars($nombre_meta); ?>" required placeholder="Nombre de la meta">
            </div>

            <div class="grupo">
              <label for="monto_objetivo">Monto objetivo ($)</label>
              <input type="number" id="monto_objetivo" name="monto_objetivo" step="0.01" min="1"
                value="<?php echo htmlspecialchars($monto_objetivo); ?>" required placeholder="0.00">
            </div>

            <div class="grupo">
              <label for="monto_inicial">Ahorro inicial ($)</label>
              <!-- se bloqueo el campo en edicion para que el usuario abone desde la lista -->
              <input type="number" id="monto_inicial" name="monto_inicial" step="0.01" min="0"
                value="<?php echo htmlspecialchars($monto_inicial); ?>" placeholder="0.00" <?php if ($es_edicion) {
                     echo "readonly title='Edita el progreso desde la lista de metas'";
                   } ?>>
              <?php if ($es_edicion): ?>
                <small class="texto-suave">El monto inicial solo se define al crear la meta.</small>
              <?php endif; ?>
            </div>

            <div class="grupo" style="display:flex; gap:15px; flex-wrap:wrap;">
              <div style="flex:1; min-width:200px;">
                <label for="fecha_inicio">Fecha de inicio</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo $fecha_inicio; ?>" required>
              </div>
              <div style="flex:1; min-width:200px;">
                <label for="fecha_limite">Fecha limite (Opcional)</label>
                <input type="date" id="fecha_limite" name="fecha_limite" value="<?php echo $fecha_limite; ?>">
              </div>
            </div>

            <div class="grupo" style="display:flex; align-items:center; gap:8px;">
              <input type="checkbox" id="notificaciones" name="notificaciones" value="1" <?php if ($notificaciones == 1)
                echo "checked"; ?> style="width:auto; margin:0;">
              <label for="notificaciones" style="margin:0; font-weight:normal;">Activar notificaciones para esta
                meta</label>
            </div>

            <div class="acciones" style="margin-top:20px;">
              <button type="submit" class="btn btn-principal">
                <?php if ($es_edicion) {
                  echo "Guardar cambios";
                } else {
                  echo "Crear meta";
                } ?>
              </button>
              <a href="metas.php" class="btn btn-plano">Cancelar</a>
            </div>
          </form>
        </section>
      </main>
    </div>
  </div>

  <script>
    /* se verifico si el usuario tenia el tema oscuro guardado para aplicarlo en todas las paginas */
    if (localStorage.getItem('tema') === 'oscuro') {
      document.body.classList.add('tema-oscuro');
    }
  </script>
</body>

</html>