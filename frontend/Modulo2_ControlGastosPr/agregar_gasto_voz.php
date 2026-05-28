<?php

// se incluyo el modulo de voz para que cargue categorias y procese el formulario
include(__DIR__ . "/../../backend/Modulo2_ControlGastosPr/agregar_gasto_voz.php");
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dyna - Gasto por Voz</title>
  <link rel="stylesheet" href="../../estilos.css">
  <style>
    /* se definen estilos  del modulo de voz */
    .zona-voz {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 20px;
      padding: 30px 0;
    }

    .boton-microfono {
      width: 56px;
      height: 56px;
      border-radius: 50%;
      background: var(--color-primario, #4f46e5);
      border: none;
      cursor: pointer;
      font-size: 1.4rem;
      color: white;
      transition: transform 0.15s, background 0.15s;
      box-shadow: 0 3px 12px rgba(79, 70, 229, 0.35);
    }

    /* se anima el boton cuando el reconocedor esta activo */
    .boton-microfono.escuchando {
      background: #d9534f;
      animation: pulso 1s infinite;
    }

    @keyframes pulso {
      0% {
        transform: scale(1);
      }

      50% {
        transform: scale(1.1);
      }

      100% {
        transform: scale(1);
      }
    }

    .texto-reconocido {
      font-size: 1.1rem;
      font-style: italic;
      color: #555;
      min-height: 30px;
      text-align: center;
    }

    .estado-voz {
      font-size: 0.9rem;
      color: #888;
    }

    /* se oculto el panel de confirmacion hasta que el reconocedor detecte algo */
    #panel-confirmacion {
      display: none;
      margin-top: 20px;
    }


    .contenedor-voz-grid {
      display: grid;
      grid-template-columns: 2fr 1fr;
    }

    .zona-principal-voz {
      padding: 30px;
    }

    .instrucciones-lateral {
      background: #fafafa;
      border-left: 1px solid var(--color-borde);
      padding: 30px;
      border-radius: 0 12px 12px 0;
    }






    /* tema oscuro */
    body.tema-oscuro .instrucciones-lateral {
      background: #181818;
    }

    @media (max-width: 768px) {
      .contenedor-voz-grid {
        grid-template-columns: 1fr;
      }

      .instrucciones-lateral {
        border-left: none;
        border-top: 1px solid var(--color-borde);
        border-radius: 0 0 12px 12px;
      }
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









      <!-- se marco gastos como activo -->
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
        <div><strong><?php echo htmlspecialchars($usuario_nombre); ?></strong></div>
      </div>
    </aside>

    <div class="cuerpo-app">
      <header class="barra-superior">
        <h2>Registro por Voz</h2>
        <div class="barra-superior-derecha">
          <span class="foto-usuario"><?php echo $iniciales; ?></span>
        </div>
      </header>

      <main class="pagina-contenido">
        <section class="pagina-encabezado">
          <div>
            <h1>registrar gasto por voz</h1>
            <p>di el nombre y el monto del gasto</p>
          </div>
          <div class="acciones">
            <a class="btn btn-plano" href="gastos.php">Volver</a>
          </div>
        </section>

        <?php if ($mensaje_error != ""): ?>
          <p style="color:#d9534f; margin-bottom:12px;"><?php echo $mensaje_error; ?></p>
        <?php endif; ?>

        <section class="panel" style="padding: 0; overflow: hidden;">
          <div class="contenedor-voz-grid">
            <div class="zona-principal-voz">
              <!-- se coloco el boton de microfono al centro para que sea el elemento principal -->
              <div class="zona-voz">
                <p class="estado-voz" id="estado-voz">Presiona el microfono y habla</p>
                <button class="boton-microfono" id="boton-mic" title="Presiona para hablar"></button>
                <p class="texto-reconocido" id="texto-reconocido">Ejemplo: <strong>camion 45</strong></p>
              </div>

              <!-- se oculto el panel hasta que el reconocedor detecte texto con nombre y monto -->
              <div id="panel-confirmacion">
                <hr style="border: 0; border-top: 1px solid var(--color-borde); margin: 20px 0;">
                <h3>Por favor confirma tus datoe</h3>
                <p class="texto-suave" style="margin-bottom:16px;">Verifica los datos antes de guardar.
                </p>

                <form action="agregar_gasto_voz.php" method="POST">
                  <div class="grupo">
                    <label for="nombre_gasto">Nombre del gasto</label>
                    <input type="text" id="nombre_gasto" name="nombre_gasto" required>
                  </div>
                  <div class="grupo">
                    <label for="monto">Monto ($)</label>
                    <input type="number" id="monto" name="monto" step="0.01" min="0.01" required>
                  </div>
                  <div class="grupo">
                    <label for="categoria_id">Categoria</label>
                    <select id="categoria_id" name="categoria_id">
                      <option value="">Selecciona categoria</option>
                      <!-- se uso data ynombre para que el JS detecte la categoria en el texto de voz -->
                      <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"
                          data-nombre="<?php echo strtolower($cat['nombre_categoria']); ?>">
                          <?php echo htmlspecialchars($cat['nombre_categoria']); ?> (Nivel
                          <?php echo $cat['nivel_prioridad']; ?>)
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <?php if ($tiene_beca): ?>
                    <div class="grupo" style="margin-top: 15px; background: #f0fdf4; padding: 15px; border-radius: 8px;">
                      <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" id="es_de_beca" name="es_de_beca" value="1"
                          style="width: 18px; height: 18px; accent-color: var(--color-primario);">
                        <label for="es_de_beca" style="margin: 0; font-weight: normal; font-size: 14px;">¿Este gasto se
                          descontara de la beca?</label>
                      </div>

                      <?php if (count($becas_usuario) > 1): ?>
                        <div id="selector-beca-container" style="display: none; margin-top: 10px;">
                          <label for="beca_id" style="font-size: 13px; color: var(--color-suave);">Selecciona de cual
                            beca:</label>
                          <select id="beca_id" name="beca_id" style="padding: 6px; font-size: 13px;">
                            <?php foreach ($becas_usuario as $beca): ?>
                              <option value="<?php echo $beca['id']; ?>">
                                <?php echo htmlspecialchars($beca['nombre_beca']); ?>
                                (disp: $<?php echo number_format(max(0, $beca['monto'] - $beca['gastado']), 2); ?>)
                              </option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                        <script>
                          document.getElementById('es_de_beca').addEventListener('change', function () {
                            document.getElementById('selector-beca-container').style.display = this.checked ? 'block' : 'none';
                          });
                        </script>
                      <?php endif; ?>
                    </div>
                  <?php endif; ?>
                  <div style="display:flex; gap:10px; margin-top:10px;">
                    <button type="submit" class="btn btn-principal">Guardar gasto</button>
                    <button type="button" class="btn btn-plano" onclick="reiniciar()">Volver a intentar</button>
                  </div>
                </form>
              </div>
            </div>

            <!-- se añadio la guia de voz en un recuadro lateral -->
            <div class="instrucciones-lateral">
              <h3>Como hablar</h3>
              <p class="texto-suave" style="margin-bottom:12px; font-size: 14px;">Di el nombre del gasto y luego el
                numero, o al reves:</p>
              <ul style="line-height: 2; padding-left: 20px; font-size: 14px; margin-bottom: 24px;">
                <li><strong>camion 45</strong></li>
                <li><strong>uniforme 325</strong></li>
                <li><strong>almuerzo 35</strong></li>
                <li><strong>libro 120</strong></li>
              </ul>
              <hr style="border: 0; border-top: 1px solid var(--color-borde); margin: 20px 0;">
              <p class="texto-suave" style="margin-top:20px; font-size: 12px;">Siempre puedes corregir el nombre, monto
                y categoria antes de
                guardar.</p>
            </div>
          </div>
        </section>
      </main>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function () {


      var botonMic = document.getElementById("boton-mic");
      var estadoVoz = document.getElementById("estado-voz");
      var textoReconocido = document.getElementById("texto-reconocido");
      var panelConfirm = document.getElementById("panel-confirmacion");

      var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

      if (!SpeechRecognition) {
        estadoVoz.textContent = "Tu navegador no soporta reconocimiento de voz. Usa Google Chrome.";
        botonMic.disabled = true;
      } else {
        var reconocedor = new SpeechRecognition();
        /* se configuro en español  */
        reconocedor.lang = "es-MX";
        reconocedor.interimResults = true;
        reconocedor.maxAlternatives = 1;

        var escuchando = false;

        botonMic.addEventListener("click", function () {
          if (escuchando) return;
          escuchando = true;
          botonMic.classList.add("escuchando");
          estadoVoz.textContent = "Escuchando... habla ahora";
          textoReconocido.textContent = "...";
          reconocedor.start();
        });

        /* se  */
        reconocedor.addEventListener("result", function (evento) {
          var textoIntermedio = "";
          var textoFinal = "";

          for (var i = evento.resultIndex; i < evento.results.length; ++i) {
            if (evento.results[i].isFinal) {
              textoFinal += evento.results[i][0].transcript;
            } else {
              textoIntermedio += evento.results[i][0].transcript;
            }
          }

          if (textoIntermedio !== "") { textoReconocido.textContent = '"' + textoIntermedio + '..."'; }
          if (textoFinal !== "") { textoReconocido.textContent = '"' + textoFinal + '"'; procesarTexto(textoFinal); }
        });

        reconocedor.addEventListener("end", function () {
          escuchando = false;
          botonMic.classList.remove("escuchando");
          estadoVoz.textContent = "Listo. Revisa los datos detectados abajo.";
        });

        reconocedor.addEventListener("error", function (evento) {
          escuchando = false;
          botonMic.classList.remove("escuchando");
          estadoVoz.textContent = "Error: " + evento.error + ". Intenta de nuevo.";
        });
      }

      /* se extrajo el monto y el nombre del texto  */
      function procesarTexto(texto) {
        texto = texto.trim().toLowerCase();

        /* se definio el mapa de palabras a numeros para interpretar montos hablados */
        var palabrasNumero = {
          "cero": 0, "uno": 1, "una": 1, "dos": 2, "tres": 3, "cuatro": 4, "cinco": 5, "seis": 6,
          "siete": 7, "ocho": 8, "nueve": 9, "diez": 10, "once": 11, "doce": 12, "trece": 13,
          "catorce": 14, "quince": 15, "veinte": 20, "treinta": 30, "cuarenta": 40,
          "cincuenta": 50, "sesenta": 60, "setenta": 70, "ochenta": 80, "noventa": 90,
          "cien": 100, "ciento": 100, "doscientos": 200, "trescientos": 300,
          "cuatrocientos": 400, "quinientos": 500
        };

        var palabras = texto.split(" ");
        var sumaPartes = 0;
        var hayPalabrasNumero = false;
        palabras.forEach(function (p) {
          if (palabrasNumero[p] !== undefined) { sumaPartes += palabrasNumero[p]; hayPalabrasNumero = true; }
        });

        /* se busco el monto primero como digitos y luego como palabras */
        var monto = 0;
        var matchMonto = texto.match(/(\d+(?:\.\d{1,2})?)/);
        if (matchMonto) { monto = parseFloat(matchMonto[1]); }
        else if (hayPalabrasNumero) { monto = sumaPartes; }





        /* se filtro el nombre eliminando los numeros y palabras numericas */
        var nombre = texto.replace(/(\d+(?:\.\d{1,2})?)/, "")
          .split(" ")
          .filter(function (p) { return palabrasNumero[p] === undefined && p !== "y" && p !== "pesos" && p !== "peso"; })
          .join(" ").trim();





        if (nombre.length > 0) { nombre = nombre.charAt(0).toUpperCase() + nombre.slice(1); }

        /* se compararon los nombres de categorias con el texto para seleccionar */
        var opciones = document.querySelectorAll("#categoria_id option[data-nombre]");
        var categoriaDetectada = "";
        opciones.forEach(function (opcion) {
          if (texto.indexOf(opcion.dataset.nombre) !== -1) { categoriaDetectada = opcion.value; }
        });
        if (categoriaDetectada === "" && opciones.length > 0) { categoriaDetectada = opciones[0].value; }

        /* se llenaron los campos del formulario con los datos extraidos del audio */
        document.getElementById("nombre_gasto").value = nombre;
        document.getElementById("monto").value = monto > 0 ? monto : "";
        document.getElementById("categoria_id").value = categoriaDetectada;

        panelConfirm.style.display = "block";
        panelConfirm.scrollIntoView({ behavior: "smooth" });
      }

      /* se reiniciaron todos los campos para permitir un nuevo intento */
      function reiniciar() {
        panelConfirm.style.display = "none";
        textoReconocido.innerHTML = 'Di por ejemplo: <strong>camion 45</strong>';
        estadoVoz.textContent = "Presiona el microfono y habla";
        document.getElementById("nombre_gasto").value = "";
        document.getElementById("monto").value = "";
        document.getElementById("categoria_id").value = "";
      }

    });
  </script>

  <script>
    /* se verifico si el usuario tenia el tema oscuro guardado para aplicarlo en todas las paginas */
    if (localStorage.getItem('tema') === 'oscuro') {
      document.body.classList.add('tema-oscuro');
    }
  </script>
</body>

</html>