<?php
// ============================================================
// modulo 6 — ajustes (vista)
// se incluye el backend que carga el perfil y guarda cambios.
// se muestra el formulario de nombre y las opciones de tema.
// ============================================================

// se incluyo el modulo que lee el perfil y procesa el POST de guardado
include(__DIR__ . "/../../backend/Modulo6_Ajustes/guardar_ajustes.php");
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dyna - Ajustes</title>
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

      <!-- se marco ajustes como activo porque es la pantalla del modulo 6 -->
      <nav class="menu-links">
        <a class="menu-link" href="../Modulo2_ControlGastosPr/gastos.php"><span class="icono-menu">G</span>Gastos</a>
        <a class="menu-link" href="../Modulo3_GestionMetas/metas.php"><span class="icono-menu">M</span>Metas</a>
        <a class="menu-link" href="../Modulo4_Proyecciones/proyecciones.php"><span
            class="icono-menu">P</span>Proyecciones</a>
        <a class="menu-link" href="../Modulo5_BitacorayNotificaciones/bitacora.php"><span
            class="icono-menu">B</span>Bitacora</a>
        <a class="menu-link activo" href="ajustes.php"><span class="icono-menu">A</span>Ajustes</a>
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
        <h2>Configuracion</h2>
        <div class="barra-superior-derecha">
          <span class="foto-usuario"><?php echo $iniciales; ?></span>
        </div>
      </header>

      <main class="pagina-contenido">
        <section class="pagina-encabezado">
          <div>
            <h1>Configuracion</h1>
          </div>
        </section>

        <section class="rejilla-ajustes">
          <!-- se mostro la foto de perfil y los datos del usuario en la tarjeta izquierda -->
          <article class="perfil-card">
            <div class="perfil-foto">
              <img src="../../img/LOGO.png" alt="Avatar usuario">
            </div>
            <h3><?php echo htmlspecialchars($usuario["nombre_completo"]); ?></h3>
            <p><?php echo htmlspecialchars($usuario["correo"]); ?></p>
          </article>

          <section class="panel">
            <h3>Detalles de cuenta</h3>

            <!-- se mostro el mensaje de confirmacion o error segun el resultado del backend -->
            <?php if ($mensaje_ok != ""): ?>
              <p style="color:#28a745; margin-bottom:12px;"><?php echo $mensaje_ok; ?></p>
            <?php endif; ?>
            <?php if ($mensaje_error != ""): ?>
              <p style="color:#d9534f; margin-bottom:12px;"><?php echo $mensaje_error; ?></p>
            <?php endif; ?>

            <!-- el action apunta al mismo archivo para que el POST lo procese el include -->
            <form class="formulario-simple" action="ajustes.php" method="POST" id="form-ajustes">
              <div class="grupo">
                <label for="usuario-aj">Cambiar nombre de usuario</label>
                <input id="usuario-aj" name="nombre_completo" type="text"
                  value="<?php echo htmlspecialchars($usuario['nombre_completo']); ?>">
              </div>
              <div class="grupo">
                <label for="contraseña">Cambiar contraseña</label>
                <div class="input-con-ojo">
                  <input id="contraseña" name="contraseña" type="password"
                    placeholder="Escribe tu nueva contraseña (min. 6 caracteres)">
                  <button type="button" class="btn-ojo" data-target="contraseña" aria-label="Mostrar contraseña">
                    <span class="icono-ojo icono-ojo-cerrado"></span>
                  </button>
                </div>
              </div>
              <div class="grupo">
                <label for="confirmar-contraseña">Confirmar contraseña</label>
                <div class="input-con-ojo">
                  <input id="confirmar-contraseña" name="confirmar_contraseña" type="password"
                    placeholder="Repite tu nueva contraseña">
                  <button type="button" class="btn-ojo" data-target="confirmar-contraseña" aria-label="Mostrar contraseña">
                    <span class="icono-ojo icono-ojo-cerrado"></span>
                  </button>
                </div>
                <small id="msg-no-coincide" style="color:#d9534f; display:none; margin-top:4px;">Las contraseñas no coinciden.</small>
              </div>
              <div class="acciones">
                <button class="btn btn-principal" type="submit">Guardar cambios</button>
              </div>
            </form>
          </section>
        </section>

        <section class="rejilla-2 margen-sup-12">
          <article class="panel">
            <h3>Apariencia</h3>
            <p class="texto-suave">Selecciona un tono visual.</p>
            <div class="tema-grid">
              <!-- se compara el tema del usuario con el valor de la BD para marcarlo como activo -->
              <div class="tema-opcion <?php if ($usuario['tema'] == 'Claro')
                echo 'activo'; ?>"
                onclick="cambiarTemaActivo(this, 'claro')">
                <div class="tema-muestra"></div>
                <strong>Modo claro</strong>
              </div>
              <div class="tema-opcion <?php if ($usuario['tema'] == 'Oscuro')
                echo 'activo'; ?>"
                onclick="cambiarTemaActivo(this, 'oscuro')">
                <div class="tema-muestra oscuro"></div>
                <strong>Modo oscuro</strong>
              </div>
            </div>
          </article>

          <article class="panel">
            <h3>Acciones</h3>
            <p class="texto-suave">Cierra tu sesion de forma segura.</p>
            <div class="formulario-simple">
              <!-- se apunto al cerrar_sesion.php del modulo 1 para destruir la sesion -->
              <a class="btn btn-peligro" href="../Modulo1_Login/cerrar_sesion.php">Cerrar sesion</a>

              <!-- separador visual entre acciones -->
              <hr style="border:none; border-top:1px solid var(--color-borde); margin: 4px 0;">

              <!-- boton de eliminar cuenta, abre modal de confirmacion -->
              <button id="btn-abrir-eliminar-cuenta" class="btn btn-eliminar-cuenta" type="button">
                Eliminar cuenta
              </button>
              <small class="texto-suave" style="font-size:12px; line-height:1.4;">
                Esta accion es permanente. Se borraran todos tus gastos, metas y datos.
              </small>
            </div>
          </article>
        </section>
      </main>
    </div>
  </div>

  <!-- modal de confirmacion para eliminar la cuenta -->
  <div id="modal-eliminar-cuenta"
    style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.55); z-index:9999; justify-content:center; align-items:center;">
    <div class="modal-cuerpo">
      <div style="margin-bottom:14px; display:flex; justify-content:center; align-items:center; color:#ef4444;">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-triangle-alert"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
      </div>
      <h3 class="modal-titulo" style="color:#c0392b;">Eliminar cuenta?</h3>
      <p class="modal-texto">
        Esta accion <strong>no se puede deshacer</strong>. Se eliminaran permanentemente
        tu perfil, todos tus gastos, metas, becas y bitacora.
      </p>
      <p class="modal-texto" style="margin-top:-10px;">
        <strong>Eres <em><?php echo htmlspecialchars($nombre); ?></em>. Confirmas que quieres eliminar tu cuenta?</strong>
      </p>
      <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
        <button id="btn-cancelar-eliminar-cuenta" class="btn btn-plano" style="width:auto;">Cancelar</button>
        <a href="../../backend/Modulo6_Ajustes/eliminar_cuenta.php"
           class="btn btn-peligro" style="width:auto;">Si, eliminar mi cuenta</a>
      </div>
    </div>
  </div>

  <!-- modal de confirmacion para guardar cambios / cambiar contraseña -->
  <div id="modal-confirmar-cambios"
    style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.55); z-index:9999; justify-content:center; align-items:center;">
    <div class="modal-cuerpo">
      <div style="margin-bottom:14px; display:flex; justify-content:center; align-items:center; color:#3b82f6;">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check"><path d="M20 13c0 5-3.5 7.5-7.66 9.7a1 1 0 0 1-.68 0C7.5 20.5 4 18 4 13V6a1 1 0 0 1 .76-.97l8-2a1 1 0 0 1 .48 0l8 2A1 1 0 0 1 20 6z"/><path d="m9 12 2 2 4-4"/></svg>
      </div>
      <h3 class="modal-titulo">¿Guardar cambios?</h3>
      <p class="modal-texto" id="texto-confirmar-cambios">
        ¿Estas seguro de que deseas guardar los cambios realizados en tu cuenta?
      </p>
      <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
        <button id="btn-cancelar-cambios" type="button" class="btn btn-plano" style="width:auto;">Cancelar</button>
        <button id="btn-confirmar-cambios-ok" type="button" class="btn btn-principal" style="width:auto;">Aceptar</button>
      </div>
    </div>
  </div>

  <script>
    /* se deseleccionan todas las opciones de tema y se activa solo la que el usuario toco */
    function cambiarTemaActivo(elemento, modo) {
      document.querySelectorAll('.tema-opcion').forEach(function (el) {
        el.classList.remove('activo');
      });
      elemento.classList.add('activo');

      /* se aplico el tema al body y se guardo en el navegador (localstorage) */
      if (modo === 'oscuro') {
        document.body.classList.add('tema-oscuro');
        localStorage.setItem('tema', 'oscuro');
      } else {
        document.body.classList.remove('tema-oscuro');
        localStorage.setItem('tema', 'claro');
      }
    }
  </script>

  <script>
    /* se verifico si el usuario tenia el tema oscuro guardado para aplicarlo en todas las paginas */
    if (localStorage.getItem('tema') === 'oscuro') {
      document.body.classList.add('tema-oscuro');
    }

    /* ---- modal de eliminar cuenta ---- */
    document.addEventListener('DOMContentLoaded', function () {
      const btnAbrirModal = document.getElementById('btn-abrir-eliminar-cuenta');
      const modalEliminar = document.getElementById('modal-eliminar-cuenta');
      const btnCancelar   = document.getElementById('btn-cancelar-eliminar-cuenta');

      if (btnAbrirModal) {
        btnAbrirModal.addEventListener('click', function () {
          modalEliminar.style.display = 'flex';
        });
      }

      if (btnCancelar) {
        btnCancelar.addEventListener('click', function () {
          modalEliminar.style.display = 'none';
        });
      }

      /* ---- ojo: mostrar / ocultar contraseña ---- */
      document.querySelectorAll('.btn-ojo').forEach(function (btn) {
        btn.addEventListener('click', function () {
          const targetId = this.dataset.target;
          const campo = document.getElementById(targetId);
          const icono = this.querySelector('.icono-ojo');
          const visible = campo.type === 'text';
          campo.type = visible ? 'password' : 'text';
          icono.classList.toggle('icono-ojo-abierto', !visible);
          icono.classList.toggle('icono-ojo-cerrado', visible);
        });
      });

      /* ---- validación de coincidencia de contraseñas ---- */
      const campoPass = document.getElementById('contraseña');
      const campoConfirm = document.getElementById('confirmar-contraseña');
      const msgNoCoincide = document.getElementById('msg-no-coincide');

      function verificarCoincidencia() {
        const coinciden = campoPass.value === campoConfirm.value;
        msgNoCoincide.style.display = campoConfirm.value.length > 0 && !coinciden ? 'block' : 'none';
        campoConfirm.setCustomValidity(coinciden ? '' : 'Las contraseñas no coinciden.');
      }

      if (campoPass && campoConfirm) {
        campoPass.addEventListener('input', verificarCoincidencia);
        campoConfirm.addEventListener('input', verificarCoincidencia);
      }

      /* ---- modal de confirmación de cambios ---- */
      const formAjustes = document.getElementById('form-ajustes');
      const modalCambios = document.getElementById('modal-confirmar-cambios');
      const btnCancelarCambios = document.getElementById('btn-cancelar-cambios');
      const btnConfirmarCambiosOk = document.getElementById('btn-confirmar-cambios-ok');
      const textoConfirmarCambios = document.getElementById('texto-confirmar-cambios');

      if (formAjustes) {
        formAjustes.addEventListener('submit', function (e) {
          // Primero verificamos si las contraseñas coinciden (si rellenó alguna)
          if (campoPass.value !== campoConfirm.value) {
            e.preventDefault();
            msgNoCoincide.style.display = 'block';
            campoConfirm.focus();
            return;
          }

          // Prevenir el submit automático para mostrar el modal
          e.preventDefault();

          // Cambiar texto del modal según si cambió la contraseña o solo el nombre
          if (campoPass.value.length > 0) {
            textoConfirmarCambios.innerHTML = '¿Estas seguro de que deseas guardar los cambios y <strong>cambiar tu contraseña</strong>?';
          } else {
            textoConfirmarCambios.innerHTML = '¿Estas seguro de que deseas guardar los cambios realizados en tu cuenta?';
          }

          // Mostrar modal
          modalCambios.style.display = 'flex';
        });
      }

      if (btnCancelarCambios) {
        btnCancelarCambios.addEventListener('click', function () {
          modalCambios.style.display = 'none';
        });
      }

      if (btnConfirmarCambiosOk) {
        btnConfirmarCambiosOk.addEventListener('click', function () {
          // Ocultar modal y enviar formulario
          modalCambios.style.display = 'none';
          formAjustes.submit();
        });
      }
    });
  </script>
</body>

</html>