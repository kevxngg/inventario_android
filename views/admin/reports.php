<?php 
require_once 'views/layouts/header.php'; 

// Bloque de consulta directa para obtener los activos que están actualmente en terreno (EN_OBRA)
$db = Database::connect();
$sqlActive = "SELECT a.*, t.name as tool_name, u.fullname, p.name as project_name 
              FROM assignments a 
              INNER JOIN tools t ON a.tool_id = t.id 
              INNER JOIN users u ON a.user_id = u.id
              LEFT JOIN projects p ON a.project_id = p.id
              WHERE a.status = 'ACTIVO' ORDER BY a.assigned_at DESC";
$activosEnObra = $db->query($sqlActive);
?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold text-dark"><i class="fa-solid fa-clipboard-check me-2"></i>Centro de Auditoría y Control</h2>
            <p class="text-muted mb-0">Gestión de retornos físicos y soporte mediante Mesa de Ayuda.</p>
        </div>
        
        <div class="d-flex gap-2">
            <button id="btnDeleteSelected" class="btn btn-danger rounded-pill px-3 shadow-sm" style="display: none;" onclick="deleteSelected()">
                <i class="fa-solid fa-trash-can me-2"></i> Purgar Registros
            </button>

            <a href="<?= base_url ?>Admin/exportExcel" class="btn btn-success rounded-pill px-3 shadow-sm fw-bold">
                <i class="fa-solid fa-file-excel me-2"></i> Exportar Excel
            </a>

            <a href="<?= base_url ?>Admin/printReports" target="_blank" class="btn btn-dark rounded-pill px-3 shadow-sm">
                <i class="fa-solid fa-print me-2"></i> Imprimir / PDF
            </a>
        </div>
    </div>

    <ul class="nav nav-tabs mb-4" id="auditoriaTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold text-primary" id="checkin-tab" data-bs-toggle="tab" data-bs-target="#checkin" type="button" role="tab">
                <i class="fa-solid fa-right-left me-2"></i>Retorno de Activos (Check-In)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold text-secondary" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
                <i class="fa-solid fa-clock-rotate-left me-2"></i>Historial de Solicitudes
            </button>
        </li>
    </ul>

    <div class="tab-content" id="auditoriaTabsContent">
        
        <div class="tab-pane fade show active" id="checkin" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-uppercase small text-muted">
                                <tr>
                                    <th class="ps-4 py-3">ID Asignación</th>
                                    <th>Activo Físico</th>
                                    <th>Responsable</th>
                                    <th>Frente de Obra</th>
                                    <th>Unidades</th>
                                    <th class="text-end pe-4">Acción Requerida</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(isset($activosEnObra) && $activosEnObra->num_rows > 0): ?>
                                    <?php while($activo = $activosEnObra->fetch_object()): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-secondary">#<?= str_pad($activo->id, 5, "0", STR_PAD_LEFT) ?></td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= $activo->tool_name ?></div>
                                            <small class="text-muted">Despacho: <?= date('d/m/Y', strtotime($activo->assigned_at)) ?></small>
                                        </td>
                                        <td><div class="d-flex align-items-center"><i class="fa-solid fa-user-helmet text-secondary me-2"></i><span class="fw-semibold text-dark"><?= $activo->fullname ?></span></div></td>
                                        <td><span class="badge bg-light text-dark border border-secondary"><i class="fa-solid fa-location-dot me-1"></i> <?= $activo->project_name ?? 'Bodega Central' ?></span></td>
                                        <td><span class="fw-bold bg-light px-2 py-1 border rounded"><?= $activo->quantity ?></span></td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-sm btn-primary shadow-sm fw-bold" onclick="processCheckIn(<?= $activo->id ?>, '<?= addslashes($activo->tool_name) ?>', '<?= addslashes($activo->fullname) ?>')">
                                                <i class="fa-solid fa-boxes-packing me-1"></i> Recibir Físicamente
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="text-center py-5 text-muted"><p class="mb-0 fw-bold">No hay activos pendientes por retorno.</p></td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="history" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-uppercase small text-muted">
                                <tr>
                                    <th class="ps-4 py-3" style="width: 40px;"><input type="checkbox" id="selectAll" class="form-check-input border-secondary" style="cursor: pointer;"></th>
                                    <th>Usuario Solicitante</th>
                                    <th>Clasificación</th>
                                    <th>Detalle Operativo</th>
                                    <th>Fecha de Emisión</th>
                                    <th class="text-end pe-4">Estado Actual</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(isset($reportes) && $reportes->num_rows > 0): ?>
                                    <?php while($row = $reportes->fetch_object()): ?>
                                    <tr id="row-<?= $row->request_unique_id ?>">
                                        <td class="ps-4"><input type="checkbox" class="form-check-input border-secondary row-checkbox" value="<?= $row->request_unique_id ?>"></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded bg-secondary text-white d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 35px; height: 35px; font-weight: bold;">
                                                    <i class="fa-solid fa-user"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark"><?= $row->fullname ?></div>
                                                    <span class="badge bg-light text-secondary border border-secondary"><?= $row->role ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if($row->type == 'SOLICITUD_HERRAMIENTA'): ?>
                                                <span class="badge border border-info text-info bg-light"><i class="fa-solid fa-arrow-right-from-bracket me-1"></i> Despacho</span>
                                            <?php else: ?>
                                                <span class="badge border border-danger text-danger bg-light"><i class="fa-solid fa-triangle-exclamation me-1"></i> Soporte / Incidencia</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <p class="mb-0 text-secondary fw-semibold" style="max-width: 300px; font-size: 0.9rem;"><?= $row->description ?></p>
                                        </td>
                                        <td class="text-muted small fw-bold"><?= date('d/m/Y', strtotime($row->created_at)) ?></td>
                                        <td class="text-end pe-4">
                                            
                                            <?php if($row->type == 'REPORTE_DAÑO' || strpos($row->type, 'REPORTE') !== false): ?>
                                                
                                                <button class="btn btn-sm btn-info text-white fw-bold shadow-sm rounded-pill px-3" 
                                                        onclick="openChat(<?= $row->request_unique_id ?>, '<?= addslashes($row->description) ?>')">
                                                    <i class="fa-solid fa-comments me-1"></i> Soporte
                                                </button>

                                            <?php else: ?>
                                                
                                                <?php if($row->status == 'PENDIENTE'): ?>
                                                    <div class="btn-group shadow-sm">
                                                        <a href="<?= base_url ?>Admin/changeStatus?id=<?= $row->request_unique_id ?>&status=APROBADO" class="btn btn-sm btn-success fw-bold"><i class="fa-solid fa-check"></i></a>
                                                        <a href="<?= base_url ?>Admin/changeStatus?id=<?= $row->request_unique_id ?>&status=RECHAZADO" class="btn btn-sm btn-danger fw-bold"><i class="fa-solid fa-xmark"></i></a>
                                                    </div>
                                                <?php elseif($row->status == 'APROBADO'): ?>
                                                    <span class="badge bg-success shadow-sm">Autorizado</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger shadow-sm">Denegado</span>
                                                <?php endif; ?>

                                            <?php endif; ?>
                                            
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="text-center py-5 text-muted"><p class="mb-0 fw-bold">El historial de auditoría se encuentra vacío.</p></td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="offcanvas offcanvas-end shadow" tabindex="-1" id="chatOffcanvas" style="width: 450px;">
    <div class="offcanvas-header bg-primary text-white border-bottom shadow-sm">
        <h5 class="offcanvas-title fw-bold"><i class="fa-solid fa-headset me-2"></i> Mesa de Ayuda</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column p-0 bg-light" style="overflow: hidden;">
        
        <div class="p-3 bg-white border-bottom shadow-sm z-1">
            <span class="badge bg-danger mb-1"><i class="fa-solid fa-triangle-exclamation"></i> Falla Reportada</span>
            <p id="chatOriginalDesc" class="mb-0 small text-secondary fw-bold fst-italic"></p>
        </div>
        
        <div id="chatMessagesBox" class="flex-grow-1 p-3 d-flex flex-column gap-3" style="background: #eef2f5; overflow-y: auto;">
            </div>
        
        <div class="p-3 bg-white border-top shadow-lg">
            <form id="chatForm">
                <input type="hidden" id="chatRequestId">
                <div class="input-group shadow-sm">
                    <input type="text" id="chatInput" class="form-control rounded-start border-primary" placeholder="Escribe tu respuesta..." required autocomplete="off">
                    <button class="btn btn-primary fw-bold px-3 border-primary" type="submit" id="btnSendChat">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>
                <div class="text-muted small text-center mt-2" style="font-size: 0.70rem;">
                    <i class="fa-solid fa-envelope me-1"></i> El mensaje será notificado al correo del técnico.
                </div>
            </form>
        </div>

    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>

