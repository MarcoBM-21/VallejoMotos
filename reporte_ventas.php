<?php
session_start();

include_once "funciones.php";

// Proteger la vista: solo usuarios logueados
if (empty($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

// Validar búsqueda por rango de fechas
if (isset($_POST['buscar']) &&
    (empty($_POST['inicio']) || empty($_POST['fin']))) {
    header("Location: reporte_ventas.php");
    exit;
}

// Validar búsqueda por usuario
if (isset($_POST['buscarPorUsuario']) &&
    empty($_POST['idUsuario'])) {
    header("Location: reporte_ventas.php");
    exit;
}

// Validar búsqueda por cliente
if (isset($_POST['buscarPorCliente']) &&
    empty($_POST['idCliente'])) {
    header("Location: reporte_ventas.php");
    exit;
}

// Parámetros de filtro
$fechaInicio = $_POST['inicio']    ?? null;
$fechaFin    = $_POST['fin']       ?? null;
$usuario     = $_POST['idUsuario'] ?? null;
$cliente     = $_POST['idCliente'] ?? null;

// Datos principales del reporte
$ventas = obtenerVentas($fechaInicio, $fechaFin, $cliente, $usuario);

$cartas = [
    [
        "titulo" => "N° Ventas",
        "icono"  => "fa fa-shopping-cart",
        "total"  => count($ventas),
        "color"  => "#A71D45"
    ],
    [
        "titulo" => "Total Ventas",
        "icono"  => "fa fa-money-bill",
        "total"  => "S./" . calcularTotalVentas($ventas),
        "color"  => "#2A8D22"
    ],
    [
        "titulo" => "Prod. Vendidos",
        "icono"  => "fa fa-box",
        "total"  => calcularProductosVendidos($ventas),
        "color"  => "#223D8D"
    ],
    [
        "titulo" => "Ganancia",
        "icono"  => "fa fa-wallet",
        "total"  => "S./" . obtenerGananciaVentas($ventas),
        "color"  => "#D55929"
    ],
];

$clientes = obtenerClientes();
$usuarios = obtenerUsuarios();

include_once "encabezado.php";
include_once "navbar.php";
?>

<div class="container">
    <br>

    <h2>
        Reporte de ventas :
        <?php
        if (empty($fechaInicio)) {
            echo HOY;
        }
        if (isset($fechaInicio, $fechaFin) && $fechaInicio && $fechaFin) {
            echo $fechaInicio . " al " . $fechaFin;
        }
        ?>
    </h2>

    <!-- Filtro por rango de fechas -->
    <form class="row mb-3" method="post">
        <div class="col-5">
            <label for="inicio" class="form-label">Fecha búsqueda inicial</label>
            <input
                type="date"
                name="inicio"
                class="form-control"
                id="inicio"
            >
        </div>

        <div class="col-5">
            <label for="fin" class="form-label">Fecha búsqueda final</label>
            <input
                type="date"
                name="fin"
                class="form-control"
                id="fin"
            >
        </div>

        <div class="col">
            <input
                type="submit"
                name="buscar"
                value="Buscar"
                class="btn btn-primary mt-4"
            >
        </div>
    </form>

    <!-- Filtro por usuario -->
    <div class="row mb-2">
        <div class="col">
            <form action="" method="post" class="row">
                <div class="col-6">
                    <select
                        class="form-select"
                        aria-label="Selecciona un usuario"
                        name="idUsuario"
                    >
                        <option selected value="">Selecciona un usuario</option>
                        <?php foreach ($usuarios as $u): ?>
                            <option value="<?= $u->id; ?>">
                                <?= $u->usuario; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-3 col-md-2 mt-2 mt-md-0">
                    <input
                        type="submit"
                        name="buscarPorUsuario"
                        value="Buscar"
                        class="btn btn-secondary w-100"
                    >
                </div>
            </form>
        </div>

        <!-- Filtro por cliente -->
        <div class="col">
            <form action="" method="post" class="row">
                <div class="col-6">
                    <select
                        class="form-select"
                        aria-label="Selecciona un cliente"
                        name="idCliente"
                    >
                        <option selected value="">Selecciona un cliente</option>
                        <?php foreach ($clientes as $c): ?>
                            <option value="<?= $c->id; ?>">
                                <?= $c->nombre; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-3 col-md-2 mt-2 mt-md-0">
                    <input
                        type="submit"
                        name="buscarPorCliente"
                        value="Buscar"
                        class="btn btn-secondary w-100"
                    >
                </div>
            </form>
        </div>
    </div>

    <?php include_once "cartas_totales.php"; ?>

    <br>

    <?php if (count($ventas) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Usuario</th>
                    <th>Productos</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventas as $venta): ?>
                    <tr>
                        <td><?= $venta->id; ?></td>
                        <td><?= $venta->fecha; ?></td>
                        <td><?= $venta->cliente; ?></td>
                        <td>$<?= $venta->total; ?></td>
                        <td><?= $venta->usuario; ?></td>
                        <td>
                            <table class="table mb-0">
                                <tbody>
                                    <?php foreach ($venta->productos as $producto): ?>
                                        <tr>
                                            <td><?= $producto->nombre; ?></td>
                                            <td><?= $producto->cantidad; ?></td>
                                            <td>X</td>
                                            <td>$<?= $producto->precio; ?></td>
                                            <th>$<?= $producto->cantidad * $producto->precio; ?></th>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning mt-3" role="alert">
            <h1>No se han encontrado ventas</h1>
        </div>
    <?php endif; ?>
</div>

<?php
include_once "footer.php";
?>
