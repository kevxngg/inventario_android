<div class="sidebar shadow-sm" id="sidebar-wrapper">
    
    <div class="sidebar-heading text-center py-4 primary-text fs-4 fw-bold text-uppercase border-bottom border-light">
        <div class="d-flex align-items-center justify-content-center gap-2">
            <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-building-user"></i>
            </div>
            <span style="letter-spacing: 1px; color: var(--primary-dark);">SICOT ERP</span>
        </div>
    </div>

    <div class="list-group list-group-flush my-3 px-2">
        
        <?php $dashboardUrl = ($_SESSION['role'] == 'ADMIN') ? base_url.'Admin/dashboard' : base_url.'User/dashboard'; ?>
        
        <a href="<?= $dashboardUrl ?>" class="list-group-item list-group-item-action rounded mb-1">
            <i class="fa-solid fa-chart-line me-2"></i> Panel de Control
        </a>

        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'ADMIN'): ?>
            <div class="sidebar-label mt-4 mb-2 px-3 small fw-bold text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px;">Control Administrativo</div>
            
            <a href="<?= base_url ?>Admin/tools" class="list-group-item list-group-item-action rounded mb-1">
                <i class="fa-solid fa-boxes-stacked me-2"></i> Maestro de Inventario
            </a>
            <a href="<?= base_url ?>Admin/workshop" class="list-group-item list-group-item-action rounded mb-1">
                <i class="fa-solid fa-screwdriver-wrench me-2"></i> Taller y Mantenimiento
            </a>
            <a href="<?= base_url ?>Admin/qrCatalog" class="list-group-item list-group-item-action rounded mb-1">
                <i class="fa-solid fa-qrcode me-2"></i> Motor de Etiquetas QR
            </a>
            <a href="<?= base_url ?>Admin/map" class="list-group-item list-group-item-action rounded mb-1">
                <i class="fa-solid fa-map-location-dot me-2"></i> Control Geográfico
            </a>
            <a href="<?= base_url ?>Admin/users" class="list-group-item list-group-item-action rounded mb-1">
                <i class="fa-solid fa-users-gear me-2"></i> Gestión de Personal
            </a>
            <a href="<?= base_url ?>Admin/reports" class="list-group-item list-group-item-action rounded mb-1">
                <i class="fa-solid fa-clipboard-check me-2"></i> Auditoría y Retornos
            </a>    
            
            <a href="<?= base_url ?>Admin/helpDesk" class="list-group-item list-group-item-action rounded mb-1 fw-bold text-primary" style="background-color: rgba(13, 110, 253, 0.05);">
                <i class="fa-solid fa-headset me-2"></i> Mesa de Ayuda
            </a>
            
            <a href="<?= base_url ?>Admin/auditTrail" class="list-group-item list-group-item-action rounded mb-1 fw-bold mt-2" style="background-color: rgba(220, 53, 69, 0.05);">
                <i class="fa-solid fa-server me-2 text-danger"></i> Trazabilidad Log
            </a>
            
        <?php endif; ?>

        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'USER'): ?>
            <div class="sidebar-label mt-4 mb-2 px-3 small fw-bold text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px;">Operaciones en Obra</div>
            
            <a href="<?= base_url ?>User/panel" class="list-group-item list-group-item-action rounded mb-1">
                <i class="fa-solid fa-helmet-safety me-2"></i> Proyectos Asignados
            </a>
            <a href="<?= base_url ?>User/catalog" class="list-group-item list-group-item-action rounded mb-1">
                <i class="fa-solid fa-list-check me-2"></i> Catálogo de Activos
            </a>
            <a href="<?= base_url ?>User/myTools" class="list-group-item list-group-item-action rounded mb-1">
                <i class="fa-solid fa-toolbox me-2"></i> Mis Herramientas
            </a>
            <a href="<?= base_url ?>User/reportView" class="list-group-item list-group-item-action rounded mb-1">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> Registro de Incidencias
            </a>

            <a href="<?= base_url ?>User/helpDesk" class="list-group-item list-group-item-action rounded mb-1 fw-bold text-primary mt-2" style="background-color: rgba(13, 110, 253, 0.05);">
                <i class="fa-solid fa-headset me-2"></i> Centro de Soporte
            </a>
        <?php endif; ?>
        
    </div>
</div>