<?php
// Sesión y bloqueo de acceso directo
session_start();
if (empty($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

// Dependencias
require_once "funciones.php";
include_once "encabezado.php";
include_once "navbar.php";

// Mensaje para alertas
$mensaje = null;

// Procesar envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar'])) {
    $nombre     = trim($_POST['nombre'] ?? '');
    $telefono   = trim($_POST['telefono'] ?? '');
    $direccion  = trim($_POST['direccion'] ?? '');

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
    }
    // Registro en BD
    else {
        $resultado = registrarCliente($nombre, $telefono, $direccion);

        if ($resultado) {
            $mensaje = [
                'tipo'  => 'success',
                'texto' => 'Cliente registrado con éxito.'
            ];
            // Si quiero dejar el formulario limpio
            $nombre = $telefono = $direccion = '';
        } else {
            $mensaje = [
                'tipo'  => 'danger',
                'texto' => 'Ocurrió un problema al registrar el cliente.'
            ];
        }
    }
}
?>

<div class="container">
    <br>
    <h3 class="mb-4">Agregar cliente</h3>

    <?php if ($mensaje): ?>
        <div class="alert alert-<?= htmlspecialchars($mensaje['tipo']); ?> mt-3" role="alert">
            <?= htmlspecialchars($mensaje['texto']); ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input
                type="text"
                name="nombre"
                class="form-control"
                id="nombre"
                placeholder="Escribe el nombre del cliente"
                value="<?= htmlspecialchars($nombre ?? ''); ?>"
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
                value="<?= htmlspecialchars($telefono ?? ''); ?>"
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
                value="<?= htmlspecialchars($direccion ?? ''); ?>"
            >
        </div>

        <div class="text-center mt-3">
            <input
                type="submit"
                name="registrar"
                value="Registrar"
                class="btn btn-primary btn-lg me-2"
            >
            <a href="clientes.php" class="btn btn-danger btn-lg">
                <i class="fa fa-times"></i>
                Cancelar
            </a>
        </div>
    </form>
</div>

<?php
include_once "footer.php";
?>
