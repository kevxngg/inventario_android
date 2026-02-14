</div> </div> </div> 

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?= base_url ?>assets/js/animations.js"></script>
    <script src="<?= base_url ?>assets/js/validations.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        var el = document.getElementById("wrapper");
        var toggleButton = document.getElementById("menu-toggle");
        if(toggleButton && el){
            toggleButton.onclick = function () { el.classList.toggle("toggled"); };
        }
        document.addEventListener("DOMContentLoaded", function() {
            document.body.classList.add('fade-in-active');
        });
    </script>

    <script>
    const BASE_URL = '<?= base_url ?>';

    // Procesar aprobación/rechazo desde la notificación
    function processRequest(id, status) {
        if(!id || id === 'undefined') return;

        const Toast = Swal.mixin({
            toast: true, position: 'top-end', showConfirmButton: false, timer: 2000
        });

        // Llamada AJAX
        fetch(BASE_URL + 'Admin/handleRequest?ajax=1&id=' + id + '&status=' + status)
            .then(response => response.json()) // Esperamos JSON directo
            .then(data => {
                loadNotifications(); // Recargar contador al procesar
                if(data.status === 'success'){
                    if(status === 'APROBADO'){
                        Toast.fire({ icon: 'success', title: 'Solicitud Aprobada' });
                    } else {
                        Toast.fire({ icon: 'info', title: 'Solicitud Rechazada' });
                    }
                } else {
                    Toast.fire({ icon: 'error', title: data.msg });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Toast.fire({ icon: 'error', title: 'Error de conexión' });
            });
    }

    // CARGAR NOTIFICACIONES Y ACTUALIZAR CONTADOR (9+)
    function loadNotifications() {
        const notifList = document.getElementById('notification-list');
        const notifBadge = document.getElementById('notif-count');
        
        if(!notifList) return;

        fetch(BASE_URL + 'Admin/getNotifications?t=' + new Date().getTime())
            .then(response => response.json())
            .then(data => {
                notifList.innerHTML = ''; 
                
                if (data.length > 0) {
                    // 1. LÓGICA DEL CONTADOR
                    // Filtramos solo las que son PENDIENTES para el número rojo
                    let pendingCount = data.filter(item => item.status === 'PENDIENTE').length;

                    if (pendingCount > 0) {
                        notifBadge.style.display = 'inline-block';
                        // Si hay más de 9, ponemos 9+
                        notifBadge.innerText = pendingCount > 9 ? '9+' : pendingCount;
                    } else {
                        notifBadge.style.display = 'none';
                    }
                    
                    // 2. RENDERIZAR LISTA (Solo las últimas 5)
                    const ultimas = data.slice(0, 5);
                    
                    ultimas.forEach(item => {
                        let actionHtml = '';
                        
                        // Si soy ADMIN y está pendiente, muestro botones
                        if(item.is_admin === true && item.status === 'PENDIENTE'){
                            actionHtml = `
                            <div class="d-flex gap-2 mt-2 justify-content-end">
                                <button type="button" onclick="event.stopPropagation(); processRequest(${item.id}, 'APROBADO')" class="btn btn-sm btn-success rounded-pill px-3 py-1" style="font-size:11px;"><i class="fa-solid fa-check"></i> Aprobar</button>
                                <button type="button" onclick="event.stopPropagation(); processRequest(${item.id}, 'RECHAZADO')" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1" style="font-size:11px;"><i class="fa-solid fa-xmark"></i></button>
                            </div>`;
                        } else {
                            // Si soy usuario o ya se procesó
                            let badgeClass = item.status == 'APROBADO' ? 'bg-success' : (item.status == 'RECHAZADO' ? 'bg-danger' : 'bg-warning text-dark');
                            actionHtml = `<div class="mt-1 text-end"><span class="badge ${badgeClass} x-small" style="font-size:0.7rem">${item.status}</span></div>`;
                        }

                        // Icono según tipo
                        let icon = item.type === 'REPORTE_DAÑO' ? '<i class="fa-solid fa-triangle-exclamation text-danger me-2"></i>' : '<i class="fa-solid fa-screwdriver-wrench text-primary me-2"></i>';

                        const html = `
                        <li class="p-3 border-bottom hover-bg" style="cursor:default;">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong class="text-dark small d-block">${icon} ${item.fullname}</strong>
                                    <p class="text-muted x-small mb-1 text-truncate" style="max-width: 180px;">${item.tool_name}</p>
                                </div>
                                <span class="text-muted x-small text-nowrap">${item.request_date}</span>
                            </div>
                            ${actionHtml}
                        </li>`;
                        notifList.innerHTML += html;
                    });

                    // Botón de "Ver todas" si hay muchas
                    if(data.length > 5){
                        notifList.innerHTML += `<li class="p-2 text-center bg-light"><a href="${BASE_URL}Admin/reports" class="text-decoration-none small fw-bold text-primary">Ver todo el historial</a></li>`;
                    }

                } else {
                    notifBadge.style.display = 'none';
                    notifList.innerHTML = `
                        <li class="p-4 text-center text-muted">
                            <i class="fa-regular fa-bell-slash fa-2x mb-2 text-secondary opacity-50"></i>
                            <p class="small m-0">No tienes notificaciones nuevas</p>
                        </li>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                notifList.innerHTML = '<li class="p-3 text-center text-danger small">Error de conexión</li>';
            });
    }

    document.addEventListener("DOMContentLoaded", function() {
        const bellBtn = document.getElementById('bellBtn');
        // Cargar al inicio
        loadNotifications();
        
        // Recargar cada 30 segundos automáticamente (Polling)
        setInterval(loadNotifications, 30000);

        if(bellBtn) {
            bellBtn.addEventListener('click', loadNotifications);
        }
    });
    </script>
</body>
</html>