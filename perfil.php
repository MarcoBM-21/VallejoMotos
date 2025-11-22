<?php
// Inicio la sesión antes de cualquier salida
session_start();

// Cargo funciones de negocio
require_once 'funciones.php';

// Verifico inactividad y redirijo si la sesión caducó
verificarInactividad();

// Si no hay usuario logueado, lo mando al login
if (empty($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

// Layout base
include_once "encabezado.php";
include_once "navbar.php";

// Datos del usuario en sesión
$nombreUsuario = $_SESSION['usuario'];
$idUsuario     = $_SESSION['idUsuario'] ?? null;

// Métricas de ventas del usuario actual
$cartas = [
    [
        "titulo" => "Total ventas",
        "icono"  => "fa fa-money-bill",
        "total"  => "S./" . obtenerTotalVentas($idUsuario),
        "color"  => "#1FB824",
    ],
    [
        "titulo" => "Ventas hoy",
        "icono"  => "fa fa-calendar-day",
        "total"  => "S./" . obtenerTotalVentasHoy($idUsuario),
        "color"  => "#D55929",
    ],
    [
        "titulo" => "Ventas semana",
        "icono"  => "fa fa-calendar-week",
        "total"  => "S./" . obtenerTotalVentasSemana($idUsuario),
        "color"  => "#4A64D5",
    ],
    [
        "titulo" => "Ventas mes",
        "icono"  => "fa fa-calendar-alt",
        "total"  => "S./" . obtenerTotalVentasMes($idUsuario),
        "color"  => "#A71D45",
    ],
];
?>

<div class="container">
    <div class="alert alert-primary text-center shadow-sm rounded text-uppercase" role="alert">
        <!-- Muestro el nombre del usuario en mayúsculas (clase Bootstrap) -->
        <h1 class="mb-0"><?= htmlspecialchars($nombreUsuario); ?></h1>
    </div>

    <?php include_once "cartas_totales.php"; ?>

    <div class="text-center mt-3">
        <a href="cambiar_password.php" class="btn btn-lg btn-primary">
            <i class="fa fa-key"></i>
            Cambiar contraseña
        </a>
    </div>
</div>

<?php
include_once "footer.php";
?>
