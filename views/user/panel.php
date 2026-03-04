<?php require_once 'views/layouts/header.php'; ?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
    /* Estilos Estructurales - Perfil Industrial */
    .dashboard-header {
        background-color: var(--white);
        border-bottom: 2px solid var(--border-color);
        padding-bottom: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background-color: var(--panel-dark); /* Azul Pizarra Oscuro */
        color: var(--white);
        border-radius: 12px;
        min-height: 140px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border-left: 5px solid var(--safety-orange); /* Toque industrial */
    }

    .stat-icon-wrapper {
        background-color: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1.5rem;
    }

    .action-card {
        background-color: var(--white);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        border-bottom: 4px solid var(--panel-dark);
    }

    .action-card:hover {
        border-color: var(--safety-orange);
        border-bottom: 4px solid var(--safety-orange);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        transform: translateY(-3px);
    }

    .action-icon-wrapper {
        background-color: rgba(234, 88, 12, 0.1);
        color: var(--safety-orange);
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Tabla de Historial Industrial */
    .history-table { border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden; }
    .history-table th {
        background-color: var(--panel-darker) !important;
        color: var(--white) !important;
        font-size: 0.85rem;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: none;
    }
    .history-table td {
        vertical-align: middle;
        padding: 1.2rem 0.8rem;
        border-bottom: 1px solid var(--border-color);
        background-color: var(--white);
    }

    /* Fix para Mapas en Modales */
    .modal { z-index: 1050; }
    #miniMapUser { z-index: 1; }
</style>

<div class="container-fluid p-4">
    
    <div class="dashboard-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1">Bienvenido, <?= htmlspecialchars($_SESSION['identity']->fullname, ENT_QUOTES) ?></h3>
            <p class="text-muted fw-bold mb-0 text-uppercase" style="letter-spacing: 1px; font-size: 0.85rem;">Panel Operativo de Control en Terreno</p>
        </div>
        <div class="d-flex align-items-center">
            <span class="badge px-3 py-2 rounded-pill shadow-sm" style="background-color: var(--panel-darker); font-size: 0.9rem;">
                <i class="fa-solid fa-user-helmet me-2" style="color: var(--safety-orange);"></i> Técnico / Residente
            </span>
        </div>
    </div>

    <h5 class="fw-bold mb-4" style="color: var(--panel-darker);"><i class="fa-solid fa-layer-group me-2" style="color: var(--safety-orange);"></i>Resumen de Asignaciones</h5>

    <div class="row mb-5 g-4">
        <div class="col-md-7">
            <div class="stat-card border-0 h-100">
                <div class="card-body p-4 d-flex align-items-center justify-content-between h-100">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon-wrapper">
                            <i class="fa-solid fa-map-location-dot fa-2x" style="color: var(--safety-orange);"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1 text-white">Frentes de Obra Asignados</h5>
                            <p class="text-white-50 small mb-0 fw-bold">Proyectos bajo su supervisión directa</p>
                        </div>
                    </div>
                    <div class="text-end">
                        <h1 class="fw-bolder display-4 m-0 text-white"><?= isset($misObras) ? $misObras->num_rows : 0 ?></h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="action-card h-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNewProject">
                <div class="card-body d-flex align-items-center justify-content-between p-4 h-100">
                    <div>
                        <h6 class="fw-bold mb-2 text-uppercase" style="color: var(--panel-darker); letter-spacing: 0.5px;">Apertura de Proyecto</h6>
                        <p class="text-muted fw-bold small mb-3">Registrar y georeferenciar nueva zona de trabajo.</p>
                        <span class="btn btn-sm btn-primary fw-bold px-3 shadow-sm rounded-pill">
                            <i class="fa-solid fa-plus me-1"></i> Iniciar Registro
                        </span>
                    </div>
                    <div class="action-icon-wrapper">
                        <i class="fa-solid fa-compass-drafting fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card p-0 border-0 shadow-sm overflow-hidden" style="border-radius: 12px;">
        <div class="card-header border-bottom p-4 d-flex justify-content-between align-items-center" style="background-color: var(--white);">
            <h6 class="fw-bold m-0 text-uppercase" style="color: var(--panel-darker); letter-spacing: 1px;"><i class="fa-solid fa-clock-rotate-left me-2" style="color: var(--safety-orange);"></i>Registro Histórico de Solicitudes</h6>
            <button id="btnDeleteSelected" class="btn btn-danger btn-sm px-3 shadow-sm fw-bold rounded-pill" style="display: none;" onclick="deleteSelected()">
                <i class="fa-solid fa-trash-can me-2"></i> Purgar Historial (<span id="countSelected">0</span>)
            </button>
        </div>
        
        <div class="card-body p-0 bg-white">
            <div class="table-responsive">
                <table class="table history-table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width: 40px;">
                                <input type="checkbox" id="selectAll" class="form-check-input" style="cursor: pointer;">
                            </th>
                            <th>Especificación Técnica / Detalle</th>
                            <th>Fecha de Emisión</th>
                            <th class="text-end pe-4">Resolución</th>
                        </tr>
                    </thead>
                    <tbody id="historyTableBody">
                        <?php if(isset($misSolicitudes) && $misSolicitudes->num_rows > 0): ?>
                            <?php while($req = $misSolicitudes->fetch_object()): ?>
                            <tr id="row-<?= $req->id ?>">
                                <td class="ps-4">
                                    <input type="checkbox" class="form-check-input border-secondary row-checkbox" value="<?= $req->id ?>">
                                </td>
                                <td>
                                    <?php if($req->type == 'SOLICITUD_HERRAMIENTA'): ?>
                                        <span class="badge border bg-light me-2 text-dark" style="border-color: var(--panel-dark) !important;"><i class="fa-solid fa-box-open me-1" style="color: var(--safety-orange);"></i> Despacho</span>
                                    <?php else: ?>
                                        <span class="badge border border-danger text-danger bg-light me-2"><i class="fa-solid fa-triangle-exclamation me-1"></i> Reporte</span>
                                    <?php endif; ?>
                                    <span class="fw-bold text-dark"><?= htmlspecialchars($req->description, ENT_QUOTES) ?></span>
                                    <?php if(!empty($req->tool_name)): ?>
                                        <br><small class="text-muted ms-5 fw-bold"><i class="fa-solid fa-toolbox me-1"></i> <?= htmlspecialchars($req->tool_name, ENT_QUOTES) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-secondary small fw-bold">
                                    <i class="fa-regular fa-calendar me-1"></i> <?= date('d / m / Y', strtotime($req->created_at)) ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex flex-column align-items-end gap-2">
                                        <?php if($req->status == 'PENDIENTE'): ?>
                                            <span class="badge text-dark shadow-sm px-3 py-2" style="background-color: #eab308;"><i class="fa-solid fa-hourglass-half me-1"></i> En Revisión</span>
                                        <?php elseif($req->status == 'APROBADO'): ?>
                                            <span class="badge bg-success shadow-sm px-3 py-2"><i class="fa-solid fa-check me-1"></i> Autorizado</span>
                                        <?php elseif($req->status == 'RESUELTO'): ?>
                                            <span class="badge shadow-sm px-3 py-2" style="background-color: var(--panel-dark);"><i class="fa-solid fa-check-double me-1" style="color: var(--safety-orange);"></i> Finalizado</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger shadow-sm px-3 py-2"><i class="fa-solid fa-xmark me-1"></i> Denegado</span>
                                        <?php endif; ?>

                                        <?php if(strpos($req->type, 'REPORTE') !== false): ?>
                                            <button class="btn btn-sm fw-bold rounded-pill shadow-sm" style="background-color: var(--white); border: 2px solid var(--panel-dark); color: var(--panel-dark);" 
                                                    onclick="openUserChat(<?= $req->id ?>, '<?= addslashes(htmlspecialchars($req->description, ENT_QUOTES)) ?>')">
                                                <i class="fa-solid fa-headset me-1 text-info"></i> Abrir Ticket
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-folder-open fa-3x mb-3" style="color: var(--border-color);"></i>
                                    <p class="fw-bold mb-0 text-uppercase" style="letter-spacing: 1px;">Registro Operativo Vacío.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNewProject" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow-lg" style="border: none; border-radius: 12px; overflow: hidden;">
            <div class="modal-header py-3" style="background-color: var(--panel-dark); color: white;">
                <h6 class="modal-title fw-bold text-uppercase" style="letter-spacing: 1px;"><i class="fa-solid fa-map-pin me-2" style="color: var(--safety-orange);"></i>Apertura de Proyecto Georeferenciado</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url ?>User/saveProject" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4 bg-light">
                    <div class="row g-4">
                        <div class="col-md-5 border-end">
                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-uppercase" style="color: var(--panel-darker);">Especificaciones Técnicas</h6>
                            
                            <div class="mb-3">
                                <label class="fw-bold text-secondary small text-uppercase">Identificación de Obra</label>
                                <input type="text" name="name" class="form-control fw-bold border-secondary" required>
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold text-secondary small text-uppercase">Entidad / Cliente Responsable</label>
                                <input type="text" name="company_client" class="form-control border-secondary" required>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="fw-bold text-secondary small text-uppercase">Clasificación</label>
                                    <select name="type_work" class="form-select border-secondary fw-bold">
                                        <option value="Residencial">Residencial</option>
                                        <option value="Comercial">Comercial</option>
                                        <option value="Vial">Infraestructura Vial</option>
                                        <option value="Industrial">Planta Industrial</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="fw-bold text-secondary small text-uppercase">Fecha de Inicio</label>
                                    <input type="date" name="start_date" class="form-control border-secondary fw-bold" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold text-secondary small text-uppercase">Presupuesto Asignado (Opcional)</label>
                                <input type="number" name="budget" class="form-control border-secondary fw-bold" placeholder="0.00" step="0.01">
                            </div>

                            <div class="mb-0">
                                <label class="fw-bold text-secondary small text-uppercase">Soporte Gráfico / Plano</label>
                                <input type="file" name="image" class="form-control border-secondary bg-white" accept="image/*">
                            </div>
                        </div>
                        
                        <div class="col-md-7">
                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-uppercase" style="color: var(--panel-darker);">Geolocalización del Terreno</h6>
                            <div class="input-group mb-3 shadow-sm">
                                <input type="text" id="txtDireccionUser" name="address" class="form-control border-secondary fw-bold" placeholder="Buscar dirección o municipio..." required>
                                <button class="btn btn-dark fw-bold px-4" type="button" id="btnBuscarUser" onclick="buscarDireccionUser()">
                                    <i class="fa-solid fa-magnifying-glass me-1"></i> Localizar
                                </button>
                            </div>
                            
                            <div id="miniMapUser" style="height: 330px; width: 100%; border-radius: 8px; border: 2px solid var(--panel-dark);"></div>
                            
                            <div class="mt-3 text-center">
                                <span class="badge bg-success d-none p-2 rounded px-3 shadow-sm fw-bold" id="msgExitoUser" style="font-size: 0.85rem;">
                                    <i class="fa-solid fa-satellite-dish me-1"></i> Coordenadas fijadas en el servidor
                                </span>
                            </div>

                            <input type="hidden" id="latUser" name="lat">
                            <input type="hidden" id="lngUser" name="lng">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top py-3">
                    <button type="button" class="btn btn-outline-secondary fw-bold rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm rounded-pill" id="btnGuardarUser" disabled>
                        <i class="fa-solid fa-satellite me-2"></i> Transmitir Coordenadas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end shadow-lg" tabindex="-1" id="userChatOffcanvas" style="width: 450px; border-left: 5px solid var(--panel-dark);">
    <div class="offcanvas-header text-white border-bottom" style="background-color: var(--panel-dark);">
        <h5 class="offcanvas-title fw-bold text-uppercase" style="letter-spacing: 1px;"><i class="fa-solid fa-headset me-2" style="color: var(--safety-orange);"></i> Soporte Técnico</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column p-0" style="background-color: var(--bg-app); overflow: hidden;">
        
        <div class="p-3 bg-white border-bottom shadow-sm z-1">
            <span class="badge text-white mb-1 px-2 py-1" style="background-color: #ef4444; font-size: 0.7rem; font-weight: bold; letter-spacing: 1px;">REPORTE ORIGINAL DEL TICKET</span>
            <p id="userChatOriginalDesc" class="mb-0 small text-dark fw-bold" style="line-height: 1.3; font-family: monospace;"></p>
        </div>
        
        <div id="userChatMessagesBox" class="flex-grow-1 p-3 d-flex flex-column gap-3" style="background: var(--bg-app); overflow-y: auto;">
            </div>
        
        <div class="p-3 bg-white border-top shadow-lg">
            <form id="userChatForm">
                <input type="hidden" id="userChatRequestId">
                <div class="input-group shadow-sm">
                    <input type="text" id="userChatInput" class="form-control rounded-start fw-bold" style="border: 2px solid var(--panel-dark);" placeholder="Agregar actualización a la bitácora..." required autocomplete="off">
                    <button class="btn btn-primary fw-bold px-4" type="submit" id="btnSendUserChat">
                        <i class="fa-solid fa-share"></i>
                    </button>
                </div>
                <div class="text-muted fw-bold text-center mt-2" style="font-size: 0.70rem; text-transform: uppercase;">
                    <i class="fa-solid fa-lock me-1"></i> Comunicación cifrada con Central
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // --- LÓGICA DE BORRADO DE HISTORIAL ---
    document.addEventListener("DOMContentLoaded", function() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.row-checkbox');
        const btnDelete = document.getElementById('btnDeleteSelected');
        const countSpan = document.getElementById('countSelected');

        function updateButton() {
            const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
            countSpan.textContent = checkedCount;
            if(checkedCount > 0) {
                btnDelete.style.display = 'inline-block';
            } else {
                btnDelete.style.display = 'none';
            }
        }

        if(selectAll){
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = selectAll.checked);
                updateButton();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateButton);
        });
    });

    function deleteSelected() {
        const checked = document.querySelectorAll('.row-checkbox:checked');
        if(checked.length === 0) return;

        let ids = [];
        checked.forEach(cb => ids.push(cb.value));

        Swal.fire({
            title: 'Confirmación de Purga',
            text: "Se procederá a archivar " + ids.length + " registros de su historial local.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ea580c', // Naranja Seguridad
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ejecutar Purga',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('<?= base_url ?>User/deleteRequests', {ids: ids}, function(response) {
                    try {
                        const data = JSON.parse(response);
                        if(data.status === 'success') {
                            Swal.fire({
                                title: 'Operación Exitosa',
                                text: data.msg,
                                icon: 'success',
                                confirmButtonColor: '#ea580c'
                            }).then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.msg, 'error');
                        }
                    } catch(e) { console.error(e); }
                });
            }
        });
    }

    // --- LÓGICA DEL MAPA GEORREFERENCIADO ---
    var miniMapUser, markerUser;
    
    document.getElementById('modalNewProject').addEventListener('shown.bs.modal', function () {
        if (!miniMapUser) {
            miniMapUser = L.map('miniMapUser').setView([4.5709, -74.2973], 5);
            L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles &copy; Esri'
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
            btn.innerHTML = '<i class="fa-solid fa-magnifying-glass"></i> Localizar';
            btn.disabled = false;

            if(data.candidates && data.candidates.length > 0) {
                var lat = data.candidates[0].location.y;
                var lng = data.candidates[0].location.x;
                miniMapUser.setView([lat, lng], 16);
                ponerPinUser(lat, lng);
            }
        }).catch(err => {
            btn.innerHTML = '<i class="fa-solid fa-magnifying-glass"></i> Localizar';
            btn.disabled = false;
        });
    }

    function ponerPinUser(lat, lng) {
        if (markerUser) miniMapUser.removeLayer(markerUser);
        
        var corporateIcon = L.divIcon({ 
            className: 'custom-div-icon', 
            html: `<div style="width: 32px; height: 32px; background: #ea580c; border: 2px solid #ffffff; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); box-shadow: 0 4px 8px rgba(0,0,0,0.3); display: flex; justify-content: center; align-items: center;"><i class='fa-solid fa-crosshairs' style='transform: rotate(45deg); color: #ffffff; font-size: 12px;'></i></div>`, 
            iconSize: [32, 32], 
            iconAnchor: [16, 32], 
            popupAnchor: [0, -32] 
        });

        markerUser = L.marker([lat, lng], {icon: corporateIcon, draggable: true}).addTo(miniMapUser);
        
        document.getElementById('latUser').value = lat;
        document.getElementById('lngUser').value = lng;
        document.getElementById('btnGuardarUser').disabled = false;
        document.getElementById('msgExitoUser').classList.remove('d-none');
        
        markerUser.on('dragend', function(e) {
            var pos = e.target.getLatLng();
            document.getElementById('latUser').value = pos.lat;
            document.getElementById('lngUser').value = pos.lng;
        });
    }

    // --- LÓGICA DEL CHAT (MODO TICKET DE SOPORTE) ---
    let currentUserIdChat = null;
    const uOffcanvasEl = document.getElementById('userChatOffcanvas');
    let uChatOffcanvas = null;

    document.addEventListener('DOMContentLoaded', function () {
        if(uOffcanvasEl) { uChatOffcanvas = new bootstrap.Offcanvas(uOffcanvasEl); }
    });

    function openUserChat(id, desc) {
        currentUserIdChat = id;
        document.getElementById('userChatRequestId').value = id;
        document.getElementById('userChatOriginalDesc').innerText = '> ' + desc;
        uChatOffcanvas.show();
        loadUserChatMessages();
    }

    function loadUserChatMessages() {
        if(!currentUserIdChat) return;
        const box = document.getElementById('userChatMessagesBox');
        box.innerHTML = '<div class="text-center mt-5"><div class="spinner-border text-primary" role="status"></div></div>';
        
        $.get('<?= base_url ?>User/loadChat?id=' + currentUserIdChat, function(res) {
            box.innerHTML = '';
            if(res.data && res.data.length > 0) {
                res.data.forEach(msg => {
                    const isMe = (msg.sender_id == '<?= $_SESSION["identity"]->id ?>');
                    
                    // Diseño tipo Ticket/Terminal en lugar de burbujitas
                    const bgColor = isMe ? '#ffffff' : '#e2e8f0';
                    const borderLeft = isMe ? '4px solid #ea580c' : '4px solid #1e293b';
                    const senderName = isMe ? '👤 OPERADOR EN CAMPO' : '⚙️ CENTRAL DE SOPORTE';
                    const align = isMe ? 'ms-auto' : 'me-auto';

                    box.innerHTML += `
                        <div class="card border-0 shadow-sm ${align} w-100 mb-2" style="background-color: ${bgColor}; border-left: ${borderLeft} !important; border-radius: 4px;">
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="fw-bold" style="font-size: 0.7rem; color: #475569;">${senderName}</small>
                                    <small class="text-muted" style="font-size: 0.65rem; font-family: monospace;">${msg.time}</small>
                                </div>
                                <p class="mb-0 text-dark fw-bold" style="font-size: 0.85rem; font-family: monospace;">> ${msg.message}</p>
                            </div>
                        </div>
                    `;
                });
                box.scrollTop = box.scrollHeight;
            } else {
                box.innerHTML = '<div class="text-center text-muted mt-5 py-5"><i class="fa-solid fa-clipboard-list fa-3x mb-3 opacity-25"></i><p class="small fw-bold text-uppercase">Bitácora de comunicación vacía.</p></div>';
            }
        }, 'json');
    }

    document.getElementById('userChatForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const msgInput = document.getElementById('userChatInput');
        const btn = document.getElementById('btnSendUserChat');
        const msg = msgInput.value.trim();
        if(!msg) return;

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
        
        $.post('<?= base_url ?>User/sendChatMessage', {request_id: currentUserIdChat, message: msg}, function(res) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-share"></i>';
            if(res.status === 'success') {
                msgInput.value = '';
                loadUserChatMessages();
            } else {
                Swal.fire('Error', res.msg, 'error');
            }
        }, 'json');
    });
</script>