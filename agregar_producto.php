<?php
// Sesión y control de acceso
session_start();
if (empty($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Funciones y acceso a BD
require_once "funciones.php";

// Layout base
include_once "encabezado.php";
include_once "navbar.php";
?>

<div class="container">
    <br>
    <h3 class="mb-4">Agregar producto</h3>

    <div class="card shadow-sm">
        <div class="card-body">
            <!-- Formulario de alta de producto -->
            <form method="post">
                <!-- Código de barras (fila completa) -->
                <div class="mb-3">
                    <label for="codigo" class="form-label">
                        Código de barras (Max. 12 caracteres)
                    </label>
                    <input
                        type="text"
                        name="codigo"
                        class="form-control"
                        id="codigo"
                        placeholder="Escribe el código del producto"
                        maxlength="12"
                        pattern="^[A-Za-z0-9\-]{1,12}$"
                        title="Máximo 12 caracteres. Solo letras, números y guiones."
                    >
                </div>

                <!-- Nombre y Marca en la misma fila -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="nombre" class="form-label">Nombre o descripción</label>
                        <input
                            type="text"
                            name="nombre"
                            class="form-control"
                            id="nombre"
                            placeholder="Ej. Casco integral"
                        >
                    </div>
                    <div class="col-md-6">
                        <label for="marca" class="form-label">Marca</label>
                        <input
                            type="text"
                            name="marca"
                            class="form-control"
                            id="marca"
                            placeholder="Ej. Yamaha, LS2, etc."
                        >
                    </div>
                </div>

                <!-- Ubicación y Existencia en la misma fila -->
                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label for="ubicacion" class="form-label">Ubicación en la tienda</label>
                        <input
                            type="text"
                            name="ubicacion"
                            class="form-control"
                            id="ubicacion"
                            placeholder="Ej. Pasillo 3, Estante B"
                            maxlength="100"
                        >
                    </div>
                    <div class="col-md-4">
                        <label for="existencia" class="form-label">Existencia</label>
                        <input
                            type="number"
                            name="existencia"
                            step="any"
                            id="existencia"
                            class="form-control"
                            placeholder="Existencia"
                            min="0"
                            oninput="this.value = Math.abs(this.value)"
                        >
                    </div>
                </div>

                <!-- Precio compra y Precio venta -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="compra" class="form-label">Precio compra</label>
                        <input
                            type="number"
                            name="compra"
                            step="any"
                            id="compra"
                            class="form-control"
                            placeholder="Precio de compra"
                            min="0"
                            oninput="this.value = Math.abs(this.value)"
                        >
                    </div>
                    <div class="col-md-6">
                        <label for="venta" class="form-label">Precio venta</label>
                        <input
                            type="number"
                            name="venta"
                            step="any"
                            id="venta"
                            class="form-control"
                            placeholder="Precio de venta"
                            min="0"
                            oninput="this.value = Math.abs(this.value)"
                        >
                    </div>
                </div>

                <div class="text-center mt-4">
                    <input
                        type="submit"
                        name="registrar"
                        value="Registrar"
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
// Manejo del POST del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar'])) {
    $codigo     = $_POST['codigo']     ?? '';
    $nombre     = $_POST['nombre']     ?? '';
    $marca      = $_POST['marca']      ?? '';
    $ubicacion  = $_POST['ubicacion']  ?? '';
    $compra     = $_POST['compra']     ?? '';
    $venta      = $_POST['venta']      ?? '';
    $existencia = $_POST['existencia'] ?? '';

    // Validar campos obligatorios
    if (
        empty($codigo)    ||
        empty($nombre)    ||
        empty($marca)     ||
        empty($ubicacion) ||
        empty($compra)    ||
        empty($venta)     ||
        empty($existencia)
    ) {
        echo '<div class="alert alert-danger mt-3" role="alert">
                Debes completar todos los datos (incluyendo marca y ubicación).
              </div>';
        return;
    }

    // Código: hasta 12 caracteres, letras, números y guiones
    if (!preg_match('/^[A-Za-z0-9\-]{1,12}$/', $codigo)) {
        echo '
        <div class="alert alert-danger mt-3" role="alert">
            El código debe tener como máximo 12 caracteres y solo puede contener letras, números y guiones.
        </div>';
        return;
    }

    // Verificar que el código no esté repetido
    $productoExistente = obtenerProductoPorCodigo($codigo);
    if ($productoExistente) {
        echo '<div class="alert alert-danger mt-3" role="alert">
                El código de barras ya existe en el sistema. Por favor, ingresa uno diferente.
              </div>';
        return;
    }

    // Registrar el producto
    $resultado = registrarProducto(
        $codigo,
        $nombre,
        $marca,
        $compra,
        $venta,
        $existencia,
        $ubicacion
    );

    if ($resultado) {
        echo '<div class="alert alert-success mt-3" role="alert">
                Producto registrado con éxito.
              </div>';
    }
}

include_once "footer.php";
?>
