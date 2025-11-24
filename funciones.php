<?php

// ===============================
// Constantes generales
// ===============================
define("PASSWORD_PREDETERMINADA", "VallejoMotos");
define("HOY", date("Y-m-d"));

// ===============================
// Conexión a la base de datos
// ===============================

/**
 * Conecto a la BD y devuelvo el PDO.
 */
function conectarBaseDatos()
{
    $host    = "localhost";
    $port    = "3306"; // puerto de MySQL
    $db      = "ventas_php";
    $user    = "root";
    $pass    = "M4rc0777";
    $charset = 'utf8mb4';

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
        return $pdo;
    } catch (PDOException $e) {
        throw new PDOException($e->getMessage(), (int) $e->getCode());
    }
}

// =====================================================================
// Funciones
// =====================================================================

/**
 * Sumo 1 a la cantidad de un producto ya existente en la lista.
 */
function agregarCantidad($idProducto, $listaProductos)
{
    foreach ($listaProductos as $producto) {
        if ($producto->id == $idProducto) {
            $producto->cantidad++;
        }
    }
    return $listaProductos;
}

/**
 * Agrego producto a la lista o aumento cantidad respetando stock.
 */
function agregarProductoALista($producto, $listaProductos)
{
    if ($producto->existencia < 1) {
        return $listaProductos;
    }

    $producto->cantidad = 1;

    $existe = verificarSiEstaEnLista($producto->id, $listaProductos);

    if (!$existe) {
        array_push($listaProductos, $producto);
    } else {
        $existenciaAlcanzada = verificarExistencia(
            $producto->id,
            $listaProductos,
            $producto->existencia
        );

        if ($existenciaAlcanzada) {
            return $listaProductos;
        }

        $listaProductos = agregarCantidad($producto->id, $listaProductos);
    }

    return $listaProductos;
}

/**
 * A cada venta le adjunto los productos vendidos.
 */
function agregarProductosVendidos($ventas)
{
    foreach ($ventas as $venta) {
        $venta->productos = obtenerProductosVendidos($venta->id);
    }
    return $ventas;
}

/**
 * Búsqueda para autocompletado (por código, id o nombre).
 */
function buscarProductosPorTermino($termino)
{
    $con = conectarBaseDatos();

    $sql    = "";
    $params = [];

    if (ctype_digit($termino)) {
        $sql = "SELECT id, codigo, nombre, existencia
                FROM productos
                WHERE codigo LIKE :codigo
                   OR id = :id
                LIMIT 10";
        $params = [
            ':codigo' => '%' . $termino . '%',
            ':id'     => (int) $termino,
        ];
    } else {
        $sql = "SELECT id, codigo, nombre, existencia
                FROM productos
                WHERE nombre LIKE :nombre
                LIMIT 10";
        $params = [
            ':nombre' => '%' . $termino . '%',
        ];
    }

    $stmt = $con->prepare($sql);
    $stmt->execute($params);
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(function ($row) {
        return [
            'id'         => $row['id'],
            'codigo'     => $row['codigo'],
            'nombre'     => $row['nombre'],
            'existencia' => $row['existencia'],
        ];
    }, $resultado);
}

/**
 * Ganancia en inventario: existencia * (venta - compra).
 */
function calcularGananciaProductos()
{
    $sentencia = "SELECT IFNULL(SUM(existencia*venta) - SUM(existencia*compra),0) AS total FROM productos";
    $fila      = select($sentencia);
    if ($fila) {
        return $fila[0]->total;
    }
}

/**
 * Total de unidades vendidas en un grupo de ventas.
 */
function calcularProductosVendidos($ventas)
{
    $total = 0;
    foreach ($ventas as $venta) {
        foreach ($venta->productos as $producto) {
            $total += $producto->cantidad;
        }
    }
    return $total;
}

/**
 * Total del carrito (lista en sesión).
 */
function calcularTotalLista($lista)
{
    $total = 0;
    foreach ($lista as $producto) {
        $total += floatval($producto->venta * $producto->cantidad);
    }
    return $total;
}

/**
 * Total de ventas (sumo campo total).
 */
function calcularTotalVentas($ventas)
{
    $total = 0;
    foreach ($ventas as $venta) {
        $total += $venta->total;
    }
    return $total;
}

/**
 * Cambio de contraseña (sin hash por ahora).
 */
function cambiarPassword($idUsuario, $password)
{
    $nueva     = $password;
    $sentencia = "UPDATE usuarios SET password = ? WHERE id = ?";
    return editar($sentencia, [$nueva, $idUsuario]);
}

/**
 * Descuento stock al registrar una venta.
 */
