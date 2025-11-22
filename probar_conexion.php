<?php
require "funciones.php";

echo "<h3>Probando conexión a MySQL...</h3>";

try {
    $conexion = conectarBaseDatos();
    echo "<p style='color: green;'>✔ Conexión EXITOSA a la base de datos</p>";

    // Consulta
    $resultado = $conexion->query("SELECT id, usuario FROM usuarios")->fetchAll();

    echo "<p><strong>Usuarios encontrados:</strong></p>";

    foreach ($resultado as $fila) {
        echo "ID: " . $fila->id . " | Usuario: " . $fila->usuario . "<br>";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
