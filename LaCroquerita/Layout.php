<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Croquerita</title>
    <!-- Boxicons CSS -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleNavBar.css">
    <link rel="stylesheet" href="css/styleViews.css">

</head>

<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo_item">
            <i class="bx bx-menu" id="sidebarOpen"></i>
            <img src="img/Logo.jpg" alt="Logo">
            <h1 class="m-0 text-primary"><span class="text-dark">LA</span> CROQUERITA</h1>
        </div>
        <div class="search_bar">
            <input type="text" placeholder="Buscar...">
        </div>
        <div class="navbar_content">
            <i class="bx bx-grid"></i>
            <i class='bx bx-sun' id="darkLight"></i>
            <i class='bx bx-bell'></i>
            <img src="img/Capibara.jpg" alt="Perfil" class="profile">
        </div>
    </nav>

    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="menu_content">
            <ul class="menu_items">
                <li class="item">
                    <a href="index.php?page=home" class="nav_link">
                        <span class="navlink_icon"><i class='bx bx-home-heart'></i></span>
                        <span class="navlink">Home</span>
                    </a>
                </li>
            </ul>

            <ul class="menu_items">
                <li class="item">
                    <div class="nav_link submenu_item">
                        <span class="navlink_icon"><i class='bx bx-group'></i></span>
                        <span class="navlink">Comunidad <br>& Participación</span>
                        <i class="bx bx-chevron-right arrow-left"></i>
                    </div>
                    <ul class="menu_items submenu">
                        <li><a href="index.php?page=clases" class="nav_link sublink">Clases</a></li>
                        <li><a href="#" class="nav_link sublink">Foro de discusión</a></li>
                        <li><a href="index.php?page=eventos" class="nav_link sublink">Eventos</a></li>
                        <li><a href="#" class="nav_link sublink">Recordatorios</a></li>
                        <li><a href="index.php?page=asistencia" class="nav_link sublink">Asistencia</a></li>
                        <li><a href="#" class="nav_link sublink">Notificaciones</a></li>
                    </ul>
                </li>

                <li class="item">
                    <div class="nav_link submenu_item">
                        <span class="navlink_icon"><i class='bx bx-palette'></i></span>
                        <span class="navlink">Mis Proyectos <br>& Recursos</span>
                        <i class="bx bx-chevron-right arrow-left"></i>
                    </div>
                    <ul class="menu_items submenu">
                        <li><a href="#" class="nav_link sublink">Mis Proyectos</a></li>
                        <li><a href="#" class="nav_link sublink">Retos de tejido</a></li>
                        <li><a href="#" class="nav_link sublink">Tutoriales</a></li>
                        <li><a href="#" class="nav_link sublink">Patrones digitales</a></li>
                    </ul>
                </li>

                <li class="item">
                    <div class="nav_link submenu_item">
                        <span class="navlink_icon"><i class='bx bx-dollar-circle'></i></span>
                        <span class="navlink">Herramientas de <br>Cálculo</span>
                        <i class="bx bx-chevron-right arrow-left"></i>
                    </div>
                    <ul class="menu_items submenu">
                        <li><a href="#" class="nav_link sublink">Cálculo de costos</a></li>
                        <li><a href="#" class="nav_link sublink">Precios Estándar</a></li>
                        <li><a href="#" class="nav_link sublink">Tutoriales</a></li>
                        <li><a href="#" class="nav_link sublink">Patrones digitales</a></li>
                    </ul>
                </li>
            </ul>

            <div class="bottom_content">
                <div class="item">
                    <a href="#" class="nav_link">
                        <span class="navlink_icon"><i class="bx bx-cog"></i></span>
                        <span class="navlink">Configuración</span>
                    </a>
                </div>
                <div class="bottom expand_sidebar">
                    <span>Expandir</span>
                    <i class='bx bx-log-in'></i>
                </div>
                <div class="bottom collapse_sidebar">
                    <span>Colapsar</span>
                    <i class='bx bx-log-out'></i>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido dinámico -->
    <div class="main-content">
        <?php include $view; ?>
    </div>

    <script src="js/script.js"></script>
</body>

</html>