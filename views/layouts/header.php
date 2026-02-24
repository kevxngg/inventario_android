<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario Constructora</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="<?= base_url ?>assets/css/styles.css">
    <link rel="stylesheet" href="<?= base_url ?>assets/css/android-theme.css">
    
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        .avatar-circle, 
        .user-profile:hover .avatar-circle, 
        .user-profile.show .avatar-circle,
        [data-bs-toggle="dropdown"][aria-expanded="true"] .avatar-circle {
            width: 45px !important;
            height: 45px !important;
            border-radius: 50% !important; /* Obliga a que sea un círculo perfecto SIEMPRE */
            object-fit: cover !important;
            background-color: #ffffff !important;
            transition: none !important; /* Anula animaciones de Bootstrap que lo vuelven cuadrado */
        }
    </style>
</head>
<body class="android-bg">

    <div class="d-flex" id="wrapper">
        
        <?php require_once 'views/layouts/sidebar.php'; ?>

        <div id="page-content-wrapper" class="w-100">
            
            <nav class="navbar-glass d-flex justify-content-between align-items-center px-4 py-3 mb-4">
                
                <div class="d-flex align-items-center">
                    <button class="btn-icon me-3" id="menu-toggle"><i class="fa-solid fa-bars"></i></button>
                    <div>
                        <h4 class="m-0 fw-bold text-primary-dark">
                            Hola, <?= isset($_SESSION['identity']) ? explode(' ', $_SESSION['identity']->fullname)[0] : 'Usuario' ?> 👋
                        </h4>
                        <small class="text-muted">Panel de Control</small>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    
                    <div class="dropdown">
                        <div class="notification-badge position-relative" data-bs-toggle="dropdown" aria-expanded="false" id="bellBtn" style="cursor: pointer;">
                            <i class="fa-regular fa-bell fa-lg text-primary-dark"></i>
                            <span id="notif-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none; font-size: 0.6rem;">
                                0
                            </span>
                        </div>
                        
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow glass-card p-0" style="width: 320px; max-height: 400px; overflow-y: auto;">
                            <li class="p-3 border-bottom text-primary fw-bold bg-light rounded-top d-flex justify-content-between align-items-center">
                                <span>Notificaciones</span>
                                <small class="text-muted x-small fw-normal">Recientes</small>
                            </li>
                            <div id="notification-list">
                                <li class="p-3 text-center text-muted small">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div> Cargando...
                                </li>
                            </div>
                        </ul>
                    </div>
                    
                    <div class="dropdown">
                        <div class="user-profile d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                            <div class="text-end d-none d-md-block">
                                <span class="d-block fw-bold small text-primary"><?= $_SESSION['role'] ?? 'GUEST' ?></span>
                                <span class="d-block x-small text-muted" style="font-size: 0.75rem;">
                                    <?= isset($_SESSION['identity']) ? $_SESSION['identity']->email : '' ?>
                                </span>
                            </div>
                            
                            <?php 
                                $userImg = (isset($_SESSION['identity']) && !empty($_SESSION['identity']->image)) 
                                           ? $_SESSION['identity']->image 
                                           : 'default_user.png'; 
                            ?>
                            <img src="<?= base_url ?>assets/img/<?= htmlspecialchars($userImg, ENT_QUOTES, 'UTF-8') ?>" 
                                 class="avatar-circle border border-2 border-primary border-opacity-25" 
                                 style="border-radius: 50% !important; object-fit: cover !important;" 
                                 alt="User" 
                                 onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['identity']->fullname ?? 'U') ?>&background=0D8ABC&color=fff';">
                        </div>

                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow glass-card mt-2">
                            <li>
                                <a class="dropdown-item py-2" href="<?= base_url ?><?= ($_SESSION['role'] == 'ADMIN') ? 'Admin' : 'User' ?>/profile">
                                    <i class="fa-solid fa-user-gear me-2 text-primary"></i> Mi Perfil Personal
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item py-2 text-danger fw-bold" href="<?= base_url ?>Auth/logout">
                                    <i class="fa-solid fa-power-off me-2"></i> Finalizar Sesión
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>
            </nav>

            <div class="container-fluid px-4">