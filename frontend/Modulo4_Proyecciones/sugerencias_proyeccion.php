<?php
// se incluyo el modulo que calcula el porcentaje y los montos sugeridos
include(__DIR__ . "/../../backend/Modulo4_Proyecciones/sugerencias_proyeccion.php");
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dyna - Sugerencias de Ahorro</title>
  <link rel="stylesheet" href="../../estilos.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <style>
    /* se definieron estilos especificos de las tarjetas de sugerencia y barra de progreso */
    .tarjetas-estadisticas {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 15px;
      margin-bottom: 25px;
    }

    .tarjeta-estat {
      background: #f8fafc;
      border-radius: 8px;
      padding: 15px 20px;
      border-left: 5px solid #3b82f6;
    }

    .tarjeta-estat small {
      display: block;
      color: #64748b;
      font-weight: 600;
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 5px;
    }

    .tarjeta-estat span {
      display: block;
      color: #1e293b;
      font-size: 1.8rem;
      font-weight: 700;
    }

    .panel-estadisticas {
      background: white;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      padding: 24px;
    }

    .btn-volver {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background: white;
      border: 1px solid #3b82f6;
      color: #3b82f6;
      padding: 8px 16px;
      border-radius: 20px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.2s;
    }

    .btn-volver:hover {
      background: #eff6ff;
    }

    .encabezado-meta-flex {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 25px;
    }

    .barra-progreso-contenedor {
      margin-top: 15px;
    }

    /* se definio la caja de sugerencia con numero grande y etiqueta de periodo */
    .sugerencia-caja {
      background: white;
      padding: 20px;
      border-radius: 8px;
      border: 1px solid #e2e8f0;
      text-align: center;
    }

    .sugerencia-caja small {
      display: block;
      color: #64748b;
      font-size: 0.9rem;
      margin-bottom: 10px;
      font-weight: bold;
    }

    .sugerencia-caja strong {
      font-size: 2rem;
      color: #15803d;
    }

    /* modo oscuro */
    body.tema-oscuro .panel-estadisticas {
      background: var(--color-panel);
      border-color: var(--color-borde);
    }

    body.tema-oscuro .tarjeta-estat {
      background: var(--color-panel);
      border-color: var(--color-borde);
    }

    body.tema-oscuro .sugerencia-caja {
      background: var(--color-panel);
      border-color: var(--color-borde);
    }

    body.tema-oscuro .tarjeta-estat span {
      color: #f1f5f9;
    }

    body.tema-oscuro .sugerencia-caja strong {
      color: #4ade80;
    }

    body.tema-oscuro .btn-volver {
      background: #1e293b;
      border-color: #3b82f6;
      color: #60a5fa;
    }

    body.tema-oscuro .btn-volver:hover {
      background: #334155;
    }

    body.tema-oscuro .barra-progreso-contenedor .barra {
      background: #334155 !important;
    }

    body.tema-oscuro .alerta-fecha {
      background: rgba(239, 68, 68, 0.1) !important;
      border-color: rgba(239, 68, 68, 0.2) !important;
    }

    body.tema-oscuro .alerta-fecha h4 {
      color: #f87171 !important;
    }

    body.tema-oscuro .alerta-fecha p {
      color: #fca5a5 !important;
    }
  </style>
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

      <!-- se marco proyecciones como activo porque sugerencias pertenece al modulo 4 -->
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
        <div>
          <strong><?php echo htmlspecialchars($usuario_nombre); ?></strong>
        </div>
      </div>
    </aside>

    <div class="cuerpo-app">
      <header class="barra-superior">
        <h2>Sugerencias de Ahorro</h2>
        <div class="barra-superior-derecha">
          <span class="etiqueta-pastilla" style="background:#dcfce7; color:#166534;">Meta:
            <?php echo htmlspecialchars($meta["nombre_meta"]); ?></span>
          <span class="foto-usuario"><?php echo $iniciales; ?></span>
        </div>
      </header>

      <main class="pagina-contenido">
        <div class="encabezado-meta-flex">
          <div>
            <h1 style="font-size: 2.2rem; margin-bottom: 5px; color: var(--color-texto);">Informacion</h1>
            <p style="color: var(--color-suave); margin:0;">Calculo desde la meta hasta tu fecha limite.</p>
          </div>
          <!-- el boton volver regresa al detalle pasando el mismo id de meta -->
          <a href="detalle_proyeccion.php?id=<?php echo $id_meta; ?>" class="btn-volver">Volver</a>
        </div>

        <div class="panel-estadisticas" style="margin-bottom: 25px;">
          <h3 style="margin-top: 0; color: var(--color-texto);">Informacion General</h3>

          <!-- se mostraron el monto inicial y el objetivo para que el usuario tenga contexto -->
          <div class="tarjetas-estadisticas" style="margin-top: 15px; margin-bottom: 15px;">
            <div class="tarjeta-estat" style="border-left-color: #f59e0b;">
              <small>CON CUANTO EMPECE</small>
              <span>$<?php echo number_format($monto_inicial, 2); ?></span>
            </div>
            <div class="tarjeta-estat" style="border-left-color: #3b82f6;">
              <small>COSTO FINAL DE LA META</small>
              <span>$<?php echo number_format($monto_objetivo, 2); ?></span>
            </div>
          </div>

          <!-- se mostro la barra de progreso con el porcentaje calculado por el backend -->
          <div class="barra-progreso-contenedor">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
              <strong style="color: var(--color-suave);">Avance actual</strong>
              <strong style="color: #15803d;"><?php echo round($porcentaje); ?>%</strong>
            </div>
            <div class="barra" style="height: 12px; background: #e2e8f0; border-radius: 6px; overflow: hidden;">
              <span
                style="width: <?php echo $porcentaje; ?>%; background: #15803d; display: block; height: 100%;"></span>
            </div>
            <p
              style="text-align: right; color: var(--color-suave); font-size: 0.9rem; margin-top: 5px; margin-bottom: 0;">
              Ahorrado: $<?php echo number_format($monto_acumulado, 2); ?>
            </p>
          </div>
        </div>

        <div class="panel-estadisticas">
          <h3
            style="margin-top: 0; color: var(--color-texto); border-bottom: 1px solid var(--color-borde); padding-bottom: 15px; margin-bottom: 20px;">
            Plan de ahorro sugerido
          </h3>

          <?php if ($tiene_limite == true): ?>
            <!-- se mostraron los tres planes calculados por el backend segun los dias disponibles -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
              <div class="sugerencia-caja">
                <small>AHORRO POR DIA</small>
                <strong>$<?php echo number_format($ahorro_diario_sugerido, 2); ?></strong>
              </div>
              <div class="sugerencia-caja">
                <small>AHORRO POR SEMANA</small>
                <strong>$<?php echo number_format($ahorro_semanal_sugerido, 2); ?></strong>
              </div>
              <div class="sugerencia-caja">
                <small>AHORRO POR MES</small>
                <strong>$<?php echo number_format($ahorro_mensual_sugerido, 2); ?></strong>
              </div>
            </div>
            <p style="text-align: center; color: var(--color-suave); margin-top: 20px; font-size: 0.9rem;">
              Basado en fecha de inicio (<?php echo date("d/m/Y", strtotime($meta["fecha_inicio"])); ?>)
              y la fecha limite (<?php echo $fecha_meta; ?>).
            </p>
          <?php else: ?>
            <!-- se informo al usuario que la meta necesita fecha limite para calcular las sugerencias -->
            <div class="alerta-fecha"
              style="display: flex; align-items: center; justify-content: center; gap: 15px; padding: 15px 20px; background: #fef2f2; border: 1px solid #fee2e2; border-radius: 8px;">
              <span style="font-size: 1.8rem;">⚠️</span>
              <div style="text-align: left;">
                <h4 style="color: #ef4444; margin: 0 0 4px 0; font-size: 1rem;">No hay fecha limite</h4>
                <p style="color: #b91c1c; margin: 0; font-size: 0.9rem;">Para calcular las sugerencias de ahorro necesitas
                  ir a Metas, editar esta meta y establecerle una fecha limite.</p>
              </div>
            </div>
          <?php endif; ?>

          <!-- grafica de progreso de la meta (ideal vs real) -->
          <hr style="border:none; border-top:1px solid var(--color-borde, #e2e8f0); margin: 24px 0 20px;">
          <h4 style="margin: 0 0 14px 0; color: var(--color-texto, #334155);">Progreso de la meta</h4>
          <p style="margin: 0 0 14px 0; font-size:13px; color:var(--color-suave, #64748b);">
            Curva ideal para llegar al objetivo vs tu avance real hasta hoy.
          </p>
          <div style="position:relative; height:260px;">
            <canvas id="grafica-progreso-meta"></canvas>
          </div>
        </div>
      </main>
    </div>
  </div>

  <script>
    const chartLabels = <?php echo $json_chart_labels; ?>;
    const chartIdeal = <?php echo $json_chart_ideal; ?>;
    const chartReal = <?php echo $json_chart_real; ?>;

    const tc = document.body.classList.contains('tema-oscuro') ? '#94a3b8' : '#64748b';

    const ctx = document.getElementById('grafica-progreso-meta').getContext('2d');

    function grad(ctx, top, bottom) {
      const g = ctx.createLinearGradient(0, 0, 0, 260);
      g.addColorStop(0, top); g.addColorStop(1, bottom); return g;
    }

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: chartLabels,
        datasets: [
          {
            label: 'Sugerencia',
            data: chartIdeal,
            borderColor: 'rgba(99,102,241,1)',
            backgroundColor: grad(ctx, 'rgba(99,102,241,0.15)', 'rgba(99,102,241,0)'),
            fill: true,
            borderDash: [6, 4],
            tension: 0.45,
            pointRadius: 3,
            pointHoverRadius: 6,
            borderWidth: 2.5
          },
          {
            label: 'Tu (tu ahorro)',
            data: chartReal,
            borderColor: 'rgba(34,197,94,1)',
            backgroundColor: grad(ctx, 'rgba(34,197,94,0.12)', 'rgba(34,197,94,0)'),
            fill: true,
            spanGaps: false,
            tension: 0.45,
            pointRadius: 3,
            pointHoverRadius: 6,
            borderWidth: 2.5
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: true, position: 'top',
            labels: { color: tc, font: { size: 12 }, boxWidth: 13 }
          },
          tooltip: {
            backgroundColor: 'rgba(15,15,30,0.82)',
            titleColor: '#e2e8f0', bodyColor: '#cbd5e1',
            cornerRadius: 8, padding: 10,
            callbacks: { label: c => ' $' + (c.parsed.y?.toLocaleString('es-MX') ?? '-') }
          }
        },
        scales: {
          x: { grid: { color: 'rgba(148,163,184,0.12)' }, ticks: { color: tc, font: { size: 11 } } },
          y: {
            grid: { color: 'rgba(148,163,184,0.12)' },
            ticks: { color: tc, font: { size: 11 }, callback: v => '$' + v.toLocaleString('es-MX') }
          }
        }
      }
    });

    /* se verifico si el usuario tenia el tema oscuro guardado para aplicarlo en todas las paginas */
    if (localStorage.getItem('tema') === 'oscuro') {
      document.body.classList.add('tema-oscuro');
    }
  </script>
</body>

</html>