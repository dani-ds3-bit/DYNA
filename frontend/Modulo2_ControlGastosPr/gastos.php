<?php
// se incluyo el modulo de gastos para preparar todos los datos
include(__DIR__ . "/../../backend/Modulo2_ControlGastosPr/obtener_gastos.php");
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dyna - Gastos</title>
  <link rel="stylesheet" href="../../estilos.css">
  <?php include(__DIR__ . "/../favicon.php"); ?>
</head>

<body>
  <div class="diseño-app">

    <!-- se construyo la barra lateral con los enlaces de navegacion a todos los modulos -->
    <aside class="barra-lateral">
      <div class="barra-lateral-marca">
        <div class="logo-menu-wrap">
          <img src="../../img/LOGO.png" alt="Logo Dyna">
        </div>
        <h2>Dyna</h2>
        <small>Finanzas Personales</small>
      </div>

      <!-- se marco gastos como activo-->
      <nav class="menu-links">
        <a class="menu-link activo" href="gastos.php"><span class="icono-menu">G</span>Gastos</a>
        <a class="menu-link" href="../Modulo3_GestionMetas/metas.php"><span class="icono-menu">M</span>Metas</a>
        <a class="menu-link" href="../Modulo4_Proyecciones/proyecciones.php"><span
            class="icono-menu">P</span>Proyecciones</a>
        <a class="menu-link" href="../Modulo5_BitacorayNotificaciones/bitacora.php"><span
            class="icono-menu">B</span>Bitacora</a>
        <a class="menu-link" href="../Modulo6_Ajustes/ajustes.php"><span class="icono-menu">A</span>Ajustes</a>
      </nav>

      <!-- se mostro el nombre e inicial del usuario  -->
      <div class="barra-lateral-pie">
        <span class="foto-usuario"><?php echo $iniciales; ?></span>
        <div>
          <strong><?php echo htmlspecialchars($nombre); ?></strong>
        </div>
      </div>
    </aside>

    <div class="cuerpo-app">
      <header class="barra-superior">
        <h2>Dyna</h2>
        <div class="barra-superior-derecha" style="display:flex; align-items:center; gap:15px;">
          <!-- campanita de notificaciones -->
          <div class="contenedor-campanita" id="btn-campanita" tabindex="0" role="button" aria-label="Notificaciones">
            <span class="icono-campanita">🔔</span>
            <?php if (count($notificaciones_metas) > 0): ?>
              <span class="badge-notificacion"><?php echo count($notificaciones_metas); ?></span>
            <?php endif; ?>

            <!-- lista de notificaciones -->
            <div class="dropdown-notificaciones" id="dropdown-notif">
              <div class="dropdown-header">Notificaciones de Metas</div>
              <?php if (count($notificaciones_metas) == 0): ?>
                <div style="padding: 15px; color: #64748b; font-size: 0.85rem; text-align: center;">No tienes
                  notificaciones pendientes.</div>
              <?php else: ?>
                <?php foreach ($notificaciones_metas as $n): ?>
                  <a href="../Modulo3_GestionMetas/metas.php" class="item-notificacion">
                    <span class="item-notif-titulo">Meta: <?php echo htmlspecialchars($n['nombre_meta']); ?></span>
                    <span class="item-notif-texto">
                      Sigue ahorrando. Progreso: $<?php echo number_format($n['monto_acumulado'], 2); ?> de
                      $<?php echo number_format($n['monto_objetivo'], 2); ?>
                      <?php if ($n['fecha_limite'])
                        echo " | Limite: " . date("d/m/Y", strtotime($n['fecha_limite'])); ?>
                    </span>
                  </a>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
          <span class="etiqueta-pastilla"><?php echo date("F Y"); ?></span>
          <span class="foto-usuario"><?php echo $iniciales; ?></span>
        </div>
      </header>

      <main class="pagina-contenido">
        <section class="pagina-encabezado">
          <div>
            <h1>Mis gastos</h1>
            <p>Tu control de pagos y prioridades.</p>
          </div>
          <div class="acciones contenedor-filtros-gastos">
            <div class="bloque-filtro-categoria-gastos">
              <label for="filtro-prioridad">Filtrar por prioridad</label>
              <select id="filtro-prioridad" aria-label="Filtrar prioridad">
                <option value="todas">Todas las prioridades</option>
                <option value="Alta">Alta</option>
                <option value="Media">Media</option>
                <option value="Baja">Baja</option>
              </select>
            </div>
          </div>
        </section>

        <!-- Tip Box de contabilidad para gastos -->
        <div class="tip-box">
          <span class="tip-box-icono">💡</span>
          <div>
            <strong>Tip financiero:</strong> Al llevar un control diario de tus gastos, en contabilidad se le llama
            <em>"Registro de egresos"</em>. Esto te permite conocer tu nivel de liquidez real y detectar "gastos
            hormiga" que drenan tu presupuesto mensual.
          </div>
        </div>

        <!-- se ofrecieron los dos metodos de registro como manual y por voz -->
        <section class="opciones-registro-gasto-compactas margen-inf-12" aria-label="Acciones rapidas de registro">
          <a class="boton-opcion-registro-gasto boton-agregar-gasto-manual boton-agregar-gastos-hormiga"
            href="agregar_gasto.php" aria-label="Agregar gasto manual" title="Agregar gasto manual">
            <span class="icono-boton-accion icono-boton-agregar-gasto" aria-hidden="true">+</span>
            <span class="texto-opcion-registro-gasto">Manual</span>
          </a>
          <a class="boton-opcion-registro-gasto boton-registrar-gasto-voz" href="agregar_gasto_voz.php"
            aria-label="Registrar gasto por voz" title="Registrar gasto por voz">
            <span class="icono-boton-accion icono-boton-microfono-gasto" aria-hidden="true"></span>
            <span class="texto-opcion-registro-gasto">Voz</span>
          </a>
        </section>

        <!-- se muestra solo si el usuario tiene becas registrada -->
        <?php if ($tiene_becas): ?>
          <section class="panel-becas margen-inf-12">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
              <p class="panel-becas-titulo" style="margin: 0;">Tus becas</p>
              <button onclick="abrirModalBeca()" aria-label="Agregar beca" title="Agregar beca"
                style="background: var(--color-principal, #3b82f6); border: none; color: white; width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; cursor: pointer; transition: background 0.2s;">+</button>
            </div>
            <div class="lista-pills-becas">
              <?php foreach ($becas as $beca): ?>
                <?php
                // se calcula el porcentaje consumido de cada beca
                $pct = 0;
                if ($beca['monto'] > 0) {
                  $pct = min(100, round(($beca['gastado'] / $beca['monto']) * 100));
                }
                $restante = max(0, $beca['monto'] - $beca['gastado']);
                $color_barra = $pct >= 90 ? '#ef4444' : ($pct >= 70 ? '#f59e0b' : '#1f8a4d');
                ?>
                <div class="pill-beca" style="position: relative;">
                  <div class="pill-beca-cabecera">
                    <span class="pill-beca-nombre"><?php echo htmlspecialchars($beca['nombre_beca']); ?></span>
                    <span class="pill-beca-pct"><?php echo $pct; ?>%</span>
                  </div>
                  <div class="pill-beca-barra">
                    <span style="width:<?php echo $pct; ?>%; background:<?php echo $color_barra; ?>;"></span>
                  </div>
                  <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span class="pill-beca-restante">$<?php echo number_format($restante, 2); ?> disp.</span>
                    <div class="acciones-beca" style="display: flex; gap: 4px;">
                      <button
                        onclick="abrirModalEditarBeca(<?php echo $beca['id']; ?>, '<?php echo htmlspecialchars($beca['nombre_beca'], ENT_QUOTES); ?>', <?php echo $beca['monto']; ?>)"
                        title="Editar beca" class="btn-icon-beca btn-editar-beca" aria-label="Editar beca">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                          stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M12 20h9" />
                          <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                        </svg>
                      </button>
                      <button
                        onclick="abrirModalEliminarBeca(<?php echo $beca['id']; ?>, '<?php echo htmlspecialchars($beca['nombre_beca'], ENT_QUOTES); ?>')"
                        title="Eliminar beca" class="btn-icon-beca btn-eliminar-beca" aria-label="Eliminar beca">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                          stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M3 6h18" />
                          <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                          <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </section>
        <?php else: ?>
          <section class="panel-becas margen-inf-12" style="border-style: dashed; border-color: var(--color-primario); display: flex; align-items: center; justify-content: space-between; padding: 16px; gap: 15px;">
            <div>
              <p style="margin: 0 0 4px 0; font-weight: bold; color: var(--color-texto);">¿Cuentas con una beca o apoyo?</p>
              <small style="color: var(--color-suave); display: block; line-height: 1.3;">Registra tu primera beca para vincular tus gastos y llevar un mejor control.</small>
            </div>
            <button onclick="abrirModalBeca()" class="btn btn-principal" style="width: auto; font-size: 0.85rem; padding: 8px 14px; flex-shrink: 0;">
              + Agregar Beca
            </button>
          </section>
        <?php endif; ?>

        <section class="panel">
          <div class="acciones" style="justify-content: space-between; width: 100%; margin-bottom: 12px;">
            <h3 class="sin-mb">Pagos recientes</h3>
            <!-- se mostro el total sumado por el   en tiempo real -->
            <span id="total-gastos" class="insignia">Total: $<?php echo number_format($total, 2); ?></span>
          </div>

          <!-- se iteraron las tarjetas de gasto generadas -->
          <div class="lista-tarjetas-gastos-recientes">
            <?php if (count($gastos) == 0): ?>
              <p class="texto-suave" style="padding: 20px 0;">No tienes gastos registrados aun. Agrega uno con el boton
                Manual.</p>
            <?php else: ?>
              <?php foreach ($gastos as $g): ?>
                <!-- se uso prioridad y monto para que el filtro del js funcione -->
                <article class="tarjeta-gasto-reciente tarjeta-gasto-estilo-classroom" data-categoria="<?php if (isset($g['nombre_categoria'])) {
                  echo strtolower($g['nombre_categoria']);
                } else {
                  echo 'general';
                } ?>" data-prioridad="<?php echo htmlspecialchars($g['prioridad']); ?>"
                  data-monto="<?php echo $g['monto']; ?>">
                  <div class="tarjeta-classroom-cabecera">
                    <div class="tarjeta-classroom-textos">
                      <h4><?php echo htmlspecialchars($g['nombre_gasto']); ?></h4>
                      <small>Prioridad
                        <?php echo htmlspecialchars($g['prioridad']); ?>     <?php if ($g['nombre_categoria'])
                                  echo ' - ' . htmlspecialchars($g['nombre_categoria']); ?></small>
                    </div>
                    <span class="tarjeta-classroom-ilustracion" aria-hidden="true"></span>
                    <span class="tarjeta-classroom-burbuja-monto">$<?php echo number_format($g['monto'], 2); ?></span>
                  </div>
                  <div class="tarjeta-classroom-cuerpo">
                    <p>Prioridad <?php echo htmlspecialchars($g['prioridad']); ?> - <?php echo $g['fecha_creacion']; ?>
                      (<?php echo $g['metodo_registro']; ?>)</p>
                  </div>
                  <div class="tarjeta-classroom-pie">
                    <!-- se paso el id del gasto para que el backend pueda cargar sus datos -->
                    <a class="btn btn-plano boton-editar-gasto"
                      href="agregar_gasto.php?id=<?php echo $g['id']; ?>">Editar</a>
                    <!-- el enlace de eliminar apunta al backend porque no tiene vista propia -->
                    <a class="btn btn-peligro boton-eliminar-gasto"
                      href="../../backend/Modulo2_ControlGastosPr/eliminar_gasto.php?id=<?php echo $g['id']; ?>">Eliminar</a>
                  </div>
                </article>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
          <p id="sin-resultados" class="texto-suave margen-sup-12" hidden>No hay tarjetas para esta prioridad.</p>
        </section>
      </main>
    </div>
  </div>

  <!-- agregar nueva beca -->
  <div id="modal-agregar-beca"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="panel"
      style="background: var(--color-panel, #fff); padding: 25px; border-radius: 12px; width: 90%; max-width: 400px; box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h3 style="margin: 0; color: var(--color-texto);">Agregar Beca</h3>
        <button onclick="cerrarModalBeca()"
          style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: var(--color-suave);">✕</button>
      </div>
      <form action="../../backend/Modulo2_ControlGastosPr/agregar_beca.php" method="POST">
        <div class="grupo" style="margin-bottom: 15px;">
          <label for="nombre_beca"
            style="color: var(--color-suave); display: block; margin-bottom: 5px; font-size: 0.9rem; font-weight: 600;">Nombre
            de la beca</label>
          <input type="text" id="nombre_beca" name="nombre_beca"
            style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid var(--color-borde, #cbd5e1); background: var(--color-fondo, #f8fafc); color: var(--color-texto, #1e293b); outline: none; font-family: inherit;"
            required>
        </div>
        <div class="grupo" style="margin-bottom: 25px;">
          <label for="monto_beca"
            style="color: var(--color-suave); display: block; margin-bottom: 5px; font-size: 0.9rem; font-weight: 600;">Monto
            total de la beca ($)</label>
          <input type="number" step="0.01" min="0.01" id="monto_beca" name="monto_beca" placeholder="Ej. 1800.00"
            style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid var(--color-borde, #cbd5e1); background: var(--color-fondo, #f8fafc); color: var(--color-texto, #1e293b); outline: none; font-family: inherit;"
            required>
        </div>
        <div style="display: flex; justify-content: flex-end; gap: 10px;">
          <button type="button" class="btn btn-plano" onclick="cerrarModalBeca()">Cancelar</button>
          <button type="submit" class="btn btn-principal">Guardar Beca</button>
        </div>
      </form>
    </div>
  </div>

  <!--  editar beca -->
  <div id="modal-editar-beca"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="panel"
      style="background: var(--color-panel, #fff); padding: 25px; border-radius: 12px; width: 90%; max-width: 400px; box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h3 style="margin: 0; color: var(--color-texto);">Editar Beca</h3>
        <button onclick="cerrarModalEditarBeca()"
          style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: var(--color-suave);">✕</button>
      </div>
      <form action="../../backend/Modulo2_ControlGastosPr/editar_beca.php" method="POST">
        <input type="hidden" id="edit_id_beca" name="id_beca">
        <div class="grupo" style="margin-bottom: 15px;">
          <label for="edit_nombre_beca"
            style="color: var(--color-suave); display: block; margin-bottom: 5px; font-size: 0.9rem; font-weight: 600;">Nombre
            de la beca</label>
          <input type="text" id="edit_nombre_beca" name="nombre_beca"
            style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid var(--color-borde, #cbd5e1); background: var(--color-fondo, #f8fafc); color: var(--color-texto, #1e293b); outline: none; font-family: inherit;"
            required>
        </div>
        <div class="grupo" style="margin-bottom: 25px;">
          <label for="edit_monto_beca"
            style="color: var(--color-suave); display: block; margin-bottom: 5px; font-size: 0.9rem; font-weight: 600;">Monto
            total de la beca ($)</label>
          <input type="number" step="0.01" min="0.01" id="edit_monto_beca" name="monto_beca"
            style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid var(--color-borde, #cbd5e1); background: var(--color-fondo, #f8fafc); color: var(--color-texto, #1e293b); outline: none; font-family: inherit;"
            required>
        </div>
        <div style="display: flex; justify-content: flex-end; gap: 10px;">
          <button type="button" class="btn btn-plano" onclick="cerrarModalEditarBeca()">Cancelar</button>
          <button type="submit" class="btn btn-principal">Actualizar Beca</button>
        </div>
      </form>
    </div>
  </div>

  <!-- confirmar eliminacion de beca -->
  <div id="modal-eliminar-beca"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="panel"
      style="background: var(--color-panel, #fff); padding: 25px; border-radius: 12px; width: 90%; max-width: 400px; box-shadow: 0 4px 20px rgba(0,0,0,0.2); text-align: center;">
      <div style="font-size: 3rem; margin-bottom: 15px;">⚠️</div>
      <h3 style="margin: 0 0 10px 0; color: var(--color-texto);">¿Eliminar beca?</h3>
      <p style="color: var(--color-suave); font-size: 0.95rem; margin-bottom: 25px;">
        ¿Seguro que deseas eliminar la beca <strong id="texto-nombre-beca-eliminar"></strong>?<br><br>
        <span style="font-size: 0.85rem;">Se mantendran los gastos registrados, pero dejaran de estar asociados a esta
          beca.</span>
      </p>
      <div style="display: flex; justify-content: center; gap: 15px;">
        <button type="button" class="btn btn-plano" onclick="cerrarModalEliminarBeca()">Cancelar</button>
        <a id="enlace-eliminar-beca" href="#" class="btn btn-peligro"
          style="background: #ef4444; color: white; border-color: #ef4444; text-decoration: none;">Sí, eliminar</a>
      </div>
    </div>
  </div>

  <script>
    function abrirModalBeca() {
      document.getElementById('modal-agregar-beca').style.display = 'flex';
    }

    function cerrarModalBeca() {
      document.getElementById('modal-agregar-beca').style.display = 'none';
    }
    function abrirModalEditarBeca(id, nombre, monto) {
      document.getElementById('edit_id_beca').value = id;
      document.getElementById('edit_nombre_beca').value = nombre;
      document.getElementById('edit_monto_beca').value = monto;
      document.getElementById('modal-editar-beca').style.display = 'flex';
    }
    function cerrarModalEditarBeca() {
      document.getElementById('modal-editar-beca').style.display = 'none';
    }
    function abrirModalEliminarBeca(id, nombre) {
      document.getElementById('texto-nombre-beca-eliminar').textContent = '"' + nombre + '"';
      document.getElementById('enlace-eliminar-beca').href = '../../backend/Modulo2_ControlGastosPr/eliminar_beca.php?id=' + id;
      document.getElementById('modal-eliminar-beca').style.display = 'flex';
    }

    function cerrarModalEliminarBeca() {
      document.getElementById('modal-eliminar-beca').style.display = 'none';
    }

    /* se implementa el filtro  */
    (function () {
      var filtro = document.getElementById("filtro-prioridad");
      var tarjetas = Array.prototype.slice.call(document.querySelectorAll(".tarjeta-gasto-reciente"));
      var totalNodo = document.getElementById("total-gastos");
      var sinResultados = document.getElementById("sin-resultados");

      function formatoPesos(monto) {
        return "$" + monto.toLocaleString("es-MX");
      }

      /* se recorrio la lista de tarjetas y se oculto o mostro segun la prioridad elegida */
      function filtrarPorPrioridad() {
        var prioridad = filtro.value;
        var visibles = 0;
        var total = 0;

        tarjetas.forEach(function (tarjeta) {
          var mostrar = prioridad === "todas" || tarjeta.dataset.prioridad === prioridad;
          tarjeta.style.display = mostrar ? '' : 'none';
          if (mostrar) {
            visibles += 1;
            /* se acumulo el monto de cada tarjeta visible para actualizar el total */
            total += Number(tarjeta.dataset.monto || 0);
          }
        });

        totalNodo.textContent = "Total: " + formatoPesos(total);
        sinResultados.hidden = visibles !== 0;
      }

      filtro.addEventListener("change", filtrarPorPrioridad);
      filtrarPorPrioridad();
    })();
  </script>

  <!-- se creo el modal de confirmacion para evitar eliminaciones accidentales -->
  <div id="modal-confirmacion"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
    <div class="modal-cuerpo">
      <h3 class="modal-titulo">Eliminar este gasto?</h3>
      <p class="modal-texto">Esta accion borrara el gasto permanentemente de tu historial.</p>
      <div style="display: flex; gap: 15px; justify-content: center;">
        <button id="btn-cancelar-eliminar" class="btn btn-plano" style="width: auto;">Cancelar</button>
        <a id="btn-confirmar-eliminar" href="#" class="btn btn-peligro" style="width: auto;">Si, eliminar</a>
      </div>
    </div>
  </div>

  <script>
    /* se intercepto el clic en eliminar para mostrar el modal antes de ejecutar la accion */
    document.addEventListener('DOMContentLoaded', function () {
      const botonesEliminar = document.querySelectorAll('.boton-eliminar-gasto');
      const modal = document.getElementById('modal-confirmacion');
      const btnCancelar = document.getElementById('btn-cancelar-eliminar');
      const btnConfirmar = document.getElementById('btn-confirmar-eliminar');

      botonesEliminar.forEach(boton => {
        boton.addEventListener('click', function (e) {
          e.preventDefault();
          modal.style.display = 'flex';
          /* se copio el href del boton al enlace de confirmacion */
          btnConfirmar.href = this.href;
        });
      });

      btnCancelar.addEventListener('click', function () {
        modal.style.display = 'none';
      });
    });
  </script>

  <script>
    /* se agrega funcionalidad al boton de notificaciones */
    document.addEventListener('DOMContentLoaded', function () {
      const btnCampanita = document.getElementById('btn-campanita');
      const dropdownNotif = document.getElementById('dropdown-notif');

      if (btnCampanita && dropdownNotif) {
        btnCampanita.addEventListener('click', function (e) {
          e.stopPropagation();
          dropdownNotif.classList.toggle('activo');
        });

        document.addEventListener('click', function (e) {
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