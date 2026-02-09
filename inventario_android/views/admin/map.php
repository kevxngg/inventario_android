<?php require_once 'views/layouts/header.php'; ?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
    /* =========================================
       1. ESTILOS GENERALES Y MODALES
       ========================================= */
    .modal-backdrop { z-index: 1040 !important; }
    .modal { z-index: 1050 !important; }
    #mainMap { z-index: 1; background: #f8f9fa; }
    
    /* Scrollbar invisible para la lista pero funcional */
    .custom-scroll::-webkit-scrollbar { width: 6px; }
    .custom-scroll::-webkit-scrollbar-thumb { background-color: #dee2e6; border-radius: 4px; }
    .custom-scroll:hover::-webkit-scrollbar-thumb { background-color: #adb5bd; }

    /* =========================================
       2. LISTA DE PROYECTOS (SIDEBAR MODERNO)
       ========================================= */
    .sidebar-container {
        background-color: #fcfcfc;
    }

    /* Estilo del Botón Nueva Obra */
    .btn-gradient {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        border: none;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        transition: all 0.3s ease;
    }
    .btn-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(13, 110, 253, 0.4);
    }

    /* TARJETA DE PROYECTO */
    .project-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #f0f0f0;
        padding: 15px;
        margin-bottom: 12px;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        position: relative;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }

    .project-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08);
        border-color: #cce5ff;
    }

    /* Icono del Proyecto (Izquierda) */
    .project-icon-box {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        background: #e7f1ff; /* Azul muy suave */
        color: #0d6efd;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0; /* Para que no se aplaste */
    }

    /* Texto */
    .project-title { font-weight: 700; color: #2c3e50; font-size: 0.95rem; margin-bottom: 2px; }
    .project-address { font-size: 0.8rem; color: #6c757d; display: block; }
    
    /* Botón de borrar (flotante o sutil) */
    .btn-delete-item {
        color: #adb5bd;
        transition: color 0.2s;
        padding: 5px;
    }
    .btn-delete-item:hover { color: #dc3545; }

    /* Estado (Badge) */
    .status-dot {
        height: 8px; width: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }
    .status-active { background-color: #198754; box-shadow: 0 0 5px #198754; }
    .status-plan { background-color: #ffc107; }


    /* =========================================
       3. ESTILOS DEL PIN EN EL MAPA
       ========================================= */
    .custom-div-icon { background: transparent; border: none; }
    .pin-wrap { position: relative; display: flex; justify-content: center; align-items: center; width: 50px; height: 50px; }
    .pin-marker {
        width: 36px; height: 36px;
        background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
        border: 2px solid white;
        border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg);
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        display: flex; justify-content: center; align-items: center;
        z-index: 2; transition: transform 0.2s;
    }
    .pin-icon { transform: rotate(45deg); color: white; font-size: 14px; }
    .pin-wrap:hover .pin-marker { transform: rotate(-45deg) scale(1.1); cursor: pointer; }
    .pin-pulse {
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
        width: 10px; height: 10px; background: rgba(255, 75, 43, 0.4);
        border-radius: 50%; animation: pulse 2s infinite; z-index: 1;
    }
    @keyframes pulse {
        0% { width: 10px; height: 10px; opacity: 1; }
        100% { width: 60px; height: 60px; opacity: 0; }
    }
    .custom-tooltip {
        background: white; border: none; box-shadow: 0 2px 15px rgba(0,0,0,0.15);
        border-radius: 8px; padding: 5px 10px; font-weight: 700; color: #333; font-size: 12px;
    }
    .custom-tooltip::before { display: none; }
    .popup-header { background: #0d6efd; color: white; padding: 10px 15px; font-weight: bold; }
    .popup-body { padding: 15px; }
</style>

<div class="row h-100 g-0">
    
    <div class="col-md-3 border-end shadow-sm d-flex flex-column sidebar-container" style="height: calc(100vh - 80px); z-index: 2;">
        
        <div class="p-4 border-bottom bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark m-0">📍 Obras Activas</h5>
                <span class="badge bg-light text-dark border"><?= $activeProjects ?? 0 ?> Total</span>
            </div>
            <button class="btn btn-gradient text-white w-100 py-2 fw-bold rounded-pill" data-bs-toggle="modal" data-bs-target="#modalProject">
                <i class="fa-solid fa-plus me-2"></i> Nueva Obra
            </button>
        </div>

        <div class="p-3 overflow-auto custom-scroll flex-grow-1">
            <?php if(isset($obras) && $obras): while($obra = $obras->fetch_object()): ?>
                
                <div class="project-card d-flex align-items-center" onclick="flyTo(<?= $obra->lat ?>, <?= $obra->lng ?>)">
                    
                    <div class="project-icon-box me-3">
                        <i class="fa-solid fa-city"></i>
                    </div>
                    
                    <div class="flex-grow-1" style="min-width: 0;"> <div class="d-flex justify-content-between align-items-start">
                            <h6 class="project-title text-truncate"><?= $obra->name ?></h6>
                            
                            <a href="<?= base_url ?>Admin/deleteProject?id=<?= $obra->id ?>" 
                               class="btn-delete-item" 
                               onclick="event.stopPropagation(); return confirm('¿Estás seguro de eliminar esta obra?');"
                               title="Eliminar Obra">
                                <i class="fa-solid fa-trash-can"></i>
                            </a>
                        </div>
                        
                        <p class="project-address text-truncate mb-1">
                            <i class="fa-solid fa-location-dot me-1 text-danger"></i> <?= $obra->location_address ?>
                        </p>
                        
                        <div class="d-flex align-items-center mt-1">
                            <?php if($obra->status == 'EN_EJECUCION'): ?>
                                <span class="status-dot status-active"></span> <span style="font-size: 11px; color: #198754; font-weight: 600;">En Ejecución</span>
                            <?php else: ?>
                                <span class="status-dot status-plan"></span> <span style="font-size: 11px; color: #b58900; font-weight: 600;">Planificación</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <script>
                    window.projectData = window.projectData || [];
                    window.projectData.push({
                        lat: <?= $obra->lat ?>, 
                        lng: <?= $obra->lng ?>, 
                        title: "<?= $obra->name ?>", 
                        address: "<?= $obra->location_address ?>"
                    });
                </script>

            <?php endwhile; endif; ?>
            
            <?php if(!isset($obras) || $obras->num_rows == 0): ?>
                <div class="text-center mt-5 text-muted">
                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486747.png" width="80" class="mb-3 opacity-50" alt="Empty">
                    <p class="fw-bold">No hay obras registradas</p>
                    <small>Usa el botón para crear la primera.</small>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-md-9 position-relative">
        <div id="mainMap" style="width: 100%; height: calc(100vh - 80px);"></div>
    </div>
</div>

<div class="modal fade" id="modalProject" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-primary text-white" style="border-radius: 16px 16px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-hard-hat me-2"></i>Nueva Obra</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url ?>Admin/saveProject" method="POST">
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold text-secondary small">NOMBRE DEL PROYECTO</label>
                                <input type="text" name="name" class="form-control form-control-lg fw-bold" placeholder="Ej: Torre Empresarial" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="fw-bold text-secondary small">UBICACIÓN (BUSCAR)</label>
                                <div class="input-group">
                                    <input type="text" id="txtDireccion" name="address" class="form-control" placeholder="Ej: Centro Comercial Buenavista" required>
                                    <button class="btn btn-dark" type="button" id="btnBuscar" onclick="buscarDireccion()">
                                        <i class="fa-solid fa-search"></i>
                                    </button>
                                </div>
                                <div class="form-text">Si no encuentra, intenta agregar la ciudad.</div>
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold text-secondary small">ESTADO</label>
                                <select name="status" class="form-select">
                                    <option value="EN_EJECUCION">🟢 En Ejecución</option>
                                    <option value="PLANIFICACION">🟡 En Planificación</option>
                                </select>
                            </div>
                            <input type="hidden" id="lat" name="lat">
                            <input type="hidden" id="lng" name="lng">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="fw-bold text-secondary small mb-2">UBICACIÓN CONFIRMADA</label>
                            <div id="miniMap" style="height: 250px; width: 100%; border-radius: 12px; border: 2px solid #e9ecef;"></div>
                            <div class="mt-2 text-center">
                                <span class="badge bg-success d-none p-2 rounded-pill" id="msgExito">
                                    <i class="fa-solid fa-check-circle me-1"></i> Coordenadas listas
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-radius: 0 0 16px 16px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold rounded-pill" id="btnGuardar" disabled>Guardar Obra</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var map, miniMap, marker;

    // --- ICONO PROFESIONAL (HTML/CSS) ---
    function createCustomIcon() {
        return L.divIcon({
            className: 'custom-div-icon',
            html: `<div class='pin-wrap'>
                        <div class='pin-pulse'></div>
                        <div class='pin-marker'>
                            <i class='fa-solid fa-building pin-icon'></i>
                        </div>
                   </div>`,
            iconSize: [50, 50],
            iconAnchor: [25, 45],
            popupAnchor: [0, -40]
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        // MAPA PRINCIPAL
        map = L.map('mainMap', { zoomControl: false }).setView([4.5709, -74.2973], 6); 
        L.control.zoom({ position: 'topright' }).addTo(map);
        
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap &copy; CARTO',
            maxZoom: 20
        }).addTo(map);

        if(window.projectData) {
            window.projectData.forEach(p => {
                var pin = L.marker([p.lat, p.lng], { icon: createCustomIcon() }).addTo(map);
                
                pin.bindTooltip(p.title, {
                    permanent: true, 
                    direction: 'top', 
                    className: 'custom-tooltip',
                    offset: [0, -40]
                });

                var popupContent = `
                    <div class="popup-header"><i class="fa-solid fa-helmet-safety me-2"></i>${p.title}</div>
                    <div class="popup-body">
                        <p class="mb-1 text-muted small">DIRECCIÓN:</p>
                        <p class="fw-bold mb-0">${p.address || 'Sin dirección'}</p>
                        <hr class="my-2">
                        <span class="badge bg-success rounded-pill">Activo</span>
                    </div>
                `;
                pin.bindPopup(popupContent);
            });
        }

        // MINI MAPA MODAL
        var modalElement = document.getElementById('modalProject');
        modalElement.addEventListener('shown.bs.modal', function () {
            if (!miniMap) {
                miniMap = L.map('miniMap').setView([4.5709, -74.2973], 5);
                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png').addTo(miniMap);
                miniMap.on('click', function(e) { ponerPin(e.latlng.lat, e.latlng.lng); });
            }
            setTimeout(() => { miniMap.invalidateSize(); }, 200);
        });

        document.getElementById('txtDireccion').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); buscarDireccion(); }
        });
    });

    // --- BÚSQUEDA (ArcGIS) ---
    function buscarDireccion() {
        var dir = document.getElementById('txtDireccion').value;
        var btn = document.getElementById('btnBuscar');
        
        if(dir.length < 3) { alert("Escribe una dirección válida"); return; }

        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        btn.disabled = true;

        var query = dir + ", Colombia";
        var url = `https://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer/findAddressCandidates?f=json&singleLine=${encodeURIComponent(query)}&outFields=Match_addr,Location`;

        fetch(url).then(res => res.json()).then(data => {
            btn.innerHTML = '<i class="fa-solid fa-search"></i>';
            btn.disabled = false;

            if(data.candidates && data.candidates.length > 0) {
                var lat = data.candidates[0].location.y;
                var lng = data.candidates[0].location.x;
                miniMap.setView([lat, lng], 16);
                ponerPin(lat, lng);
            } else {
                alert("❌ No encontrada. Intenta ser más específico.");
            }
        }).catch(err => {
            btn.innerHTML = '<i class="fa-solid fa-search"></i>';
            btn.disabled = false;
        });
    }

    function ponerPin(lat, lng) {
        if (marker) miniMap.removeLayer(marker);
        marker = L.marker([lat, lng], { icon: createCustomIcon() }).addTo(miniMap);
        
        document.getElementById('lat').value = lat;
        document.getElementById('lng').value = lng;
        document.getElementById('btnGuardar').disabled = false;
        document.getElementById('msgExito').classList.remove('d-none');
        
        marker.dragging.enable();
        marker.on('dragend', function(e) {
            var pos = e.target.getLatLng();
            document.getElementById('lat').value = pos.lat;
            document.getElementById('lng').value = pos.lng;
        });
    }

    function flyTo(lat, lng) {
        map.flyTo([lat, lng], 16, {duration: 1.5});
    }
</script>

<?php require_once 'views/layouts/footer.php'; ?>