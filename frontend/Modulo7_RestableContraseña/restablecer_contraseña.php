<?php
// se incluyo el modulo que procesa el POST y valida el correo
include(__DIR__ . "/../../backend/Modulo7_RestableContraseña/restablecer_contraseña.php");
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dyna - Restablecer contraseña</title>
  <link rel="stylesheet" href="../../estilos.css">
  <?php include(__DIR__ . "/../favicon.php"); ?>
</head>

<body>
  <main class="pagina-acceso">
    <section class="caja-acceso">
      <article class="columna-marca">
        <div class="logo-acceso">
          <img src="../../img/LOGO.png" alt="Logo Dyna">
        </div>
        <h1>Dyna</h1>
      </article>

      <article class="columna-formulario">
        <h2>Recuperar cuenta</h2>
        <p class="texto-suave" style="margin-bottom: 20px;">
          Ingresa tu correo electronico y tu nueva contraseña para actualizar el acceso a tu cuenta.
        </p>

        <!-- se muestra el error si el backend detecto datos invalidos -->
        <?php if ($mensaje_error != ""): ?>
          <p style="color:#d9534f; margin-bottom:12px; font-weight:bold;"><?php echo $mensaje_error; ?></p>
        <?php endif; ?>

        <!-- se muestra el mensaje de confirmacion -->
        <?php if ($mensaje_ok != ""): ?>
          <p style="color:#28a745; margin-bottom:12px; font-weight:bold;"><?php echo $mensaje_ok; ?></p>
        <?php endif; ?>

        <form action="restablecer_contraseña.php" method="POST">
          <div class="grupo">
            <label for="correo">Correo electronico</label>
            <input id="correo" name="correo" type="email" placeholder="Escribe tu correo" required>
          </div>

          <div class="grupo">
            <label for="nueva_contraseña">Nueva contraseña</label>
            <input id="nueva_contraseña" name="nueva_contraseña" type="password"
              placeholder="Escribe la nueva contraseña" required>
          </div>

          <div class="grupo">
            <label for="confirmar_contraseña">Confirmar contraseña</label>
            <input id="confirmar_contraseña" name="confirmar_contraseña" type="password"
              placeholder="Vuelve a escribir la contraseña" required>
          </div>

          <button class="btn btn-principal" type="submit">Actualizar contraseña</button>
        </form>

        <div class="enlaces" style="margin-top: 20px;">
          <p><a href="../Modulo1_Login/index.php">Volver al inicio de sesion</a></p>
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