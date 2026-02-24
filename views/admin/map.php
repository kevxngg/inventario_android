<?php require_once 'views/layouts/header.php'; ?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
    #mainMap { z-index: 1; background: #f1f3f5; }
    .sidebar-container { background-color: #ffffff; }
    .custom-scroll::-webkit-scrollbar { width: 5px; }
    .custom-scroll::-webkit-scrollbar-thumb { background-color: #cbd5e0; border-radius: 10px; }
    
    .project-card {
        background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0;
        padding: 16px; margin-bottom: 12px; cursor: pointer;
        transition: all 0.25s ease; position: relative;
    }
    .project-card:hover { 
        border-color: #004b87; 
        box-shadow: 0 4px 12px rgba(0, 75, 135, 0.08); 
        transform: translateX(4px);
    }
    
    .project-icon-box {
        width: 40px; height: 40px; border-radius: 6px; background: #f1f5f9;
        color: #004b87; display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; flex-shrink: 0; border: 1px solid #e2e8f0;
    }
    .project-title { font-weight: 700; color: #1e293b; font-size: 0.9rem; margin-bottom: 2px; }
    .project-address { font-size: 0.75rem; color: #64748b; display: block; }
    
    .status-indicator { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
    .dot { height: 7px; width: 7px; border-radius: 50%; display: inline-block; margin-right: 5px; }
    .dot-active { background-color: #10b981; }
    .dot-plan { background-color: #f59e0b; }

    .custom-div-icon { background: transparent; border: none; }
    .pin-marker {
        width: 32px; height: 32px;
        background: #004b87; border: 2px solid #ffffff; border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg); box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        display: flex; justify-content: center; align-items: center;
    }
    .pin-icon { transform: rotate(45deg); color: #ffffff; font-size: 12px; }
    
    .leaflet-popup-content-wrapper { border-radius: 8px; padding: 0; overflow: hidden; }
    .leaflet-popup-content { margin: 0; width: 300px !important; }
    .popup-header { background: #1e293b; color: #ffffff; padding: 10px 15px; font-weight: 700; font-size: 0.85rem; }
    .popup-body { padding: 15px; background: #ffffff; }
    .tech-data-table { width: 100%; font-size: 0.8rem; margin-bottom: 0; }
    .tech-data-table td { padding: 4px 0; border-bottom: 1px solid #f1f5f9; }
    .tech-label { color: #64748b; font-weight: 600; width: 40%; }
    .tech-value { color: #1e293b; font-weight: 700; text-align: right; }
    
    /* Z-Index Fixes */
    .modal { z-index: 1055 !important; }
    .modal-backdrop { z-index: 1050 !important; }
</style>

<?php 
$projectsArray = []; 
if(isset($obras) && $obras){
    while($obra = $obras->fetch_object()){
        $projectsArray[] = [
            'id' => $obra->id,
            'title' => $obra->name,
            'lat' => $obra->lat,
            'lng' => $obra->lng,
            'address' => $obra->location,
            'status' => $obra->status,
            'desc' => $obra->description 
        ];
    }
}
?>

<div class="row h-100 g-0">
    <div class="col-md-3 border-end d-flex flex-column sidebar-container" style="height: calc(100vh - 80px); z-index: 2;">
        <div class="p-4 border-bottom bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark m-0 text-uppercase" style="letter-spacing: 1px;">Centros de Costo</h6>
                <span class="badge bg-dark rounded-pill"><?= count($projectsArray) ?></span>
            </div>
            <button class="btn btn-primary w-100 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalProject">
                <i class="fa-solid fa-layer-plus me-2"></i> Registrar Proyecto
            </button>
        </div>

        <div class="p-3 overflow-auto custom-scroll flex-grow-1 bg-light">
            <?php foreach($projectsArray as $index => $obra): ?>
                <?php 
                    $latVal = !empty($obra['lat']) ? $obra['lat'] : 'null';
                    $lngVal = !empty($obra['lng']) ? $obra['lng'] : 'null';
                ?>
                <div class="project-card d-flex align-items-start" onclick="flyToSafely(<?= $latVal ?>, <?= $lngVal ?>)">
                    <div class="project-icon-box me-3">
                        <i class="fa-solid fa-industry"></i>
                    </div>
                    <div class="flex-grow-1" style="min-width: 0;"> 
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="project-title text-truncate m-0" title="<?= htmlspecialchars($obra['title'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($obra['title'], ENT_QUOTES, 'UTF-8') ?>
                            </h6>
                            <div class="d-flex gap-1">
                                <button class="btn btn-link p-0 text-secondary" onclick="event.stopPropagation(); editProject(<?= $index ?>)"><i class="fa-solid fa-pen-to-square"></i></button>
                                <a href="<?= base_url ?>Admin/deleteProject?id=<?= $obra['id'] ?>" class="text-secondary" onclick="event.stopPropagation(); return confirm('¿Confirmar eliminación de registro?');"><i class="fa-solid fa-trash-can"></i></a>
                            </div>
                        </div>
                        <span class="project-address text-truncate mb-2"><i class="fa-solid fa-location-crosshairs me-1 text-primary"></i> <?= htmlspecialchars($obra['address'], ENT_QUOTES, 'UTF-8') ?></span>
                        
                        <div class="status-indicator">
                            <?php if($obra['status'] == 'EN_EJECUCION'): ?>
                                <span class="dot dot-active"></span> <span class="text-success">En Ejecución</span>
                            <?php else: ?>
                                <span class="dot dot-plan"></span> <span class="text-warning">En Planificación</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="col-md-9 position-relative">
        <div id="mainMap" style="width: 100%; height: calc(100vh - 80px);"></div>
    </div>
</div>

<div class="modal fade" id="modalProject" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white py-3">
                <h6 class="modal-title fw-bold text-uppercase" style="letter-spacing: 1px;"><i class="fa-solid fa-map-pin me-2 text-primary"></i>Alta de Proyecto Georeferenciado</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url ?>Admin/saveProject" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-5 border-end">
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Especificaciones Técnicas</h6>
                            
                            <div class="mb-3">
                                <label class="fw-bold text-secondary small text-uppercase">Nombre del Proyecto</label>
                                <input type="text" name="name" class="form-control fw-bold" required>
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold text-secondary small text-uppercase">Entidad / Cliente Responsable</label>
                                <input type="text" name="company_client" class="form-control" required>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="fw-bold text-secondary small text-uppercase">Clasificación</label>
                                    <select name="type_work" class="form-select">
                                        <option value="Residencial">Residencial</option>
                                        <option value="Comercial">Comercial</option>
                                        <option value="Vial">Infraestructura Vial</option>
                                        <option value="Industrial">Planta Industrial</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="fw-bold text-secondary small text-uppercase">Fecha de Inicio</label>
                                    <input type="date" name="start_date" class="form-control" required>
                                </div>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="fw-bold text-secondary small text-uppercase">Presupuesto (USD)</label>
                                    <input type="number" name="budget" class="form-control" placeholder="0.00" step="0.01">
                                </div>
                                <div class="col-6">
                                    <label class="fw-bold text-secondary small text-uppercase">Estado Inicial</label>
                                    <select name="status" class="form-select fw-bold">
                                        <option value="EN_EJECUCION">Ejecución Activa</option>
                                        <option value="PLANIFICACION">Fase de Planificación</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-0">
                                <label class="fw-bold text-secondary small text-uppercase">Soporte Gráfico / Plano</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                        </div>
                        
                        <div class="col-md-7">
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Validación de Coordenadas</h6>
                            <div class="input-group mb-3">
                                <input type="text" id="txtDireccion" name="address" class="form-control" placeholder="Buscar dirección o ciudad..." required>
                                <button class="btn btn-dark" type="button" onclick="buscarDireccion('txtDireccion', 'miniMap', 'lat', 'lng')">
                                    <i class="fa-solid fa-magnifying-glass me-1"></i> Localizar
                                </button>
                            </div>
                            <div id="miniMap" style="height: 350px; width: 100%; border-radius: 8px; border: 1px solid #cbd5e0;"></div>
                            <input type="hidden" id="lat" name="lat">
                            <input type="hidden" id="lng" name="lng">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold" id="btnGuardar" disabled>
                        Confirmar Registro de Obra
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h6 class="modal-title fw-bold text-uppercase">Modificación de Parámetros de Obra</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEdit" action="" method="POST">
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold small text-secondary">NOMBRE DEL PROYECTO</label>
                                <input type="text" name="name" id="editName" class="form-control fw-bold" required>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold small text-secondary">UBICACIÓN TÉCNICA</label>
                                <div class="input-group">
                                    <input type="text" id="editAddress" name="address" class="form-control" required>
                                    <button class="btn btn-dark" type="button" onclick="buscarDireccion('editAddress', 'miniMapEdit', 'editLat', 'editLng')"><i class="fa-solid fa-location-dot"></i></button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold small text-secondary">ESTADO OPERATIVO</label>
                                <select name="status" id="editStatus" class="form-select fw-bold">
                                    <option value="EN_EJECUCION">EN EJECUCIÓN</option>
                                    <option value="PLANIFICACION">PLANIFICACIÓN</option>
                                </select>
                            </div>
                            
                            <input type="hidden" id="editLat" name="lat">
                            <input type="hidden" id="editLng" name="lng">
                            
                            <div class="bg-light p-3 rounded">
                                <div class="mb-2">
                                    <label class="x-small fw-bold text-muted text-uppercase">Cliente</label>
                                    <input type="text" name="company_client" id="editClient" class="form-control form-control-sm">
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="x-small fw-bold text-muted text-uppercase">Tipo</label>
                                        <input type="text" name="type_work" id="editType" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-6">
                                        <label class="x-small fw-bold text-muted text-uppercase">Fecha Inicio</label>
                                        <input type="date" name="start_date" id="editDate" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-12 mt-2">
                                        <label class="x-small fw-bold text-muted text-uppercase">Presupuesto</label>
                                        <input type="number" name="budget" id="editBudget" class="form-control form-control-sm" step="0.01">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold small text-secondary mb-2">AJUSTAR COORDENADAS (OPCIONAL)</label>
                            <div id="miniMapEdit" style="height: 350px; width: 100%; border-radius: 8px; border: 1px solid #cbd5e0;"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Actualizar Registro</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    var map, miniMap, miniMapEdit, marker, markerEdit;
    var projectData = <?= json_encode($projectsArray) ?>;

    function createCustomIcon() {
        return L.divIcon({ 
            className: 'custom-div-icon', 
            html: `<div class='pin-marker'><i class='fa-solid fa-helmet-safety pin-icon'></i></div>`, 
            iconSize: [32, 32], 
            iconAnchor: [16, 32], 
            popupAnchor: [0, -32] 
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        
        // SOLUCIÓN AL PROBLEMA DE LA PANTALLA GRIS Y BLOQUEO DE ESCRITURA
        // Extraemos los modales del contenedor actual y los ponemos directo en el <body>
        // Esto evita que hereden el z-index de la plantilla y se bloqueen bajo la capa gris
        document.body.appendChild(document.getElementById('modalProject'));
        document.body.appendChild(document.getElementById('modalEdit'));

        // Inicializar Mapa Principal
        map = L.map('mainMap', { zoomControl: false }).setView([4.5709, -74.2973], 6); 
        L.control.zoom({ position: 'topright' }).addTo(map);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { 
            attribution: 'SICOT ERP' 
        }).addTo(map);

        if(projectData && projectData.length > 0) {
            projectData.forEach(p => {
                if(p.lat && p.lng) {
                    var pin = L.marker([p.lat, p.lng], { icon: createCustomIcon() }).addTo(map);
                    
                    let detailsHtml = '';
                    try {
                        const data = JSON.parse(p.desc);
                        detailsHtml = `
                            <table class="tech-data-table">
                                <tr><td class="tech-label">Cliente</td><td class="tech-value">${data.cliente || 'N/A'}</td></tr>
                                <tr><td class="tech-label">Tipo Obra</td><td class="tech-value">${data.tipo || 'N/A'}</td></tr>
                                <tr><td class="tech-label">Inicio</td><td class="tech-value">${data.fecha || 'N/A'}</td></tr>
                                <tr><td class="tech-label">Inversión</td><td class="tech-value">$${data.presupuesto || '0.00'}</td></tr>
                            </table>
                        `;
                    } catch(e) { detailsHtml = `<p class='small text-muted mb-0'>Sin detalles adicionales.</p>`; }

                    var popupContent = `
                        <div class="popup-header">FICHA TÉCNICA: ${p.title}</div>
                        <div class="popup-body">
                            <p class="mb-3 text-muted x-small fw-bold"><i class="fa-solid fa-location-dot me-1"></i> ${p.address}</p>
                            ${detailsHtml}
                            <div class="mt-3 text-end">
                                <span class="badge ${p.status == 'EN_EJECUCION' ? 'bg-success' : 'bg-warning text-dark'} fw-bold">
                                    ESTADO: ${p.status}
                                </span>
                            </div>
                        </div>
                    `;
                    pin.bindPopup(popupContent);
                    pin.bindTooltip(p.title, { direction: 'top', offset: [0, -35], className: 'fw-bold border-0 shadow-sm' });
                }
            });
        }

        // Inicializar mapas de modales
        initModalMap('modalProject', 'miniMap', 'lat', 'lng', false);
        initModalMap('modalEdit', 'miniMapEdit', 'editLat', 'editLng', true);
    });

    function flyToSafely(lat, lng) { 
        if(lat && lng) { map.flyTo([lat, lng], 15, {duration: 1.2}); } 
    }

    function editProject(index) {
        var p = projectData[index];
        if(!p) return;

        document.getElementById('editName').value = p.title;
        document.getElementById('editAddress').value = p.address;
        document.getElementById('editStatus').value = p.status;
        document.getElementById('editLat').value = p.lat || '';
        document.getElementById('editLng').value = p.lng || '';

        document.getElementById('editClient').value = '';
        document.getElementById('editType').value = '';
        document.getElementById('editDate').value = '';
        document.getElementById('editBudget').value = '';

        try {
            const data = JSON.parse(p.desc);
            if(data.cliente) document.getElementById('editClient').value = data.cliente;
            if(data.tipo) document.getElementById('editType').value = data.tipo;
            if(data.fecha) document.getElementById('editDate').value = data.fecha;
            if(data.presupuesto) document.getElementById('editBudget').value = parseFloat(data.presupuesto.replace(/,/g, ''));
        } catch(e) {}

        document.getElementById('formEdit').action = "<?= base_url ?>Admin/updateProject?id=" + p.id;
        
        // Uso de getOrCreateInstance para evitar duplicación de la pantalla gris
        var myModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEdit'));
        myModal.show();
    }

    function initModalMap(modalId, mapId, inputLat, inputLng, isEdit) {
        document.getElementById(modalId).addEventListener('shown.bs.modal', function () {
            var mapInstance = isEdit ? miniMapEdit : miniMap;
            
            if (!mapInstance) {
                mapInstance = L.map(mapId).setView([4.5709, -74.2973], 5);
                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png').addTo(mapInstance);
                mapInstance.on('click', function(e) { ponerPin(e.latlng.lat, e.latlng.lng, mapInstance, inputLat, inputLng, isEdit); });
                if(isEdit) miniMapEdit = mapInstance; else miniMap = mapInstance;
            }
            
            setTimeout(function() {
                mapInstance.invalidateSize();
                if(isEdit) {
                    var currentLat = document.getElementById(inputLat).value;
                    var currentLng = document.getElementById(inputLng).value;
                    if(currentLat && currentLng) {
                        mapInstance.setView([currentLat, currentLng], 15);
                        ponerPin(currentLat, currentLng, mapInstance, inputLat, inputLng, true);
                    } else {
                        mapInstance.setView([4.5709, -74.2973], 5);
                        if(markerEdit) mapInstance.removeLayer(markerEdit);
                    }
                }
            }, 250); 
        });
    }

    function buscarDireccion(inputId, mapId, latId, lngId) {
        var dir = document.getElementById(inputId).value;
        if(dir.length < 4) return;
        var mapInstance = (mapId === 'miniMapEdit') ? miniMapEdit : miniMap;
        var isEdit = (mapId === 'miniMapEdit');
        
        fetch(`https://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer/findAddressCandidates?f=json&singleLine=${encodeURIComponent(dir + ", Colombia")}&outFields=Location`)
        .then(res => res.json()).then(data => {
            if(data.candidates.length > 0) {
                var loc = data.candidates[0].location;
                mapInstance.setView([loc.y, loc.x], 15);
                ponerPin(loc.y, loc.x, mapInstance, latId, lngId, isEdit);
            }
        });
    }

    function ponerPin(lat, lng, mapInstance, latId, lngId, isEdit) {
        if (isEdit && markerEdit) mapInstance.removeLayer(markerEdit);
        if (!isEdit && marker) mapInstance.removeLayer(marker);
        
        var newMarker = L.marker([lat, lng], { icon: createCustomIcon() }).addTo(mapInstance);
        if(isEdit) markerEdit = newMarker; else marker = newMarker;
        
        document.getElementById(latId).value = lat;
        document.getElementById(lngId).value = lng;
        if(!isEdit) document.getElementById('btnGuardar').disabled = false;
    }
</script>

<?php require_once 'views/layouts/footer.php'; ?>