<?php
include_once "encabezado.php"
?>
<main class="flex-grow-1 d-flex align-items-center login-page">
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-6 d-none d-md-flex justify-content-center mb-4 mb-md-0">
                <img 
                    src="img\Logo_Main.png" 
                    class="img-fluid"
                    style="max-height: 75vh;"
                    alt="Logo Vallejo Motos"
                >
            </div>
            <div class="col-md-6 col-lg-4 login-card p-4 p-md-5 shadow-sm rounded">
                <h3 class="mb-4 text-center fw-bold">Comencemos a trabajar</h3>
                <form action="iniciar_sesion.php" method="post" autocomplete="off">
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="usuario" name="usuario" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <button type="submit" name="ingresar" class="btn w-100 fw-semibold mt-2 btn-login">
                        Ingresar
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>

<style>
    .btn-login:hover {
        transform: scale(1.03);
        transition: transform 0.2s ease;
        background-color: #0b3a82;   /* un poco más oscuro al pasar el mouse */
        border-color: #0b3a82;
        color: #ffffff;
    }

    .btn-login {
    background-color: #0d47a1;   /* azul oscuro */
    border-color: #0d47a1;
    color: #ffffff;
    }

    .login-page {
        background: url("img/wallpaper.jpg") no-repeat center center fixed;
        background-size: cover; /* que la imagen cubra toda la pantalla */
    }

    .login-card {
        background-color: rgba(255, 255, 255, 0.7); /* blanco con 90% opacidad */
        border-radius: 0.75rem;                     /* por si quieres esquinas más suaves */
    }
</style>

</body>
</html>

