<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Constructora | Gestión Inteligente</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="<?= base_url ?>assets/css/android-theme.css">
    
    <style>
        /* Estilos específicos solo para la Landing */
        .hero-section {
            min-height: 90vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        
        /* Círculos decorativos de fondo (Bubbles) */
        .bubble {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(204, 229, 255, 0.5), rgba(255, 255, 255, 0.1));
            z-index: -1;
            animation: float 6s ease-in-out infinite;
        }
        .bubble-1 { top: -10%; right: -5%; width: 500px; height: 500px; }
        .bubble-2 { bottom: 10%; left: -10%; width: 300px; height: 300px; animation-delay: 2s; }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>
<body class="android-bg">

    <nav class="navbar navbar-expand-lg fixed-top mt-3 mx-3 rounded-pill glass-card py-2 px-4 shadow-sm fade-in-down">
        <div class="container-fluid">
            
            <a class="navbar-brand fw-bold text-primary-dark fs-4" href="#">
                <i class="fa-solid fa-layer-group me-2"></i>Constructora
            </a>

            <div class="d-flex gap-3">
                <a href="<?= base_url ?>Auth/login" class="btn btn-outline-primary rounded-pill px-4 fw-bold">
                    Iniciar Sesión
                </a>
                <a href="<?= base_url ?>Auth/register" class="btn btn-android rounded-pill px-4 shadow-sm">
                    Registrarse
                </a>
            </div>
        </div>
    </nav>

    <header class="hero-section">
        <div class="bubble bubble-1"></div>
        <div class="bubble bubble-2"></div>

        <div class="container">
            <div class="row align-items-center">
                
                <div class="col-lg-6 fade-in-up">
                    <span class="badge bg-primary-light text-primary-dark px-3 py-2 rounded-pill mb-3 fw-bold">
                        🚀 Versión 2.0 con Android Style
                    </span>
                    <h1 class="display-3 fw-bolder text-dark mb-4" style="line-height: 1.1;">
                        Control total de tu <br>
                        <span class="text-primary">Maquinaria y Obras</span>
                    </h1>
                    <p class="lead text-muted mb-5" style="max-width: 90%;">
                        Optimiza los recursos de tu constructora. Gestiona inventarios, asigna equipos y visualiza tus proyectos en un mapa interactivo en tiempo real.
                    </p>
                    
                    <div class="d-flex gap-3">
                        <a href="<?= base_url ?>Auth/register" class="btn btn-android btn-lg px-5 py-3 shadow">
                            Empezar Ahora <i class="fa-solid fa-arrow-right ms-2"></i>
                        </a>
                        <a href="#features" class="btn btn-outline-dark btn-lg px-4 py-3 rounded-pill border-0 bg-white shadow-sm">
                            <i class="fa-solid fa-play me-2 text-primary"></i> Ver Demo
                        </a>
                    </div>
                </div>

                <div class="col-lg-6 mt-5 mt-lg-0 text-center fade-in-up" style="animation-delay: 0.2s;">
                    <div class="glass-card p-4 d-inline-block position-relative rotate-3d">
                        <i class="fa-solid fa-helmet-safety text-primary" style="font-size: 180px; opacity: 0.8;"></i>
                        
                        <div class="glass-card position-absolute bottom-0 start-0 mb-4 ms-n4 p-3 shadow text-start animate-float">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-success text-white rounded-circle p-2">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">Sistema Activo</h6>
                                    <small class="text-muted">Inventario Sincronizado</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </header>

    <section id="features" class="py-5">
        <div class="container">
            <div class="row g-4">
                
                <div class="col-md-4 fade-in-up" style="animation-delay: 0.1s;">
                    <div class="glass-card p-4 h-100 hover-elevate">
                        <div class="icon-box bg-primary-light text-primary rounded-circle p-3 d-inline-block mb-3">
                            <i class="fa-solid fa-boxes-stacked fa-2x"></i>
                        </div>
                        <h4 class="fw-bold">Gestión de Inventario</h4>
                        <p class="text-muted">Controla maquinaria pesada, herramientas y vehículos. Conoce su estado y ubicación exacta.</p>
                    </div>
                </div>

                <div class="col-md-4 fade-in-up" style="animation-delay: 0.2s;">
                    <div class="glass-card p-4 h-100 hover-elevate">
                        <div class="icon-box bg-warning-light text-warning rounded-circle p-3 d-inline-block mb-3">
                            <i class="fa-solid fa-map-location-dot fa-2x"></i>
                        </div>
                        <h4 class="fw-bold">Geolocalización</h4>
                        <p class="text-muted">Asigna obras en el mapa y rastrea dónde está cada equipo en tiempo real.</p>
                    </div>
                </div>

                <div class="col-md-4 fade-in-up" style="animation-delay: 0.3s;">
                    <div class="glass-card p-4 h-100 hover-elevate">
                        <div class="icon-box bg-success-light text-success rounded-circle p-3 d-inline-block mb-3">
                            <i class="fa-solid fa-user-shield fa-2x"></i>
                        </div>
                        <h4 class="fw-bold">Roles y Permisos</h4>
                        <p class="text-muted">Panel exclusivo para administradores y vista simplificada para contratistas y operarios.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <footer class="text-center py-4 text-muted small">
        <p class="mb-0">&copy; 2026 Sistema de Gestión Avanzado. Desarrollado con PHP MVC.</p>
    </footer>

</body>
</html>