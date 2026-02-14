<div class="sidebar" id="sidebar-wrapper">
    
    <div class="sidebar-heading text-center py-4 primary-text fs-4 fw-bold text-uppercase border-bottom border-light">
        <div class="d-flex align-items-center justify-content-center gap-2">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <span style="letter-spacing: 1px; color: var(--primary-dark);">Constructora</span>
        </div>
    </div>

    <div class="list-group list-group-flush my-3 px-2">
        
        <?php $dashboardUrl = ($_SESSION['role'] == 'ADMIN') ? base_url.'Admin/dashboard' : base_url.'User/dashboard'; ?>
        
        <a href="<?= $dashboardUrl ?>" class="list-group-item list-group-item-action">
            <i class="fa-solid fa-chart-pie me-2"></i> Dashboard
        </a>

        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'ADMIN'): ?>
            <div class="sidebar-label mt-3 mb-2 px-3 small fw-bold text-uppercase text-muted" style="font-size: 0.7rem;">Gestión Administrativa</div>
            
            <a href="<?= base_url ?>Admin/tools" class="list-group-item list-group-item-action">
                <i class="fa-solid fa-toolbox me-2"></i> Inventario
            </a>
            <a href="<?= base_url ?>Admin/map" class="list-group-item list-group-item-action">
                <i class="fa-solid fa-map-location-dot me-2"></i> Mapa de Obras
            </a>
            <a href="<?= base_url ?>Admin/users" class="list-group-item list-group-item-action">
                <i class="fa-solid fa-users me-2"></i> Usuarios
            </a>
            <a href="<?= base_url ?>Admin/reports" class="list-group-item list-group-item-action">
                <i class="fa-solid fa-file-contract me-2"></i> Auditoría
            </a>
        <?php endif; ?>

        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'USER'): ?>
            <div class="sidebar-label mt-3 mb-2 px-3 small fw-bold text-uppercase text-muted" style="font-size: 0.7rem;">Zona de Obra</div>
            
            <a href="<?= base_url ?>User/panel" class="list-group-item list-group-item-action">
                <i class="fa-solid fa-helmet-safety me-2"></i> Mis Proyectos
            </a>
            <a href="<?= base_url ?>User/catalog" class="list-group-item list-group-item-action">
                <i class="fa-solid fa-truck-front me-2"></i> Catálogo
            </a>
            <a href="<?= base_url ?>User/reportView" class="list-group-item list-group-item-action">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> Reportar
            </a>
        <?php endif; ?>

        <div class="mt-auto pt-4 pb-4">
            <a href="<?= base_url ?>Auth/logout" class="list-group-item list-group-item-action text-danger logout-btn border-0">
                <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Cerrar Sesión
            </a>
        </div>
    </div>
</div>