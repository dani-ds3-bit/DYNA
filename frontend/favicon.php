<!-- global para cargar el logo en todas las paginas -->
<link rel="icon" href="../../img/LOGO.png" type="image/png">
<script>
// Evita que el navegador muestre modulos cacheados despues de cerrar sesion usando la flecha de atras
window.onpageshow = function(event) {
    if (event.persisted) {
        window.location.reload();
    }
};
</script>