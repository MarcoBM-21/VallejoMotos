# Vallejo Motos 🏍️

Sistema web de **ventas e inventario** para la tienda *Vallejo Motos*, desarrollado en PHP y MySQL.  
Permite gestionar productos, clientes, usuarios y registrar ventas con control de stock y reportes detallados.

---

## 🚀 Características principales

### 🔐 Autenticación y sesión

- Inicio de sesión de usuarios.
- Control de sesión por inactividad (cierre automático tras un tiempo sin uso).
- Pantalla de **perfil** con resumen de ventas del usuario y acceso a cambio de contraseña.

### 📦 Gestión de productos

- CRUD completo de productos:
  - Código de barras de **6 dígitos numéricos**.
  - Nombre / descripción.
  - Marca.
  - Ubicación en tienda.
  - Existencia (stock).
  - Precio de compra y venta.
- Validaciones en el formulario de registro y edición.
- Listado de productos con:
  - Búsqueda por nombre o código.
  - Mostrar/ocultar columnas (código, nombre, marca, ubicación, precios, ganancia, existencia).
  - Etiqueta de **“Sin Stock”** cuando la existencia es 0.

### 👥 Gestión de clientes

- CRUD de clientes con:
  - Nombre.
  - Teléfono (9 dígitos, empezando en 9).
  - Dirección.
- Validación de nombre y formato de teléfono.

### 👤 Gestión de usuarios

- CRUD de usuarios:
  - Usuario, nombre completo, teléfono y dirección.
- Contraseña predeterminada definida en `funciones.php` mediante la constante `PASSWORD_PREDETERMINADA`.
- Validaciones básicas de datos.

### 💳 Punto de venta

- Pantalla de **Vender** con:
  - Buscador de productos por código o nombre.
  - Sugerencias/autocompletado mientras el usuario escribe.
  - Lista de productos seleccionados guardada en sesión.
  - Actualización de cantidades con validación de stock disponible.
  - Selección de cliente (o “MOSTRADOR” por defecto).
- Registro de venta:
  - Guarda la venta en la tabla `ventas`.
  - Guarda los productos vendidos en `productos_ventas`.
  - Descuenta automáticamente el stock del producto.
- Posibilidad de **cancelar** la venta en curso.

### 📊 Reporte de ventas y dashboard

- **Dashboard** principal con tarjetas de resumen:
  - Total de productos, usuarios, clientes, ventas registradas.
  - Total de ventas, ventas de hoy, de la semana y del mes.
- Ranking:
  - Ventas por usuario.
  - Ventas por cliente.
  - Top 10 productos más vendidos.
- **Reporte de ventas**:
  - Filtro por rango de fechas.
  - Filtro por usuario.
  - Filtro por cliente.
  - Tarjetas con:
    - Número de ventas.
    - Total vendido.
    - Productos vendidos.
    - Ganancia estimada.
  - Detalle de cada venta con productos incluidos.

---

## 🛠️ Tecnologías utilizadas

- **PHP** (programación estructurada, sin framework).
- **MySQL/MariaDB** como motor de base de datos.
- **Bootstrap 5.1.3** para el layout y componentes.
- **Font Awesome** y **Bootstrap Icons** para iconos.
- **Google Fonts**:
  - `Rubik` para la tipografía base.
  - `Science Gothic` para títulos, navbar, botones y elementos destacados.

---

## 📁 Estructura del proyecto (simplificada)

```text
BAMBOO- / VallejoMotos
├── bootstrap/              # Archivos CSS/JS de Bootstrap y dependencias
├── img/                    # Imágenes, favicon y logotipo
├── webfonts/               # Fuentes de iconos
├── bd.sql                  # Script de creación de la base de datos
├── funciones.php           # Conexión a BD y funciones de negocio
├── encabezado.php          # <head> + configuración de fuentes y CSS
├── navbar.php              # Barra de navegación principal
├── footer.php              # Pie de página
├── index.php               # Dashboard principal
├── productos.php           # Listado de productos
├── agregar_producto.php    # Registro de nuevo producto
├── editar_producto.php     # Edición de producto
├── clientes.php            # Listado de clientes
├── agregar_cliente.php     # Registro de cliente
├── editar_cliente.php      # Edición de cliente
├── usuarios.php            # Listado de usuarios
├── agregar_usuario.php     # Registro de usuario
├── editar_usuario.php      # Edición de usuario
├── vender.php              # Punto de venta
├── reporte_ventas.php      # Reporte de ventas
├── login.php               # Pantalla de inicio de sesión
├── perfil.php              # Perfil del usuario logueado
└── ... otros archivos auxiliares (quitar_producto, registrar_venta, etc.)

