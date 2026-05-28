<?php
include(__DIR__ . "/../../backend/Modulo4_Proyecciones/obtener_proyecciones.php");
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dyna - Proyecciones</title>
  <link rel="stylesheet" href="../../estilos.css">
  <?php include(__DIR__ . "/../favicon.php"); ?>
</head>

<body class="modulo-3-proyecciones">
  <div class="diseño-app">
    <aside class="barra-lateral">
      <div class="barra-lateral-marca">
        <div class="logo-menu-wrap">
          <img src="../../img/LOGO.png" alt="Logo Dyna">
        </div>
        <h2>Dyna</h2>
        <small>Finanzas Personales</small>
      </div>

      <nav class="menu-links">
        <a class="menu-link" href="../Modulo2_ControlGastosPr/gastos.php"><span class="icono-menu">G</span>Gastos</a>
        <a class="menu-link" href="../Modulo3_GestionMetas/metas.php"><span class="icono-menu">M</span>Metas</a>
        <a class="menu-link activo" href="proyecciones.php"><span class="icono-menu">P</span>Proyecciones</a>
        <a class="menu-link" href="../Modulo5_BitacorayNotificaciones/bitacora.php"><span
            class="icono-menu">B</span>Bitacora</a>
        <a class="menu-link" href="../Modulo6_Ajustes/ajustes.php"><span class="icono-menu">A</span>Ajustes</a>
      </nav>

      <div class="barra-lateral-pie">
        <span class="foto-usuario"><?php echo $iniciales; ?></span>
        <div><strong><?php echo htmlspecialchars($usuario_nombre); ?></strong></div>
      </div>
    </aside>

    <div class="cuerpo-app">
      <header class="barra-superior">
        <h2>Proyecciones</h2>
        <div class="barra-superior-derecha">
          <span class="etiqueta-pastilla">Selecciona una meta</span>
          <span class="foto-usuario"><?php echo $iniciales; ?></span>
        </div>
      </header>

      <main class="pagina-contenido">
        <section class="pagina-encabezado encabezado-modulo-proyecciones">
          <div>
            <h1>Proyeccion de metas</h1>
            <p>Selecciona una tarjeta para ver la pantalla de estadisticas.</p>
          </div>
          <div class="acciones">
            <button id="boton-regresar-pantalla" class="btn btn-plano boton-regresar-flecha" type="button"
              aria-label="Regresar" title="Regresar"></button>
          </div>
        </section>

        <!-- Tip Box de contabilidad para proyecciones -->
        <div class="tip-box">
          <span class="tip-box-icono">💡</span>
          <div>
            <strong>Tip financiero:</strong> Calcular como pagaras algo a futuro se conoce como <em>"Proyeccion
              financiera"</em> o <em>"Presupuestacion"</em>. Esto te ayuda a visualizar escenarios y ajustar tus habitos
            de gasto antes de que sea demasiado tarde.
          </div>
        </div>

        <section class="rejilla-auto lista-metas-proyeccion">
          <?php if (count($metas) == 0): ?>
            <p class="texto-suave" style="padding: 20px 0;">No hay metas registradas para proyectar.</p>
          <?php else: ?>
            <?php foreach ($metas as $m): ?>
              <?php
              $porcentaje = 0;
              if ($m['monto_objetivo'] > 0) {
                $porcentaje = ($m['monto_acumulado'] / $m['monto_objetivo']) * 100;
              }
              $porcentaje = min(100, round($porcentaje));
              ?>
              <article class="meta-mini tarjeta-meta-proyeccion tarjeta-meta-modulo-proyecciones">
                <h4><?php echo htmlspecialchars($m['nombre_meta']); ?></h4>
                <p>Meta: $<?php echo number_format($m['monto_objetivo'], 2); ?> - Progreso <?php echo $porcentaje; ?>%</p>
                <div class="barra margen-inf-12"><span style="width: <?php echo $porcentaje; ?>%;"></span></div>
                <!-- el id de la meta viaja en la URL para que el detalle cargue la meta correcta -->
                <a class="btn btn-secundario" href="detalle_proyeccion.php?id=<?php echo $m['id']; ?>">Ver</a>
              </article>
            <?php endforeach; ?>
          <?php endif; ?>
        </section>
      </main>
    </div>
  </div>

  <script>
    document.getElementById('boton-regresar-pantalla').addEventListener('click', function () {
      window.history.back();
    });

    if (localStorage.getItem('tema') === 'oscuro') {
      document.body.classList.add('tema-oscuro');
    }
  </script>
</body>

</html>