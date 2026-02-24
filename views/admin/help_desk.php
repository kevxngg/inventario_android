<?php require_once 'views/layouts/header.php'; ?>

<style>
    .helpdesk-container {
        height: calc(100vh - 120px);
        display: flex;
        border-radius: 12px;
        overflow: hidden;
        background-color: #ffffff;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
    }
    
    .chat-sidebar {
        width: 350px;
        background-color: #f8fafc;
        border-right: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
    }
    
    .chat-list {
        overflow-y: auto;
        flex-grow: 1;
    }
    
    .ticket-item {
        padding: 15px;
        border-bottom: 1px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .ticket-item:hover, .ticket-item.active { background-color: #eef2f6; }
    
    .chat-main {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        background-color: #e5ddd5;
        background-image: url('https://www.transparenttextures.com/patterns/cubes.png');
    }
    
    .chat-header {
        background-color: #004b87;
        color: white;
        padding: 15px 20px;
        display: flex;
        align-items: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        z-index: 10;
    }
    
    .chat-messages {
        flex-grow: 1;
        padding: 20px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    
    .message-bubble {
        max-width: 75%;
        padding: 10px 15px;
        border-radius: 10px;
        position: relative;
        font-size: 0.95rem;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    
    .message-admin {
        background-color: #dcf8c6;
        align-self: flex-end;
        border-bottom-right-radius: 0;
    }
    
    .message-user {
        background-color: #ffffff;
        align-self: flex-start;
        border-bottom-left-radius: 0;
    }
    
    .chat-input-area {
        background-color: #f0f0f0;
        padding: 15px 20px;
        display: flex;
        gap: 10px;
        align-items: center;
    }
    
    .chat-input-area input {
        border-radius: 25px;
        padding: 10px 20px;
        border: none;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #64748b;
    }
</style>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold text-dark m-0"><i class="fa-solid fa-headset me-2 text-primary"></i> Mesa de Ayuda</h3>
            <p class="text-muted mb-0">Centro de soporte y resolución de incidencias en tiempo real.</p>
        </div>
    </div>

    <div class="helpdesk-container">
        <div class="chat-sidebar">
            <div class="p-3 border-bottom bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold text-secondary">Tickets Activos</span>
                <span class="badge bg-danger rounded-pill"><?= count($tickets) ?></span>
            </div>
            
            <div class="chat-list">
                <?php if(!empty($tickets)): ?>
                    <?php foreach($tickets as $t): ?>
                        <div class="ticket-item position-relative" onclick="openChat(<?= $t->request_unique_id ?>, '<?= addslashes($t->fullname) ?>', '<?= addslashes($t->description) ?>', this)">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <span class="fw-bold text-dark truncate-text" style="max-width: 180px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <?= $t->fullname ?>
                                </span>
                                <small class="text-muted" style="font-size: 0.7rem;"><?= date('d/m', strtotime($t->created_at)) ?></small>
                            </div>
                            <div class="small text-secondary mb-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                <?= $t->description ?>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <?php if($t->status == 'PENDIENTE'): ?>
                                        <span class="badge bg-warning text-dark px-2" style="font-size: 0.65rem;">En Revisión</span>
                                    <?php else: ?>
                                        <span class="badge bg-success px-2" style="font-size: 0.65rem;">Resuelto</span>
                                    <?php endif; ?>
                                </div>
                                <?php if($t->unread_count > 0): ?>
                                    <span class="badge bg-success rounded-circle px-2"><?= $t->unread_count ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="p-4 text-center text-muted small mt-5">
                        <i class="fa-solid fa-check-double fa-2x mb-2 opacity-50"></i><br>
                        No hay reportes de daños pendientes.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="chat-main" id="chatWindow" style="display: none;">
            <div class="chat-header">
                <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold me-3 shadow-sm" style="width: 45px; height: 45px; font-size: 1.2rem;">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="flex-grow-1">
                    <h5 class="m-0 fw-bold" id="chatUserName">Nombre del Usuario</h5>
                    <small class="text-white-50" id="chatUserStatus">Cargando estado...</small> 
                    <span class="text-white-50 mx-1">|</span>
                    <small class="text-white-50">Ticket #<span id="chatTicketId">000</span></small>
                </div>
                
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-light rounded-circle" title="Vaciar chat" onclick="clearMyChat()" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-light rounded-pill px-3" onclick="closeTicket()">
                        <i class="fa-solid fa-check me-1"></i> Cerrar Ticket
                    </button>
                </div>
            </div>

            <div class="bg-white p-3 border-bottom shadow-sm z-1" style="font-size: 0.85rem;">
                <span class="fw-bold text-danger"><i class="fa-solid fa-triangle-exclamation me-1"></i> Motivo del Reporte:</span> 
                <span id="chatIssueDesc" class="text-secondary fst-italic"></span>
            </div>

            <div class="chat-messages" id="chatMessagesBox">
                </div>

            <form id="chatForm" class="chat-input-area border-top z-1">
                <input type="hidden" id="activeRequestId">
                <button type="button" class="btn btn-light text-secondary rounded-circle"><i class="fa-solid fa-paperclip"></i></button>
                <input type="text" id="chatInputMsg" class="form-control" placeholder="Escribe una respuesta para el técnico..." required autocomplete="off">
                <button type="submit" class="btn btn-primary rounded-circle shadow-sm" style="width: 45px; height: 45px;" id="btnSendMsg">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>
        </div>

        <div class="chat-main empty-state" id="emptyStateWindow">
            <div class="text-center text-muted" style="background: rgba(255,255,255,0.8); padding: 40px; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                <i class="fa-brands fa-whatsapp fa-4x mb-3 text-secondary opacity-50"></i>
                <h4 class="fw-bold text-dark">SICOT Help Desk</h4>
                <p>Selecciona un ticket para iniciar el soporte.</p>
            </div>
        </div>

    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>

<script>
    let currentTicketId = null;

    function openChat(id, userName, description, element) {
        document.querySelectorAll('.ticket-item').forEach(el => el.classList.remove('active'));
        element.classList.add('active');

        // Al abrir, ocultamos el globo de notificaciones visualmente al instante
        let badge = element.querySelector('.bg-success.rounded-circle');
        if(badge) badge.style.display = 'none';

        document.getElementById('emptyStateWindow').style.display = 'none';
        document.getElementById('chatWindow').style.display = 'flex';

        currentTicketId = id;
        document.getElementById('activeRequestId').value = id;
        document.getElementById('chatUserName').innerText = userName;
        document.getElementById('chatTicketId').innerText = String(id).padStart(5, '0');
        document.getElementById('chatIssueDesc').innerText = '"' + description + '"';

        loadChatMessages();
    }

    function loadChatMessages() {
        if(!currentTicketId) return;
        const box = document.getElementById('chatMessagesBox');
        
        $.get('<?= base_url ?>Admin/loadChat?id=' + currentTicketId, function(res) {
            box.innerHTML = '';
            
            // Actualizar estado en línea
            if(res.user_status) {
                document.getElementById('chatUserStatus').innerText = res.user_status;
            }

            if(res.data && res.data.length > 0) {
                res.data.forEach(msg => {
                    const isMe = (msg.role === 'ADMIN');
                    const bubbleClass = isMe ? 'message-admin' : 'message-user';
                    const senderName = isMe ? 'Tú (Soporte)' : msg.fullname;
                    
                    // Lógica del Doble Check Azul
                    let checks = '';
                    if(isMe) {
                        checks = (msg.is_read == 1) 
                            ? '<i class="fa-solid fa-check-double ms-1 text-primary"></i>' // Azul (Leído)
                            : '<i class="fa-solid fa-check ms-1 text-secondary"></i>'; // Gris (Enviado)
                    }

                    box.innerHTML += `
                        <div class="message-bubble ${bubbleClass}">
                            <div class="fw-bold mb-1" style="font-size: 0.70rem; color: ${isMe ? '#004b87' : '#d33'};">${senderName}</div>
                            <div>${msg.message}</div>
                            <div class="text-end mt-1 opacity-75" style="font-size: 0.65rem;">
                                ${msg.time} ${checks}
                            </div>
                        </div>
                    `;
                });
                box.scrollTop = box.scrollHeight;
            } else {
                box.innerHTML = `<div class="text-center mt-4 text-muted small bg-white p-3 rounded mx-auto shadow-sm" style="max-width: 80%;">Ticket abierto. Escribe un mensaje para iniciar.</div>`;
            }
        }, 'json');
    }

    // Refrescar mensajes cada 10 segundos automáticamente
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

        $.post('<?= base_url ?>Admin/sendChatMessage', {request_id: currentTicketId, message: msg}, function(res) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i>';
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
            title: '¿Vaciar chat?',
            text: "Los mensajes se borrarán solo para ti. El usuario aún podrá verlos.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sí, vaciar'
        }).then((res) => {
            if(res.isConfirmed) {
                $.post('<?= base_url ?>Admin/clearChat', {request_id: currentTicketId}, function(response){
                    if(response.status === 'success'){
                        loadChatMessages(); // Recarga la caja de chat (quedará vacía)
                    }
                }, 'json');
            }
        });
    }

    function closeTicket() {
        if(!currentTicketId) return;
        Swal.fire({
            title: '¿Cerrar Ticket?',
            text: "El reporte se marcará como resuelto.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#004b87',
            confirmButtonText: 'Sí, cerrar ticket'
        }).then((res) => {
            if(res.isConfirmed) {
                $.get('<?= base_url ?>Admin/changeStatus?ajax=true&id=' + currentTicketId + '&status=RESUELTO', function(response){
                    Swal.fire('Ticket Cerrado', 'La incidencia ha sido solucionada.', 'success').then(() => location.reload());
                }, 'json');
            }
        });
    }
</script>