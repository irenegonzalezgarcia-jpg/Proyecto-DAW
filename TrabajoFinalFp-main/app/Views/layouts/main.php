<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Inspire Beauty - Centro de estética' ?></title>
    <!-- Estilos base -->
    <link rel="stylesheet" href="/css/estilos.css">
    
    <!-- CSS Adicional pasado desde la vista -->
    <?php if (isset($extraCss)): ?>
        <?php foreach ($extraCss as $css): ?>
            <link rel="stylesheet" href="<?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- SweetAlert2 para modales -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="shortcut icon" href="/imagenes/favicon.ico" type="image/x-icon">
</head>

<body>

    <!-- Header -->
    <header id="inicio">
        <div class="encabezado">
            <div class="logo">
                <a href="/"><img src="/imagenes/logo.png" alt="logo" class="logo-pequeño"></a>
            </div>

            <!--Barra de navegación-->
            <div class="contenedor-nav">
                <nav>
                    <ul>
                        <li><a href="/">Inicio</a></li>
                        <li class="dropdown">
                            <a href="#">Catálogo</a>
                            <ul class="menu-desplegable">
                                <li><a href="/facial">Facial</a></li>
                                <li><a href="/corporal">Corporal</a></li>
                                <li><a href="/tratamientos">Tratamientos</a></li>
                                <li><a href="/promociones">Promociones</a></li>
                            </ul>
                        </li>
                        <?php $reservaLink = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') ? '/admin/reservar' : '/reservar'; ?>
                        <li><a href="<?= $reservaLink ?>" class="destacado">Reservar cita</a></li>
                        <li><a href="/contacto">Contacto</a></li>
                        <?php 
                        $cartCount = 0;
                        if (isset($_SESSION['cart'])) {
                            foreach ($_SESSION['cart'] as $q) $cartCount += $q;
                        }
                        ?>
                        <li>
                            <a href="/cesta" style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="9" cy="21" r="1"></circle>
                                    <circle cx="20" cy="21" r="1"></circle>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                </svg>
                                Cesta<?= $cartCount > 0 ? " ($cartCount)" : "" ?>
                            </a>
                        </li>
                    </ul>
                </nav>
                <div class="botones-sesion">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($_SESSION['user_role'] === 'admin'): ?>
                            <span style="margin-right: 15px; font-weight: bold; color: #b89a42; display: flex; align-items: center; gap: 5px;">
                                👑 Administrador
                            </span>
                        <?php else: ?>
                            <span style="margin-right: 15px; font-weight: bold; color: var(--text-dark); display: flex; align-items: center;">
                                Hola, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Cliente') ?>
                            </span>
                        <?php endif; ?>
                        <a href="<?= $_SESSION['user_role'] === 'admin' ? '/admin' : '/panel' ?>" class="btn-inicio">Mi Panel</a>
                        <a href="/logout" class="btn-inicio">Salir</a>
                    <?php else: ?>
                        <a href="/login" class="btn-inicio">Iniciar sesión</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenido principal inyectado por el Controller -->
    <main>
        <?= $content ?>
    </main>

    <!--Flecha a inicio-->
    <a href="#inicio" class="flecha">
        <img src="/imagenes/flecha-hacia-arriba.png" alt="Subir">
    </a>

    <!--Footer-->
    <footer class="row final">
        <div class="col-4">
            <a href="/"><img src="/imagenes/logo.png" alt="logo" class="logo-pequeño"></a>
        </div>
        <nav class="col-4">
            <a href="/">Inicio</a>
            <a href="/facial">Facial</a>
            <a href="/corporal">Corporal</a>
            <a href="/tratamientos">Tratamientos</a>
            <a href="/promociones">Promociones</a>
            <a href="<?= $reservaLink ?>">Reservar cita</a>
            <a href="/contacto">Contacto</a>
        </nav>
        <div class="col-4 redes">
            <div>
                <img src="/imagenes/facebook.png" alt="">
                <img src="/imagenes/instagram.png" alt="">
                <img src="/imagenes/whatsapp.png" alt="">
            </div>
        </div>
        <div class="col-12">
            <p>© 2026 Inspire Beauty - Centro de estética. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- JS Adicional pasado desde la vista -->
    <?php if (isset($extraJs)): ?>
        <?php foreach ($extraJs as $js): ?>
            <script src="<?= $js ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>
