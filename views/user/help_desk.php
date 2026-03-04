<?php require_once 'views/layouts/header.php'; ?>

<style>
    /* ==============================================
       ESTILOS INDUSTRIALES - TICKETS DE USUARIO
       ============================================== */
    .helpdesk-container {
        height: calc(100vh - 120px);
        display: flex;
        border-radius: 8px; /* Menos redondeado para aspecto industrial */
        overflow: hidden;
        background-color: var(--white);
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        border: 2px solid var(--panel-dark); /* Borde rígido */
    }
    
    /* Panel Lateral (Lista de Tickets) */
    .chat-sidebar {
        width: 350px;
        background-color: var(--bg-app);
        border-right: 2px solid var(--border-color);
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
    
    .chat-list { overflow-y: auto; flex-grow: 1; }
    
    .ticket-item {
        padding: 15px;
        border-bottom: 1px solid var(--border-color);
        cursor: pointer;
        transition: all 0.2s ease;
        border-left: 4px solid transparent;
        background-color: var(--white);
    }
    
    .ticket-item:hover, .ticket-item.active { 
        background-color: rgba(30, 41, 59, 0.05); 
        border-left: 4px solid var(--panel-dark); 
    }
    
    /* Área Principal (Consola) */
    .chat-main {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        background-color: #f8fafc; /* Gris técnico limpio, sin texturas informales */
    }
    
    .chat-header {
        background-color: var(--white);
        border-bottom: 2px solid var(--border-color);
        color: var(--panel-darker);
        padding: 15px 20px;
        display: flex;
        align-items: center;
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
    
    /* Diseño de Ticket / Terminal */
    .message-bubble {
        max-width: 85%;
        padding: 12px 15px;
        border-radius: 4px; /* Bordes cuadrados */
        position: relative;
        font-size: 0.95rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        border: 1px solid var(--border-color);
    }
    
    .message-me {
        background-color: var(--white);
        align-self: flex-end;
        border-left: 4px solid var(--safety-orange); /* Color del Operario */
    }
    
    .message-admin {
        background-color: rgba(30, 41, 59, 0.05);
        align-self: flex-start;
        border-left: 4px solid var(--panel-dark); /* Color de la Administración */
    }
    
    /* Input de datos */
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
        border: 2px solid var(--panel-dark); /* Borde sólido */
        font-family: monospace;
        font-weight: bold;
    }
    .chat-input-area input:focus { box-shadow: inset 0 0 5px rgba(0,0,0,0.1); }
    
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
    }
