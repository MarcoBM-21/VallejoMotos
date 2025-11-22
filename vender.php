<?php
session_start();

include_once "funciones.php";

// Proteger la vista: solo usuarios logueados
if (empty($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

// Siempre tener la lista inicializada como array
if (!isset($_SESSION['lista']) || !is_array($_SESSION['lista'])) {
    $_SESSION['lista'] = [];
}

$total              = calcularTotalLista($_SESSION['lista']);
$clientes           = obtenerClientes();
$clienteSeleccionado = isset($_SESSION['clienteVenta'])
    ? obtenerClientePorId($_SESSION['clienteVenta'])
    : null;

// Errores al actualizar cantidad (se limpian después de leer)
$erroresCantidad = $_SESSION['errores_cantidad'] ?? [];
unset($_SESSION['errores_cantidad']);

include_once "encabezado.php";
include_once "navbar.php";
?>

<div class="container mt-3">
    <!-- Buscador con autocompletado -->
    <form action="agregar_producto_venta.php" method="post" class="row position-relative">
        <div class="col-10 position-relative">
            <!-- input visible -->
            <input
                class="form-control form-control-lg"
                id="buscadorProducto"
                type="text"
                placeholder="Código o nombre del producto"
                autocomplete="off"
            >

            <!-- contenedor de sugerencias -->
            <div
                id="sugerenciasProductos"
                class="list-group position-absolute w-100"
                style="z-index: 1000;"
            ></div>

            <!-- input real que se envía al backend -->
            <input type="hidden" name="codigo" id="codigo">
        </div>

        <div class="col">
            <input
                type="submit"
                value="Agregar"
                name="agregar"
                class="btn btn-success mt-2"
            >
        </div>
    </form>

    <?php if (isset($_SESSION['mensaje_error'])): ?>
        <div class="alert alert-danger mt-3" role="alert">
            <?= $_SESSION['mensaje_error']; ?>
        </div>
        <?php unset($_SESSION['mensaje_error']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['lista'])): ?>
        <div class="mt-3">
            <table class="table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th>Quitar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['lista'] as $lista): ?>
                        <tr>
                            <td><?= $lista->codigo; ?></td>
                            <td><?= $lista->nombre; ?></td>
                            <td>$<?= $lista->venta; ?></td>
                            <td>
                                <!-- Form para actualizar cantidad de este producto -->
                                <form
                                    action="actualizar_cantidad_venta.php"
                                    method="post"
                                    class="d-flex align-items-center"
                                >
                                    <!-- id del producto en la lista -->
                                    <input type="hidden" name="id" value="<?= $lista->id; ?>">

                                    <!-- campo editable de cantidad -->
                                    <input
                                        type="number"
                                        name="cantidad"
                                        class="form-control form-control-sm text-center"
                                        value="<?= $lista->cantidad; ?>"
                                        min="1"
                                        max="<?= $lista->existencia; ?>"
                                        style="max-width: 80px;"
                                        onchange="this.form.submit()"
                                    >

                                    <?php if (isset($erroresCantidad[$lista->id])): ?>
                                        <small class="text-danger ms-2">
                                            <?= $erroresCantidad[$lista->id]; ?>
                                        </small>
                                    <?php endif; ?>
                                </form>
                            </td>
                            <td>$<?= floatval($lista->cantidad * $lista->venta); ?></td>
                            <td>
                                <a
                                    href="quitar_producto_venta.php?id=<?= $lista->id; ?>"
                                    class="btn btn-danger"
                                >
                                    <i class="fa fa-times"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Selección de cliente -->
            <form class="row" method="post" action="establecer_cliente_venta.php">
                <div class="col-8">
                    <select
                        class="form-select"
                        aria-label="Selecciona el cliente"
                        name="idCliente"
                    >
                        <option selected value="">Selecciona el cliente</option>
                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?= $cliente->id; ?>">
                                <?= $cliente->nombre; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <input
                        class="btn btn-info"
                        type="submit"
                        value="Seleccionar cliente"
                    >
                </div>
            </form>

            <!-- Cliente seleccionado -->
            <?php if ($clienteSeleccionado): ?>
                <div class="alert alert-primary mt-3" role="alert">
                    <b>Cliente seleccionado:</b><br>
                    <b>Nombre: </b> <?= $clienteSeleccionado->nombre; ?><br>
                    <b>Teléfono: </b> <?= $clienteSeleccionado->telefono; ?><br>
                    <b>Dirección: </b> <?= $clienteSeleccionado->direccion; ?><br>
                    <a href="quitar_cliente_venta.php" class="btn btn-warning">Quitar</a>
                </div>
            <?php endif; ?>

            <!-- Total y acciones -->
            <div class="text-center mt-3">
                <h1>Total: S/.<?= $total; ?></h1>

                <a class="btn btn-primary btn-lg" href="registrar_venta.php">
                    <i class="fa fa-check"></i>
                    Terminar venta
                </a>

                <a class="btn btn-danger btn-lg" href="cancelar_venta.php">
                    <i class="fa fa-times"></i>
                    Cancelar
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const inputBuscador = document.getElementById('buscadorProducto');
    const inputCodigo   = document.getElementById('codigo');
    const contenedorSug = document.getElementById('sugerenciasProductos');
    const form          = inputBuscador.closest('form');

    let timeout = null;

    // Busco productos mientras el usuario escribe
    inputBuscador.addEventListener('input', () => {
        const termino = inputBuscador.value.trim();

        if (termino.length < 2) {
            contenedorSug.innerHTML = '';
            return;
        }

        clearTimeout(timeout);
        timeout = setTimeout(() => {
            buscarProductos(termino);
        }, 250);
    });

    async function buscarProductos(termino) {
        try {
            const resp = await fetch('buscar_productos.php?q=' + encodeURIComponent(termino));
            if (!resp.ok) return;

            const productos = await resp.json();
            contenedorSug.innerHTML = '';

            if (!productos.length) {
                return;
            }

            productos.forEach(prod => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'list-group-item list-group-item-action';
                btn.dataset.codigo = prod.codigo;
                btn.textContent = `${prod.nombre} (Código: ${prod.codigo})`;

                btn.addEventListener('click', () => {
                    inputBuscador.value = prod.nombre; // visible
                    inputCodigo.value   = prod.codigo; // real que va al backend
                    contenedorSug.innerHTML = '';
                });

                contenedorSug.appendChild(btn);
            });
        } catch (e) {
            console.error('Error buscando productos', e);
        }
    }

    // Antes de enviar, me aseguro de tener un código válido
    form.addEventListener('submit', (e) => {
        if (!inputCodigo.value) {
            const valor = inputBuscador.value.trim();

            // Si escribió manualmente un código de 6 dígitos, lo acepto
            if (/^\d{6}$/.test(valor)) {
                inputCodigo.value = valor;
            } else {
                e.preventDefault();
                alert('Selecciona un producto de la lista o escribe un código de barras válido.');
            }
        }
    });
});
</script>

<?php
include_once "footer.php";
?>
