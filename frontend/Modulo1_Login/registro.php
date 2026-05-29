<?php
// se incluyo el modulo de registro para que procese el POST
include(__DIR__ . "/../../backend/Modulo1_Login/registro.php");
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dyna - Registro</title>
  <link rel="stylesheet" href="../../estilos.css">
  <?php include(__DIR__ . "/../favicon.php"); ?>
</head>

<body>
  <main class="pagina-acceso">
    <section class="caja-acceso">

      <!-- se coloco la marca visual en la columna izquierda -->
      <article class="columna-marca">
        <div class="logo-acceso">
          <img src="../../img/LOGO.png" alt="Logo Dyna">
        </div>
        <h1>Dyna</h1>
      </article>

      <article class="columna-formulario">
        <h2>Crear cuenta</h2>

        <!-- se muestra el error solo si el backend lo genero tras validar -->
        <?php if ($mensaje_error != ""): ?>
          <p style="color:#d9534f; margin-bottom:12px;"><?php echo $mensaje_error; ?></p>
        <?php endif; ?>

        <!-- se muestra el exito si el usuario se creo correctamente -->
        <?php if ($mensaje_ok != ""): ?>
          <p style="color:#28a745; margin-bottom:12px;"><?php echo $mensaje_ok; ?></p>
        <?php endif; ?>

        <form action="registro.php" method="POST" id="form-registro">
          <div class="grupo">
            <label for="nombre">Nombre completo</label>
            <input id="nombre" name="nombre" type="text" placeholder="Escribe tu nombre completo" required>
          </div>

          <div class="grupo">
            <label for="correo">Correo</label>
            <input id="correo" name="correo" type="email" placeholder="Escribe tu correo" required>
          </div>

          <div class="grupo">
            <label for="contraseña">Contraseña</label>
            <div class="input-con-ojo">
              <input id="contraseña" name="contraseña" type="password"
                placeholder="Crea una contraseña (min. 6 caracteres)" required>
              <button type="button" class="btn-ojo" data-target="contraseña" aria-label="Mostrar contraseña">
                <span class="icono-ojo icono-ojo-cerrado"></span>
              </button>
            </div>
          </div>

          <div class="grupo">
            <label for="confirmar-contrasena">Repite tu contraseña</label>
            <div class="input-con-ojo">
              <input id="confirmar-contrasena" name="confirmar_contrasena" type="password"
                placeholder="Escribe la contraseña de nuevo" required>
              <button type="button" class="btn-ojo" data-target="confirmar-contrasena" aria-label="Mostrar contraseña">
                <span class="icono-ojo icono-ojo-cerrado"></span>
              </button>
            </div>
            <!-- mensaje que aparece si las contraseñas no coinciden -->
            <small id="msg-no-coincide" style="color:#d9534f; display:none; margin-top:4px;">Las contraseñas no
              coinciden.</small>
          </div>

          <!-- checkbox para indicar si tiene beca -->
          <div class="grupo" style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
            <input type="checkbox" id="tiene_beca" name="tiene_beca" value="1"
              style="width:18px; height:18px; accent-color: var(--color-primario);">
            <label for="tiene_beca" style="margin:0; font-weight:normal;">¿Cuentas con alguna beca o apoyo
              economico?</label>
          </div>

          <!-- seccion de becas, se muestra solo si el checkbox esta marcado -->
          <div id="seccion-becas" style="display:none; margin-bottom:14px;">
            <div style="background: #f0fdf4; border:1px solid #c6e9d4; border-radius:10px; padding:14px;">
              <p style="margin:0 0 10px; font-weight:700; font-size:14px; color:#1a5c38;">Tus becas/ apoyos</p>

              <!-- contenedor donde se agregan las filas de beca dinámicamente -->
              <div id="lista-becas">
                <div class="fila-beca" style="display:flex; gap:8px; margin-bottom:8px; align-items:center;">
                  <input type="text" name="nombre_beca[]" placeholder="Nombre (ej. BENITO JUAREZ)"
                    style="flex:1; padding:8px 10px; border:1px solid #c6e9d4; border-radius:8px; font-size:14px;">
                  <input type="number" name="monto_beca[]" step="0.01" min="0.01" placeholder="Monto $"
                    style="width:110px; padding:8px 10px; border:1px solid #c6e9d4; border-radius:8px; font-size:14px;">
                  <button type="button" class="btn-quitar-beca"
                    style="width:28px; height:28px; border-radius:50%; border:none; background:#fee2e2; color:#b91c1c; font-size:16px; cursor:pointer; flex-shrink:0; display:none;">×</button>
                </div>
              </div>

              <button type="button" id="btn-otra-beca"
                style="margin-top:4px; background:none; border:1px dashed #86cfb2; color:#1f7a4e; border-radius:8px; padding:6px 12px; font-size:13px; cursor:pointer;">
                + Agregar otra beca
              </button>
            </div>
          </div>

          <button class="btn btn-principal" type="submit">Registrarse</button>
        </form>

        <!-- se ofrecio el enlace al login para usuarios que ya tienen cuenta -->
        <div class="enlaces">
          <p>Ya tienes cuenta? <a href="index.php">Iniciar sesion</a></p>
        </div>
      </article>
    </section>
  </main>

  <script>
    /* se controla la visibilidad del bloque de becas segun el checkbox */
    const checkBeca = document.getElementById('tiene_beca');
    const seccionBecas = document.getElementById('seccion-becas');
    const listaBecas = document.getElementById('lista-becas');
    const btnOtraBeca = document.getElementById('btn-otra-beca');

    checkBeca.addEventListener('change', function () {
      seccionBecas.style.display = this.checked ?
        'block' : 'none';
      /* se ajustan los required segun visibilidad */
      actualizarRequired(this.checked);
    });

    /* se agrega una nueva fila de beca al hacer clic en el boton */
    btnOtraBeca.addEventListener('click', function () {
      const fila = document.createElement('div');
      fila.className = 'fila-beca';
      fila.style.cssText = 'display:flex; gap:8px; margin-bottom:8px; align-items:center;';
      fila.innerHTML = `

        <input type="text" name="nombre_beca[]" placeholder="Nombre (ej. BENITO JUAREZ)"
          style="flex:1; padding:8px 10px; border:1px solid #c6e9d4; border-radius:8px; font-size:14px;">
        <input type="number" name="monto_beca[]" step="0.01" min="0.01" placeholder="Monto $"
          style="width:110px; padding:8px 10px; border:1px solid #c6e9d4; border-radius:8px; font-size:14px;">
        <button type="button" class="btn-quitar-beca"
          style="width:28px; height:28px; border-radius:50%; border:none; background:#fee2e2; color:#b91c1c; font-size:16px; cursor:pointer; flex-shrink:0;">×</button>

          
      `;
      listaBecas.appendChild(fila);
      /* se muestra el boton de quitar en todas las filas cuando hay mas de una */
      actualizarBotonesQuitar();
    });

    /* se elimina la fila cuando se pulsa el boton x */
    listaBecas.addEventListener('click', function (e) {
      if (e.target.classList.contains('btn-quitar-beca')) {
        e.target.closest('.fila-beca').remove();
        actualizarBotonesQuitar();
      }
    });

    function actualizarBotonesQuitar() {
      const filas = listaBecas.querySelectorAll('.fila-beca');
      filas.forEach(function (fila) {
        const btn = fila.querySelector('.btn-quitar-beca');
        btn.style.display = filas.length > 1 ? 'flex' : 'none';
      });
    }

    function actualizarRequired(activo) {
      const inputs = listaBecas.querySelectorAll('input');
      inputs.forEach(function (inp) { inp.required = activo; });
    }

    /* se verifico si el usuario tenia el tema oscuro guardado */
    if (localStorage.getItem('tema') === 'oscuro') {
      document.body.classList.add('tema-oscuro');
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

    /* ---- validación: las dos contraseñas deben coincidir ---- */
    const campoPass = document.getElementById('contraseña');
    const campoConfirm = document.getElementById('confirmar-contrasena');
    const msgNoCoincide = document.getElementById('msg-no-coincide');

    function verificarCoincidencia() {
      const coinciden = campoPass.value === campoConfirm.value;
      msgNoCoincide.style.display = campoConfirm.value.length > 0 && !coinciden ? 'block' : 'none';
      campoConfirm.setCustomValidity(coinciden ? '' : 'Las contraseñas no coinciden.');
    }

    campoPass.addEventListener('input', verificarCoincidencia);
    campoConfirm.addEventListener('input', verificarCoincidencia);

    /* se bloquea el submit si las contraseñas no coinciden */
    document.getElementById('form-registro').addEventListener('submit', function (e) {
      if (campoPass.value !== campoConfirm.value) {
        e.preventDefault();
        msgNoCoincide.style.display = 'block';
        campoConfirm.focus();
      }
    });
  </script>
</body>

</html>