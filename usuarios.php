<?php
session_start();

include_once "funciones.php";
verificarInactividad(); // Valido inactividad y renuevo la sesión

// Si no hay usuario logueado, regreso al login
if (empty($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

include_once "encabezado.php";
include_once "navbar.php";

// Cargo todos los usuarios para mostrarlos en la tabla
$usuarios = obtenerUsuarios();
?>
<div class="container mt-3">
    <h1>
        <a
            class="btn btn-lg"
            style="color:#fff; background:#0d47a1;"
            href="agregar_usuario.php"
        >
            <i class="fa fa-plus"></i>
            Agregar
        </a>
        Usuarios
    </h1>

    <table class="table mt-3">
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Editar</th>
                <th>Eliminar</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?= htmlspecialchars($usuario->usuario); ?></td>
                <td><?= htmlspecialchars($usuario->nombre); ?></td>
                <td><?= htmlspecialchars($usuario->telefono); ?></td>
                <td><?= htmlspecialchars($usuario->direccion); ?></td>
                <td>
                    <a
                        class="btn"
                        style="color:#fff; background:#f77519;"
                        href="editar_usuario.php?id=<?= $usuario->id; ?>"
                    >
                        <i class="fa fa-edit"></i>
                    </a>
                </td>
                <td>
                    <a
                        href="eliminar_usuario.php?id=<?= $usuario->id; ?>"
                        onclick="return confirmarEliminacion();"
                        class="btn btn-danger"
                    >
                        Eliminar
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
// Confirmo con el usuario antes de eliminar
function confirmarEliminacion() {
    return confirm('¿Estás seguro de que deseas eliminar este usuario?');
}
</script>

<?php
include_once "footer.php";
?>
