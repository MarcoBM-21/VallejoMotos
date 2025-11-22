<?php
session_start();
include_once "funciones.php";

// Agregar producto al carrito de venta
if (!isset($_POST['agregar'])) {
    header("Location: vender.php");
    exit;
}

// Validar que venga el código
if (!isset($_POST['codigo'])) {
    $_SESSION['mensaje_error'] = "Debes ingresar un código de producto.";
    header("Location: vender.php");
    exit;
}

$codigo = trim($_POST['codigo']);

// Validar formato de código (6 dígitos numéricos)
if (!ctype_digit($codigo) || strlen($codigo) !== 6) {
    $_SESSION['mensaje_error'] = "El código debe tener exactamente 6 dígitos.";
    header("Location: vender.php");
    exit;
}

// Buscar producto por código
$producto = obtenerProductoPorCodigo($codigo);

if (!$producto) {
    $_SESSION['mensaje_error'] = "No se ha encontrado el producto.";
    header("Location: vender.php");
    exit;
}

// Validar stock
if ($producto->existencia <= 0) {
    $_SESSION['mensaje_error'] = "El producto '{$producto->nombre}' se encuentra sin stock.";
    header("Location: vender.php");
    exit;
}

// Asegurar cantidad inicial
$producto->cantidad = 1;

// Inicializar lista si no existe
if (!isset($_SESSION['lista']) || !is_array($_SESSION['lista'])) {
    $_SESSION['lista'] = [];
}

// Agregar o incrementar en la lista
$_SESSION['lista'] = agregarProductoALista($producto, $_SESSION['lista']);

header("Location: vender.php");
exit;

