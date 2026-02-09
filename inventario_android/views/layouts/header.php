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
                        <div class="notification-badge" data-bs-toggle="dropdown" aria-expanded="false" id="bellBtn" style="cursor: pointer;">
                            <i class="fa-regular fa-bell"></i>
                            <span class="badge-dot" id="notif-dot" style="display:none;"></span>
                        </div>
                        
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow glass-card p-0" style="width: 320px; max-height: 400px; overflow-y: auto;">
                            <li class="p-3 border-bottom text-primary fw-bold bg-light rounded-top">
                                Notificaciones
                            </li>
                            <div id="notification-list">
                                <li class="p-3 text-center text-muted small">Cargando...</li>
                            </div>
                        </ul>
                    </div>
                    
                    <div class="user-profile d-flex align-items-center gap-2">
                        <div class="text-end d-none d-md-block">
                            <span class="d-block fw-bold small text-primary"><?= $_SESSION['role'] ?? 'GUEST' ?></span>
                            <span class="d-block x-small text-muted" style="font-size: 0.75rem;">
                                <?= isset($_SESSION['identity']) ? $_SESSION['identity']->email : '' ?>
                            </span>
                        </div>
                        <img src="<?= base_url ?>assets/img/default.png" class="avatar-circle" alt="User" onerror="this.src='https://ui-avatars.com/api/?name=User&background=0D8ABC&color=fff';">
                    </div>
                </div>
            </nav>

            <div class="container-fluid px-4">