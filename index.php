<?php
session_start();

include_once "funciones.php";

// Cerrar sesión por inactividad si corresponde
verificarInactividad();

// Proteger la vista: solo usuarios logueados
if (empty($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

include_once "encabezado.php";
include_once "navbar.php";

// Tarjetas de resumen
$cartas = [
    [
        "titulo" => "Total ventas",
        "icono"  => "fa fa-money-bill",
        "total"  => "S./" . obtenerTotalVentas(),
        "color"  => "#A71D45"
    ],
    [
        "titulo" => "Ventas hoy",
        "icono"  => "fa fa-calendar-day",
        "total"  => "S./" . obtenerTotalVentasHoy(),
        "color"  => "#2A8D22"
    ],
    [
        "titulo" => "Ventas semana",
        "icono"  => "fa fa-calendar-week",
        "total"  => "S./" . obtenerTotalVentasSemana(),
        "color"  => "#223D8D"
    ],
    [
        "titulo" => "Ventas mes",
        "icono"  => "fa fa-calendar-alt",
        "total"  => "S./" . obtenerTotalVentasMes(),
        "color"  => "#D55929"
    ],
];

// Totales principales
$totales = [
    ["nombre" => "Total productos",      "total" => obtenerNumeroProductos(), "imagen" => "img/productos.png"],
    ["nombre" => "Ventas registradas",   "total" => obtenerNumeroVentas(),    "imagen" => "img/ventas.png"],
    ["nombre" => "Usuarios",             "total" => obtenerNumeroUsuarios(),  "imagen" => "img/usuarios.png"],
    ["nombre" => "Clientes",             "total" => obtenerNumeroClientes(),  "imagen" => "img/clientes.png"],
];

// Datos para tablas
$ventasUsuarios      = obtenerVentasPorUsuario();
$ventasClientes      = obtenerVentasPorCliente();
$productosMasVendidos = obtenerProductosMasVendidos();
?>

<div class="container mt-3">
    <!-- Tarjetas de totales -->
    <div class="card-deck row mb-2">
        <?php foreach ($totales as $total): ?>
            <div class="col-xs-12 col-sm-6 col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <img class="w-25" src="<?= $total['imagen']; ?>" alt="">
                        <h4 class="card-title">
                            <?= $total['nombre']; ?>
                        </h4>
                        <h2><?= $total['total']; ?></h2>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php include_once "cartas_totales.php"; ?>

    <br>

    <div class="row mt-2">
        <!-- Ventas por usuarios -->
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h4>Ventas por usuarios</h4>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nombre usuario</th>
                                <th>Número ventas</th>
                                <th>Total ventas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ventasUsuarios as $usuario): ?>
                                <tr>
                                    <td><?= $usuario->usuario; ?></td>
                                    <td><?= $usuario->numeroVentas; ?></td>
                                    <td>S./<?= $usuario->total; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Ventas por clientes -->
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h4>Ventas por clientes</h4>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nombre cliente</th>
                                <th>Número compras</th>
                                <th>Total ventas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ventasClientes as $cliente): ?>
                                <tr>
                                    <td><?= $cliente->cliente; ?></td>
                                    <td><?= $cliente->numeroCompras; ?></td>
                                    <td>S./<?= $cliente->total; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <br>

    <!-- Top 10 productos más vendidos -->
    <h4>10 Productos más vendidos</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Unidades vendidas</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($productosMasVendidos as $producto): ?>
                <tr>
                    <td><?= $producto->nombre; ?></td>
                    <td><?= $producto->unidades; ?></td>
                    <td>S./<?= $producto->total; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
include_once "footer.php";
?>
