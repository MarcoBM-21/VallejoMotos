<?php
session_start();

include_once "funciones.php";

// Proteger la vista: solo usuarios logueados
if (empty($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

include_once "encabezado.php";
include_once "navbar.php";

$nombreProducto = $_POST['nombreProducto'] ?? null;
$productos      = obtenerProductos($nombreProducto);

$cartas = [
    ["titulo" => "No. Productos",      "icono" => "fa fa-box",            "total" => count($productos),                 "color" => "#3578FE"],
    ["titulo" => "Total productos",    "icono" => "fa fa-shopping-cart",  "total" => obtenerNumeroProductos(),          "color" => "#4F7DAF"],
    ["titulo" => "Total inventario",   "icono" => "fa fa-money-bill",     "total" => "$" . obtenerTotalInventario(),    "color" => "#D55929"],
    ["titulo" => "Ganancia",           "icono" => "fa fa-wallet",         "total" => "$" . calcularGananciaProductos(), "color" => "#1FB824"],
];

// Para activar/desactivar scroll interno de la tabla
$usarScroll = count($productos) > 10;
?>

<div class="container mt-3">
    <h1>
        <a
            class="btn btn-lg"
            style="color:#fff; background:#0d47a1;"
            href="agregar_producto.php"
        >
            <i class="fa fa-plus"></i>
            Agregar
        </a>
        Productos
    </h1>

    <br>

    <?php include_once "cartas_totales.php"; ?>

    <!-- Buscador de productos -->
    <form action="" method="post" class="input-group mb-3 mt-3">
        <input
            autofocus
            name="nombreProducto"
            type="text"
            class="form-control"
            placeholder="Escribe el nombre o código del producto que deseas buscar"
            aria-label="Nombre producto"
            aria-describedby="button-addon2"
        >
        <button
            type="submit"
            name="buscarProducto"
            class="btn btn-primary"
            id="button-addon2"
        >
            <i class="fa fa-search"></i>
            Buscar
        </button>
    </form>

    <!-- Controles para mostrar/ocultar columnas -->
    <div class="d-flex justify-content-end mb-2">
        <div class="dropdown">
            <button
                class="btn btn-outline-secondary btn-sm dropdown-toggle"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
            >
                Columnas
            </button>

            <ul class="dropdown-menu dropdown-menu-end p-2">
                <li>
                    <div class="form-check">
                        <input
                            class="form-check-input toggle-column"
                            type="checkbox"
                            value="col-codigo"
                            id="colCodigo"
                            checked
                        >
                        <label class="form-check-label" for="colCodigo">Código</label>
                    </div>
                </li>
                <li>
                    <div class="form-check">
                        <input
                            class="form-check-input toggle-column"
                            type="checkbox"
                            value="col-nombre"
                            id="colNombre"
                            checked
                        >
                        <label class="form-check-label" for="colNombre">Nombre</label>
                    </div>
                </li>
                <li>
                    <div class="form-check">
                        <input
                            class="form-check-input toggle-column"
                            type="checkbox"
                            value="col-marca"
                            id="colMarca"
                            checked
                        >
                        <label class="form-check-label" for="colMarca">Marca</label>
                    </div>
                </li>
                <li>
                    <div class="form-check">
                        <input
                            class="form-check-input toggle-column"
                            type="checkbox"
                            value="col-ubicacion"
                            id="colUbicacion"
                            checked
                        >
                        <label class="form-check-label" for="colUbicacion">Ubicación</label>
                    </div>
                </li>

                <li><hr class="dropdown-divider"></li>

                <li>
                    <div class="form-check">
                        <input
                            class="form-check-input toggle-column"
                            type="checkbox"
                            value="col-compra"
                            id="colCompra"
                            checked
                        >
                        <label class="form-check-label" for="colCompra">Precio compra</label>
                    </div>
                </li>
                <li>
                    <div class="form-check">
                        <input
                            class="form-check-input toggle-column"
                            type="checkbox"
                            value="col-venta"
                            id="colVenta"
                            checked
                        >
                        <label class="form-check-label" for="colVenta">Precio venta</label>
                    </div>
                </li>
                <li>
                    <div class="form-check">
                        <input
                            class="form-check-input toggle-column"
                            type="checkbox"
                            value="col-ganancia"
                            id="colGanancia"
                            checked
                        >
                        <label class="form-check-label" for="colGanancia">Ganancia</label>
                    </div>
                </li>
                <li>
                    <div class="form-check">
                        <input
                            class="form-check-input toggle-column"
                            type="checkbox"
                            value="col-existencia"
                            id="colExistencia"
                            checked
                        >
                        <label class="form-check-label" for="colExistencia">Existencia</label>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <div class="table-responsive">
        <?php if ($usarScroll): ?>
            <div class="overflow-auto" style="max-height: 50vh;">
        <?php endif; ?>

            <table class="table mb-0">
                <thead class="sticky-top bg-body">
                    <tr>
                        <th class="col-codigo">Código</th>
                        <th class="col-nombre">Nombre</th>
                        <th class="col-marca">Marca</th>
                        <th class="col-ubicacion">Ubicación</th>
                        <th class="col-compra">Precio compra</th>
                        <th class="col-venta">Precio venta</th>
                        <th class="col-ganancia">Ganancia</th>
                        <th class="col-existencia">Existencia</th>
                        <th>Editar</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td class="col-codigo">
                                <?= htmlspecialchars($producto->codigo); ?>
                            </td>

                            <td class="col-nombre">
                                <span
                                    class="d-inline-block w-75 text-truncate"
                                    title="<?= htmlspecialchars($producto->nombre); ?>"
                                >
                                    <?= htmlspecialchars($producto->nombre); ?>
                                </span>
                            </td>

                            <td class="col-marca">
                                <span
                                    class="d-inline-block w-75 text-truncate"
                                    title="<?= htmlspecialchars($producto->marca); ?>"
                                >
                                    <?= htmlspecialchars($producto->marca); ?>
                                </span>
                            </td>

                            <td class="col-ubicacion">
                                <span
                                    class="d-inline-block w-75 text-truncate"
                                    title="<?= htmlspecialchars($producto->ubicacion); ?>"
                                >
                                    <?= htmlspecialchars($producto->ubicacion); ?>
                                </span>
                            </td>

                            <td class="col-compra">
                                <?= '$' . $producto->compra; ?>
                            </td>

                            <td class="col-venta">
                                <?= '$' . $producto->venta; ?>
                            </td>

                            <td class="col-ganancia">
                                <?= '$' . floatval($producto->venta - $producto->compra); ?>
                            </td>

                            <td class="col-existencia">
                                <?php if ((int) $producto->existencia === 0): ?>
                                    <div>
                                        <a class="btn btn-danger">
                                            Sin Stock
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <?= $producto->existencia; ?>
                                <?php endif; ?>
                            </td>

                            <td>
                                <a
                                    class="btn"
                                    style="color:#fff; background:#f77519;"
                                    href="editar_producto.php?id=<?= $producto->id; ?>"
                                >
                                    <i class="fa fa-edit"></i>
                                </a>
                            </td>

                            <td>
                                <a
                                    class="btn btn-danger"
                                    href="eliminar_producto.php?id=<?= $producto->id; ?>"
                                    onclick="return confirmarEliminacion();"
                                >
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php if ($usarScroll): ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function confirmarEliminacion() {
        return confirm('¿Estás seguro de que deseas eliminar este producto?');
    }

    // Mostrar/ocultar columnas desde el dropdown
    document.addEventListener('DOMContentLoaded', function () {
        const checkboxes = document.querySelectorAll('.toggle-column');

        checkboxes.forEach(function (chk) {
            chk.addEventListener('change', function () {
                const colClass = chk.value; // ej. "col-nombre"
                const celdas  = document.querySelectorAll('.' + colClass);

                celdas.forEach(function (celda) {
                    if (chk.checked) {
                        celda.classList.remove('d-none');
                    } else {
                        celda.classList.add('d-none');
                    }
                });
            });
        });
    });
</script>

<?php
include_once "footer.php";
?>
