<?php
session_start();

// se eliminaron todas las variables de sesion del servidor
session_unset();

// se destruyo el identificador de sesion por completo
session_destroy();

// se redirigio al usuario al login despues de cerrar su sesion
header("Location: index.php");
exit;
?>
