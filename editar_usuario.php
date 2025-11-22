<?php
// Sesión y restricción de acceso
session_start();
if (empty($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

// Dependencias
require_once "funciones.php";

// Mensaje para alertas
$mensaje = null;

// ID del usuario a editar
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$usuario = null;

if ($id <= 0) {
    $mensaje = [
        'tipo'  => 'danger',
        'texto' => 'No se ha seleccionado el usuario para editar.'
    ];
} else {
    // Cargar datos del usuario
    $usuario = obtenerUsuarioPorId($id);
    if (!$usuario) {
        $mensaje = [
            'tipo'  => 'danger',
            'texto' => 'Usuario no encontrado.'
        ];
    }
}

// Procesar actualización solo si hay usuario válido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar']) && $usuario) {
    $usuarioNombre = trim($_POST['usuario']   ?? '');
    $nombre        = trim($_POST['nombre']    ?? '');
    $telefono      = trim($_POST['telefono']  ?? '');
    $direccion     = trim($_POST['direccion'] ?? '');

    // Campos obligatorios
    if ($usuarioNombre === '' || $nombre === '' || $telefono === '' || $direccion === '') {
        $mensaje = [
            'tipo'  => 'danger',
            'texto' => 'Debes completar todos los datos.'
        ];
    }
    // Usuario solo letras y espacios
    elseif (!preg_match('/^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/', $usuarioNombre)) {
        $mensaje = [
            'tipo'  => 'danger',
            'texto' => 'El nombre de usuario no debe contener números ni caracteres especiales.'
        ];
    }
    // Nombre completo solo letras y espacios
    elseif (!preg_match('/^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/', $nombre)) {
        $mensaje = [
            'tipo'  => 'danger',
            'texto' => 'El nombre completo no debe contener números ni caracteres especiales.'
        ];
    }
    // Teléfono: 9 dígitos
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
    // Actualizar en BD
    else {
        $resultado = editarUsuario($usuarioNombre, $nombre, $telefono, $direccion, $id);

        if ($resultado) {
            $mensaje = [
                'tipo'  => 'success',
                'texto' => 'Información de usuario actualizada con éxito.'
            ];

            // Refrescar datos en memoria para mostrar valores actualizados
            $usuario->usuario   = $usuarioNombre;
            $usuario->nombre    = $nombre;
            $usuario->telefono  = $telefono;
            $usuario->direccion = $direccion;
        } else {
            $mensaje = [
                'tipo'  => 'danger',
                'texto' => 'Error al actualizar la información del usuario.'
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
    <h3 class="mb-4">Editar usuario</h3>

    <?php if ($mensaje): ?>
        <div class="alert alert-<?= htmlspecialchars($mensaje['tipo']); ?> mt-2" role="alert">
            <?= htmlspecialchars($mensaje['texto']); ?>
        </div>
    <?php endif; ?>

    <?php if ($usuario): ?>
        <form method="post">
            <div class="mb-3">
                <label for="usuario" class="form-label">Nombre de usuario</label>
                <input
                    type="text"
                    name="usuario"
                    class="form-control"
                    id="usuario"
                    placeholder="Escribe el nombre de usuario. Ej. Paco"
                    value="<?= htmlspecialchars($usuario->usuario, ENT_QUOTES, 'UTF-8'); ?>"
                >
            </div>

            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre completo</label>
                <input
                    type="text"
                    name="nombre"
                    class="form-control"
                    id="nombre"
                    placeholder="Escribe el nombre completo del usuario"
                    value="<?= htmlspecialchars($usuario->nombre, ENT_QUOTES, 'UTF-8'); ?>"
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
                    value="<?= htmlspecialchars($usuario->telefono, ENT_QUOTES, 'UTF-8'); ?>"
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
                    value="<?= htmlspecialchars($usuario->direccion, ENT_QUOTES, 'UTF-8'); ?>"
                >
            </div>

            <div class="text-center mt-3">
                <input
                    type="submit"
                    name="actualizar"
                    value="Actualizar"
                    class="btn btn-primary btn-lg me-2"
                >
                <a href="usuarios.php" class="btn btn-danger btn-lg">
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
