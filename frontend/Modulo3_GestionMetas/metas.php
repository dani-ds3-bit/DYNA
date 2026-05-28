<?php
// ============================================================
// modulo 3 — gestion de metas (vista principal)
// se incluye el backend que trae las metas del usuario,
// se calcula el porcentaje de progreso de cada una en la vista.
// ============================================================

// se incluyo el modulo que consulta y prepara el arreglo de metas
include(__DIR__ . "/../../backend/Modulo3_GestionMetas/obtener_metas.php");
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dyna - Metas</title>
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

      <!-- se marco metas como activo porque es la pantalla actual -->
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
        <h2>Metas de ahorro</h2>
        <div class="barra-superior-derecha" style="display:flex; align-items:center; gap:15px;">
          <!-- campanita de notificaciones -->
          <div class="contenedor-campanita" id="btn-campanita" tabindex="0" role="button" aria-label="Notificaciones">
            <span class="icono-campanita">🔔</span>
            <?php if (count($notificaciones_metas) > 0): ?>
              <span class="badge-notificacion"><?php echo count($notificaciones_metas); ?></span>
            <?php endif; ?>
            
            <!-- dropdown de notificaciones -->
            <div class="dropdown-notificaciones" id="dropdown-notif">
              <div class="dropdown-header">Notificaciones de Metas</div>
              <?php if (count($notificaciones_metas) == 0): ?>
                <div style="padding: 15px; color: #64748b; font-size: 0.85rem; text-align: center;">No tienes notificaciones pendientes.</div>
              <?php else: ?>
                <?php foreach ($notificaciones_metas as $n): ?>
                  <a href="../Modulo3_GestionMetas/metas.php" class="item-notificacion">
                    <span class="item-notif-titulo">Meta: <?php echo htmlspecialchars($n['nombre_meta']); ?></span>
                    <span class="item-notif-texto">
                      Sigue ahorrando. Progreso: $<?php echo number_format($n['monto_acumulado'], 2); ?> de $<?php echo number_format($n['monto_objetivo'], 2); ?>
                      <?php if($n['fecha_limite']) echo " | Limite: " . date("d/m/Y", strtotime($n['fecha_limite'])); ?>
                    </span>
                  </a>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
          <span class="etiqueta-pastilla">Meta mensual activa</span>
          <span class="foto-usuario"><?php echo $iniciales; ?></span>
        </div>
      </header>

      <main class="pagina-contenido">
        <section class="pagina-encabezado">
          <div>
            <h1>Metas de ahorro</h1>
            <p>Define objetivos, revisa avance y ajusta aportes.</p>
          </div>
          <div class="acciones">
            <a class="btn btn-principal" href="agregar_meta.php">Crear nueva meta</a>
          </div>
        </section>

        <!-- Tip Box de contabilidad para metas -->
        <div class="tip-box">
          <span class="tip-box-icono">💡</span>
          <div>
            <strong>Tip financiero:</strong> Al guardar para una meta, en contabilidad se considera la creacion de un <em>"Fondo de provision"</em> o ahorro para inversion. Esto fortalece tu solvencia y evita que recurras a deudas cuando tienes un objetivo grande.
          </div>
        </div>

        <!-- se iteraron las tarjetas de meta con su porcentaje calculado en tiempo real -->
        <section class="rejilla-auto">
          <?php if (count($metas) == 0): ?>
            <p class="texto-suave" style="padding: 20px 0;">No tienes metas registradas aun. Crea una con el boton de
              arriba.</p>
          <?php else: ?>
            <?php foreach ($metas as $m): ?>
              <?php
              // se calculo el porcentaje dividiendo el acumulado entre el objetivo
              $porcentaje = 0;
              if ($m['monto_objetivo'] > 0) {
                  $porcentaje = ($m['monto_acumulado'] / $m['monto_objetivo']) * 100;
              }
              // se limitó a 100 para que la barra no se desborde
              $porcentaje    = min(100, round($porcentaje));
              $es_completada = ($porcentaje >= 100);
              ?>
              <article class="tarjeta-gasto-reciente tarjeta-gasto-estilo-classroom meta-card-estilo-classroom">
                <div class="tarjeta-classroom-cabecera">
                  <div class="tarjeta-classroom-textos">
                    <h4><?php echo htmlspecialchars($m['nombre_meta']); ?></h4>
                    <small>Fecha limite:
                      <?php echo isset($m['fecha_limite']) ? $m['fecha_limite'] : 'Sin fecha'; ?> —
                      <?php echo htmlspecialchars($m['estado']); ?></small>
                  </div>
                  <span class="tarjeta-classroom-ilustracion" aria-hidden="true"></span>
                  <!-- se mostró el porcentaje en la burbuja para verlo de un vistazo -->
                  <span class="tarjeta-classroom-burbuja-monto"><?php echo $porcentaje; ?>%</span>

                  <!-- badge de completada cuando se llegó al 100% -->
                  <?php if ($es_completada): ?>
                    <span class="badge-meta-completada">✓ Completada</span>
                  <?php endif; ?>
                </div>
                <div class="tarjeta-classroom-cuerpo">
                  <p>Ahorro actual: $<?php echo number_format($m['monto_acumulado'], 2); ?> de
                    $<?php echo number_format($m['monto_objetivo'], 2); ?></p>
                  <div class="barra margen-sup-12"><span style="width: <?php echo $porcentaje; ?>%"></span></div>
                </div>
                <div class="tarjeta-classroom-pie">
                  <?php if (!$es_completada): ?>
                    <!-- solo se muestra Abonar si la meta no esta completa -->
                    <a class="btn btn-secundario boton-ver-mas-gasto"
                      href="abonar_meta.php?id=<?php echo $m['id']; ?>">Abonar</a>
                  <?php endif; ?>
                  <a class="btn btn-plano boton-editar-meta" href="agregar_meta.php?id=<?php echo $m['id']; ?>">Editar</a>
                  <?php if ($es_completada): ?>
                    <!-- boton especial para marcar completada (tambien elimina la meta) -->
                    <a class="btn btn-completar boton-completar-meta"
                      href="../../backend/Modulo3_GestionMetas/completar_meta.php?id=<?php echo $m['id']; ?>">
                      ✓ Completado
                    </a>
                  <?php else: ?>
                    <!-- el enlace de eliminar apunta al backend porque no tiene vista propia -->
                    <a class="btn btn-peligro boton-eliminar-meta"
                      href="../../backend/Modulo3_GestionMetas/eliminar_meta.php?id=<?php echo $m['id']; ?>">Eliminar</a>
                  <?php endif; ?>
                </div>
              </article>
            <?php endforeach; ?>
          <?php endif; ?>
        </section>
      </main>
    </div>
  </div>

  <!-- modal de confirmacion para eliminar una meta -->
  <div id="modal-confirmacion"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
    <div class="modal-cuerpo">
      <h3 class="modal-titulo">Eliminar esta meta?</h3>
      <p class="modal-texto">Esta accion borrara la meta y todo su progreso. No se puede deshacer.</p>
      <div style="display: flex; gap: 15px; justify-content: center;">
        <button id="btn-cancelar-eliminar" class="btn btn-plano" style="width: auto;">Cancelar</button>
        <a id="btn-confirmar-eliminar" href="#" class="btn btn-peligro" style="width: auto;">Si, eliminar</a>
      </div>
    </div>
  </div>

  <!-- modal de confirmacion para completar una meta -->
  <div id="modal-completar"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
    <div class="modal-cuerpo">
      <h3 class="modal-titulo" style="color: #1f8a4d;">🎉 Felicidades!</h3>
      <p class="modal-texto">Lograste tu meta. Al confirmar se eliminara de tu lista de metas activas.</p>
      <div style="display: flex; gap: 15px; justify-content: center;">
        <button id="btn-cancelar-completar" class="btn btn-plano" style="width: auto;">Cancelar</button>
        <a id="btn-confirmar-completar" href="#" class="btn btn-completar" style="width: auto;">Si, marcar completada</a>
      </div>
    </div>
  </div>

  <script>
    /* se intercepto el clic en eliminar para mostrar el modal antes de redirigir al backend */
    document.addEventListener('DOMContentLoaded', function () {
      // modal de eliminar
      const botonesEliminar = document.querySelectorAll('.boton-eliminar-meta');
      const modalEliminar   = document.getElementById('modal-confirmacion');
      const btnCancelarEl   = document.getElementById('btn-cancelar-eliminar');
      const btnConfirmarEl  = document.getElementById('btn-confirmar-eliminar');

      botonesEliminar.forEach(boton => {
        boton.addEventListener('click', function (e) {
          e.preventDefault();
          modalEliminar.style.display = 'flex';
          btnConfirmarEl.href = this.href;
        });
      });

      btnCancelarEl.addEventListener('click', function () {
        modalEliminar.style.display = 'none';
      });

      // modal de completar
      const botonesCompletar = document.querySelectorAll('.boton-completar-meta');
      const modalCompletar   = document.getElementById('modal-completar');
      const btnCancelarCo    = document.getElementById('btn-cancelar-completar');
      const btnConfirmarCo   = document.getElementById('btn-confirmar-completar');

      botonesCompletar.forEach(boton => {
        boton.addEventListener('click', function (e) {
          e.preventDefault();
          modalCompletar.style.display = 'flex';
          btnConfirmarCo.href = this.href;
        });
      });

      btnCancelarCo.addEventListener('click', function () {
        modalCompletar.style.display = 'none';
      });
    });
  </script>

  <script>
    /* se agrega funcionalidad al boton de notificaciones */
    document.addEventListener('DOMContentLoaded', function () {
      const btnCampanita = document.getElementById('btn-campanita');
      const dropdownNotif = document.getElementById('dropdown-notif');
      
      if(btnCampanita && dropdownNotif) {
        btnCampanita.addEventListener('click', function(e) {
          e.stopPropagation();
          dropdownNotif.classList.toggle('activo');
        });

        document.addEventListener('click', function(e) {
          if (!btnCampanita.contains(e.target)) {
            dropdownNotif.classList.remove('activo');
          }
        });
      }
    });

    /* se verifico si el usuario tenia el tema oscuro guardado para aplicarlo en todas las paginas */
    if (localStorage.getItem('tema') === 'oscuro') {
      document.body.classList.add('tema-oscuro');
    }
  </script>
</body>

</html>