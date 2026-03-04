<?php require_once 'views/layouts/header.php'; ?>

<style>
    /* ==============================================
       ESTILOS INDUSTRIALES - TICKETS DE SOPORTE
       ============================================== */
    .helpdesk-container {
        height: calc(100vh - 120px);
        display: flex;
        border-radius: 12px;
        overflow: hidden;
        background-color: var(--bg-app);
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        border: 2px solid var(--panel-dark);
    }
    
    /* Panel Izquierdo: Lista de Tickets con Pestañas */
    .chat-sidebar {
        width: 380px;
        background-color: var(--white);
        border-right: 2px solid var(--panel-dark);
        display: flex;
        flex-direction: column;
    }

    /* Pestañas de filtrado (Bootstrap Nav-Tabs adaptadas) */
    .nav-tickets {
        background-color: var(--panel-darker);
        padding: 10px 10px 0 10px;
    }
    .nav-tickets .nav-link {
        color: #94a3b8;
        border: none;
        border-bottom: 3px solid transparent;
        border-radius: 0;
        font-size: 0.75rem;
        font-weight: bold;
        text-transform: uppercase;
        padding: 10px 12px;
    }
    .nav-tickets .nav-link:hover { color: white; }
    .nav-tickets .nav-link.active {
        color: var(--safety-orange);
        background-color: transparent;
        border-bottom: 3px solid var(--safety-orange);
    }
    
    .chat-list {
        overflow-y: auto;
        flex-grow: 1;
        background-color: var(--bg-app);
    }
    
    .ticket-item {
        padding: 15px;
        border-bottom: 1px solid var(--border-color);
        cursor: pointer;
        transition: all 0.2s ease;
        border-left: 4px solid transparent;
        background-color: var(--white);
    }
    
    .ticket-item:hover { background-color: rgba(30, 41, 59, 0.05); }
    .ticket-item.active { background-color: rgba(234, 88, 12, 0.1); border-left: 4px solid var(--safety-orange); }
    
    /* Panel Derecho: Área de Trabajo / Consola */
    .chat-main {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        background-color: #f8fafc;
    }
    
    .chat-header {
        background-color: var(--panel-dark);
        color: white;
        padding: 15px 20px;
        display: flex;
        align-items: center;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        z-index: 10;
    }
    
    .chat-messages {
        flex-grow: 1;
        padding: 20px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    /* Cajas de Bitácora */
    .message-bubble {
        max-width: 85%;
        padding: 12px 15px;
        border-radius: 4px;
        position: relative;
        font-size: 0.95rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        border: 1px solid var(--border-color);
    }
    
    .message-admin {
        background-color: var(--white);
        align-self: flex-end;
        border-left: 4px solid var(--panel-dark);
    }
    
    .message-user {
        background-color: var(--white);
        align-self: flex-start;
        border-left: 4px solid var(--safety-orange);
    }
    
    /* Barra de Escritura */
    .chat-input-area {
        background-color: var(--white);
        padding: 15px 20px;
        display: flex;
        gap: 10px;
        align-items: center;
        border-top: 2px solid var(--border-color);
    }
    
    .chat-input-area input {
        border-radius: 4px;
        padding: 12px 20px;
        border: 2px solid var(--border-color);
        font-family: monospace;
        font-weight: bold;
    }
    .chat-input-area input:focus { border-color: var(--panel-dark); box-shadow: none; }
    
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        background-color: var(--bg-app);
    }
