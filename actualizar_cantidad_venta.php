<?php
// Sesión y control de acceso
session_start();
if (empty($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Validamos que vengan los datos necesarios
if (!isset($_POST['id'], $_POST['cantidad'])) {
    header("Location: vender.php");
    exit();
}

$id       = (int) $_POST['id'];
$cantidad = (int) $_POST['cantidad'];

// Cantidad mínima 1
if ($cantidad < 1) {
    $cantidad = 1;
}

// Array para guardar mensajes de error por producto
if (!isset($_SESSION['errores_cantidad'])) {
    $_SESSION['errores_cantidad'] = [];
}

// Actualizamos la cantidad en la lista de venta
if (!empty($_SESSION['lista'])) {
    foreach ($_SESSION['lista'] as $producto) {
        if ($producto->id == $id) {

            // Si intenta pasar el stock disponible
            if ($cantidad > $producto->existencia) {
                $producto->cantidad = $producto->existencia;
                $_SESSION['errores_cantidad'][$id] = "No hay más stock disponible.";
            } else {
                $producto->cantidad = $cantidad;
                // Si antes tenía error y ahora ya no, lo quitamos
                unset($_SESSION['errores_cantidad'][$id]);
            }

            break;
        }
    }
}

// Volvemos a la pantalla de venta
header("Location: vender.php");
exit();
