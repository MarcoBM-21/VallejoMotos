<?php
session_start();

if (empty($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

require_once "funciones.php";

$productos = $_SESSION['lista'] ?? [];
$idUsuario = $_SESSION['idUsuario'] ?? null;
// Si no hay cliente en sesión, lo trato como NULL
$idCliente = $_SESSION['clienteVenta'] ?? null;

// Si no hay productos en la lista, regreso a vender
if (count($productos) === 0) {
    header("Location: vender.php");
    exit;
}

// Normalizo el idCliente: si viene vacío o raro, lo mando como NULL
if ($idCliente === '' || $idCliente === null || !ctype_digit((string)$idCliente)) {
    $idCliente = null;
} else {
    $idCliente = (int)$idCliente;
}

$total = calcularTotalLista($productos);

$resultado = registrarVenta($productos, $idUsuario, $idCliente, $total);

if (!$resultado) {
    echo "Error al registrar la venta";
    exit;
}

// Limpio la venta en sesión para la siguiente operación
$_SESSION['lista']         = [];
unset($_SESSION['clienteVenta']);

echo "
<script type='text/javascript'>
    alert('Venta realizada con éxito');
    window.location.href = 'vender.php';
</script>";
