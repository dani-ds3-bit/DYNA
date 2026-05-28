<?php
// ============================================================
// modulo 4 — detalle de proyeccion (vista)
// se incluye el backend que carga la meta, calcula montos y
// genera los datos JSON para las gráficas con Chart.js
// ============================================================

include(__DIR__ . "/../../backend/Modulo4_Proyecciones/detalle_proyeccion.php");

function formatoD($val) {
    return floor($val) == $val ? number_format($val, 0) : number_format($val, 2);
}
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dyna - Detalle de Proyeccion</title>
  <link rel="stylesheet" href="../../estilos.css">
  <?php include(__DIR__ . "/../favicon.php"); ?>
  <!-- Chart.js desde CDN, sin dependencias adicionales -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <style>
    .tarjetas-estadisticas {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 14px;
      margin-bottom: 24px;
    }

    .tarjeta-estat {
      background: var(--color-panel, #f8fafc);
      border: 1px solid var(--color-borde, #e2e8f0);
      border-radius: 12px;
      padding: 16px 20px;
      border-left: 4px solid #6366f1;
    }

    .tarjeta-estat small {
      display: block;
      color: var(--color-suave, #64748b);
      font-weight: 700;
      font-size: 0.72rem;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      margin-bottom: 6px;
    }

    .tarjeta-estat span {
      display: block;
      color: #6366f1;
      font-size: 1.45rem;
      font-weight: 800;
      letter-spacing: -0.02em;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .panel-graficas {
      background: var(--color-panel, #fff);
      border: 1px solid var(--color-borde, #e2e8f0);
      border-radius: 14px;
      padding: 24px;
      margin-bottom: 20px;
    }

    .encabezado-grafica {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      flex-wrap: wrap;
      gap: 10px;
    }

    .encabezado-grafica h3 {
      margin: 0;
      font-size: 1.05rem;
      color: var(--color-texto, #1e293b);
    }

    .encabezado-grafica p {
      margin: 2px 0 0;
      font-size: 12px;
      color: var(--color-suave, #64748b);
    }



    /* ---- toggle pill: Semanal / Mensual ---- */
    .botones-toggle {
      display: inline-flex;
      background: rgba(99, 102, 241, 0.08);
      border: 1px solid rgba(99, 102, 241, 0.18);
      border-radius: 50px;
      padding: 4px;
      gap: 2px;
    }

    .botones-toggle button {
      background: none;
      border: none;
      padding: 7px 20px;
      border-radius: 50px;
      font-weight: 600;
      font-size: 13px;
      color: #6366f1;
      cursor: pointer;
      transition: background 0.2s, color 0.2s, box-shadow 0.2s, transform 0.15s;
      letter-spacing: 0.01em;
    }

    .botones-toggle button:hover:not(.activo) {
      background: rgba(99, 102, 241, 0.1);
      transform: scale(1.02);
    }

    .botones-toggle button.activo {
      background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);
      color: #fff;
      box-shadow: 0 3px 10px rgba(99, 102, 241, 0.35);
    }

    /* modo oscuro */
    body.tema-oscuro .botones-toggle {
      background: rgba(99, 102, 241, 0.12);
      border-color: rgba(99, 102, 241, 0.25);
    }

    body.tema-oscuro .botones-toggle button {
      color: #a5b4fc;
    }

    body.tema-oscuro .botones-toggle button:hover:not(.activo) {
      background: rgba(99, 102, 241, 0.18);
    }

    body.tema-oscuro .botones-toggle button.activo {
      background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
      color: #fff;
      box-shadow: 0 3px 14px rgba(99, 102, 241, 0.4);
    }

    .canvas-contenedor {
      position: relative;
      height: 260px;
    }

    /* encabezado de la meta */
    .encabezado-meta-flex {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 22px;
      flex-wrap: wrap;
      gap: 10px;
    }

    .btn-volver {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: none;
      border: 1px solid #6366f1;
      color: #6366f1;
      padding: 8px 18px;
      border-radius: 20px;
      text-decoration: none;
      font-weight: 600;
      font-size: 14px;
      transition: background 0.2s;
    }

    .btn-volver:hover { background: #eef2ff; }

    /* modo oscuro */
    body.tema-oscuro .tarjeta-estat { background: var(--color-panel); border-color: var(--color-borde); }
    body.tema-oscuro .panel-graficas { background: var(--color-panel); border-color: var(--color-borde); }
    body.tema-oscuro .toggle-periodo { background: #111; }
    body.tema-oscuro .toggle-periodo button.activo { background: var(--color-panel); }
    body.tema-oscuro .btn-volver { border-color: #818cf8; color: #818cf8; }
    body.tema-oscuro .btn-volver:hover { background: #1e1b4b; }
  </style>
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

      <nav class="menu-links">
        <a class="menu-link" href="../Modulo2_ControlGastosPr/gastos.php"><span class="icono-menu">G</span>Gastos</a>
        <a class="menu-link" href="../Modulo3_GestionMetas/metas.php"><span class="icono-menu">M</span>Metas</a>
        <a class="menu-link activo" href="proyecciones.php"><span class="icono-menu">P</span>Proyecciones</a>
        <a class="menu-link" href="../Modulo5_BitacorayNotificaciones/bitacora.php"><span class="icono-menu">B</span>Bitacora</a>
        <a class="menu-link" href="../Modulo6_Ajustes/ajustes.php"><span class="icono-menu">A</span>Ajustes</a>
      </nav>

      <div class="barra-lateral-pie">
        <span class="foto-usuario"><?php echo $iniciales; ?></span>
        <div><strong><?php echo htmlspecialchars($usuario_nombre); ?></strong></div>
      </div>
    </aside>

    <div class="cuerpo-app">
      <header class="barra-superior">
        <h2>Detalle de proyeccion</h2>
        <div class="barra-superior-derecha">
          <span class="etiqueta-pastilla" style="background:#eef2ff; color:#4f46e5;">Meta:
            <?php echo htmlspecialchars($meta["nombre_meta"]); ?></span>
          <span class="foto-usuario"><?php echo $iniciales; ?></span>
        </div>
      </header>

      <main class="pagina-contenido">

        <!-- encabezado de la meta -->
        <div class="encabezado-meta-flex">
          <div>
            <h1 style="font-size: 2rem; margin-bottom: 4px; color: var(--color-texto, #1e293b);">
              <?php echo htmlspecialchars($meta["nombre_meta"]); ?>
            </h1>
            <p style="color: var(--color-suave, #64748b); margin: 0;">
              Revisa tu avance y proyeccion de ahorro.
            </p>
          </div>
          <a href="proyecciones.php" class="btn-volver">← Volver</a>
        </div>

        <!-- tarjetas de resumen -->
        <div class="tarjetas-estadisticas">
          <div class="tarjeta-estat">
            <small>Meta total</small>
            <span>$<?php echo formatoD($monto_objetivo); ?></span>
          </div>
          <div class="tarjeta-estat" style="border-left-color:#22c55e;">
            <small>Ahorrado</small>
            <span style="color:#16a34a;">$<?php echo formatoD($monto_acumulado); ?></span>
          </div>
          <div class="tarjeta-estat" style="border-left-color:#f59e0b;">
            <small>Faltante</small>
            <span style="color:#d97706;">$<?php echo formatoD($monto_faltante); ?></span>
          </div>
          <div class="tarjeta-estat" style="border-left-color:#6366f1;">
            <small>Fecha límite</small>
            <span style="font-size:1.2rem;"><?php echo $fecha_meta; ?></span>
          </div>
          <?php if ($tiene_limite && $ahorro_mensual_sugerido > 0): ?>
          <div class="tarjeta-estat" style="border-left-color:#8b5cf6;">
            <small>Ahorro mensual</small>
            <span style="color:#7c3aed;">$<?php echo formatoD($ahorro_mensual_sugerido); ?></span>
          </div>
          <div class="tarjeta-estat" style="border-left-color:#06b6d4;">
            <small>Ahorro semanal</small>
            <span style="color:#0891b2;">$<?php echo formatoD($ahorro_semanal_sugerido); ?></span>
          </div>
          <?php endif; ?>
        </div>

        <!-- panel de estadisticas con graficas -->
        <div class="panel-estadisticas">
          <div class="encabezado-estadisticas">
            <div>
              <h3>Abonos por periodo</h3>
              <p style="margin:2px 0 0; font-size:12px; color:var(--color-suave,#64748b);">
                Cuanto has abonado a esta meta en las ultimas semanas y meses.
              </p>
            </div>
            <!-- toggle semanal / mensual -->
            <div class="botones-toggle">
              <button id="btn-tab-semanal" class="activo" onclick="cambiarTab('semanal')">Semanal</button>
              <button id="btn-tab-mensual" onclick="cambiarTab('mensual')">Mensual</button>
            </div>
          </div>

          <!-- grafica semanal (visible por defecto) -->
          <div id="tab-semanal" class="canvas-contenedor" style="margin-top:12px;">
            <canvas id="grafica-semanal"></canvas>
          </div>

          <!-- grafica mensual -->
          <div id="tab-mensual" class="canvas-contenedor" style="display:none; margin-top:12px;">
            <canvas id="grafica-mensual"></canvas>
          </div>
        </div>

        <div style="margin-top: 20px; text-align: center;">
          <a href="sugerencias_proyeccion.php?id=<?php echo $id_meta; ?>" class="btn btn-secundario">
            Ver sugerencias detalladas →
          </a>
        </div>

      </main>
    </div>
  </div>

  <script>
    /* ---- datos generados por PHP ---- */
    const semanasLabels = <?php echo $json_semanas_labels; ?>;
    const semanasMontos = <?php echo $json_semanas_montos; ?>;
    const mesesLabels   = <?php echo $json_meses_labels; ?>;
    const mesesMontos   = <?php echo $json_meses_montos; ?>;

    const C_INDIGO      = 'rgba(99, 102, 241, 1)';
    const C_INDIGO_AREA = 'rgba(99, 102, 241, 0.15)';
    const C_VIOLET      = '#8b5cf6';
    const C_VIOLET_AREA = 'rgba(139,92,246,0.15)';

    function tickColor() {
      return document.body.classList.contains('tema-oscuro') ? '#94a3b8' : '#64748b';
    }

    function crearGradiente(ctx, colorTop, colorBottom) {
      const g = ctx.createLinearGradient(0, 0, 0, 260);
      g.addColorStop(0, colorTop);
      g.addColorStop(1, colorBottom);
      return g;
    }

    function opcionesBase() {
      const tc = tickColor();
      return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: true, position: 'top',
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
          y: { grid: { color: 'rgba(148,163,184,0.12)' },
               ticks: { color: tc, font: { size: 11 }, callback: v => '$' + v.toLocaleString('es-MX') } }
        },
        elements: { line: { tension: 0.45, borderWidth: 2.5 }, point: { radius: 3, hoverRadius: 6 } }
      };
    }

    /* ---- instancias lazy (se crean solo al abrir el tab) ---- */
    let instancias = { semanal: null, mensual: null };

    function inicializarGrafica(tab) {
      if (tab === 'semanal' && !instancias.semanal) {
        const ctx = document.getElementById('grafica-semanal').getContext('2d');
        instancias.semanal = new Chart(ctx, {
          type: 'line',
          data: {
            labels: semanasLabels,
            datasets: [{ label: 'Abonos semanales', data: semanasMontos,
              borderColor: C_INDIGO,
              backgroundColor: crearGradiente(ctx, C_INDIGO_AREA, 'rgba(99,102,241,0)'),
              fill: true }]
          },
          options: opcionesBase()
        });
      }

      if (tab === 'mensual' && !instancias.mensual) {
        const ctx = document.getElementById('grafica-mensual').getContext('2d');
        instancias.mensual = new Chart(ctx, {
          type: 'line',
          data: {
            labels: mesesLabels,
            datasets: [{ label: 'Abonos mensuales', data: mesesMontos,
              borderColor: C_VIOLET,
              backgroundColor: crearGradiente(ctx, C_VIOLET_AREA, 'rgba(139,92,246,0)'),
              fill: true }]
          },
          options: opcionesBase()
        });
      }
    }

    /* ---- toggle semanal / mensual ---- */
    function cambiarTab(tab) {
      ['semanal', 'mensual'].forEach(function (t) {
        document.getElementById('tab-' + t).style.display = 'none';
        document.getElementById('btn-tab-' + t).classList.remove('activo');
      });
      document.getElementById('tab-' + tab).style.display = 'block';
      document.getElementById('btn-tab-' + tab).classList.add('activo');
      inicializarGrafica(tab);
    }

    /* ---- carga la grafica semanal al abrir la pagina ---- */
    document.addEventListener('DOMContentLoaded', function () {
      inicializarGrafica('semanal');
    });

    /* ---- tema oscuro ---- */
    if (localStorage.getItem('tema') === 'oscuro') {
      document.body.classList.add('tema-oscuro');
    }
  </script>

</body>

</html>