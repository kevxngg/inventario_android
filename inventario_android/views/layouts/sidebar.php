<div class="sidebar glass-sidebar" id="sidebar-wrapper">
    
    <div class="sidebar-heading text-center py-4 primary-text fs-4 fw-bold text-uppercase border-bottom">
        <i class="fa-solid fa-layer-group me-2"></i> Constructora
    </div>

    <div class="list-group list-group-flush my-3">
        
        <?php $dashboardUrl = ($_SESSION['role'] == 'ADMIN') ? base_url.'Admin/dashboard' : base_url.'User/panel'; ?>
        
        <a href="<?= $dashboardUrl ?>" class="list-group-item list-group-item-action bg-transparent second-text active">
            <i class="fa-solid fa-chart-line me-2"></i> Dashboard
        </a>

        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'ADMIN'): ?>
            <div class="sidebar-label">GESTIÓN</div>
            
            <a href="<?= base_url ?>Admin/tools" class="list-group-item list-group-item-action bg-transparent second-text fw-bold">
                <i class="fa-solid fa-screwdriver-wrench me-2"></i> Inventario
            </a>
            
            <a href="<?= base_url ?>Admin/map" class="list-group-item list-group-item-action bg-transparent second-text fw-bold">
                <i class="fa-solid fa-map-location-dot me-2"></i> Mapa de Obras
            </a>
            
            <a href="<?= base_url ?>Admin/users" class="list-group-item list-group-item-action bg-transparent second-text fw-bold">
                <i class="fa-solid fa-users-gear me-2"></i> Usuarios & Roles
            </a>
            
            <a href="<?= base_url ?>Admin/reports" class="list-group-item list-group-item-action bg-transparent second-text fw-bold">
                <i class="fa-solid fa-file-contract me-2"></i> Reportes
            </a>
        <?php endif; ?>

        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'USER'): ?>
            <div class="sidebar-label">MI OBRA</div>
            
            <a href="<?= base_url ?>User/panel" class="list-group-item list-group-item-action bg-transparent second-text fw-bold">
                <i class="fa-solid fa-hard-hat me-2"></i> Mis Proyectos
            </a>
            
            <a href="<?= base_url ?>User/catalog" class="list-group-item list-group-item-action bg-transparent second-text fw-bold">
                <i class="fa-solid fa-cart-flatbed me-2"></i> Solicitar Equipo
            </a>
            
            <a href="<?= base_url ?>User/panel" class="list-group-item list-group-item-action bg-transparent second-text fw-bold">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> Reportar Daño
            </a>
        <?php endif; ?>

        <div class="mt-5">
            <a href="<?= base_url ?>Auth/logout" class="list-group-item list-group-item-action bg-transparent text-danger fw-bold logout-btn">
                <i class="fa-solid fa-power-off me-2"></i> Cerrar Sesión
            </a>
        </div>
    </div>
</div>