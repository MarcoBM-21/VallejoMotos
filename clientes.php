<?php
session_start();

// Si no hay usuario autenticado, volvemos al login
if (empty($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Funciones y acceso a datos
require_once "funciones.php";
$clientes = obtenerClientes();

// A partir de aquí recién incluimos la parte visual
include_once "encabezado.php";
include_once "navbar.php";
?>

<div class="container">
    <br>
    <h1>
        <a
            class="btn btn-lg"
            style="color:#fff; background:#0d47a1;"
            href="agregar_cliente.php"
        >
            <i class="fa fa-plus"></i>
            Agregar
        </a>
        Clientes
    </h1>

    <table class="table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Editar</th>
                <th>Eliminar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td><?= htmlspecialchars($cliente->nombre); ?></td>
                    <td><?= htmlspecialchars($cliente->telefono); ?></td>
                    <td><?= htmlspecialchars($cliente->direccion); ?></td>

                    <td>
                        <a
                            class="btn"
                            style="color:#fff; background:#f77519;"
                            href="editar_cliente.php?id=<?= $cliente->id; ?>"
                        >
                            <i class="fa fa-edit"></i>
                        </a>
                    </td>

                    <td>
                        <a
                            href="eliminar_cliente.php?id=<?= $cliente->id; ?>"
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
    // Confirmación antes de eliminar un cliente
    function confirmarEliminacion() {
        return confirm('¿Estás seguro de que deseas eliminar este cliente?');
    }
</script>

<?php
include_once "footer.php";
?>