</style>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold m-0" style="color: var(--panel-darker);"><i class="fa-solid fa-headset me-2" style="color: var(--safety-orange);"></i> Centro de Incidencias</h3>
            <p class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.85rem; letter-spacing: 1px;">Resolución de fallas y soporte a operadores en campo.</p>
        </div>
    </div>

    <div class="helpdesk-container">
        
        <div class="chat-sidebar">
            <ul class="nav nav-tabs nav-tickets" id="ticketTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-pendientes" data-bs-toggle="tab" data-bs-target="#pane-pendientes" type="button" role="tab">En Revisión</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-resueltos" data-bs-toggle="tab" data-bs-target="#pane-resueltos" type="button" role="tab">Resueltos</button>
                </li>
                <li class="nav-item ms-auto" role="presentation">
                    <div class="text-white fw-bold d-flex align-items-center h-100 pe-2" style="font-size: 0.8rem;">
                        Total: <?= count($tickets) ?>
                    </div>
                </li>
            </ul>
            
            <div class="tab-content chat-list custom-scroll" id="ticketTabsContent">
                
                <div class="tab-pane fade show active" id="pane-pendientes" role="tabpanel">
                    <?php 
                        $hasPendientes = false;
                        if(!empty($tickets)){
                            foreach($tickets as $t){
                                if($t->status == 'PENDIENTE'){
                                    $hasPendientes = true;
                                    $displayUnread = $t->unread_count > 9 ? '9+' : $t->unread_count;
                                    ?>
                                    <div class="ticket-item position-relative" onclick="openChat(<?= $t->request_unique_id ?>, '<?= addslashes($t->fullname) ?>', '<?= addslashes($t->description) ?>', this)">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <span class="fw-bold text-dark text-truncate" style="max-width: 180px;">
                                                <i class="fa-solid fa-user-helmet me-1 text-secondary"></i> <?= $t->fullname ?>
                                            </span>
                                            <small class="text-muted font-monospace fw-bold" style="font-size: 0.7rem;">
                                                <?= date('d/M H:i', strtotime($t->last_activity ?? $t->created_at)) ?>
                                            </small>
                                        </div>
                                        <div class="small text-secondary mb-2 fw-bold" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-family: monospace;">
                                            > <?= $t->description ?>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge text-dark px-2 shadow-sm" style="background-color: #eab308; font-size: 0.65rem;">En Revisión</span>
                                            <?php if($t->unread_count > 0): ?>
                                                <span class="badge bg-danger rounded px-2 shadow-sm font-monospace"><?= $displayUnread ?> Msg</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                        }
                        if(!$hasPendientes): 
                    ?>
                        <div class="p-4 text-center text-muted small mt-5">
                            <i class="fa-solid fa-clipboard-check fa-3x mb-3 opacity-25"></i><br>
                            <span class="fw-bold text-uppercase">Bandeja Limpia.</span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="tab-pane fade" id="pane-resueltos" role="tabpanel">
                    <?php 
                        $hasResueltos = false;
                        if(!empty($tickets)){
                            foreach($tickets as $t){
                                if($t->status != 'PENDIENTE'){
                                    $hasResueltos = true;
                                    $displayUnread = $t->unread_count > 9 ? '9+' : $t->unread_count;
                                    
                                    $badgeColor = ($t->status == 'RESUELTO' || $t->status == 'APROBADO') ? 'bg-success' : 'bg-danger';
                                    ?>
                                    <div class="ticket-item position-relative" onclick="openChat(<?= $t->request_unique_id ?>, '<?= addslashes($t->fullname) ?>', '<?= addslashes($t->description) ?>', this)">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <span class="fw-bold text-dark text-truncate" style="max-width: 180px;">
                                                <i class="fa-solid fa-user-helmet me-1 text-secondary"></i> <?= $t->fullname ?>
                                            </span>
                                            <small class="text-muted font-monospace fw-bold" style="font-size: 0.7rem;">
                                                <?= date('d/M H:i', strtotime($t->last_activity ?? $t->created_at)) ?>
                                            </small>
                                        </div>
                                        <div class="small text-secondary mb-2 fw-bold" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-family: monospace;">
                                            > <?= $t->description ?>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge <?= $badgeColor ?> px-2 shadow-sm" style="font-size: 0.65rem;"><?= $t->status ?></span>
                                            <?php if($t->unread_count > 0): ?>
                                                <span class="badge bg-danger rounded px-2 shadow-sm font-monospace"><?= $displayUnread ?> Msg</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                        }
                        if(!$hasResueltos): 
                    ?>
                        <div class="p-4 text-center text-muted small mt-5">
                            <i class="fa-solid fa-folder-open fa-3x mb-3 opacity-25"></i><br>
                            <span class="fw-bold text-uppercase">Historial Vacío.</span>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>

        <div class="chat-main" id="chatWindow" style="display: none;">
            
            <div class="chat-header">
                <div class="bg-white rounded d-flex align-items-center justify-content-center fw-bold me-3 shadow-sm" style="width: 45px; height: 45px; font-size: 1.2rem; color: var(--panel-dark);">
                    <i class="fa-solid fa-file-invoice"></i>
                </div>
                <div class="flex-grow-1">
                    <h5 class="m-0 fw-bold text-uppercase" id="chatUserName" style="letter-spacing: 1px;">Operador</h5>
                    <small class="text-white-50 font-monospace" id="chatUserStatus">Verificando señal...</small> 
                    <span class="text-white-50 mx-1">|</span>
                    <small class="text-white-50 fw-bold">ID TICKET #<span id="chatTicketId">000</span></small>
                </div>
                
                <div class="d-flex gap-2">
                    <button class="btn btn-sm border-0 text-white" title="Limpiar Consola" onclick="clearMyChat()">
                        <i class="fa-solid fa-eraser"></i>
                    </button>
                    <button class="btn btn-sm fw-bold text-uppercase shadow-sm" style="background-color: var(--safety-orange); color: white;" onclick="closeTicket()">
                        <i class="fa-solid fa-lock me-1"></i> Sellar Ticket
                    </button>
                </div>
            </div>

            <div class="p-3 border-bottom shadow-sm z-1" style="background-color: rgba(239, 68, 68, 0.1); font-size: 0.85rem; border-left: 4px solid #ef4444;">
                <span class="fw-bold text-danger text-uppercase"><i class="fa-solid fa-triangle-exclamation me-1"></i> Falla Reportada:</span> 
                <span id="chatIssueDesc" class="text-dark fw-bold font-monospace"></span>
            </div>

            <div class="chat-messages custom-scroll" id="chatMessagesBox">
                </div>

            <form id="chatForm" class="chat-input-area z-1">
                <input type="hidden" id="activeRequestId">
                <input type="text" id="chatInputMsg" class="form-control" placeholder="Añadir actualización a la bitácora del ticket..." required autocomplete="off">
                <button type="submit" class="btn btn-primary rounded shadow-sm fw-bold" style="width: 50px; height: 48px;" id="btnSendMsg">
                    <i class="fa-solid fa-terminal"></i>
                </button>
            </form>
        </div>

        <div class="chat-main empty-state" id="emptyStateWindow">
            <div class="text-center" style="background: var(--white); padding: 50px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid var(--border-color);">
                <i class="fa-solid fa-tower-broadcast fa-4x mb-4" style="color: var(--steel-gray);"></i>
                <h4 class="fw-bold text-uppercase" style="color: var(--panel-darker); letter-spacing: 1px;">Centro de Control</h4>
                <p class="text-muted fw-bold small">Seleccione una incidencia en el panel lateral para iniciar el protocolo de soporte.</p>
            </div>
        </div>

    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let currentTicketId = null;

    function openChat(id, userName, description, element) {
        document.querySelectorAll('.ticket-item').forEach(el => el.classList.remove('active'));
        element.classList.add('active');

        let badge = element.querySelector('.badge.bg-danger');
        if(badge) badge.style.display = 'none';

        document.getElementById('emptyStateWindow').style.display = 'none';
        document.getElementById('chatWindow').style.display = 'flex';

        currentTicketId = id;
        document.getElementById('activeRequestId').value = id;
        document.getElementById('chatUserName').innerText = userName;
        document.getElementById('chatTicketId').innerText = String(id).padStart(5, '0');
        document.getElementById('chatIssueDesc').innerText = '> ' + description;

        loadChatMessages();
    }

    function loadChatMessages() {
        if(!currentTicketId) return;
        const box = document.getElementById('chatMessagesBox');
        
        $.get('<?= base_url ?>Admin/loadChat?id=' + currentTicketId, function(res) {
            box.innerHTML = '';
            
            if(res.user_status) {
                document.getElementById('chatUserStatus').innerText = res.user_status.toUpperCase();
            }

            if(res.data && res.data.length > 0) {
                res.data.forEach(msg => {
                    const isMe = (msg.role === 'ADMIN' || msg.role === 'BODEGA');
                    const bubbleClass = isMe ? 'message-admin' : 'message-user';
                    const senderName = isMe ? 'ADMIN / BODEGA' : 'OPERARIO (' + msg.fullname + ')';
                    const iconColor = isMe ? 'var(--panel-dark)' : 'var(--safety-orange)';
                    
                    let checks = '';
                    if(isMe) {
                        checks = (msg.is_read == 1) 
                            ? '<i class="fa-solid fa-check-double ms-2" style="color: #10b981;"></i>' 
                            : '<i class="fa-solid fa-check ms-2 text-secondary"></i>';
                    }

                    box.innerHTML += `
                        <div class="message-bubble ${bubbleClass}">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-1 mb-2">
                                <div class="fw-bold text-uppercase" style="font-size: 0.70rem; color: ${iconColor}; letter-spacing: 0.5px;">
                                    <i class="fa-solid fa-user-tag me-1"></i> ${senderName}
                                </div>
                                <div class="text-muted font-monospace fw-bold" style="font-size: 0.7rem;">${msg.time} ${checks}</div>
                            </div>
                            <div class="font-monospace text-dark fw-bold">> ${msg.message}</div>
                        </div>
                    `;
                });
                box.scrollTop = box.scrollHeight;
            } else {
                box.innerHTML = `<div class="text-center mt-5"><span class="badge bg-light text-dark border p-2 font-monospace">CONSOLA INICIADA. ESPERANDO ENTRADA DE DATOS.</span></div>`;
            }
        }, 'json');
    }

    setInterval(() => { if(currentTicketId) loadChatMessages(); }, 10000);

    document.getElementById('chatForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const input = document.getElementById('chatInputMsg');
        const msg = input.value.trim();
        const btn = document.getElementById('btnSendMsg');

        if(!msg) return;

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

        $.post('<?= base_url ?>Admin/sendChatMessage', {request_id: currentTicketId, message: msg}, function(res) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-terminal"></i>';
            if(res.status === 'success') {
                input.value = '';
                loadChatMessages();
            } else {
                Swal.fire('Error', res.msg, 'error');
            }
        }, 'json');
    });

    function clearMyChat() {
        if(!currentTicketId) return;
        Swal.fire({
            title: '¿Purgar Consola Local?',
            text: "Se borrará su vista del historial. El usuario mantendrá su copia intacta.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Proceder',
            cancelButtonText: 'Cancelar'
        }).then((res) => {
            if(res.isConfirmed) {
                $.post('<?= base_url ?>Admin/clearChat', {request_id: currentTicketId}, function(response){
                    if(response.status === 'success') loadChatMessages(); 
                }, 'json');
            }
        });
    }

    function closeTicket() {
        if(!currentTicketId) return;
        Swal.fire({
            title: 'Protocolo de Cierre',
            text: "El ticket se marcará como resuelto y se enviará un correo final al usuario indicando la solución.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1e293b',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sellar Incidencia',
            cancelButtonText: 'Cancelar'
        }).then((res) => {
            if(res.isConfirmed) {
                // Al sellar el ticket, llamamos al método que lo cierra y envía el correo.
                $.get('<?= base_url ?>Admin/closeTicketMail?ajax=true&id=' + currentTicketId, function(response){
                    Swal.fire('Ticket Sellado', 'La incidencia ha sido solucionada y el operario fue notificado por correo.', 'success').then(() => location.reload());
                }, 'json').fail(function(){
                    Swal.fire('Error', 'Problema al intentar cerrar el ticket.', 'error');
                });
            }
        });
    }
</script>