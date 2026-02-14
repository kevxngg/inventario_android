<?php require_once 'views/layouts/header.php'; ?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Historial de Reportes</h2>
            <p class="text-muted">Gestión total de auditoría y reportes.</p>
        </div>
        
        <div class="d-flex gap-2">
            <button id="btnDeleteSelected" class="btn btn-danger rounded-pill px-3 shadow-sm" style="display: none;" onclick="deleteSelected()">
                <i class="fa-solid fa-trash-can me-2"></i> Eliminar
            </button>

            <a href="<?= base_url ?>Admin/printReports" target="_blank" class="btn btn-dark rounded-pill px-3 shadow-sm">
                <i class="fa-solid fa-file-pdf me-2"></i> Generar PDF
            </a>

            <div class="bg-white p-2 rounded shadow-sm border d-flex align-items-center">
                <span class="text-muted small fw-bold">TOTAL:</span>
                <span class="fw-bold text-primary ms-2"><?= $reportes->num_rows ?></span>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase small text-muted">
                        <tr>
                            <th class="ps-4 py-3" style="width: 40px;">
                                <input type="checkbox" id="selectAll" class="form-check-input" style="cursor: pointer;">
                            </th>
                            <th>Usuario</th>
                            <th>Tipo</th>
                            <th>Detalle</th>
                            <th>Fecha</th>
                            <th class="text-end pe-4">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($reportes) && $reportes->num_rows > 0): ?>
                            <?php while($row = $reportes->fetch_object()): ?>
                            <tr id="row-<?= $row->request_unique_id ?>">
                                <td class="ps-4">
                                    <input type="checkbox" class="form-check-input row-checkbox" value="<?= $row->request_unique_id ?>">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold;">
                                            <?= strtoupper(substr($row->fullname, 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark"><?= $row->fullname ?></div>
                                            <span class="badge bg-light text-secondary border"><?= $row->role ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if($row->type == 'SOLICITUD_HERRAMIENTA'): ?>
                                        <span class="badge bg-info text-dark">Pedido</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Reporte</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <p class="mb-0 text-secondary" style="max-width: 300px;"><?= $row->description ?></p>
                                </td>
                                <td class="text-muted small">
                                    <?= date('d/m/Y', strtotime($row->created_at)) ?>
                                </td>
                                <td class="text-end pe-4">
                                    <?php if($row->status == 'PENDIENTE'): ?>
                                        <span class="badge bg-warning text-dark">⏳ Pendiente</span>
                                    <?php elseif($row->status == 'APROBADO'): ?>
                                        <span class="badge bg-success">✅ Aprobado</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">❌ Rechazado</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>

<script>
    // --- LÓGICA DE SELECCIÓN Y BORRADO (ADMIN) ---
    document.addEventListener("DOMContentLoaded", function() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.row-checkbox');
        const btnDelete = document.getElementById('btnDeleteSelected');

        // Función para actualizar estado del botón
        function updateButton() {
            const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
            if(checkedCount > 0) {
                btnDelete.style.display = 'inline-block';
                btnDelete.innerHTML = `<i class="fa-solid fa-trash-can me-2"></i> Eliminar (${checkedCount})`;
            } else {
                btnDelete.style.display = 'none';
            }
        }

        // Evento Select All
        if(selectAll){
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = selectAll.checked);
                updateButton();
            });
        }

        // Eventos individuales
        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateButton);
        });
    });

    // Función Borrar (AJAX) - Conecta con Admin/deleteReports
    function deleteSelected() {
        const checked = document.querySelectorAll('.row-checkbox:checked');
        if(checked.length === 0) return;

        let ids = [];
        checked.forEach(cb => ids.push(cb.value));

        Swal.fire({
            title: '¿Eliminar ' + ids.length + ' registros?',
            text: "Esta acción borrará los datos permanentemente de la base de datos.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sí, borrar todo',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('<?= base_url ?>Admin/deleteReports', {ids: ids}, function(response) {
                    try {
                        const data = JSON.parse(response);
                        if(data.status === 'success') {
                            Swal.fire('Eliminado', data.msg, 'success').then(() => {
                                location.reload(); // Recargar para ver cambios
                            });
                        } else {
                            Swal.fire('Error', data.msg, 'error');
                        }
                    } catch(e) { console.error(e); }
                });
            }
        });
    }
</script>