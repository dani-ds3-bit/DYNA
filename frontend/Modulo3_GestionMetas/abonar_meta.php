<?php
// se incluyo el modulo que verifica la meta y procesa el POST
include(__DIR__ . "/../../backend/Modulo3_GestionMetas/abonar_meta.php");
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dyna - Abonar a Meta</title>
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

      <!-- se marco metas como activo porque abonar pertenece al modulo 3 -->
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
        <h2>Dyna</h2>
        <div class="barra-superior-derecha">
          <span class="foto-usuario"><?php echo $iniciales; ?></span>
        </div>
      </header>

      <main class="pagina-contenido">
        <section class="pagina-encabezado">
          <div>
            <!-- se mostro el nombre de la meta en el titulo para que el usuario confirme a cual abona -->
            <h1>Abonar a "<?php echo htmlspecialchars($meta['nombre_meta']); ?>"</h1>
            <p>Registra un nuevo deposito para acercarte a tu meta.</p>
          </div>
          <div class="acciones">
            <a class="btn btn-plano" href="metas.php">Volver</a>
          </div>
        </section>

        <section class="panel">
          <?php if ($mensaje_error != ""): ?>
            <p style="color:#d9534f; margin-bottom:12px;"><?php echo $mensaje_error; ?></p>
          <?php endif; ?>

          <!-- se mostro el progreso actual para que el usuario sepa cuanto falta -->
          <div class="caja-info-progreso">
            <p style="margin: 0;">Progreso actual:
              <strong>$<?php echo number_format($meta['monto_acumulado'], 2); ?></strong>
              de $<?php echo number_format($meta['monto_objetivo'], 2); ?>
            </p>
          </div>

          <!-- el id de la meta viaja en la URL para que el backend sepa a cual actualizar -->
          <form action="abonar_meta.php?id=<?php echo $id_meta; ?>" method="POST" style="margin-top: 10px;">
            <div class="grupo">
              <label for="monto_abono">Monto a abonar ($)</label>
              <input id="monto_abono" name="monto_abono" type="number" step="0.01" min="0.01" placeholder="Ej. 150.00"
                required>
            </div>
            <button class="btn btn-principal" type="submit">Guardar abono</button>
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