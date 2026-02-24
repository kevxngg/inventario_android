<?php require_once 'views/layouts/header.php'; ?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
    /* Estilos Estructurales - Perfil Corporativo */
    .dashboard-header {
        background-color: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: linear-gradient(135deg, #004b87 0%, #002c53 100%);
        color: #ffffff;
        border-radius: 12px;
        min-height: 140px;
        box-shadow: 0 4px 15px rgba(0, 75, 135, 0.2);
    }

    .stat-icon-wrapper {
        background-color: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(5px);
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1.5rem;
    }

    .action-card {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .action-card:hover {
        border-color: #004b87;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        transform: translateY(-3px);
    }

    .action-icon-wrapper {
        background-color: #f1f5f9;
        color: #004b87;
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Tabla de Historial */
    .history-table th {
        background-color: #f8fafc;
        color: #64748b;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
    }
    .history-table td {
        vertical-align: middle;
        padding: 1rem 0.5rem;
    }

    /* Fix para Mapas en Modales */
    .modal { z-index: 1050; }
    #miniMapUser { z-index: 1; }
</style>

<div class="container-fluid p-4">
    
    <div class="dashboard-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1">Bienvenido, <?= $_SESSION['identity']->fullname ?></h3>
            <p class="text-muted mb-0">Panel Operativo de Control en Terreno</p>
        </div>
        <div class="d-flex align-items-center">
            <span class="badge bg-light text-secondary border border-secondary px-3 py-2 rounded-pill shadow-sm">
                <i class="fa-solid fa-user-helmet me-2"></i> Técnico / Residente
            </span>
        </div>
    </div>

    <h5 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-layer-group me-2 text-primary"></i>Resumen de Asignaciones</h5>

    <div class="row mb-5 g-4">
        <div class="col-md-7">
            <div class="stat-card border-0 h-100">
                <div class="card-body p-4 d-flex align-items-center justify-content-between h-100">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon-wrapper">
                            <i class="fa-solid fa-map-location-dot fa-2x text-white"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1 text-white">Frentes de Obra Asignados</h5>
                            <p class="text-white-50 small mb-0">Proyectos bajo su supervisión directa</p>
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
                        <h6 class="fw-bold text-dark mb-2 text-uppercase" style="letter-spacing: 0.5px;">Apertura de Proyecto</h6>
                        <p class="text-muted small mb-3">Registrar y georeferenciar nueva zona de trabajo.</p>
                        <span class="btn btn-sm btn-outline-primary fw-bold px-3">
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

    <div class="glass-card p-0 border-0 shadow-sm overflow-hidden">
        <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-dark m-0"><i class="fa-solid fa-clock-rotate-left me-2 text-secondary"></i>Registro Histórico de Solicitudes</h6>
            <button id="btnDeleteSelected" class="btn btn-danger btn-sm px-3 shadow-sm fw-bold" style="display: none;" onclick="deleteSelected()">
                <i class="fa-solid fa-trash-can me-2"></i> Purgar Historial (<span id="countSelected">0</span>)
            </button>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table history-table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width: 40px;">
                                <input type="checkbox" id="selectAll" class="form-check-input border-secondary" style="cursor: pointer;">
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
                                        <span class="badge border border-info text-info bg-light me-2"><i class="fa-solid fa-box-open me-1"></i> Despacho</span>
                                    <?php else: ?>
                                        <span class="badge border border-danger text-danger bg-light me-2"><i class="fa-solid fa-triangle-exclamation me-1"></i> Reporte</span>
                                    <?php endif; ?>
                                    <span class="fw-semibold text-dark"><?= $req->description ?></span>
                                    <?php if(!empty($req->tool_name)): ?>
                                        <br><small class="text-muted ms-5"><i class="fa-solid fa-toolbox me-1"></i> <?= $req->tool_name ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-secondary small fw-bold">
                                    <i class="fa-regular fa-calendar me-1"></i> <?= date('d/m/Y', strtotime($req->created_at)) ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex flex-column align-items-end gap-2">
                                        <?php if($req->status == 'PENDIENTE'): ?>
                                            <span class="badge bg-warning text-dark shadow-sm px-2 py-1"><i class="fa-solid fa-hourglass-half me-1"></i> En Revisión</span>
                                        <?php elseif($req->status == 'APROBADO'): ?>
                                            <span class="badge bg-success shadow-sm px-2 py-1"><i class="fa-solid fa-check me-1"></i> Autorizado</span>
                                        <?php elseif($req->status == 'RESUELTO'): ?>
                                            <span class="badge bg-primary shadow-sm px-2 py-1"><i class="fa-solid fa-check-double me-1"></i> Finalizado</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger shadow-sm px-2 py-1"><i class="fa-solid fa-xmark me-1"></i> Denegado</span>
                                        <?php endif; ?>

                                        <?php if(strpos($req->type, 'REPORTE') !== false): ?>
                                            <button class="btn btn-sm btn-outline-primary fw-bold rounded-pill shadow-sm" 
                                                    onclick="openUserChat(<?= $req->id ?>, '<?= addslashes($req->description) ?>')">
                                                <i class="fa-solid fa-comments me-1"></i> Ver Chat
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-file-circle-question fa-2x mb-3 text-secondary"></i>
                                    <p class="fw-bold mb-0">Su registro de transacciones operativas está vacío.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNewProject" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white py-3">
                <h6 class="modal-title fw-bold text-uppercase" style="letter-spacing: 1px;"><i class="fa-solid fa-map-pin me-2 text-primary"></i>Apertura de Proyecto Georeferenciado</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url ?>User/saveProject" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-5 border-end">
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Especificaciones Técnicas</h6>
                            
                            <div class="mb-3">
                                <label class="fw-bold text-secondary small text-uppercase">Identificación de Obra</label>
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

                            <div class="mb-3">
                                <label class="fw-bold text-secondary small text-uppercase">Presupuesto Asignado (Opcional)</label>
                                <input type="number" name="budget" class="form-control" placeholder="0.00" step="0.01">
                            </div>

                            <div class="mb-0">
                                <label class="fw-bold text-secondary small text-uppercase">Soporte Gráfico / Plano</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                        </div>
                        
                        <div class="col-md-7">
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Geolocalización del Terreno</h6>
                            <div class="input-group mb-3">
                                <input type="text" id="txtDireccionUser" name="address" class="form-control" placeholder="Buscar dirección o municipio..." required>
                                <button class="btn btn-dark" type="button" id="btnBuscarUser" onclick="buscarDireccionUser()">
                                    <i class="fa-solid fa-magnifying-glass me-1"></i> Localizar
                                </button>
                            </div>
                            
                            <div id="miniMapUser" style="height: 330px; width: 100%; border-radius: 8px; border: 1px solid #cbd5e0;"></div>
                            
                            <div class="mt-3 text-center">
                                <span class="badge bg-success d-none p-2 rounded px-3 shadow-sm fw-bold" id="msgExitoUser">
                                    <i class="fa-solid fa-satellite-dish me-1"></i> Coordenadas fijadas en el servidor
                                </span>
                            </div>

                            <input type="hidden" id="latUser" name="lat">
                            <input type="hidden" id="lngUser" name="lng">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary fw-bold px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm" id="btnGuardarUser" disabled>
                        <i class="fa-solid fa-satellite me-2"></i> Transmitir Coordenadas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end shadow" tabindex="-1" id="userChatOffcanvas" style="width: 450px;">
    <div class="offcanvas-header bg-dark text-white border-bottom shadow-sm">
        <h5 class="offcanvas-title fw-bold"><i class="fa-solid fa-headset me-2 text-info"></i> Soporte Central</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column p-0 bg-light" style="overflow: hidden;">
        
        <div class="p-3 bg-white border-bottom shadow-sm z-1">
            <span class="badge border border-danger text-danger mb-1" style="font-size: 0.65rem;">TU REPORTE ORIGINAL</span>
            <p id="userChatOriginalDesc" class="mb-0 small text-secondary fw-bold fst-italic" style="line-height: 1.2;"></p>
        </div>
        
        <div id="userChatMessagesBox" class="flex-grow-1 p-3 d-flex flex-column gap-3" style="background: #eef2f5; overflow-y: auto;">
            </div>
        
        <div class="p-3 bg-white border-top shadow-lg">
            <form id="userChatForm">
                <input type="hidden" id="userChatRequestId">
                <div class="input-group shadow-sm">
                    <input type="text" id="userChatInput" class="form-control rounded-start border-primary" placeholder="Escribe un mensaje..." required autocomplete="off">
                    <button class="btn btn-primary fw-bold px-3 border-primary" type="submit" id="btnSendUserChat">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>
                <div class="text-muted small text-center mt-2" style="font-size: 0.70rem;">
                    <i class="fa-solid fa-check me-1"></i> El administrador será notificado.
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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
            confirmButtonColor: '#004b87',
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
                                confirmButtonColor: '#004b87'
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
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: 'SICOT ERP | &copy; CARTO'
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
            html: `<div style="width: 32px; height: 32px; background: #004b87; border: 2px solid #ffffff; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); box-shadow: 0 4px 8px rgba(0,0,0,0.2); display: flex; justify-content: center; align-items: center;"><i class='fa-solid fa-crosshairs' style='transform: rotate(45deg); color: #ffffff; font-size: 12px;'></i></div>`, 
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

    // --- LÓGICA DEL CHAT DE SOPORTE PARA USUARIO ---
    let currentUserIdChat = null;
    const uOffcanvasEl = document.getElementById('userChatOffcanvas');
    let uChatOffcanvas = null;

    document.addEventListener('DOMContentLoaded', function () {
        if(uOffcanvasEl) { uChatOffcanvas = new bootstrap.Offcanvas(uOffcanvasEl); }
    });

    function openUserChat(id, desc) {
        currentUserIdChat = id;
        document.getElementById('userChatRequestId').value = id;
        document.getElementById('userChatOriginalDesc').innerText = '"' + desc + '"';
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
                    // Verificamos si el mensaje lo envió el usuario actual o el admin
                    const isMe = (msg.sender_id == '<?= $_SESSION["identity"]->id ?>');
                    const alignClass = isMe ? 'align-self-end text-end' : 'align-self-start text-start';
                    const bgClass = isMe ? 'bg-primary text-white shadow-sm' : 'bg-white border text-dark shadow-sm';
                    const radius = isMe ? '15px 15px 0 15px' : '15px 15px 15px 0';
                    const senderName = isMe ? 'Tú' : 'Soporte Central';

                    box.innerHTML += `
                        <div class="${alignClass}" style="max-width: 85%;">
                            <div class="small text-muted fw-bold mb-1" style="font-size: 0.65rem;">${senderName}</div>
                            <div class="p-2 px-3" style="${bgClass}; border-radius: ${radius};">
                                <p class="mb-0 small">${msg.message}</p>
                            </div>
                            <div class="text-muted mt-1" style="font-size: 0.65rem;"><i class="fa-regular fa-clock me-1"></i>${msg.time}</div>
                        </div>
                    `;
                });
                box.scrollTop = box.scrollHeight;
            } else {
                box.innerHTML = '<div class="text-center text-muted mt-5 py-5"><i class="fa-solid fa-comments fa-3x mb-3 opacity-25"></i><p class="small">Aún no hay mensajes. Escribe para contactar a soporte.</p></div>';
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
            btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i>';
            if(res.status === 'success') {
                msgInput.value = '';
                loadUserChatMessages();
                // Opcional: Recargar la página si quieres que el estado cambie visualmente en la tabla
                // location.reload(); 
            } else {
                Swal.fire('Error', res.msg, 'error');
            }
        }, 'json');
    });
</script>