function descontarProductos($idProducto, $cantidad)
{
    $sentencia  = "UPDATE productos SET existencia = existencia - ? WHERE id = ?";
    $parametros = [$cantidad, $idProducto];
    return editar($sentencia, $parametros);
}

/**
 * Helper para UPDATE genérico.
 */
function editar($sentencia, $parametros)
{
    $bd        = conectarBaseDatos();
    $respuesta = $bd->prepare($sentencia);
    return $respuesta->execute($parametros);
}

/**
 * Update de datos de cliente.
 */
function editarCliente($nombre, $telefono, $direccion, $id)
{
    $sentencia  = "UPDATE clientes SET nombre = ?, telefono = ?, direccion = ? WHERE id = ?";
    $parametros = [$nombre, $telefono, $direccion, $id];
    return editar($sentencia, $parametros);
}

/**
 * Update de datos de producto.
 */
function editarProducto($codigo, $nombre, $marca, $compra, $venta, $existencia, $ubicacion, $id)
{
    $sentencia  = "UPDATE productos SET codigo = ?, nombre = ?, marca = ?, compra = ?, venta = ?, existencia = ?, ubicacion = ? WHERE id = ?";
    $parametros = [$codigo, $nombre, $marca, $compra, $venta, $existencia, $ubicacion, $id];
    return editar($sentencia, $parametros);
}

/**
 * Update de datos básicos de usuario.
 */
function editarUsuario($usuario, $nombre, $telefono, $direccion, $id)
{
    $sentencia  = "UPDATE usuarios SET usuario = ?, nombre = ?, telefono = ?, direccion = ? WHERE id = ?";
    $parametros = [$usuario, $nombre, $telefono, $direccion, $id];
    return editar($sentencia, $parametros);
}

/**
 * Helper para DELETE genérico.
 */
function eliminar($sentencia, $id)
{
    $bd        = conectarBaseDatos();
    $respuesta = $bd->prepare($sentencia);
    return $respuesta->execute([$id]);
}

/**
 * Eliminación de cliente.
 */
function eliminarCliente($id)
{
    $sentencia = "DELETE FROM clientes WHERE id = ?";
    return eliminar($sentencia, $id);
}

/**
 * Eliminación de producto.
 */
function eliminarProducto($id)
{
    $sentencia = "DELETE FROM productos WHERE id = ?";
    return eliminar($sentencia, $id);
}

/**
 * Eliminación de usuario.
 */
function eliminarUsuario($id)
{
    $sentencia = "DELETE FROM usuarios WHERE id = ?";
    return eliminar($sentencia, $id);
}

/**
 * Lógica de login (sin hash, solo comparación directa).
 */
function iniciarSesion($usuario, $password)
{
    error_log("DEBUG: Intentando login con usuario = '$usuario'");

    $sentencia = "SELECT id, usuario FROM usuarios WHERE usuario = ?";
    $resultado = select($sentencia, [$usuario]);

    if ($resultado) {
        error_log("DEBUG: Usuario encontrado en BD");

        $usuarioObj   = $resultado[0];
        $verificaPass = verificarPassword($usuarioObj->id, $password);

        if ($verificaPass) {
            error_log("DEBUG: LOGIN EXITOSO");
            return $usuarioObj;
        }

        error_log("DEBUG: CONTRASEÑA INCORRECTA");
    } else {
        error_log("DEBUG: Usuario NO encontrado");
    }

    return null;
}

/**
 * Helper para INSERT genérico.
 */
function insertar($sentencia, $parametros)
{
    $bd        = conectarBaseDatos();
    $respuesta = $bd->prepare($sentencia);
    return $respuesta->execute($parametros);
}

/**
 * Devuelvo cliente por id.
 */
function obtenerClientePorId($id)
{
    $sentencia = "SELECT * FROM clientes WHERE id = ?";
    $cliente   = select($sentencia, [$id]);
    if ($cliente) {
        return $cliente[0];
    }
    return null;
}

/**
 * Devuelvo todos los clientes.
 */
function obtenerClientes()
{
    $sentencia = "SELECT * FROM clientes";
    return select($sentencia);
}

/**
 * Ganancia de un conjunto de ventas (precio - compra).
 */
function obtenerGananciaVentas($ventas)
{
    $total = 0;
    foreach ($ventas as $venta) {
        foreach ($venta->productos as $producto) {
            $total += $producto->cantidad * ($producto->precio - $producto->compra);
        }
    }
    return $total;
}

/**
 * Total de clientes registrados.
 */
