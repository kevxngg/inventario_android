<?php require_once 'views/layouts/header.php'; ?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
    /* Estilos del Mapa y Sidebar */
    .modal-backdrop { z-index: 1040 !important; }
    .modal { z-index: 1050 !important; }
    #mainMap { z-index: 1; background: #f8f9fa; }
    .custom-scroll::-webkit-scrollbar { width: 6px; }
    .custom-scroll::-webkit-scrollbar-thumb { background-color: #dee2e6; border-radius: 4px; }
    .sidebar-container { background-color: #fcfcfc; }
    
    .project-card {
        background: white; border-radius: 12px; border: 1px solid #f0f0f0;
        padding: 15px; margin-bottom: 12px; cursor: pointer;
        transition: all 0.2s ease-in-out; position: relative;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .project-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.08); border-color: #cce5ff; }
    
    .project-icon-box {
        width: 45px; height: 45px; border-radius: 10px; background: #e7f1ff;
        color: #0d6efd; display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem; flex-shrink: 0;
    }
    .project-title { font-weight: 700; color: #2c3e50; font-size: 0.95rem; margin-bottom: 2px; }
    .project-address { font-size: 0.8rem; color: #6c757d; display: block; }
    .action-btn { transition: color 0.2s; padding: 5px; background: none; border: none; }
    .btn-edit:hover { color: #0d6efd; }
    .btn-delete:hover { color: #dc3545; }
    
    .status-dot { height: 8px; width: 8px; border-radius: 50%; display: inline-block; margin-right: 5px; }
    .status-active { background-color: #198754; box-shadow: 0 0 5px #198754; }
    .status-plan { background-color: #ffc107; }

    .custom-div-icon { background: transparent; border: none; }
    .pin-wrap { position: relative; display: flex; justify-content: center; align-items: center; width: 50px; height: 50px; }
    .pin-marker {
        width: 36px; height: 36px;
        background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
        border: 2px solid white; border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg); box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        display: flex; justify-content: center; align-items: center; z-index: 2; transition: transform 0.2s;
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
    
    .custom-tooltip { background: white; border: none; box-shadow: 0 2px 15px rgba(0,0,0,0.15); border-radius: 8px; padding: 5px 10px; font-weight: 700; color: #333; font-size: 12px; }
    .custom-tooltip::before { display: none; }
    .leaflet-popup-content-wrapper { border-radius: 12px; padding: 0; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
    .leaflet-popup-content { margin: 0; width: 320px !important; }
    
    .popup-header { background: linear-gradient(135deg, #0d6efd, #0a58ca); color: white; padding: 12px 15px; display: flex; align-items: center; justify-content: space-between; }
    .popup-body { padding: 15px; background: #fff; font-size: 0.9rem; }
    .details-box { background: #f8f9fa; border-radius: 8px; padding: 10px; border: 1px solid #e9ecef; margin-bottom: 10px; }
</style>

<div class="row h-100 g-0">
    <div class="col-md-3 border-end shadow-sm d-flex flex-column sidebar-container" style="height: calc(100vh - 80px); z-index: 2;">
        
        <div class="p-4 border-bottom bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark m-0">📍 Obras Activas</h5>
                <span class="badge bg-light text-dark border"><?= isset($obras) ? $obras->num_rows : 0 ?> Total</span>
            </div>
            <button class="btn btn-primary w-100 py-2 fw-bold rounded-pill" data-bs-toggle="modal" data-bs-target="#modalProject">
                <i class="fa-solid fa-plus me-2"></i> Nueva Obra
            </button>
        </div>

        <div class="p-3 overflow-auto custom-scroll flex-grow-1">
            <?php 
                $projectsArray = []; 
                if(isset($obras) && $obras): 
                    $i = 0;
                    while($obra = $obras->fetch_object()): 
                        // Guardamos el JSON tal cual en el array de JS
                        $projectsArray[] = [
                            'id' => $obra->id,
                            'title' => $obra->name,
                            'lat' => $obra->lat,
                            'lng' => $obra->lng,
                            'address' => $obra->location,
                            'status' => $obra->status,
                            'desc' => $obra->description 
                        ];
            ?>
                
                <div class="project-card d-flex align-items-center" onclick="flyTo(<?= $obra->lat ?>, <?= $obra->lng ?>)">
                    <div class="project-icon-box me-3">
                        <i class="fa-solid fa-city"></i>
                    </div>
                    <div class="flex-grow-1" style="min-width: 0;"> 
                        <div class="d-flex justify-content-between align-items-start">
                            <h6 class="project-title text-truncate" style="max-width: 140px;"><?= $obra->name ?></h6>
                            <div class="d-flex gap-1">
                                <button class="action-btn btn-edit text-secondary" onclick="event.stopPropagation(); editProject(<?= $i ?>)" title="Editar Obra"><i class="fa-solid fa-pen-to-square"></i></button>
                                <a href="<?= base_url ?>Admin/deleteProject?id=<?= $obra->id ?>" class="action-btn btn-delete text-secondary" onclick="event.stopPropagation(); return confirm('¿Eliminar?');"><i class="fa-solid fa-trash-can"></i></a>
                            </div>
                        </div>
                        <p class="project-address text-truncate mb-1"><i class="fa-solid fa-location-dot me-1 text-danger"></i> <?= $obra->location ?></p>
                        <div class="d-flex align-items-center mt-1">
                            <?php if($obra->status == 'EN_EJECUCION'): ?>
                                <span class="status-dot status-active"></span> <span class="fw-bold text-success x-small">En Ejecución</span>
                            <?php else: ?>
                                <span class="status-dot status-plan"></span> <span class="fw-bold text-warning x-small">Planificación</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php $i++; endwhile; endif; ?>
        </div>
    </div>

    <div class="col-md-9 position-relative">
        <div id="mainMap" style="width: 100%; height: calc(100vh - 80px);"></div>
    </div>
</div>

<div class="modal fade" id="modalProject" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-primary text-white" style="border-radius: 16px 16px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-hard-hat me-2"></i>Registrar Nueva Obra (Admin)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url ?>Admin/saveProject" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-5 border-end">
                            <h6 class="text-primary fw-bold mb-3">Información del Proyecto</h6>
                            
                            <div class="mb-3">
                                <label class="fw-bold text-secondary small">NOMBRE DEL PROYECTO</label>
                                <input type="text" name="name" class="form-control fw-bold" placeholder="Ej: Torre Empresarial Norte" required>
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold text-secondary small">EMPRESA / CLIENTE</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-building"></i></span>
                                    <input type="text" name="company_client" class="form-control border-start-0" placeholder="Ej: Inversiones SAS" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="fw-bold text-secondary small">TIPO DE OBRA</label>
                                    <select name="type_work" class="form-select">
                                        <option value="Residencial">🏠 Residencial</option>
                                        <option value="Comercial">🏢 Comercial</option>
                                        <option value="Vial">🛣️ Vial / Carretera</option>
                                        <option value="Industrial">🏭 Industrial</option>
                                        <option value="Otro">🏗️ Otro</option>
                                    </select>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="fw-bold text-secondary small">FECHA INICIO</label>
                                    <input type="date" name="start_date" class="form-control" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold text-secondary small">PRESUPUESTO ESTIMADO</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="budget" class="form-control" placeholder="0.00">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="fw-bold text-secondary small">ESTADO INICIAL</label>
                                <select name="status" class="form-select fw-bold text-dark">
                                    <option value="EN_EJECUCION">🟢 En Ejecución</option>
                                    <option value="PLANIFICACION">🟡 En Planificación</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold text-secondary small">IMAGEN REFERENCIA</label>
                                <input type="file" name="image" class="form-control">
                            </div>
                        </div>
                        
                        <div class="col-md-7 ps-md-4">
                            <h6 class="text-primary fw-bold mb-3">Ubicación Geográfica</h6>
                            
                            <div class="mb-3">
                                <div class="input-group">
                                    <input type="text" id="txtDireccion" name="address" class="form-control" placeholder="Escribe ciudad o barrio para buscar..." required>
                                    <button class="btn btn-dark" type="button" onclick="buscarDireccion('txtDireccion', 'miniMap', 'lat', 'lng')">
                                        <i class="fa-solid fa-search"></i> Buscar
                                    </button>
                                </div>
                            </div>

                            <div id="miniMap" style="height: 350px; width: 100%; border-radius: 12px; border: 2px solid #e9ecef;"></div>
                            
                            <div class="mt-2 text-center">
                                <small class="text-muted d-block mb-2">Arrastra el pin para mayor precisión</small>
                            </div>

                            <input type="hidden" id="lat" name="lat">
                            <input type="hidden" id="lng" name="lng">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-radius: 0 0 16px 16px;">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-5 fw-bold rounded-pill" id="btnGuardar" disabled>
                        <i class="fa-solid fa-save me-2"></i> Registrar Obra
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Editar Detalles de Obra</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEdit" action="" method="POST">
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3"><label class="fw-bold small text-secondary">NOMBRE PROYECTO</label><input type="text" name="name" id="editName" class="form-control fw-bold" required></div>
                            <div class="mb-3"><label class="fw-bold small text-secondary">UBICACIÓN</label><div class="input-group"><input type="text" id="editAddress" name="address" class="form-control" required><button class="btn btn-dark" type="button" onclick="buscarDireccion('editAddress', 'miniMapEdit', 'editLat', 'editLng')">Buscar</button></div></div>
                            <div class="mb-3"><label class="fw-bold small text-secondary">ESTADO</label><select name="status" id="editStatus" class="form-select"><option value="EN_EJECUCION">🟢 En Ejecución</option><option value="PLANIFICACION">🟡 En Planificación</option></select></div>
                            
                            <hr>
                            <div class="mb-2"><label class="small fw-bold text-muted">CLIENTE / EMPRESA</label><input type="text" name="company_client" id="editClient" class="form-control form-control-sm"></div>
                            <div class="row">
                                <div class="col-6 mb-2"><label class="small fw-bold text-muted">TIPO OBRA</label><input type="text" name="type_work" id="editType" class="form-control form-control-sm"></div>
                                <div class="col-6 mb-2"><label class="small fw-bold text-muted">FECHA</label><input type="date" name="start_date" id="editDate" class="form-control form-control-sm"></div>
                            </div>
                            <div class="mb-2"><label class="small fw-bold text-muted">PRESUPUESTO</label><div class="input-group input-group-sm"><span class="input-group-text">$</span><input type="number" name="budget" id="editBudget" class="form-control"></div></div>

                            <input type="hidden" id="editLat" name="lat"><input type="hidden" id="editLng" name="lng">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold small text-secondary mb-2">AJUSTAR UBICACIÓN</label>
                            <div id="miniMapEdit" style="height: 350px; width: 100%; border-radius: 12px; border: 2px solid #e9ecef;"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary">Actualizar Cambios</button></div>
            </form>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var map, miniMap, miniMapEdit, marker, markerEdit;
    var projectData = <?= json_encode($projectsArray) ?>;

    function createCustomIcon() {
        return L.divIcon({ className: 'custom-div-icon', html: `<div class='pin-wrap'><div class='pin-pulse'></div><div class='pin-marker'><i class='fa-solid fa-building pin-icon'></i></div></div>`, iconSize: [50, 50], iconAnchor: [25, 45], popupAnchor: [0, -40] });
    }

    document.addEventListener("DOMContentLoaded", function() {
        map = L.map('mainMap', { zoomControl: false }).setView([4.5709, -74.2973], 6); 
        L.control.zoom({ position: 'topright' }).addTo(map);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { attribution: '&copy; CARTO' }).addTo(map);

        if(projectData) {
            projectData.forEach(p => {
                var pin = L.marker([p.lat, p.lng], { icon: createCustomIcon() }).addTo(map);
                pin.bindTooltip(p.title, { permanent: true, direction: 'top', className: 'custom-tooltip', offset: [0, -40] });

                let detailsHtml = '<p class="text-muted small">Sin detalles.</p>';
                try {
                    const data = JSON.parse(p.desc);
                    detailsHtml = `
                        <div class="details-box">
                            <ul style='list-style:none; padding:0; margin:0; font-size:0.85rem;'>
                                <li class='mb-1'><i class='fa-solid fa-building text-primary me-2'></i> <b>Cliente:</b> ${data.cliente}</li>
                                <li class='mb-1'><i class='fa-solid fa-industry text-primary me-2'></i> <b>Tipo:</b> ${data.tipo}</li>
                                <li class='mb-1'><i class='fa-regular fa-calendar text-primary me-2'></i> <b>Inicio:</b> ${data.fecha}</li>
                                <li class='mb-1'><i class='fa-solid fa-money-bill text-success me-2'></i> <b>Presupuesto:</b> $${data.presupuesto}</li>
                            </ul>
                        </div>
                    `;
                } catch(e) {
                    if(p.desc && p.desc.length > 5) detailsHtml = `<div class="details-box small">${p.desc}</div>`;
                }

                var popupContent = `
                    <div class="popup-header"><div><i class="fa-solid fa-helmet-safety me-2"></i>${p.title}</div></div>
                    <div class="popup-body">
                        <p class="mb-2 text-muted small"><i class="fa-solid fa-location-dot me-1"></i> ${p.address}</p>
                        ${detailsHtml}
                        <div class="mt-2 text-end"><span class="badge ${p.status == 'EN_EJECUCION' ? 'bg-success' : 'bg-warning text-dark'} rounded-pill">${p.status}</span></div>
                    </div>
                `;
                pin.bindPopup(popupContent);
            });
        }

        initModalMap('modalProject', 'miniMap', 'lat', 'lng', false);
        initModalMap('modalEdit', 'miniMapEdit', 'editLat', 'editLng', true);
    });

    function editProject(index) {
        var p = projectData[index];
        if(!p) return;

        document.getElementById('editName').value = p.title;
        document.getElementById('editAddress').value = p.address;
        document.getElementById('editStatus').value = p.status;
        document.getElementById('editLat').value = p.lat;
        document.getElementById('editLng').value = p.lng;

        try {
            const data = JSON.parse(p.desc);
            document.getElementById('editClient').value = data.cliente || '';
            document.getElementById('editType').value = data.tipo || '';
            document.getElementById('editDate').value = data.fecha || '';
            document.getElementById('editBudget').value = data.presupuesto || '';
        } catch(e) {
            document.getElementById('editClient').value = '';
            document.getElementById('editType').value = '';
            document.getElementById('editDate').value = '';
            document.getElementById('editBudget').value = '';
        }

        document.getElementById('formEdit').action = "<?= base_url ?>Admin/updateProject?id=" + p.id;
        if(miniMapEdit) { miniMapEdit.setView([p.lat, p.lng], 16); ponerPin(p.lat, p.lng, miniMapEdit, 'editLat', 'editLng', true); }
        var myModal = new bootstrap.Modal(document.getElementById('modalEdit'));
        myModal.show();
    }

    function initModalMap(modalId, mapId, inputLat, inputLng, isEdit) {
        var modalEl = document.getElementById(modalId);
        modalEl.addEventListener('shown.bs.modal', function () {
            var mapInstance = isEdit ? miniMapEdit : miniMap;
            if (!mapInstance) {
                var newMap = L.map(mapId).setView([4.5709, -74.2973], 5);
                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png').addTo(newMap);
                newMap.on('click', function(e) { ponerPin(e.latlng.lat, e.latlng.lng, newMap, inputLat, inputLng, isEdit); });
                if(isEdit) miniMapEdit = newMap; else miniMap = newMap;
            } else { mapInstance.invalidateSize(); }
        });
    }

    function buscarDireccion(inputId, mapId, latId, lngId) {
        var dir = document.getElementById(inputId).value; if(dir.length < 3) return;
        var mapInstance = (mapId === 'miniMapEdit') ? miniMapEdit : miniMap;
        var isEdit = (mapId === 'miniMapEdit');
        fetch(`https://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer/findAddressCandidates?f=json&singleLine=${encodeURIComponent(dir + ", Colombia")}&outFields=Match_addr,Location`)
        .then(res => res.json()).then(data => {
            if(data.candidates.length > 0) { var l = data.candidates[0].location; mapInstance.setView([l.y, l.x], 16); ponerPin(l.y, l.x, mapInstance, latId, lngId, isEdit); }
        });
    }

    function ponerPin(lat, lng, mapInstance, latId, lngId, isEdit) {
        var markerInstance = isEdit ? markerEdit : marker;
        if (markerInstance) mapInstance.removeLayer(markerInstance);
        var newMarker = L.marker([lat, lng], { icon: createCustomIcon() }).addTo(mapInstance);
        if(isEdit) markerEdit = newMarker; else marker = newMarker;
        document.getElementById(latId).value = lat; document.getElementById(lngId).value = lng;
        if(!isEdit) document.getElementById('btnGuardar').disabled = false;
    }
    function flyTo(lat, lng) { map.flyTo([lat, lng], 16, {duration: 1.5}); }
</script>

<?php require_once 'views/layouts/footer.php'; ?>