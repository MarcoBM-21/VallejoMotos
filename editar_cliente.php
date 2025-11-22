<?php
// Sesión y acceso
session_start();
if (empty($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

// Funciones
require_once "funciones.php";

// Mensaje para alertas
$mensaje = null;

// ID del cliente a editar
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$cliente = null;

if ($id <= 0) {
    $mensaje = [
        'tipo'  => 'danger',
        'texto' => 'No se ha seleccionado el cliente.'
    ];
} else {
    $cliente = obtenerClientePorId($id);
    if (!$cliente) {
        $mensaje = [
            'tipo'  => 'danger',
            'texto' => 'Cliente no encontrado.'
        ];
    }
}

// Actualizar datos del cliente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar']) && $cliente) {
    $nombre    = trim($_POST['nombre']    ?? '');
    $telefono  = trim($_POST['telefono']  ?? '');
    $direccion = trim($_POST['direccion'] ?? '');

    // Campos obligatorios
    if ($nombre === '' || $telefono === '' || $direccion === '') {
        $mensaje = [
            'tipo'  => 'danger',
            'texto' => 'Debes completar todos los datos.'
        ];
    }
    // Nombre solo letras y espacios
    elseif (!preg_match('/^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/', $nombre)) {
        $mensaje = [
            'tipo'  => 'danger',
            'texto' => 'El nombre no debe contener números ni caracteres especiales.'
        ];
    }
    // Teléfono: 9 dígitos numéricos
    elseif (!ctype_digit($telefono) || strlen($telefono) !== 9) {
        $mensaje = [
            'tipo'  => 'danger',
            'texto' => 'El número de teléfono debe tener 9 dígitos numéricos.'
        ];
    }
    // Teléfono debe empezar con 9
    elseif ($telefono[0] !== '9') {
        $mensaje = [
            'tipo'  => 'danger',
            'texto' => 'El número de teléfono debe comenzar con el dígito 9.'
        ];
    } else {
        // Persistir cambios
        $resultado = editarCliente($nombre, $telefono, $direccion, $id);

        if ($resultado) {
            $mensaje = [
                'tipo'  => 'success',
                'texto' => 'Información del cliente actualizada con éxito.'
            ];

            // Refrescar datos en memoria
            $cliente->nombre    = $nombre;
            $cliente->telefono  = $telefono;
            $cliente->direccion = $direccion;
        } else {
            $mensaje = [
                'tipo'  => 'danger',
                'texto' => 'Error al actualizar la información del cliente.'
            ];
        }
    }
}

// Vista
include_once "encabezado.php";
include_once "navbar.php";
?>

<div class="container">
    <br>
    <h3 class="mb-4">Editar cliente</h3>

    <?php if ($mensaje): ?>
        <div class="alert alert-<?= htmlspecialchars($mensaje['tipo']); ?> mt-2" role="alert">
            <?= htmlspecialchars($mensaje['texto']); ?>
        </div>
    <?php endif; ?>

    <?php if ($cliente): ?>
        <form method="post">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input
                    type="text"
                    name="nombre"
                    class="form-control"
                    id="nombre"
                    placeholder="Escribe el nombre del cliente"
                    value="<?= htmlspecialchars($cliente->nombre, ENT_QUOTES, 'UTF-8'); ?>"
                >
            </div>

            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input
                    type="text"
                    name="telefono"
                    class="form-control"
                    id="telefono"
                    placeholder="Ej. 911156897"
                    value="<?= htmlspecialchars($cliente->telefono, ENT_QUOTES, 'UTF-8'); ?>"
                >
            </div>

            <div class="mb-3">
                <label for="direccion" class="form-label">Dirección</label>
                <input
                    type="text"
                    name="direccion"
                    class="form-control"
                    id="direccion"
                    placeholder="Ej. Av Collar 1005 Col Las Cruces"
                    value="<?= htmlspecialchars($cliente->direccion, ENT_QUOTES, 'UTF-8'); ?>"
                >
            </div>

            <div class="text-center mt-3">
                <input
                    type="submit"
                    name="actualizar"
                    value="Actualizar"
                    class="btn btn-primary btn-lg me-2"
                >
                <a href="clientes.php" class="btn btn-danger btn-lg">
                    <i class="fa fa-times"></i>
                    Cancelar
                </a>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php
include_once "footer.php";
?>
