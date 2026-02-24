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
    
    .chat-list { overflow-y: auto; flex-grow: 1; }
    
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
        background-color: #0d6efd; 
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
    
    .message-me {
        background-color: #dcf8c6;
        align-self: flex-end;
        border-bottom-right-radius: 0;
    }
    
    .message-admin {
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
            <h3 class="fw-bold text-dark m-0"><i class="fa-solid fa-headset me-2 text-primary"></i> Centro de Soporte</h3>
            <p class="text-muted mb-0">Comunícate directamente con la central administrativa.</p>
        </div>
    </div>

    <div class="helpdesk-container">
        <div class="chat-sidebar">
            <div class="p-3 border-bottom bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold text-secondary">Mis Reportes</span>
                <span class="badge bg-primary rounded-pill"><?= count($tickets) ?></span>
            </div>
            
            <div class="chat-list">
                <?php if(!empty($tickets)): ?>
                    <?php foreach($tickets as $t): ?>
                        <div class="ticket-item position-relative" onclick="openChat(<?= $t->request_unique_id ?>, '<?= addslashes($t->description) ?>', this)">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <span class="fw-bold text-dark">
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
                                        <span class="badge bg-warning text-dark px-2" style="font-size: 0.65rem;">Esperando Respuesta</span>
                                    <?php else: ?>
                                        <span class="badge bg-success px-2" style="font-size: 0.65rem;">Ticket Resuelto</span>
                                    <?php endif; ?>
                                </div>
                                <?php if($t->unread_count > 0): ?>
                                    <span class="badge bg-primary rounded-circle px-2"><?= $t->unread_count ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="p-4 text-center text-muted small mt-5">
                        <i class="fa-solid fa-face-smile fa-2x mb-2 opacity-50"></i><br>
                        No tienes reportes de daños o incidencias activos.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="chat-main" id="chatWindow" style="display: none;">
            <div class="chat-header">
                <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold me-3 shadow-sm" style="width: 45px; height: 45px; font-size: 1.2rem;">
                    <i class="fa-solid fa-building-shield"></i>
                </div>
                <div class="flex-grow-1">
                    <h5 class="m-0 fw-bold">Administración Central</h5>
                    <small class="text-white-50" id="chatAdminStatus">Soporte Técnico en Línea</small>
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-light rounded-circle" title="Vaciar chat" onclick="clearMyChat()" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            </div>

            <div class="bg-white p-3 border-bottom shadow-sm z-1" style="font-size: 0.85rem;">
                <span class="fw-bold text-danger"><i class="fa-solid fa-triangle-exclamation me-1"></i> Mi Reporte Original:</span> 
                <span id="chatIssueDesc" class="text-secondary fst-italic"></span>
            </div>

            <div class="chat-messages" id="chatMessagesBox">
            </div>

            <form id="chatForm" class="chat-input-area border-top z-1">
                <input type="text" id="chatInputMsg" class="form-control" placeholder="Escribe un mensaje al administrador..." required autocomplete="off">
                <button type="submit" class="btn btn-primary rounded-circle shadow-sm" style="width: 45px; height: 45px;" id="btnSendMsg">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>
        </div>

        <div class="chat-main empty-state" id="emptyStateWindow">
            <div class="text-center text-muted" style="background: rgba(255,255,255,0.8); padding: 40px; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                <i class="fa-solid fa-headset fa-4x mb-3 text-primary opacity-50"></i>
                <h4 class="fw-bold text-dark">Centro de Soporte</h4>
                <p>Selecciona un reporte de la lista para ver las respuestas de administración.</p>
            </div>
        </div>

    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>

<script>
    let currentTicketId = null;

    function openChat(id, description, element) {
        document.querySelectorAll('.ticket-item').forEach(el => el.classList.remove('active'));
        element.classList.add('active');

        let badge = element.querySelector('.bg-primary.rounded-circle');
        if(badge) badge.style.display = 'none';

        document.getElementById('emptyStateWindow').style.display = 'none';
        document.getElementById('chatWindow').style.display = 'flex';

        currentTicketId = id;
        document.getElementById('chatIssueDesc').innerText = '"' + description + '"';

        loadChatMessages();
    }

    function loadChatMessages() {
        if(!currentTicketId) return;
        const box = document.getElementById('chatMessagesBox');
        
        $.get('<?= base_url ?>User/loadChat?id=' + currentTicketId, function(res) {
            box.innerHTML = '';
            
            if(res.admin_status) {
                document.getElementById('chatAdminStatus').innerText = res.admin_status;
            }

            if(res.data && res.data.length > 0) {
                res.data.forEach(msg => {
                    const isMe = (msg.sender_id == '<?= $_SESSION["identity"]->id ?>');
                    const bubbleClass = isMe ? 'message-me' : 'message-admin';
                    const senderName = isMe ? 'Yo' : 'Administrador';

                    let checks = '';
                    if(isMe) {
                        checks = (msg.is_read == 1) 
                            ? '<i class="fa-solid fa-check-double ms-1 text-primary"></i>'
                            : '<i class="fa-solid fa-check ms-1 text-secondary"></i>';
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
                box.innerHTML = `<div class="text-center mt-4 text-muted small bg-white p-3 rounded mx-auto shadow-sm" style="max-width: 80%;">El ticket está abierto. Puedes enviar un mensaje a la central.</div>`;
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
            text: "Los mensajes se borrarán de tu dispositivo.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sí, vaciar'
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