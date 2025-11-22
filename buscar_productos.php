<?php
// End-point AJAX para buscar productos (autocompletado)

require_once "funciones.php";

session_start();

// Si no hay usuario logueado, devolvemos 401 y lista vacía
if (empty($_SESSION['usuario'])) {
    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([]);
    exit();
}

// Término de búsqueda recibido por GET
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

// Siempre devolvemos JSON
header('Content-Type: application/json; charset=utf-8');

// Si no hay texto, no buscamos
if ($q === '') {
    echo json_encode([]);
    exit();
}

// Buscar y devolver resultados
try {
    $productos = buscarProductosPorTermino($q);
    echo json_encode($productos);
} catch (Exception $e) {
    // Error interno: devolvemos 500 y lista vacía
    http_response_code(500);
    echo json_encode([]);
}