function obtenerNumeroClientes()
{
    $sentencia = "SELECT IFNULL(COUNT(*),0) AS total FROM clientes";
    return select($sentencia)[0]->total;
}

/**
 * Total de productos (sumando existencia).
 */
function obtenerNumeroProductos()
{
    $sentencia = "SELECT IFNULL(SUM(existencia),0) AS total FROM productos";
    $fila      = select($sentencia);
    if ($fila) {
        return $fila[0]->total;
    }
}

/**
 * Total de usuarios registrados.
 */
function obtenerNumeroUsuarios()
{
    $sentencia = "SELECT IFNULL(COUNT(*),0) AS total FROM usuarios";
    return select($sentencia)[0]->total;
}

/**
 * Total de ventas registradas.
 */
function obtenerNumeroVentas()
{
    $sentencia = "SELECT IFNULL(COUNT(*),0) AS total FROM ventas";
    return select($sentencia)[0]->total;
}

/**
 * Devuelvo producto por código.
 */
function obtenerProductoPorCodigo($codigo)
{
    $sentencia = "SELECT * FROM productos WHERE codigo = ?";
    $producto  = select($sentencia, [$codigo]);
    if ($producto) {
        return $producto[0];
    }
    return [];
}

/**
 * Devuelvo producto por id.
 */
function obtenerProductoPorId($id)
{
    $sentencia = "SELECT * FROM productos WHERE id = ?";
    return select($sentencia, [$id])[0];
}

/**
 * Listado de productos, con filtro opcional por nombre o código.
 */
function obtenerProductos($busqueda = null)
{
    $parametros = [];
    $sentencia  = "SELECT * FROM productos ";
    if (isset($busqueda)) {
        $sentencia .= " WHERE nombre LIKE ? OR codigo LIKE ?";
        array_push($parametros, "%" . $busqueda . "%", "%" . $busqueda . "%");
    }
    return select($sentencia, $parametros);
}

/**
 * Top 10 de productos más vendidos.
 */
function obtenerProductosMasVendidos()
{
    $sentencia = "SELECT 
                    SUM(productos_ventas.cantidad * productos_ventas.precio) AS total, 
                    SUM(productos_ventas.cantidad) AS unidades,
                    productos.nombre 
                  FROM productos_ventas 
                  INNER JOIN productos ON productos.id = productos_ventas.idProducto
                  GROUP BY productos_ventas.idProducto
                  ORDER BY total DESC
                  LIMIT 10";
    return select($sentencia);
}

/**
 * Productos asociados a una venta específica.
 */
function obtenerProductosVendidos($idVenta)
{
    $sentencia = "SELECT 
                    productos_ventas.cantidad, 
                    productos_ventas.precio, 
                    productos.nombre,
                    productos.compra
                  FROM productos_ventas
                  INNER JOIN productos ON productos.id = productos_ventas.idProducto
                  WHERE idVenta = ?";
    return select($sentencia, [$idVenta]);
}

/**
 * Valor total del inventario actual.
 */
function obtenerTotalInventario()
{
    $sentencia = "SELECT IFNULL(SUM(existencia * venta),0) AS total FROM productos";
    $fila      = select($sentencia);
    if ($fila) {
        return $fila[0]->total;
    }
}

/**
 * Total de ventas (global o por usuario).
 */
function obtenerTotalVentas($idUsuario = null)
{
    $parametros = [];
    $sentencia  = "SELECT IFNULL(SUM(total),0) AS total FROM ventas";
    if (isset($idUsuario)) {
        $sentencia .= " WHERE idUsuario = ?";
        array_push($parametros, $idUsuario);
    }
    $fila = select($sentencia, $parametros);
    if ($fila) {
        return $fila[0]->total;
    }
}

/**
 * Total vendido hoy (global o por usuario).
 */
function obtenerTotalVentasHoy($idUsuario = null)
{
    $parametros = [];
    $sentencia  = "SELECT IFNULL(SUM(total),0) AS total FROM ventas WHERE DATE(fecha) = CURDATE()";
    if (isset($idUsuario)) {
        $sentencia .= " AND idUsuario = ?";
        array_push($parametros, $idUsuario);
    }
    $fila = select($sentencia, $parametros);
    if ($fila) {
        return $fila[0]->total;
    }
}

/**
 * Total vendido en la semana actual (global o por usuario).
 */
function obtenerTotalVentasSemana($idUsuario = null)
{
    $parametros = [];
    $sentencia  = "SELECT IFNULL(SUM(total),0) AS total FROM ventas WHERE WEEK(fecha) = WEEK(NOW())";
    if (isset($idUsuario)) {
        $sentencia .= " AND idUsuario = ?";
        array_push($parametros, $idUsuario);
    }
    $fila = select($sentencia, $parametros);
    if ($fila) {
        return $fila[0]->total;
    }
}

