<?php
// Iniciar sesión antes de enviar cualquier salida
session_start();

// Si no hay usuario en sesión, redirigir al login
if (empty($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

// Funciones y layout común
require_once "funciones.php";
include_once "encabezado.php";
include_once "navbar.php";

// Validar id recibido por GET
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    ?>
    <div class="container mt-3">
        <div class="alert alert-danger" role="alert">
            No se ha seleccionado un producto válido.
        </div>
    </div>
    <?php
    include_once "footer.php";
    exit;
}

// Obtener datos del producto
$producto = obtenerProductoPorId($id);
if (!$producto) {
    ?>
    <div class="container mt-3">
        <div class="alert alert-danger" role="alert">
            El producto solicitado no existe.
        </div>
    </div>
    <?php
    include_once "footer.php";
    exit;
}
?>

<div class="container">
    <br>
    <h3 class="mb-4">Editar producto</h3>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="post">
                <!-- Código de barras -->
                <div class="mb-3">
                    <label for="codigo" class="form-label">
                        Código de barras (Máx. 20 caracteres alfanuméricos y guiones)
                    </label>
                    <input
                        type="text"
                        name="codigo"
                        id="codigo"
                        class="form-control"
                        placeholder="Escribe el código del producto"
                        maxlength="20"
                        pattern="^[A-Za-z0-9\-]{1,20}$"
                        title="El código puede tener hasta 20 caracteres, letras, números y guiones."
                        value="<?= htmlspecialchars($producto->codigo); ?>"
                    >
                </div>


                <!-- Nombre y Marca -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="nombre" class="form-label">Nombre o descripción</label>
                        <input
                            type="text"
                            name="nombre"
                            id="nombre"
                            class="form-control"
                            placeholder="Ej. Casco integral"
                            value="<?= htmlspecialchars($producto->nombre); ?>"
                        >
                    </div>
                    <div class="col-md-6">
                        <label for="marca" class="form-label">Marca</label>
                        <input
                            type="text"
                            name="marca"
                            id="marca"
                            class="form-control"
                            placeholder="Ej. Yamaha, LS2, etc."
                            value="<?= htmlspecialchars($producto->marca ?? ''); ?>"
                        >
                    </div>
                </div>

                <!-- Ubicación y existencia -->
                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label for="ubicacion" class="form-label">Ubicación en la tienda</label>
                        <input
                            type="text"
                            name="ubicacion"
                            id="ubicacion"
                            class="form-control"
                            placeholder="Ej. Pasillo 3, Estante B"
                            maxlength="100"
                            value="<?= htmlspecialchars($producto->ubicacion ?? ''); ?>"
                        >
                    </div>
                    <div class="col-md-4">
                        <label for="existencia" class="form-label">Existencia</label>
                        <input
                            type="number"
                            name="existencia"
                            id="existencia"
                            class="form-control"
                            placeholder="Existencia"
                            min="0"
                            oninput="this.value = Math.abs(this.value)"
                            value="<?= htmlspecialchars($producto->existencia); ?>"
                        >
                    </div>
                </div>

                <!-- Precio compra y venta -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="compra" class="form-label">Precio compra</label>
                        <input
                            type="number"
                            name="compra"
                            id="compra"
                            class="form-control"
                            placeholder="Precio de compra"
                            step="any"
                            min="0"
                            oninput="this.value = Math.abs(this.value)"
                            value="<?= htmlspecialchars($producto->compra); ?>"
                        >
                    </div>
                    <div class="col-md-6">
                        <label for="venta" class="form-label">Precio venta</label>
                        <input
                            type="number"
                            name="venta"
                            id="venta"
                            class="form-control"
                            placeholder="Precio de venta"
                            step="any"
                            min="0"
                            oninput="this.value = Math.abs(this.value)"
                            value="<?= htmlspecialchars($producto->venta); ?>"
                        >
                    </div>
                </div>

                <div class="text-center mt-4">
                    <input
                        type="submit"
                        name="registrar"
                        value="Guardar cambios"
                        class="btn btn-primary btn-lg me-2"
                    >
                    <a class="btn btn-danger btn-lg" href="productos.php">
                        <i class="fa fa-times"></i>
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Procesar actualización al enviar el formulario
if (isset($_POST['registrar'])) {
    $codigo     = trim($_POST['codigo']     ?? '');
    $nombre     = trim($_POST['nombre']     ?? '');
    $marca      = trim($_POST['marca']      ?? '');
    $ubicacion  = trim($_POST['ubicacion']  ?? '');
    $compra     = $_POST['compra']          ?? '';
    $venta      = $_POST['venta']           ?? '';
    $existencia = $_POST['existencia']      ?? '';

    // Validar campos obligatorios
    if (
        $codigo     === '' ||
        $nombre     === '' ||
        $marca      === '' ||
        $ubicacion  === '' ||
        $compra     === '' ||
        $venta      === '' ||
        $existencia === ''
    ) {
        echo '
        <div class="container mt-3">
            <div class="alert alert-danger" role="alert">
                Debes completar todos los datos (incluyendo marca y ubicación).
            </div>
        </div>';
        include_once "footer.php";
        exit;
    }

    // Código: hasta 20 caracteres, letras, números y guiones
    if (!preg_match('/^[A-Za-z0-9\-]{1,20}$/', $codigo)) {
        echo '
        <div class="alert alert-danger mt-3" role="alert">
            El código debe tener como máximo 20 caracteres y solo puede contener letras, números y guiones.
        </div>';
        return;
    }

    // (Opcional pero recomendable) validar que no se repita el código en otro producto
    $otroProducto = obtenerProductoPorCodigo($codigo);
    if ($otroProducto && $otroProducto->id != $id) {
        echo '
        <div class="container mt-3">
            <div class="alert alert-danger" role="alert">
                Ya existe otro producto con ese código de barras.
            </div>
        </div>';
        include_once "footer.php";
        exit;
    }

    // Actualizar producto
    $resultado = editarProducto(
        $codigo,
        $nombre,
        $marca,
        $compra,
        $venta,
        $existencia,
        $ubicacion,
        $id
    );

    if ($resultado) {
        echo '
        <div class="container mt-3">
            <div class="alert alert-success" role="alert">
                Información del producto actualizada con éxito.
            </div>
        </div>';
    }
}

include_once "footer.php";
?>
