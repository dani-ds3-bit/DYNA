<?php
// se incluyo el modulo de login para que el POST antes de mostrar la vista
include(__DIR__ . "/../../backend/Modulo1_Login/login.php");
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- se definio el titulo para que el navegador y los buscadores lo identifiquen -->
  <title>Dyna - Iniciar sesion</title>
  <!-- se referencio la hoja de estilos global con ruta relativa desde esta carpeta -->
  <link rel="stylesheet" href="../../estilos.css">
  <?php include(__DIR__ . "/../favicon.php"); ?>
</head>

<body>
  <!-- se uso main y section para dar semantica HTML5 a la estructura de la pagina -->
  <main class="pagina-acceso">
    <section class="caja-acceso">

      <!-- se coloco la marca y el logo en la columna izquierda -->
      <article class="columna-marca">
        <div class="logo-acceso logo-acceso-login">
          <img src="../../img/LOGO.png" alt="Logo Dyna">
        </div>
        <p>Finanzas Personales</p>
      </article>

      <!-- se coloco el formulario en la columna derecha -->
      <article class="columna-formulario">
        <h2>Iniciar sesion</h2>

        <!-- se muestra el mensaje de error solo si el backend lo genero -->
        <?php if ($mensaje_error != ""): ?>
          <p style="color:#d9534f; margin-bottom:12px;"><?php echo $mensaje_error; ?></p>
        <?php endif; ?>

        <!-- mensaje de confirmacion al eliminar la cuenta -->
        <?php if (isset($_GET['cuenta_eliminada']) && $_GET['cuenta_eliminada'] == '1'): ?>
          <p style="color:#28a745; margin-bottom:12px; font-weight:600;">
            Tu cuenta y todos tus datos han sido eliminados correctamente.
          </p>
        <?php endif; ?>

        <!-- mensaje por intentar usar un modulo sin sesion -->
        <?php if (isset($_GET['error']) && $_GET['error'] == 'sesion_expirada'): ?>
          <p
            style="color:#d9534f; margin-bottom:12px; font-weight:600; padding:10px; background:#fdf2f2; border-left:4px solid #d9534f; border-radius:4px;">
            Tu sesion expiro o fue cerrada, ingresa nuevamente.
          </p>
        <?php endif; ?>

        <!-- el action apunta al mismo archivo para que el POST lo procese el include -->
        <form action="index.php" method="POST">
          <div class="grupo">
            <label for="correo">Correo</label>
            <input id="correo" name="correo" type="email" placeholder="Escribe tu correo" required>
          </div>

          <div class="grupo" style="margin-bottom: 24px;">
            <label for="contraseña">Contraseña</label>
            <input id="contraseña" name="contraseña" type="password" placeholder="Escribe tu contraseña" required>
            <div style="text-align: right; margin-top: 8px;">
              <a href="../Modulo7_RestableContraseña/restablecer_contraseña.php"
                style="font-size: 13px; font-weight: 600; color: var(--color-primario); text-decoration: none;">¿Olvidaste
                tu contraseña?</a>
            </div>
          </div>

          <button class="btn btn-principal" type="submit">Entrar</button>

          <div style="text-align: center; margin: 15px 0; color: #64748b;">o</div>

          <!-- boton de google  -->
          <button class="btn btn-secundario" type="button" onclick="alert('Próximamente');"
            style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 10px; background-color: white; border: 1px solid #cbd5e1; color: #334155;">
            <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google"
              style="width: 20px; height: 20px;">
            Iniciar sesion con Google
          </button>
        </form>

        <!-- se ofrecio el enlace a registro para usuarios nuevos -->
        <div class="enlaces">
          <p>No tienes cuenta?, crea una con nosotros. <a href="registro.php">Crear cuenta</a></p>
        </div>
      </article>
    </section>
  </main>

  <script>
    /* se verifico si el usuario tenia el tema oscuro guardado para aplicarlo en todas las paginas */
    if (localStorage.getItem('tema') === 'oscuro') {
      document.body.classList.add('tema-oscuro');
    }
  </script>
</body>

</html>