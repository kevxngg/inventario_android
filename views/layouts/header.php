<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SICOT ERP | Control Logístico</title> <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="<?= base_url ?>assets/css/styles.css">
    <link rel="stylesheet" href="<?= base_url ?>assets/css/android-theme.css">
    
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        /* Asegurar que el avatar sea un círculo perfecto */
        .avatar-circle, 
        .user-profile:hover .avatar-circle, 
        .user-profile.show .avatar-circle,
        [data-bs-toggle="dropdown"][aria-expanded="true"] .avatar-circle {
            width: 45px !important;
            height: 45px !important;
            border-radius: 50% !important; 
            object-fit: cover !important;
            background-color: #ffffff !important;
            transition: none !important; 
        }

        /* Animación para cuando llega una notificación nueva */
        @keyframes ring-bell {
            0% { transform: rotate(0); }
            10% { transform: rotate(15deg); }
            20% { transform: rotate(-10deg); }
            30% { transform: rotate(5deg); }
            40% { transform: rotate(-5deg); }
            50% { transform: rotate(0); }
            100% { transform: rotate(0); }
        }
        .bell-ringing i {
            animation: ring-bell 2s ease infinite;
            color: #dc3545 !important; /* Se pone roja si hay algo nuevo */
        }
    </style>
</head>
<body class="android-bg">

    <div class="d-flex" id="wrapper">
        
        <?php require_once 'views/layouts/sidebar.php'; ?>

        <div id="page-content-wrapper" class="w-100">
            
            <nav class="navbar-glass d-flex justify-content-between align-items-center px-4 py-3 mb-4">
                
                <div class="d-flex align-items-center">
                    <button class="btn-icon me-3 border-0 bg-transparent" id="menu-toggle">
                        <i class="fa-solid fa-bars fs-4 text-secondary"></i>
                    </button>
                    <div>
                        <h4 class="m-0 fw-bold text-primary-dark" style="letter-spacing: -0.5px;">
                            Hola, <?= isset($_SESSION['identity']) ? explode(' ', $_SESSION['identity']->fullname)[0] : 'Usuario' ?> 👋
                        </h4>
                        <small class="text-muted fw-semibold">Panel Logístico Central</small>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-4">
                    
                    <div class="dropdown">
                        <div class="notification-badge position-relative" data-bs-toggle="dropdown" aria-expanded="false" id="bellContainer" style="cursor: pointer;">
                            <i class="fa-regular fa-bell fa-lg text-secondary transition-all" id="bellIcon"></i>
                            <span id="notif-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white shadow-sm" style="display: none; font-size: 0.65rem;">
                                0
                            </span>
                        </div>
                        
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-0" style="width: 320px; max-height: 400px; overflow-y: auto; border-radius: 12px;">
                            <li class="p-3 border-bottom text-dark fw-bold bg-light d-flex justify-content-between align-items-center" style="border-radius: 12px 12px 0 0;">
                                <span><i class="fa-solid fa-inbox me-2 text-primary"></i> Bandeja de Entrada</span>
                            </li>
                            <div id="notification-list" class="py-1">
                                <li class="p-4 text-center text-muted small">
                                    <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div><br>
                                    Sincronizando...
                                </li>
                            </div>
                            <li class="p-0 border-top text-center" style="border-radius: 0 0 12px 12px; overflow: hidden;">
                                <?php $notifUrl = ($_SESSION['role'] == 'ADMIN') ? base_url.'Admin/reports' : base_url.'User/panel'; ?>
                                <a href="<?= $notifUrl ?>" class="d-block py-2 bg-light text-decoration-none small fw-bold text-primary">
                                    Ver todo el historial
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="dropdown border-start ps-3 border-secondary border-opacity-25">
                        <div class="user-profile d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                            <div class="text-end d-none d-md-block">
                                <span class="d-block fw-bold small text-dark"><?= $_SESSION['role'] == 'ADMIN' ? 'Administrador' : 'Jefe de Obra' ?></span>
                                <span class="d-block text-muted" style="font-size: 0.70rem; font-weight: 500;">
                                    <?= isset($_SESSION['identity']) ? $_SESSION['identity']->email : '' ?>
                                </span>
                            </div>
                            
                            <?php 
                                $userImg = (isset($_SESSION['identity']) && !empty($_SESSION['identity']->image)) 
                                           ? $_SESSION['identity']->image 
                                           : 'default_user.png'; 
                            ?>
                            <img src="<?= base_url ?>assets/img/<?= htmlspecialchars($userImg, ENT_QUOTES, 'UTF-8') ?>" 
                                 class="avatar-circle shadow-sm border border-2 border-white" 
                                 alt="User" 
                                 onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['identity']->fullname ?? 'U') ?>&background=004b87&color=fff';">
                        </div>

                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-3" style="border-radius: 12px;">
                            <li>
                                <?php $profileUrl = ($_SESSION['role'] == 'ADMIN') ? 'Admin/profile' : 'User/profile'; ?>
                                <a class="dropdown-item py-2 fw-semibold text-secondary" href="<?= base_url ?><?= $profileUrl ?>">
                                    <i class="fa-solid fa-user-gear me-2 text-primary"></i> Configuración de Cuenta
                                </a>
                            </li>
                            <li><hr class="dropdown-divider opacity-25"></li>
                            <li>
                                <a class="dropdown-item py-2 text-danger fw-bold" href="<?= base_url ?>Auth/logout">
                                    <i class="fa-solid fa-power-off me-2"></i> Cerrar Sesión Segura
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>
            </nav>

            <div class="container-fluid px-4">

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Variable para saber cuántas notificaciones teníamos antes (y saber si suena la campana)
    let previousCount = 0;
    
    // Función que trae la data del servidor
    function fetchNotifications() {
        // Asumiendo que ambos controladores tienen getNotifications()
        const controller = '<?= ($_SESSION['role'] == 'ADMIN') ? 'Admin' : 'User' ?>';
        const url = '<?= base_url ?>' + controller + '/getNotifications';

        fetch(url)
        .then(response => response.json())
        .then(data => {
            const list = document.getElementById('notification-list');
            const countBadge = document.getElementById('notif-count');
            const bellContainer = document.getElementById('bellContainer');
            const bellIcon = document.getElementById('bellIcon');
            
            // Si hay datos (notificaciones)
            if(data && data.length > 0) {
                // Actualizar el numerito
                countBadge.innerText = data.length;
                countBadge.style.display = 'block';
                
                // Si hay NUEVAS notificaciones que antes no estaban, hacer temblar la campana
                if(data.length > previousCount) {
                    bellContainer.classList.add('bell-ringing');
                    setTimeout(() => bellContainer.classList.remove('bell-ringing'), 4000); // Para a los 4 segs
                }
                previousCount = data.length;
                
                // Construir la lista HTML
                list.innerHTML = '';
                // Mostrar solo las últimas 5 para no saturar el menú
                data.slice(0, 5).forEach(item => {
                    // Adaptar el icono según si es admin o usuario
                    let icon = 'fa-solid fa-box-open text-primary';
                    if(item.type && item.type.includes('REPORTE')) {
                        icon = 'fa-solid fa-triangle-exclamation text-danger';
                    }

                    list.innerHTML += `
                        <a class="dropdown-item p-3 border-bottom d-flex align-items-start text-wrap" href="#">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px; flex-shrink: 0;">
                                <i class="${icon}"></i>
                            </div>
                            <div class="flex-grow-1" style="min-width: 0;">
                                <div class="fw-bold text-dark small" style="font-size: 0.8rem;">
                                    ${item.is_admin ? item.fullname : 'Estado de Solicitud'}
                                </div>
                                <div class="text-muted mb-1" style="font-size: 0.75rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    ${item.tool_name}
                                </div>
                                <div class="text-secondary opacity-75" style="font-size: 0.65rem;">
                                    <i class="fa-regular fa-clock me-1"></i> ${item.request_date}
                                </div>
                            </div>
                        </a>
                    `;
                });
            } else {
                // No hay nada pendiente
                countBadge.style.display = 'none';
                previousCount = 0;
                list.innerHTML = `
                    <li class="p-4 text-center text-muted">
                        <i class="fa-regular fa-face-smile fa-2x mb-2 opacity-50"></i><br>
                        <span class="small">Todo al día. No hay pendientes.</span>
                    </li>
                `;
            }
        })
        .catch(error => console.error("Error cargando notificaciones:", error));
    }

    // Ejecutar al cargar la página
    fetchNotifications();
    
    // Y luego ejecutar cada 15 segundos silenciosamente en el fondo
    setInterval(fetchNotifications, 15000);
});
</script>