/**
 * Total vendido en el mes actual (global o por usuario).
 */
function obtenerTotalVentasMes($idUsuario = null)
{
    $parametros = [];
    $sentencia  = "SELECT IFNULL(SUM(total),0) AS total 
                   FROM ventas  
                   WHERE MONTH(fecha) = MONTH(CURRENT_DATE()) 
                     AND YEAR(fecha) = YEAR(CURRENT_DATE())";
    if (isset($idUsuario)) {
        $sentencia .= " AND idUsuario = ?";
        array_push($parametros, $idUsuario);
    }
    $fila = select($sentencia, $parametros);
    if ($fila) {
        return $fila[0]->total;
    }
}

/**
 * Devuelvo el último id de venta registrado.
 */
function obtenerUltimoIdVenta()
{
    $sentencia = "SELECT id FROM ventas ORDER BY id DESC LIMIT 1";
    return select($sentencia)[0]->id;
}

/**
 * Devuelvo usuario por id.
 */
function obtenerUsuarioPorId($id)
{
    $sentencia = "SELECT id, usuario, nombre, telefono, direccion FROM usuarios WHERE id = ?";
    return select($sentencia, [$id])[0];
}

/**
 * Listado de usuarios.
 */
function obtenerUsuarios()
{
    $sentencia = "SELECT id, usuario, nombre, telefono, direccion FROM usuarios";
    return select($sentencia);
}

/**
 * Ventas con filtros (usuario, cliente, fechas) + productos.
 */
function obtenerVentas($fechaInicio, $fechaFin, $cliente, $usuario)
{
    $parametros = [];
    $sentencia  = "SELECT 
                        ventas.*, 
                        usuarios.usuario, 
                        IFNULL(clientes.nombre, 'MOSTRADOR') AS cliente
                   FROM ventas 
                   INNER JOIN usuarios ON usuarios.id = ventas.idUsuario
                   LEFT JOIN clientes ON clientes.id = ventas.idCliente";

    if (isset($usuario)) {
        $sentencia .= " WHERE ventas.idUsuario = ?";
        array_push($parametros, $usuario);
        $ventas = select($sentencia, $parametros);
        return agregarProductosVendidos($ventas);
    }

    if (isset($cliente)) {
        $sentencia .= " WHERE ventas.idCliente = ?";
        array_push($parametros, $cliente);
        $ventas = select($sentencia, $parametros);
        return agregarProductosVendidos($ventas);
    }

    if (empty($fechaInicio) && empty($fechaFin)) {
        $sentencia .= " WHERE DATE(ventas.fecha) = ?";
        array_push($parametros, HOY);
        $ventas = select($sentencia, $parametros);
        return agregarProductosVendidos($ventas);
    }

    if (isset($fechaInicio) && isset($fechaFin)) {
        $sentencia .= " WHERE DATE(ventas.fecha) >= ? AND DATE(ventas.fecha) <= ?";
        array_push($parametros, $fechaInicio, $fechaFin);
    }

    $ventas = select($sentencia, $parametros);
    return agregarProductosVendidos($ventas);
}

/**
 * Ventas agrupadas por cliente.
 */
function obtenerVentasPorCliente()
{
    $sentencia = "SELECT 
                    SUM(ventas.total) AS total, 
                    IFNULL(clientes.nombre, 'MOSTRADOR') AS cliente,
                    COUNT(*) AS numeroCompras
                  FROM ventas
                  LEFT JOIN clientes ON clientes.id = ventas.idCliente
                  GROUP BY ventas.idCliente
                  ORDER BY total DESC";
    return select($sentencia);
}

/**
 * Ventas agrupadas por usuario.
 */
function obtenerVentasPorUsuario()
{
    $sentencia = "SELECT 
                    SUM(ventas.total) AS total, 
                    usuarios.usuario, 
                    COUNT(*) AS numeroVentas 
                  FROM ventas
                  INNER JOIN usuarios ON usuarios.id = ventas.idUsuario
                  GROUP BY ventas.idUsuario
                  ORDER BY total DESC";
    return select($sentencia);
}

/**
 * Registro de cliente nuevo.
 */
function registrarCliente($nombre, $telefono, $direccion)
{
    $sentencia  = "INSERT INTO clientes (nombre, telefono, direccion) VALUES (?,?,?)";
    $parametros = [$nombre, $telefono, $direccion];
    return insertar($sentencia, $parametros);
}

