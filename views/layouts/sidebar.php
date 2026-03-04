<div class="sidebar" id="sidebar-wrapper">
    
    <div class="sidebar-heading text-center py-4 border-bottom" style="border-color: rgba(255,255,255,0.05) !important;">
        <div class="d-flex align-items-center justify-content-center gap-3">
            <div class="rounded d-flex align-items-center justify-content-center shadow" style="width: 45px; height: 45px; background-color: var(--safety-orange); color: white;">
                <i class="fa-solid fa-helmet-safety fs-4"></i>
            </div>
            <div class="text-start">
                <span class="d-block fw-bold text-white fs-5" style="letter-spacing: 1px; line-height: 1;">SICOT</span>
                <span class="d-block text-uppercase" style="font-size: 0.7rem; color: #94a3b8; letter-spacing: 2px;">ERP Industrial</span>
            </div>
        </div>
    </div>

    <div class="list-group list-group-flush my-3 px-2">
        
        <?php $dashboardUrl = ($_SESSION['role'] == 'ADMIN') ? base_url.'Admin/dashboard' : base_url.'User/dashboard'; ?>
        
        <a href="<?= $dashboardUrl ?>" class="list-group-item list-group-item-action">
            <i class="fa-solid fa-chart-line me-2"></i> Monitor de Operaciones
        </a>

        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'ADMIN'): ?>
            
            <div class="sidebar-label">Gestión Logística</div>
            
            <a href="<?= base_url ?>Admin/tools" class="list-group-item list-group-item-action">
                <i class="fa-solid fa-boxes-stacked me-2"></i> Maestro de Activos
            </a>
            <a href="<?= base_url ?>Admin/workshop" class="list-group-item list-group-item-action">
                <i class="fa-solid fa-screwdriver-wrench me-2"></i> Control de Taller
            </a>
            <a href="<?= base_url ?>Admin/qrCatalog" class="list-group-item list-group-item-action">
                <i class="fa-solid fa-qrcode me-2"></i> Generador Etiquetado QR
            </a>
            <a href="<?= base_url ?>Admin/map" class="list-group-item list-group-item-action">
                <i class="fa-solid fa-map-location-dot me-2"></i> Geolocalización GPS
            </a>
            
            <div class="sidebar-label">Administración</div>

            <a href="<?= base_url ?>Admin/users" class="list-group-item list-group-item-action">
                <i class="fa-solid fa-users-gear me-2"></i> Plantilla de Personal
            </a>
            <a href="<?= base_url ?>Admin/reports" class="list-group-item list-group-item-action">
                <i class="fa-solid fa-clipboard-check me-2"></i> Actas y Auditoría
            </a>    
            
            <a href="<?= base_url ?>Admin/helpDesk" class="list-group-item list-group-item-action mt-3 border border-secondary border-opacity-25" style="background-color: rgba(255,255,255,0.03);">
                <i class="fa-solid fa-headset me-2 text-info"></i> Centro de Incidencias
            </a>
            
            <a href="<?= base_url ?>Admin/auditTrail" class="list-group-item list-group-item-action border border-secondary border-opacity-25 mt-2" style="background-color: rgba(220, 38, 38, 0.1);">
                <i class="fa-solid fa-server me-2 text-danger"></i> Caja Negra (Logs)
            </a>
            
        <?php endif; ?>

        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'USER'): ?>
            
            <div class="sidebar-label">Trabajo de Campo</div>
            
            <a href="<?= base_url ?>User/panel" class="list-group-item list-group-item-action">
                <i class="fa-solid fa-location-crosshairs me-2"></i> Frentes Asignados
            </a>
            <a href="<?= base_url ?>User/catalog" class="list-group-item list-group-item-action">
                <i class="fa-solid fa-truck-ramp-box me-2"></i> Solicitud de Equipos
            </a>
            <a href="<?= base_url ?>User/myTools" class="list-group-item list-group-item-action">
                <i class="fa-solid fa-toolbox me-2"></i> Billetera de Activos
            </a>
            <a href="<?= base_url ?>User/reportView" class="list-group-item list-group-item-action text-danger fw-bold mt-3" style="background-color: rgba(220, 38, 38, 0.1); border: 1px solid rgba(220, 38, 38, 0.2);">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> Reportar Avería
            </a>

            <a href="<?= base_url ?>User/helpDesk" class="list-group-item list-group-item-action mt-2 border border-secondary border-opacity-25" style="background-color: rgba(255,255,255,0.03);">
                <i class="fa-solid fa-headset me-2 text-info"></i> Seguimiento de Tickets
            </a>
        <?php endif; ?>
        
    </div>
</div>