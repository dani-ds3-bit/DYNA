<?php
// verificar las credenciales
// consulta a la base de datoss
include(__DIR__ . "/../conexion.php");
session_start();

// se reviso si el usuario ya tenia una sesion activa para redirigirlo directo
if (isset($_SESSION["usuario_id"])) {
    header("Location: ../../frontend/Modulo2_ControlGastosPr/gastos.php");
    exit;
}

$mensaje_error = "";

// se verifico si el formulario fue enviado con el metodo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // echo "se envio el form";


    // se limpiaron los espacios de los datos que envio el usuario
    $correo = "";
    if (isset($_POST["correo"])) {
        $correo = trim($_POST["correo"]);
    }

    $contraseña = "";
    if (isset($_POST["contraseña"])) {
        $contraseña = trim($_POST["contraseña"]);
    }

    // se comprueba que ningun campo llegara vacio antes de consultar la base de datos
    if ($correo == "" || $contraseña == "") {
        $mensaje_error = "¨Por favor  de ingresar tu correo y contraseña.";
    } else {

        // se prepara la consulta para buscar al usuario por su correo
        $buscar = $conexion->prepare("SELECT id, nombre_completo, contraseña FROM usuarios WHERE correo = ?");
        $buscar->bind_param("s", $correo);
        $buscar->execute();
        $resultado = $buscar->get_result();
        $usuario = $resultado->fetch_assoc();
        $buscar->close();

        // se comparo la contraseña ingresada con la almacenada en la base de datos
        // print_r($usuario);

        if ($usuario && (password_verify($contraseña, $usuario["contraseña"]) || $usuario["contraseña"] === $contraseña)) {

            // se guardaron los datos del usuario 
            $_SESSION["usuario_id"] = $usuario["id"];
            $_SESSION["usuario_nombre"] = $usuario["nombre_completo"];

            // se registro el inicio de sesion en la bitacora 
            $bit = $conexion->prepare(
                "INSERT INTO bitacora (tipo_evento, descripcion, usuario_id) VALUES ('Inicio sesion', 'el usuario inicio sesion en el sistema', ?)"
            );
            $bit->bind_param("i", $usuario["id"]);
            $bit->execute();
            $bit->close();

            // se dirige al usuario a la pantalla principal de gastos
            header("Location: ../../frontend/Modulo2_ControlGastosPr/gastos.php");
            exit;
        } else {
            // aqui le muestra al usuario que las credenciales no coincidieron
            $mensaje_error = "Correo o contraseña incorrectos, favor de verificar tus credenciales.";
        }
    }
}
?>