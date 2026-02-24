<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SICOT ERP | Gestión Logística para Construcción</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8f9fc;
            color: #333333;
            overflow-x: hidden;
        }

        .navbar-custom {
            background-color: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            border-bottom: 1px solid #eef2f7;
        }

        /* Hero Section Centrado y Tipográfico */
        .hero-section {
            padding-top: 160px;
            padding-bottom: 100px;
            background: radial-gradient(circle at 50% 0%, #ffffff 0%, #f0f4f8 100%);
            border-bottom: 1px solid #eef2f7;
            text-align: center;
        }

        .text-corporate {
            color: #004b87;
        }

        .bg-corporate {
            background-color: #004b87;
        }

        .btn-corporate-primary {
            background-color: #004b87;
            color: #ffffff;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-corporate-primary:hover {
            background-color: #003666;
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 75, 135, 0.25);
        }

        .btn-corporate-outline {
            background-color: transparent;
            color: #004b87;
            border: 2px solid #004b87;
            transition: all 0.3s ease;
        }

        .btn-corporate-outline:hover {
            background-color: #004b87;
            color: #ffffff;
        }

        /* Tarjetas de Información */
        .info-card {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 2.5rem;
            height: 100%;
            border: 1px solid #eef2f7;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .info-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background-color: #eef2f7;
            transition: all 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.08);
        }

        .info-card:hover::before {
            background-color: #004b87;
        }

        .icon-wrapper-large {
            width: 80px;
            height: 80px;
            border-radius: 15px;
            background-color: rgba(0, 75, 135, 0.05);
            color: #004b87;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 2rem auto;
        }

        /* Sección de Detalles Técnicos */
        .tech-list-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        .tech-icon {
            min-width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e8f1f8;
            color: #004b87;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg fixed-top navbar-custom py-3">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center text-corporate" href="#">
                <i class="fa-solid fa-building-user me-2 fs-4"></i>
                SICOT ERP
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <i class="fa-solid fa-bars text-dark"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link fw-semibold text-dark px-3" href="#solucion">La Solución</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold text-dark px-3" href="#modulos">Módulos Core</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold text-dark px-3" href="#tecnologia">Infraestructura</a></li>
                </ul>
                <div class="d-flex gap-2 mt-3 mt-lg-0">
                    <a href="<?= base_url ?>Auth/login" class="btn btn-corporate-outline fw-bold px-4 rounded-pill">
                        Acceso
                    </a>
                    <a href="<?= base_url ?>Auth/register" class="btn btn-corporate-primary fw-bold px-4 rounded-pill">
                        Registro
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <header class="hero-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <span class="badge bg-light text-corporate border border-primary px-4 py-2 fw-bold text-uppercase rounded-pill mb-4" style="letter-spacing: 2px;">
                        Sistema de Planificación de Recursos Empresariales
                    </span>
                    <h1 class="display-3 fw-bolder text-dark mb-4" style="line-height: 1.15;">
                        Inteligencia Logística para el <br>
                        <span class="text-corporate">Sector Construcción</span>
                    </h1>
                    <p class="lead text-secondary mb-5 mx-auto" style="max-width: 800px; font-size: 1.25rem;">
                        SICOT (Sistema de Inventarios y Control de Obras y Herramientas) centraliza la administración de su maquinaria pesada y activos físicos. Elimine las pérdidas en terreno, audite las asignaciones en tiempo real y garantice la rentabilidad de sus proyectos mediante un control exhaustivo del ciclo de vida de cada equipo.
                    </p>
                    
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="<?= base_url ?>Auth/login" class="btn btn-corporate-primary btn-lg px-5 py-3 rounded-pill fw-bold shadow-sm">
                            Iniciar Sesión en el Panel <i class="fa-solid fa-arrow-right ms-2"></i>
                        </a>
                        <a href="#solucion" class="btn btn-white btn-lg px-5 py-3 rounded-pill fw-bold shadow-sm border text-dark">
                            Explorar Funcionalidades
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="row justify-content-center mt-5 pt-4">
                <div class="col-md-8">
                    <div class="row text-center g-4">
                        <div class="col-sm-4 border-end">
                            <h2 class="fw-bolder text-dark mb-0">100%</h2>
                            <span class="text-muted small fw-bold text-uppercase">Trazabilidad</span>
                        </div>
                        <div class="col-sm-4 border-end">
                            <h2 class="fw-bolder text-dark mb-0"><i class="fa-solid fa-lock text-corporate me-2"></i>ACID</h2>
                            <span class="text-muted small fw-bold text-uppercase">Base de Datos Segura</span>
                        </div>
                        <div class="col-sm-4">
                            <h2 class="fw-bolder text-dark mb-0"><i class="fa-solid fa-clock-rotate-left text-corporate me-2"></i>24/7</h2>
                            <span class="text-muted small fw-bold text-uppercase">Auditoría Continua</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section id="solucion" class="py-5 bg-white">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-lg-5 mb-5 mb-lg-0 pe-lg-5">
                    <h6 class="text-corporate fw-bold text-uppercase mb-3" style="letter-spacing: 2px;">Gestión de Pérdidas</h6>
                    <h2 class="fw-bold text-dark mb-4 display-6">¿Por qué implementar SICOT en su constructora?</h2>
                    <p class="text-secondary mb-4">
                        Uno de los mayores sumideros financieros en la industria de la construcción es la pérdida, daño no reportado o mala asignación de herramientas y maquinaria. El control en hojas de cálculo estáticas es insuficiente ante la dinámica de múltiples frentes de obra simultáneos.
                    </p>
                    <p class="text-secondary mb-4">
                        SICOT actúa como un puente digital entre la bodega central y el personal en terreno. Cada solicitud de equipo debe ser aprobada, cada despacho se vincula a un centro de costo geolocalizado, y cada retorno exige una evaluación física.
                    </p>
                </div>
                
                <div class="col-lg-7">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-card bg-light border-0">
                                <i class="fa-solid fa-money-bill-trend-up fs-2 text-corporate mb-3"></i>
                                <h5 class="fw-bold text-dark">Reducción de Costos</h5>
                                <p class="text-muted small mb-0">Evite la compra de herramientas duplicadas. Conozca exactamente qué obra tiene el equipo que necesita y reasígnelo eficientemente.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card bg-light border-0">
                                <i class="fa-solid fa-file-contract fs-2 text-corporate mb-3"></i>
                                <h5 class="fw-bold text-dark">Responsabilidad</h5>
                                <p class="text-muted small mb-0">Cada asignación queda registrada a nombre de un operario o ingeniero, forzando el cuidado del activo y facilitando auditorías precisas.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card bg-light border-0">
                                <i class="fa-solid fa-screwdriver-wrench fs-2 text-corporate mb-3"></i>
                                <h5 class="fw-bold text-dark">Mantenimiento Oportuno</h5>
                                <p class="text-muted small mb-0">Gestione el estado de sus máquinas. Si un equipo reporta daños, el sistema bloquea su salida hasta que sea reparado en taller.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card bg-light border-0">
                                <i class="fa-solid fa-mobile-screen fs-2 text-corporate mb-3"></i>
                                <h5 class="fw-bold text-dark">Acceso en Terreno</h5>
                                <p class="text-muted small mb-0">Los residentes de obra pueden consultar el catálogo de bodega y emitir solicitudes digitales directamente desde sus dispositivos móviles.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="modulos" class="py-5" style="background-color: #f0f4f8;">
        <div class="container py-5">
            <div class="text-center mb-5 pb-4">
                <h6 class="text-corporate fw-bold text-uppercase" style="letter-spacing: 2px;">Capacidades del Sistema</h6>
                <h2 class="fw-bold text-dark display-6">Módulos de Gestión Empresarial</h2>
            </div>

            <div class="row g-4 text-center">
                
                <div class="col-lg-4 col-md-6">
                    <div class="info-card">
                        <div class="icon-wrapper-large">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-3">Inventario Maestro</h4>
                        <p class="text-secondary small mb-0">Catálogo centralizado de activos. Soporte para maquinaria pesada, herramientas manuales, equipos de seguridad y vehículos. Visualización inmediata de stock total, unidades en uso y alertas automáticas por puntos de reorden mínimos.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="info-card">
                        <div class="icon-wrapper-large">
                            <i class="fa-solid fa-map-location-dot"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-3">Trazabilidad Geográfica</h4>
                        <p class="text-secondary small mb-0">Integración de mapas interactivos. Alta de proyectos y frentes de obra con coordenadas GPS. El sistema enlaza lógicamente cada despacho de inventario al proyecto de destino, evitando los "inventarios huérfanos".</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="info-card">
                        <div class="icon-wrapper-large">
                            <i class="fa-solid fa-clipboard-check"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-3">Protocolo de Check-In</h4>
                        <p class="text-secondary small mb-0">Módulo de auditoría para el retorno de activos. Al recibir un equipo, el administrador debe clasificar su estado físico (Buen estado, Dañado, Perdido). El sistema actualiza el Kardex e inhabilita las herramientas que requieran taller.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section id="tecnologia" class="py-5 bg-white border-top border-light">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="bg-corporate text-white rounded-4 p-5 shadow-lg relative overflow-hidden">
                        
                        <div class="row align-items-center">
                            <div class="col-md-6 mb-4 mb-md-0">
                                <h3 class="fw-bold mb-4">Infraestructura y Seguridad a Nivel de Código</h3>
                                <p class="opacity-75 mb-4">
                                    SICOT no es solo una interfaz visual. El núcleo lógico de la plataforma está diseñado para soportar la concurrencia operativa de grandes empresas sin comprometer la integridad de los datos financieros y logísticos.
                                </p>
                                <a href="<?= base_url ?>Auth/register" class="btn btn-light text-corporate fw-bold px-4 py-2 rounded-pill">
                                    Crear Cuenta Corporativa
                                </a>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="bg-white text-dark p-4 rounded-3 shadow-sm">
                                    <div class="tech-list-item">
                                        <div class="tech-icon"><i class="fa-solid fa-database"></i></div>
                                        <div>
                                            <h6 class="fw-bold mb-1">Transacciones ACID y Bloqueos</h6>
                                            <p class="small text-muted mb-0">Implementación de <code class="text-corporate bg-light px-1 rounded">FOR UPDATE</code>. Previene inconsistencias de inventario incluso cuando decenas de usuarios solicitan material en el mismo milisegundo.</p>
                                        </div>
                                    </div>
                                    <div class="tech-list-item">
                                        <div class="tech-icon"><i class="fa-solid fa-shield-cat"></i></div>
                                        <div>
                                            <h6 class="fw-bold mb-1">Criptografía y Blindaje</h6>
                                            <p class="small text-muted mb-0">Uso estricto de Sentencias Preparadas (Prepared Statements) para hacer matemáticamente imposible la Inyección SQL, asegurando sus registros.</p>
                                        </div>
                                    </div>
                                    <div class="tech-list-item mb-0">
                                        <div class="tech-icon"><i class="fa-solid fa-network-wired"></i></div>
                                        <div>
                                            <h6 class="fw-bold mb-1">Arquitectura MVC Estricta</h6>
                                            <p class="small text-muted mb-0">Código modular estructurado mediante el patrón Modelo-Vista-Controlador, garantizando alta escalabilidad para futuras integraciones y aplicaciones móviles.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="text-center py-5 bg-dark text-light border-top border-secondary">
        <div class="container">
            <div class="d-flex align-items-center justify-content-center mb-4">
                <i class="fa-solid fa-building-user fs-3 text-secondary me-3"></i>
                <h4 class="fw-bold mb-0">SICOT ERP</h4>
            </div>
            <p class="mb-4 text-secondary mx-auto" style="max-width: 600px;">
                Sistema de Inventarios y Control de Obras y Herramientas. Solución logística integral para la optimización de procesos en el sector de la construcción y maquinaria pesada.
            </p>
            <div class="d-flex justify-content-center gap-4 mb-4 border-top border-secondary pt-4 mx-auto" style="max-width: 400px;">
                <a href="#" class="text-secondary text-decoration-none hover-white"><i class="fa-solid fa-shield-halved me-1"></i> Políticas de Privacidad</a>
                <a href="#" class="text-secondary text-decoration-none hover-white"><i class="fa-solid fa-file-signature me-1"></i> Términos de Servicio</a>
            </div>
            <p class="mb-0 small text-secondary fw-semibold">&copy; <?= date('Y') ?> Derechos Reservados. Arquitectura de Software Institucional.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>