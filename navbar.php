<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<nav class="navbar navbar-expand-lg navbar-dark mb-2 shadow"
     style="background-color: #073575ff; font-family: 'Science Gothic';">
    <div class="container-fluid">
        <!-- Logo / Inicio -->
        <a class="navbar-brand" href="index.php">
            <img src="img\Logo_Relative.png"
                 alt="Vallejo Motos"
                 width="100"
                 class="d-inline-block align-text-top">
        </a>

        <!-- Botón mobile -->
        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent"
                aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Contenido colapsable -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Menú principal -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item mx-2">
                    <a class="nav-link active px-3" href="productos.php">
                        <i class="fa fa-shopping-cart me-1"></i>
                        Productos
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link active px-3" href="usuarios.php">
                        <i class="fa fa-users me-1"></i>
                        Usuarios
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link active px-3" href="clientes.php">
                        <i class="fa fa-user-friends me-1"></i>
                        Clientes
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link active px-3" href="vender.php">
                        <i class="fa fa-cash-register me-1"></i>
                        Vender
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link active px-3" href="reporte_ventas.php">
                        <i class="fa fa-file-alt me-1"></i>
                        Reporte ventas
                    </a>
                </li>
            </ul>

            <!-- Iconos de perfil y logout -->
            <ul class="navbar-nav">
                <li class="nav-item me-3">
                    <a href="perfil.php" class="nav-link text-white fs-2 p-0">
                        <i class="bi bi-person-circle"></i>
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a href="cerrar_sesion.php" class="nav-link text-white fs-2 p-0">
                        <i class="bi bi-arrow-bar-right"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
