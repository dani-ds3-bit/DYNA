<?php
// ============================================================
// modulo 5 — bitacora y notificaciones (vista)
// se incluye el backend que agrupa los eventos por dia.
// la vista recorre $por_dia para construir la linea de tiempo.
// ============================================================

// se incluyo el modulo que consulta y agrupa los eventos del usuario
include(__DIR__ . "/../../backend/Modulo5_BitacorayNotificaciones/obtener_bitacora.php");
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dyna - Bitacora</title>
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

      <!-- se marco bitacora como activo porque es la pantalla del modulo 5 -->
      <nav class="menu-links">
        <a class="menu-link" href="../Modulo2_ControlGastosPr/gastos.php"><span class="icono-menu">G</span>Gastos</a>
        <a class="menu-link" href="../Modulo3_GestionMetas/metas.php"><span class="icono-menu">M</span>Metas</a>
        <a class="menu-link" href="../Modulo4_Proyecciones/proyecciones.php"><span
            class="icono-menu">P</span>Proyecciones</a>
        <a class="menu-link activo" href="bitacora.php"><span class="icono-menu">B</span>Bitacora</a>
        <a class="menu-link" href="../Modulo6_Ajustes/ajustes.php"><span class="icono-menu">A</span>Ajustes</a>
      </nav>

      <div class="barra-lateral-pie">
        <span class="foto-usuario"><?php echo $iniciales; ?></span>
        <div>
          <strong><?php echo htmlspecialchars($nombre); ?></strong>
        </div>
      </div>
    </aside>

    <div class="cuerpo-app">
      <header class="barra-superior">
        <h2>Historial</h2>
        <div class="barra-superior-derecha">
          <span class="etiqueta-pastilla">Actividad del sistema</span>
          <span class="foto-usuario"><?php echo $iniciales; ?></span>
        </div>
      </header>

      <main class="pagina-contenido">
        <section class="pagina-encabezado">
          <div>
            <h1>Historial de actividad</h1>
            <p>Registro de actividad</p>
          </div>
          <div class="acciones" style="display: flex; gap: 10px; align-items: center;">
            <input type="date" id="filtro-fecha" style="padding: 8px 12px; border-radius: 8px; border: 1px solid var(--color-borde, #cbd5e1); background: var(--color-panel, #fff); color: var(--color-texto, #1e293b); outline: none; font-family: inherit;" onchange="filtrarBitacora()">
            <button class="btn btn-plano" id="btn-limpiar-filtro" onclick="limpiarFiltro()" style="display: none; padding: 8px 12px;">X Limpiar</button>
          </div>
        </section>

        <section class="panel" style="padding: 10px 20px;">
          <!-- Contenedor estatico con scroll -->
          <div style="max-height: 550px; overflow-y: auto; padding-right: 10px; margin-top: 10px;" id="contenedor-scroll-bitacora">
            <div class="linea-tiempo" id="lista-bitacora">
              <?php if (count($eventos) == 0): ?>
                <p class="texto-suave" style="padding: 20px 0;">No hay actividad registrada aun.</p>
              <?php else: ?>
                <?php foreach ($por_dia as $dia => $lista): ?>
                  <?php $fecha_raw = date('Y-m-d', strtotime($lista[0]['fecha_hora'])); ?>
                  <!-- Grupo entero de un dia especifico -->
                  <div class="grupo-fecha-bitacora" data-fecha="<?php echo $fecha_raw; ?>">
                    <!-- se mostro la etiqueta de dia en espanol generada por el backend -->
                    <h3 class="linea-tiempo-dia"><?php echo $dia; ?></h3>
                    <?php foreach ($lista as $ev): ?>
                      <div class="linea-tiempo-elemento">
                        <span class="linea-tiempo-punto"></span>
                        <article class="linea-tiempo-tarjeta">
                          <div>
                            <strong><?php echo htmlspecialchars($ev['tipo_evento']); ?></strong>
                            <p><?php echo htmlspecialchars($ev['descripcion']); ?></p>
                          </div>
                          <!-- se formateo la hora en HH:MM para que se lea claramente -->
                          <div class="linea-tiempo-derecha">
                            <strong><?php echo date("H:i", strtotime($ev['fecha_hora'])); ?></strong>
                          </div>
                        </article>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </section>
      </main>
    </div>
  </div>

  <script>
    /* se verifico si el usuario tenia el tema oscuro guardado para aplicarlo en todas las paginas */
    if (localStorage.getItem('tema') === 'oscuro') {
      document.body.classList.add('tema-oscuro');
    }

    /* ---- logica de filtrado por fecha ---- */
    function filtrarBitacora() {
      const inputFecha = document.getElementById('filtro-fecha').value;
      const grupos = document.querySelectorAll('.grupo-fecha-bitacora');
      const btnLimpiar = document.getElementById('btn-limpiar-filtro');

      if (!inputFecha) {
        limpiarFiltro();
        return;
      }

      btnLimpiar.style.display = 'inline-block';

      grupos.forEach(grupo => {
        const fechaGrupo = grupo.getAttribute('data-fecha');
        if (fechaGrupo === inputFecha) {
          grupo.style.display = 'block';
        } else {
          grupo.style.display = 'none';
        }
      });
    }

    function limpiarFiltro() {
      document.getElementById('filtro-fecha').value = '';
      document.getElementById('btn-limpiar-filtro').style.display = 'none';
      
      const grupos = document.querySelectorAll('.grupo-fecha-bitacora');
      grupos.forEach(grupo => {
        grupo.style.display = 'block';
      });
    }

    /* personalizacion del scroll para que se vea mas elegante */
    const contenedorScroll = document.getElementById('contenedor-scroll-bitacora');
    contenedorScroll.style.scrollbarWidth = 'thin';
    contenedorScroll.style.scrollbarColor = 'var(--color-borde, #cbd5e1) transparent';
  </script>
</body>

</html>