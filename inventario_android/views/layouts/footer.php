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
    document.addEventListener("DOMContentLoaded", function() {
        const bellBtn = document.getElementById('bellBtn');
        const notifList = document.getElementById('notification-list');
        const notifDot = document.getElementById('notif-dot');

        function loadNotifications() {
            if(!bellBtn) return;

            // Agregamos un timestamp para evitar caché en la llamada AJAX
            fetch('<?= base_url ?>Admin/getNotifications?t=' + new Date().getTime())
                .then(response => response.json())
                .then(data => {
                    notifList.innerHTML = ''; 
                    
                    if (data.length > 0) {
                        let hayPendientes = data.some(item => item.status === 'PENDIENTE');
                        if(hayPendientes) notifDot.style.display = 'block';
                        else notifDot.style.display = 'none';
                        
                        const ultimas = data.slice(0, 5);

                        ultimas.forEach(item => {
                            let actionHtml = '';

                            if(item.is_admin === true){
                                // ADMIN: Botones
                                actionHtml = `
                                <div class="d-flex flex-column gap-1">
                                    <a href="<?= base_url ?>Admin/handleRequest?id=${item.id}&status=APROBADO" class="btn btn-sm btn-success py-0 px-2" style="font-size:10px;"><i class="fa-solid fa-check"></i></a>
                                    <a href="<?= base_url ?>Admin/handleRequest?id=${item.id}&status=RECHAZADO" class="btn btn-sm btn-danger py-0 px-2" style="font-size:10px;"><i class="fa-solid fa-xmark"></i></a>
                                </div>`;
                            } else {
                                // USER: Badge
                                let badgeClass = 'bg-warning text-dark';
                                if(item.status == 'APROBADO') badgeClass = 'bg-success';
                                if(item.status == 'RECHAZADO') badgeClass = 'bg-danger';
                                actionHtml = `<span class="badge ${badgeClass} x-small" style="font-size:0.7rem">${item.status}</span>`;
                            }

                            const html = `
                            <li class="p-3 border-bottom hover-bg">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong class="text-dark small">${item.fullname}</strong>
                                        <p class="text-muted x-small mb-1">Equipo: <b>${item.tool_name}</b></p>
                                        <span class="text-muted x-small">${item.request_date}</span>
                                    </div>
                                    ${actionHtml}
                                </div>
                            </li>`;
                            notifList.innerHTML += html;
                        });
                    } else {
                        notifDot.style.display = 'none';
                        notifList.innerHTML = '<li class="p-3 text-center text-muted small">Sin novedades</li>';
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        if(bellBtn) {
            loadNotifications();
            bellBtn.addEventListener('click', loadNotifications);
        }
    });
    </script>
</body>
</html>