</style>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold" style="color: var(--panel-darker);"><i class="fa-solid fa-headset me-2" style="color: var(--safety-orange);"></i> Mis Tickets de Soporte</h3>
            <p class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.85rem; letter-spacing: 1px;">Comunicación cifrada con la base central.</p>
        </div>
    </div>

    <div class="helpdesk-container">
        
        <div class="chat-sidebar">
            <ul class="nav nav-tabs nav-tickets" id="ticketTabsUser" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-user-pendientes" data-bs-toggle="tab" data-bs-target="#pane-user-pendientes" type="button" role="tab">En Proceso</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-user-resueltos" data-bs-toggle="tab" data-bs-target="#pane-user-resueltos" type="button" role="tab">Cerrados</button>
                </li>
                <li class="nav-item ms-auto" role="presentation">
                    <div class="text-white fw-bold d-flex align-items-center h-100 pe-2" style="font-size: 0.8rem;">
                        Total: <?= count($tickets) ?>
                    </div>
                </li>
            </ul>
            
            <div class="tab-content chat-list custom-scroll" id="ticketTabsUserContent">
                
                <div class="tab-pane fade show active" id="pane-user-pendientes" role="tabpanel">
                    <?php 
                        $hasPendientes = false;
                        if(!empty($tickets)): 
                            foreach($tickets as $t): 
                                if($t->status == 'PENDIENTE'):
                                    $hasPendientes = true;
                                    $displayUnread = $t->unread_count > 9 ? '9+' : $t->unread_count;
                    ?>
                                    <div class="ticket-item position-relative" onclick="openChat(<?= $t->request_unique_id ?>, '<?= addslashes($t->description) ?>', this)">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <span class="fw-bold text-dark text-uppercase" style="font-size: 0.85rem;">
                                                Ticket #<?= str_pad($t->request_unique_id, 4, '0', STR_PAD_LEFT) ?>
                                            </span>
                                            <small class="text-muted font-monospace fw-bold" style="font-size: 0.7rem;">
                                                <?= date('d/M H:i', strtotime($t->last_activity ?? $t->created_at)) ?>
                                            </small>
                                        </div>
                                        <div class="small text-secondary mb-2 fw-bold font-monospace" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            > <?= $t->description ?>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge text-dark px-2 shadow-sm" style="background-color: #eab308; font-size: 0.65rem;">En Proceso</span>
                                            <?php if($t->unread_count > 0): ?>
                                                <span class="badge rounded px-2" style="background-color: var(--safety-orange);"><?= $displayUnread ?> Msg</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                    <?php 
                                endif;
                            endforeach; 
                        endif; 
                        if(!$hasPendientes):
                    ?>
                        <div class="p-4 text-center text-muted small mt-5">
                            <i class="fa-solid fa-clipboard-check fa-3x mb-3 opacity-25"></i><br>
                            <span class="fw-bold text-uppercase">Bandeja Limpia.</span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="tab-pane fade" id="pane-user-resueltos" role="tabpanel">
                    <?php 
                        $hasCerrados = false;
                        if(!empty($tickets)): 
                            foreach($tickets as $t): 
                                if($t->status != 'PENDIENTE'):
                                    $hasCerrados = true;
                                    $displayUnread = $t->unread_count > 9 ? '9+' : $t->unread_count;
                                    $badgeColor = ($t->status == 'RESUELTO' || $t->status == 'APROBADO') ? 'bg-success' : 'bg-danger';
                    ?>
                                    <div class="ticket-item position-relative" onclick="openChat(<?= $t->request_unique_id ?>, '<?= addslashes($t->description) ?>', this)">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <span class="fw-bold text-dark text-uppercase" style="font-size: 0.85rem;">
                                                Ticket #<?= str_pad($t->request_unique_id, 4, '0', STR_PAD_LEFT) ?>
                                            </span>
                                            <small class="text-muted font-monospace fw-bold" style="font-size: 0.7rem;">
                                                <?= date('d/M H:i', strtotime($t->last_activity ?? $t->created_at)) ?>
                                            </small>
                                        </div>
                                        <div class="small text-secondary mb-2 fw-bold font-monospace" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            > <?= $t->description ?>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge <?= $badgeColor ?> px-2 shadow-sm" style="font-size: 0.65rem;"><?= $t->status ?></span>
                                            <?php if($t->unread_count > 0): ?>
                                                <span class="badge rounded px-2" style="background-color: var(--safety-orange);"><?= $displayUnread ?> Msg</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                    <?php 
                                endif;
                            endforeach; 
                        endif; 
                        if(!$hasCerrados):
                    ?>
                        <div class="p-4 text-center text-muted small mt-5">
                            <i class="fa-solid fa-folder-open fa-3x mb-3 opacity-25"></i><br>
                            <span class="fw-bold text-uppercase">No hay historial cerrado.</span>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>

        <div class="chat-main" id="chatWindow" style="display: none;">
            <div class="chat-header">
                <div class="rounded d-flex align-items-center justify-content-center fw-bold me-3 shadow-sm" style="width: 45px; height: 45px; font-size: 1.2rem; background-color: var(--panel-dark); color: white;">
                    <i class="fa-solid fa-building-shield"></i>
                </div>
                <div class="flex-grow-1">
                    <h5 class="m-0 fw-bold text-uppercase" style="letter-spacing: 1px;">Soporte Central</h5>
                    <small class="text-muted font-monospace fw-bold" id="chatAdminStatus">Estableciendo conexión...</small>
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-danger fw-bold rounded-pill px-3" title="Purgar chat local" onclick="clearMyChat()">
                        <i class="fa-solid fa-eraser me-1"></i> Purgar Datos
                    </button>
                </div>
            </div>

            <div class="p-3 border-bottom shadow-sm z-1" style="background-color: var(--bg-app); font-size: 0.85rem; border-left: 4px solid var(--safety-orange);">
                <span class="fw-bold text-dark text-uppercase"><i class="fa-solid fa-hashtag me-1"></i> Detalle Inicial:</span> 
                <span id="chatIssueDesc" class="text-secondary fw-bold font-monospace"></span>
            </div>

            <div class="chat-messages custom-scroll" id="chatMessagesBox">
                </div>

            <form id="chatForm" class="chat-input-area border-top z-1">
                <input type="text" id="chatInputMsg" class="form-control" placeholder="Añadir datos a la bitácora..." required autocomplete="off">
                <button type="submit" class="btn fw-bold shadow-sm px-4" style="background-color: var(--safety-orange); color: white; border-radius: 4px;" id="btnSendMsg">
                    <i class="fa-solid fa-share me-1"></i> Reportar
                </button>
            </form>
        </div>

        <div class="chat-main empty-state" id="emptyStateWindow">
            <div class="text-center" style="background: var(--white); padding: 40px; border-radius: 8px; border: 2px dashed var(--border-color);">
                <i class="fa-solid fa-satellite-dish fa-4x mb-3" style="color: var(--panel-dark);"></i>
                <h4 class="fw-bold text-uppercase" style="color: var(--panel-darker); letter-spacing: 1px;">Transmisión Segura</h4>
                <p class="text-muted fw-bold small">Seleccione un reporte para visualizar las órdenes de la central.</p>
            </div>
        </div>

    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let currentTicketId = null;

    function openChat(id, description, element) {
        document.querySelectorAll('.ticket-item').forEach(el => el.classList.remove('active'));
        element.classList.add('active');

        // Ocultar globo si existe (al abrir el chat)
        let badge = element.querySelector('.badge[style*="background-color: var(--safety-orange)"]');
        if(badge) badge.style.display = 'none';

        document.getElementById('emptyStateWindow').style.display = 'none';
        document.getElementById('chatWindow').style.display = 'flex';

        currentTicketId = id;
        document.getElementById('chatIssueDesc').innerText = '> ' + description;

        loadChatMessages();
    }

    function loadChatMessages() {
        if(!currentTicketId) return;
        const box = document.getElementById('chatMessagesBox');
        
        $.get('<?= base_url ?>User/loadChat?id=' + currentTicketId, function(res) {
            box.innerHTML = '';
            
            if(res.admin_status) {
                document.getElementById('chatAdminStatus').innerText = "ESTADO CENTRAL: " + res.admin_status.toUpperCase();
            }

            if(res.data && res.data.length > 0) {
                res.data.forEach(msg => {
                    const isMe = (msg.sender_id == '<?= $_SESSION["identity"]->id ?>');
                    const bubbleClass = isMe ? 'message-me' : 'message-admin';
                    const senderName = isMe ? 'MI REPORTE' : 'ADMINISTRACIÓN';
                    const iconColor = isMe ? 'var(--safety-orange)' : 'var(--panel-dark)';

                    let checks = '';
                    if(isMe) {
                        checks = (msg.is_read == 1) 
                            ? '<i class="fa-solid fa-check-double ms-1" style="color: #10b981;"></i>'
                            : '<i class="fa-solid fa-check ms-1 text-secondary"></i>';
                    }

                    box.innerHTML += `
                        <div class="message-bubble ${bubbleClass}">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-1 mb-2">
                                <div class="fw-bold text-uppercase" style="font-size: 0.70rem; color: ${iconColor}; letter-spacing: 0.5px;">
                                    <i class="fa-solid fa-terminal me-1"></i> ${senderName}
                                </div>
                                <div class="text-muted font-monospace fw-bold" style="font-size: 0.7rem;">${msg.time} ${checks}</div>
                            </div>
                            <div class="font-monospace text-dark fw-bold">> ${msg.message}</div>
                        </div>
                    `;
                });
                box.scrollTop = box.scrollHeight;
            } else {
                box.innerHTML = `<div class="text-center mt-4"><span class="badge bg-light text-dark border p-2 font-monospace shadow-sm">CONEXIÓN ESTABLECIDA. ESPERANDO DATOS.</span></div>`;
            }
        }, 'json');
    }

    setInterval(() => {
        if(currentTicketId) loadChatMessages();
    }, 10000);

    document.getElementById('chatForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const input = document.getElementById('chatInputMsg');
        const msg = input.value.trim();
        const btn = document.getElementById('btnSendMsg');

        if(!msg) return;

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

        $.post('<?= base_url ?>User/sendChatMessage', {request_id: currentTicketId, message: msg}, function(res) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-share me-1"></i> Reportar';
            if(res.status === 'success') {
                input.value = '';
                loadChatMessages();
            } else {
                Swal.fire('Error de Transmisión', res.msg, 'error');
            }
        }, 'json');
    });

    function clearMyChat() {
        if(!currentTicketId) return;
        Swal.fire({
            title: '¿Purgar Registro Local?',
            text: "Los datos se borrarán de este dispositivo. La central conservará su copia.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, purgar',
            cancelButtonText: 'Cancelar'
        }).then((res) => {
            if(res.isConfirmed) {
                $.post('<?= base_url ?>User/clearChat', {request_id: currentTicketId}, function(response){
                    if(response.status === 'success'){
                        loadChatMessages(); 
                    }
                }, 'json');
            }
        });
    }
</script>