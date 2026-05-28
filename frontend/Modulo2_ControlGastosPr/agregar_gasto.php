<?php
//  modulo que procesa el formulario 
include(__DIR__ . "/../../backend/Modulo2_ControlGastosPr/agregar_gasto.php");
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dyna - <?php echo $es_edicion ? "Editar" : "Agregar"; ?> gasto</title>
  <link rel="stylesheet" href="../../estilos.css">
  <?php include(__DIR__ . "/../favicon.php"); ?>
</head>

<body>
  <div class="diseño-app">

    <!-- se replico la barra lateral con gastos marcado  activo -->
    <aside class="barra-lateral">
      <div class="barra-lateral-marca">
        <div class="logo-menu-wrap">
          <img src="../../img/LOGO.png" alt="Logo Dyna">
        </div>
        <h2>Dyna</h2>
        <small>Finanzas personales</small>
      </div>

      <nav class="menu-links">
        <a class="menu-link activo" href="gastos.php"><span class="icono-menu">G</span>Gastos</a>
        <a class="menu-link" href="../Modulo3_GestionMetas/metas.php"><span class="icono-menu">M</span>Metas</a>
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
        <h2><?php echo $es_edicion ? "Editar gasto" : "Agregar gasto"; ?></h2>
        <div class="barra-superior-derecha">
          <span class="foto-usuario"><?php echo $iniciales; ?></span>
        </div>
      </header>

      <main class="pagina-contenido">
        <section class="pagina-encabezado">
          <div>
            <h1><?php echo $es_edicion ? "Editar gasto" : "Agregar gasto"; ?></h1>
            <p><?php echo $es_edicion ? "Modifica los datos del gasto seleccionado." : "Registra un nuevo gasto en tu cuenta."; ?></p>
          </div>
          <div class="acciones">
            <!--  boton volver para regresar sin guardar -->
            <a class="btn btn-plano" href="gastos.php">Volver</a>
          </div>
        </section>

        <section class="panel">
          <!-- se muestra el error si  no se pudo guardar el gasto -->
          <?php if ($mensaje_error != ""): ?>
            <p style="color:#d9534f; margin-bottom:12px;"><?php echo $mensaje_error; ?></p>
          <?php endif; ?>

          <!-- se verifico si hay categorias antes de mostrar el formulario -->
          <?php if (count($categorias) == 0): ?>
            <p class="texto-suave" style="margin-bottom: 12px;">
              No hay categorias registradas aun. Antes de agregar gastos necesitas crear al menos una categoria en
              phpMyAdmin:
              tabla <strong>categorias</strong>, columnas: <em>nombre_categoria</em>, <em>nivel_prioridad</em> (1, 2 o 3).
            </p>
          <?php else: ?>
      
            <form action="agregar_gasto.php" method="POST" style="margin-top: 10px;">
          
              <input type="hidden" name="id_gasto" value="<?php echo $id_gasto; ?>">

              <div class="grupo">
                <label for="nombre_gasto">Nombre del gasto</label>
                <input id="nombre_gasto" name="nombre_gasto" type="text"
                  placeholder="Ej. Camion, Almuerzo"
                  value="<?php echo htmlspecialchars($nombre_gasto_val); ?>" required>
              </div>

              <div class="grupo">
                <label for="monto">Monto ($)</label>
                <input id="monto" name="monto" type="number" step="0.01" min="0.01"
                  placeholder="Ej. 45.00"
                  value="<?php echo htmlspecialchars($monto_val); ?>" required>
              </div>

              <div class="grupo">
                <label for="categoria_id">Categoria</label>
                <select id="categoria_id" name="categoria_id" required>
                  <option value="">Selecciona una categoria</option>
                  <!-- se iteraron las categorias -->
                  <?php foreach ($categorias as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>"
                      <?php if ($cat['id'] == $categoria_id_val) echo "selected"; ?>>
                      <?php echo htmlspecialchars($cat['nombre_categoria']); ?> (Nivel
                      <?php echo $cat['nivel_prioridad']; ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- se muestra el selector de beca solo si el usuario tiene becas registradas -->
              <?php if ($tiene_beca): ?>
                <div class="grupo" style="margin-top: 15px; background: #f0fdf4; padding: 15px; border-radius: 8px; border: 1px solid #c6e9d4;">
                  <div style="display: flex; align-items: center; gap: 10px;">
                    <input type="checkbox" id="es_de_beca" name="es_de_beca" value="1" 
                      <?php if ($beca_id_val > 0) echo 'checked'; ?>
                      style="width: 18px; height: 18px; accent-color: var(--color-primario);">
                    <label for="es_de_beca" style="margin: 0; font-weight: normal; font-size: 14px; color: #1a5c38;">¿Este gasto se descontara de la beca?</label>
                  </div>

                  <?php if (count($becas_usuario) > 1): ?>
                    <div id="selector-beca-container" style="display: <?php echo ($beca_id_val > 0) ? 'block' : 'none'; ?>; margin-top: 10px;">
                      <label for="beca_id" style="font-size: 13px; color: var(--color-suave);">Selecciona de cual beca:</label>
                      <select id="beca_id" name="beca_id" style="padding: 6px; font-size: 13px;">
                        <?php foreach ($becas_usuario as $beca): ?>
                          <?php
                          // se calcula el porcentaje consumido de cada beca para mostrarlo
                          $pct_beca = 0;
                          if ($beca['monto'] > 0) {
                              $pct_beca = min(100, round(($beca['gastado'] / $beca['monto']) * 100));
                          }
                          $restante = max(0, $beca['monto'] - $beca['gastado']);
                          ?>
                          <option value="<?php echo $beca['id']; ?>"
                            <?php if ($beca['id'] == $beca_id_val) echo "selected"; ?>>
                            <?php echo htmlspecialchars($beca['nombre_beca']); ?> 
                            (disp: $<?php echo number_format($restante, 2); ?> — <?php echo $pct_beca; ?>% usado)
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <script>
                      document.getElementById('es_de_beca').addEventListener('change', function() {
                        document.getElementById('selector-beca-container').style.display = this.checked ? 'block' : 'none';
                      });
                    </script>
                  <?php endif; ?>
                </div>
              <?php endif; ?>

              <button class="btn btn-principal" type="submit">
                <?php echo $es_edicion ? "Guardar cambios" : "Guardar gasto"; ?>
              </button>
            </form>
          <?php endif; ?>
        </section>
      </main>
    </div>
  </div>

  <script>
    /* se verifico si el usuario tenia el tema oscuro guardado para aplicarlo en todo*/
    if (localStorage.getItem('tema') === 'oscuro') {
      document.body.classList.add('tema-oscuro');
    }
  </script>
</body>

</html>