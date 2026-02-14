<?php require_once 'views/layouts/header.php'; ?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="container-fluid p-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Hola, <?= $_SESSION['identity']->fullname ?> 👋</h2>
            <p class="text-muted">Panel de Control</p>
        </div>
        <span class="badge bg-primary px-3 py-2 rounded-pill">👷 Técnico / Usuario</span>
    </div>

    <h4 class="fw-bold mb-3">🚧 Panel de Gestión</h4>

    <div class="row mb-5">
        <div class="col-md-7 mb-3">
            <div class="card border-0 shadow-sm bg-primary text-white h-100" style="background: linear-gradient(135deg, #0d6efd 0%, #e7f1ff 100%); min-height: 140px;">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                            <i class="fa-solid fa-helmet-safety fa-2x"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0 text-dark">Mis Obras</h4>
                            <small class="text-muted">Proyectos bajo supervisión</small>
                        </div>
                    </div>
                    <div class="text-end">
                        <h1 class="fw-bold display-3 m-0 text-dark"><?= isset($misObras) ? $misObras->num_rows : 0 ?></h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-white hover-elevate" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalNewProject">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <h5 class="fw-bold text-primary mb-1">Nueva Obra</h5>
                        <p class="text-muted small mb-0">Registra un nuevo proyecto en el mapa.</p>
                        <button class="btn btn-sm btn-primary rounded-pill mt-2">
                            <i class="fa-solid fa-plus me-1"></i> Registrar Ahora
                        </button>
                    </div>
                    <div class="bg-light text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fa-solid fa-map-location-dot fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-end mb-2">
        <h5 class="fw-bold text-dark m-0"><i class="fa-solid fa-list-check me-2"></i>Historial de Solicitudes</h5>
        
        <button id="btnDeleteSelected" class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm" style="display: none;" onclick="deleteSelected()">
            <i class="fa-solid fa-trash-can me-2"></i> Eliminar (<span id="countSelected">0</span>)
        </button>
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="ps-3" style="width: 40px;">
                                <input type="checkbox" id="selectAll" class="form-check-input" style="cursor: pointer;">
                            </th>
                            <th>DETALLE</th>
                            <th>FECHA</th>
                            <th class="text-end pe-4">ESTADO</th>
                        </tr>
                    </thead>
                    <tbody id="historyTableBody">
                        <?php if(isset($misSolicitudes) && $misSolicitudes->num_rows > 0): ?>
                            <?php while($req = $misSolicitudes->fetch_object()): ?>
                            <tr id="row-<?= $req->id ?>">
                                <td class="ps-3">
                                    <input type="checkbox" class="form-check-input row-checkbox" value="<?= $req->id ?>">
                                </td>
                                <td>
                                    <?php if($req->type == 'SOLICITUD_HERRAMIENTA'): ?>
                                        <span class="badge bg-info text-dark me-2">Pedido</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger me-2">Reporte</span>
                                    <?php endif; ?>
                                    <span class="fw-bold text-dark"><?= $req->description ?></span>
                                </td>
                                <td class="text-muted"><?= date('d/m/Y', strtotime($req->created_at)) ?></td>
                                <td class="text-end pe-4">
                                    <?php if($req->status == 'PENDIENTE'): ?>
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    <?php elseif($req->status == 'APROBADO'): ?>
                                        <span class="badge bg-success">Aprobado</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Rechazado</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    No hay registros recientes.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNewProject" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-primary text-white" style="border-radius: 16px 16px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-hard-hat me-2"></i>Registrar Nueva Obra</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url ?>User/saveProject" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-5 border-end">
                            <h6 class="text-primary fw-bold mb-3">Información General</h6>
                            
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
                                <label class="fw-bold text-secondary small">PRESUPUESTO ESTIMADO (Opcional)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="budget" class="form-control" placeholder="0.00">
                                </div>
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
                                    <input type="text" id="txtDireccionUser" name="address" class="form-control" placeholder="Escribe ciudad o barrio para buscar..." required>
                                    <button class="btn btn-dark" type="button" id="btnBuscarUser" onclick="buscarDireccionUser()">
                                        <i class="fa-solid fa-search"></i> Buscar
                                    </button>
                                </div>
                            </div>

                            <div id="miniMapUser" style="height: 350px; width: 100%; border-radius: 12px; border: 2px solid #e9ecef;"></div>
                            
                            <div class="mt-2 text-center">
                                <small class="text-muted d-block mb-2">Arrastra el pin para mayor precisión</small>
                                <span class="badge bg-success d-none p-2 rounded-pill" id="msgExitoUser">
                                    <i class="fa-solid fa-check-circle me-1"></i> Coordenadas listas
                                </span>
                            </div>

                            <input type="hidden" id="latUser" name="lat">
                            <input type="hidden" id="lngUser" name="lng">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-radius: 0 0 16px 16px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-5 fw-bold rounded-pill" id="btnGuardarUser" disabled>
                        <i class="fa-solid fa-save me-2"></i> Registrar Obra
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // --- 1. LÓGICA DE BORRADO DE HISTORIAL ---
    document.addEventListener("DOMContentLoaded", function() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.row-checkbox');
        const btnDelete = document.getElementById('btnDeleteSelected');
        const countSpan = document.getElementById('countSelected');

        // Actualizar botón rojo
        function updateButton() {
            const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
            countSpan.textContent = checkedCount;
            if(checkedCount > 0) {
                btnDelete.style.display = 'inline-block';
            } else {
                btnDelete.style.display = 'none';
            }
        }

        // Seleccionar todo
        if(selectAll){
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = selectAll.checked);
                updateButton();
            });
        }

        // Checkbox individual
        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateButton);
        });
    });

    // Función AJAX para borrar
    function deleteSelected() {
        const checked = document.querySelectorAll('.row-checkbox:checked');
        if(checked.length === 0) return;

        let ids = [];
        checked.forEach(cb => ids.push(cb.value));

        Swal.fire({
            title: '¿Borrar ' + ids.length + ' registros?',
            text: "Se eliminarán de tu historial.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sí, borrar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('<?= base_url ?>User/deleteRequests', {ids: ids}, function(response) {
                    try {
                        const data = JSON.parse(response);
                        if(data.status === 'success') {
                            Swal.fire('Eliminado', data.msg, 'success').then(() => {
                                location.reload(); 
                            });
                        } else {
                            Swal.fire('Error', data.msg, 'error');
                        }
                    } catch(e) { console.error(e); }
                });
            }
        });
    }

    // --- 2. LÓGICA DEL MAPA (REGISTRAR OBRA) ---
    var miniMapUser, markerUser;
    var modalElement = document.getElementById('modalNewProject');
    
    modalElement.addEventListener('shown.bs.modal', function () {
        if (!miniMapUser) {
            miniMapUser = L.map('miniMapUser').setView([4.5709, -74.2973], 5);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; CARTO'
            }).addTo(miniMapUser);
            
            miniMapUser.on('click', function(e) { ponerPinUser(e.latlng.lat, e.latlng.lng); });
        }
        setTimeout(() => { miniMapUser.invalidateSize(); }, 200);
    });

    document.getElementById('txtDireccionUser').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); buscarDireccionUser(); }
    });

    function buscarDireccionUser() {
        var dir = document.getElementById('txtDireccionUser').value;
        var btn = document.getElementById('btnBuscarUser');
        
        if(dir.length < 3) { return; }

        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        btn.disabled = true;

        var query = dir + ", Colombia"; 
        var url = `https://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer/findAddressCandidates?f=json&singleLine=${encodeURIComponent(query)}&outFields=Match_addr,Location`;

        fetch(url).then(res => res.json()).then(data => {
            btn.innerHTML = '<i class="fa-solid fa-search"></i> Buscar';
            btn.disabled = false;

            if(data.candidates && data.candidates.length > 0) {
                var lat = data.candidates[0].location.y;
                var lng = data.candidates[0].location.x;
                miniMapUser.setView([lat, lng], 16);
                ponerPinUser(lat, lng);
            }
        }).catch(err => {
            btn.innerHTML = '<i class="fa-solid fa-search"></i> Buscar';
            btn.disabled = false;
        });
    }

    function ponerPinUser(lat, lng) {
        if (markerUser) miniMapUser.removeLayer(markerUser);
        
        var userIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/markers/marker-icon-2x-blue.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        markerUser = L.marker([lat, lng], {icon: userIcon}).addTo(miniMapUser);
        
        document.getElementById('latUser').value = lat;
        document.getElementById('lngUser').value = lng;
        document.getElementById('btnGuardarUser').disabled = false;
        document.getElementById('msgExitoUser').classList.remove('d-none');
        
        markerUser.dragging.enable();
        markerUser.on('dragend', function(e) {
            var pos = e.target.getLatLng();
            document.getElementById('latUser').value = pos.lat;
            document.getElementById('lngUser').value = pos.lng;
        });
    }
</script>

<style>
/* Efecto hover suave para la tarjeta de Nueva Obra */
.hover-elevate {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-elevate:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(13, 110, 253, 0.15) !important;
}
</style>