/**
 * Registro de producto nuevo.
 */
function registrarProducto($codigo, $nombre, $marca, $compra, $venta, $existencia, $ubicacion)
{
    $sentencia  = "INSERT INTO productos (codigo, nombre, marca, compra, venta, existencia, ubicacion) 
                   VALUES (?, ?, ?, ?, ?, ?, ?)";
    $parametros = [$codigo, $nombre, $marca, $compra, $venta, $existencia, $ubicacion];
    return insertar($sentencia, $parametros);
}

/**
 * Registro de detalle de productos en una venta.
 */
function registrarProductosVenta($productos, $idVenta)
{
    $sentencia = "INSERT INTO productos_ventas (cantidad, precio, idProducto, idVenta) 
                  VALUES (?,?,?,?)";
    foreach ($productos as $producto) {
        $parametros = [$producto->cantidad, $producto->venta, $producto->id, $idVenta];
        insertar($sentencia, $parametros);
        descontarProductos($producto->id, $producto->cantidad);
    }
    return true;
}

/**
 * Registro de usuario con contraseña por defecto.
 */
function registrarUsuario($usuario, $nombre, $telefono, $direccion)
{
    $password   = PASSWORD_PREDETERMINADA;
    $sentencia  = "INSERT INTO usuarios (usuario, nombre, telefono, direccion, password) 
                  VALUES (?,?,?,?,?)";
    $parametros = [$usuario, $nombre, $telefono, $direccion, $password];
    return insertar($sentencia, $parametros);
}

/**
 * Registro de la venta y sus productos.
 */
function registrarVenta(array $productos, int $idUsuario, ?int $idCliente, float $total): bool
{
    $sql = "INSERT INTO ventas (fecha, total, idUsuario, idCliente)
            VALUES (?, ?, ?, ?)";

    $params = [
        date("Y-m-d H:i:s"),
        $total,
        $idUsuario,
        // Si viene null, PDO lo insertará como NULL en la columna
        $idCliente,
    ];

    $resultadoVenta = insertar($sql, $params);

    if (!$resultadoVenta) {
        return false;
    }

    $idVenta = obtenerUltimoIdVenta();

    // Registro el detalle de productos de la venta
    $productosRegistrados = registrarProductosVenta($productos, $idVenta);

    return $resultadoVenta && $productosRegistrados;
}


/**
 * Helper para SELECT genérico.
 */
function select($sentencia, $parametros = [])
{
    $bd        = conectarBaseDatos();
    $respuesta = $bd->prepare($sentencia);
    $respuesta->execute($parametros);
    return $respuesta->fetchAll();
}

/**
 * Chequeo si ya existe usuario o nombre repetidos.
 */
function usuarioExiste($usuario, $nombre)
{
    $sentencia = "SELECT COUNT(*) AS total FROM usuarios WHERE usuario = ? OR nombre = ?";
    $resultado = select($sentencia, [$usuario, $nombre]);
    return $resultado[0]->total > 0;
}

/**
 * Verifico que no se pase del stock disponible.
 */
function verificarExistencia($idProducto, $listaProductos, $existencia)
{
    foreach ($listaProductos as $producto) {
        if ($producto->id == $idProducto) {
            if ($existencia <= $producto->cantidad) {
                return true;
            }
        }
    }
    return false;
}

/**
 * Expira sesión por inactividad y manda a login.
 */
function verificarInactividad()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['usuario'])) {
        // 20 minutos
        $inactivity_limit = 1200;

        if (isset($_SESSION['last_activity'])) {
            $inactive_time = time() - $_SESSION['last_activity'];

            error_log("Tiempo de inactividad: $inactive_time segundos");

            if ($inactive_time > $inactivity_limit) {
                echo "Sesión expirada. Cerrando sesión...<br>";
                error_log("Sesión expirada. Cerrando sesión...");

                session_unset();
                session_destroy();
                header("Location: login.php");
                exit();
            }
        }

        $_SESSION['last_activity'] = time();
    }
}

/**
 * Comparación de password (por ahora sin hash).
 * Ojo: dejar preparado para migrar a password_hash().
 */
function verificarPassword($idUsuario, $password)
{
    $sentencia   = "SELECT password FROM usuarios WHERE id = ?";
    $contrasenia = select($sentencia, [$idUsuario])[0]->password;

    return $password === $contrasenia;
}

/**
 * Veo si el producto ya está en la lista.
 */
function verificarSiEstaEnLista($idProducto, $listaProductos)
{
    foreach ($listaProductos as $producto) {
        if ($producto->id == $idProducto) {
            return true;
        }
    }
    return false;
}