<script>
    // LÓGICA DEL CHAT DE SOPORTE
    let currentChatRequest = null;
    const offcanvasEl = document.getElementById('chatOffcanvas');
    let chatOffcanvas = null;

    document.addEventListener('DOMContentLoaded', function () {
        if(offcanvasEl) { chatOffcanvas = new bootstrap.Offcanvas(offcanvasEl); }
    });

    function openChat(reqId, description) {
        currentChatRequest = reqId;
        document.getElementById('chatRequestId').value = reqId;
        document.getElementById('chatOriginalDesc').innerText = '"' + description + '"';
        chatOffcanvas.show();
        loadChatMessages();
    }

    function loadChatMessages() {
        if(!currentChatRequest) return;
        const box = document.getElementById('chatMessagesBox');
        box.innerHTML = '<div class="text-center mt-5"><div class="spinner-border text-primary" role="status"></div></div>';
        
        $.get('<?= base_url ?>Admin/loadChat?id=' + currentChatRequest, function(res) {
            box.innerHTML = '';
            if(res.data && res.data.length > 0) {
                res.data.forEach(msg => {
                    // Validar si el mensaje es del Administrador (Azul a la derecha) o del Usuario (Blanco a la izquierda)
                    const isMe = (msg.role === 'ADMIN');
                    const alignClass = isMe ? 'align-self-end text-end' : 'align-self-start text-start';
                    const bgClass = isMe ? 'bg-primary text-white shadow-sm' : 'bg-white border text-dark shadow-sm';
                    const radius = isMe ? '15px 15px 0 15px' : '15px 15px 15px 0';
                    const userName = isMe ? 'Soporte Técnico' : msg.fullname;

                    box.innerHTML += `
                        <div class="${alignClass}" style="max-width: 85%;">
                            <div class="small text-muted fw-bold mb-1" style="font-size: 0.65rem; text-transform: uppercase;">${userName}</div>
                            <div class="p-2 px-3" style="${bgClass}; border-radius: ${radius};">
                                <p class="mb-0 small" style="line-height: 1.4;">${msg.message}</p>
                            </div>
                            <div class="text-muted mt-1" style="font-size: 0.65rem;"><i class="fa-regular fa-clock me-1"></i>${msg.time}</div>
                        </div>
                    `;
                });
                box.scrollTop = box.scrollHeight; // Auto-scroll al fondo
            } else {
                box.innerHTML = `
                    <div class="text-center text-muted mt-5 small">
                        <i class="fa-solid fa-headset fa-3x mb-3 text-secondary opacity-25"></i><br>
                        <span class="fw-bold text-dark">Ticket Abierto</span><br>
                        Aún no hay respuestas en este ticket.<br>Escribe un mensaje para iniciar el soporte.
                    </div>`;
            }
        }, 'json');
    }

    // Enviar Mensaje
    document.getElementById('chatForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const reqId = document.getElementById('chatRequestId').value;
        const msgInput = document.getElementById('chatInput');
        const msg = msgInput.value;
        const btnSend = document.getElementById('btnSendChat');
        
        if(!msg.trim()) return;

        btnSend.disabled = true;
        btnSend.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
        
        $.post('<?= base_url ?>Admin/sendChatMessage', {request_id: reqId, message: msg}, function(res) {
            btnSend.disabled = false;
            btnSend.innerHTML = '<i class="fa-solid fa-paper-plane"></i>';
            if(res.status === 'success') {
                msgInput.value = '';
                loadChatMessages();
            } else {
                Swal.fire('Error', res.msg, 'error');
            }
        }, 'json').fail(function(){
            btnSend.disabled = false;
            btnSend.innerHTML = '<i class="fa-solid fa-paper-plane"></i>';
            Swal.fire('Error de Red', 'No se pudo enviar el mensaje.', 'error');
        });
    });

    // LÓGICA CHECK-IN Y PURGA ORIGINALES
    function processCheckIn(id, tool, user) {
        Swal.fire({
            title: 'Recibir Activo',
            html: `<div class="text-start"><p class="mb-1 small"><b>Activo:</b> ${tool}</p><p class="mb-3 small"><b>Responsable:</b> ${user}</p><label class="form-label small fw-bold">ESTADO DE RETORNO</label><select id="retCond" class="form-select"><option value="BUENO">Bueno</option><option value="DAÑADO">Dañado</option><option value="PERDIDO">Perdido</option></select></div>`,
            showCancelButton: true,
            confirmButtonText: 'Confirmar'
        }).then((res) => { if(res.isConfirmed) { $.post('<?= base_url ?>Admin/processReturn', {assignment_id: id, return_condition: document.getElementById('retCond').value}, () => location.reload()); } });
    }

    function deleteSelected() {
        let ids = []; document.querySelectorAll('.row-checkbox:checked').forEach(cb => ids.push(cb.value));
        Swal.fire({ title: '¿Purgar registros?', icon: 'warning', showCancelButton: true }).then((res) => {
            if(res.isConfirmed) { $.post('<?= base_url ?>Admin/deleteReports', {ids: ids}, () => location.reload()); }
        });
    }

    document.getElementById('selectAll')?.addEventListener('change', function() {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
        document.getElementById('btnDeleteSelected').style.display = document.querySelectorAll('.row-checkbox:checked').length > 0 ? 'inline-block' : 'none';
    });
</script>