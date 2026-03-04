<?php require_once 'views/layouts/header.php'; ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

<style>
    /* Estilos originales */
    .inventory-card {
        background-color: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .inventory-card:hover {
        box-shadow: 0 10px 25px rgba(0, 75, 135, 0.1);
        border-color: #004b87;
        transform: translateY(-5px);
    }
    
    /* ESTILOS: Timeline del Kardex */
    .timeline { position: relative; padding: 10px 0; list-style: none; margin: 0; }
    .timeline:before { 
        content: ''; position: absolute; top: 0; bottom: 0; left: 35px; 
        width: 3px; background: #cbd5e0; border-radius: 2px; 
    }
    .timeline-item { position: relative; margin-bottom: 25px; padding-left: 75px; padding-right: 15px; }
    .timeline-icon { 
        position: absolute; left: 18px; top: 0; width: 38px; height: 38px; 
        border-radius: 50%; display: flex; align-items: center; justify-content: center; 
        color: white; border: 4px solid #fff; box-shadow: 0 0 0 2px #cbd5e0; font-size: 1.1rem; z-index: 2;
    }
    .timeline-content { 
        background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; position: relative; 
    }
    .timeline-content::before { 
        content: ''; position: absolute; left: -8px; top: 12px; width: 0; height: 0; 
        border-top: 8px solid transparent; border-bottom: 8px solid transparent; border-right: 8px solid #e2e8f0; 
    }
    .timeline-content::after { 
        content: ''; position: absolute; left: -7px; top: 12px; width: 0; height: 0; 
        border-top: 8px solid transparent; border-bottom: 8px solid transparent; border-right: 8px solid #f8fafc; 
    }
    .timeline-date { font-size: 0.75rem; color: #64748b; font-weight: 800; margin-bottom: 5px; text-transform: uppercase; }
    .timeline-title { font-size: 0.95rem; font-weight: bold; color: #1e293b; margin-bottom: 5px; }
    .timeline-body { font-size: 0.85rem; color: #475569; }

    /* ESTILOS DE DATATABLES PARA QUE ENCAJE CON TU DISEÑO */
    .dataTables_wrapper .row { align-items: center; margin-bottom: 1rem; }
    .dataTables_filter input {
        border-radius: 20px;
        border: 1px solid #ced4da;
        padding: 5px 15px;
        outline: none;
    }
    .dataTables_filter input:focus { border-color: #004b87; box-shadow: 0 0 0 0.2rem rgba(0, 75, 135, 0.25); }
    .page-item.active .page-link { background-color: #004b87; border-color: #004b87; }
    .page-link { color: #004b87; border-radius: 8px; margin: 0 3px; }
    table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control:before { background-color: #004b87; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold text-primary-dark"><i class="fa-solid fa-boxes-stacked me-2"></i>Gestión de Inventario Maestro</h2>
        <p class="text-muted mb-0">Control general de activos, maquinaria y herramientas de la compañía.</p>
    </div>
    <button class="btn btn-android shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTool">
        <i class="fa-solid fa-plus me-2"></i> Registrar Nuevo Activo
    </button>
</div>

<div class="glass-card p-4 border-0 shadow-sm overflow-hidden bg-white">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 w-100" id="toolsTable">
            <thead class="bg-light text-uppercase small text-muted">
                <tr>
                    <th class="ps-4" style="width: 80px;">Fotografía</th>
                    <th>Identificación del Activo</th>
                    <th class="text-center">Control de Stock</th>
                    <th>Estado Operativo</th>
                    <th class="text-end pe-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($herramientas) && $herramientas->num_rows > 0): ?>
                    <?php while($tool = $herramientas->fetch_object()): ?>
                    <tr>
                        <td class="ps-4 py-3">
                            <img src="<?= base_url ?>assets/img/<?= $tool->image ?>" class="rounded shadow-sm border bg-white" width="55" height="55" style="object-fit:contain; padding: 2px;">
                        </td>
                        <td>
                            <div class="fw-bold text-dark fs-6">
                                <?= htmlspecialchars($tool->name, ENT_QUOTES) ?> 
                                <span class="badge bg-light text-muted border ms-2" style="font-size: 0.65rem;">REF-<?= str_pad($tool->id, 4, "0", STR_PAD_LEFT) ?></span>
                            </div>
                            <span class="badge bg-light text-secondary border border-secondary mt-1">
                                <i class="fa-solid fa-tag me-1"></i> <?= str_replace('_', ' ', htmlspecialchars($tool->category, ENT_QUOTES)) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex flex-column align-items-center">
                                <span class="fw-bold fs-5 mb-0 <?= $tool->stock_available <= $tool->stock_min ? 'text-danger' : 'text-success' ?>">
                                    <?= $tool->stock_available ?>
                                </span>
                                <small class="text-muted fw-bold" style="font-size: 0.75rem;">DISPONIBLES DE <?= $tool->stock_total ?></small>
                            </div>
                        </td>
                        <td>
                            <?php 
                                $badgeClass = 'bg-success';
                                $icon = 'fa-check-circle';
                                $statusText = 'Disponible';

                                if($tool->status == 'AGOTADO') {
                                    $badgeClass = 'bg-secondary';
                                    $icon = 'fa-ban';
                                    $statusText = 'Sin Existencias';
                                }
                                if($tool->status == 'MANTENIMIENTO') {
                                    $badgeClass = 'bg-warning text-dark';
                                    $icon = 'fa-screwdriver-wrench';
                                    $statusText = 'Mantenimiento';
                                }
                                if($tool->status == 'EN_OBRA') {
                                    $badgeClass = 'bg-primary';
                                    $icon = 'fa-helmet-safety';
                                    $statusText = 'En Operación';
                                }
                            ?>
                            <span class="badge <?= $badgeClass ?> rounded shadow-sm px-2 py-1">
                                <i class="fa-solid <?= $icon ?> me-1"></i> <?= $statusText ?>
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex gap-2 justify-content-end">
                                <button type="button" class="btn btn-sm btn-light border text-info shadow-sm" title="Ver Kardex / Historial" onclick="openKardex(<?= $tool->id ?>)">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                </button>
                                
                                <a href="<?= base_url ?>Admin/editTool?id=<?= $tool->id ?>" class="btn btn-sm btn-light border text-primary shadow-sm" title="Modificar Registro">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <a href="<?= base_url ?>Admin/deleteTool?id=<?= $tool->id ?>" class="btn btn-sm btn-light border text-danger shadow-sm btn-delete" title="Dar de Baja" onclick="return confirm('ATENCIÓN: ¿Confirmas dar de baja este activo? Esta acción eliminará su historial del Kardex y no se puede deshacer.');">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalTool" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-file-circle-plus me-2 text-primary"></i>Ficha de Ingreso de Activo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url ?>Admin/saveTool" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-secondary">IDENTIFICACIÓN DEL ACTIVO / REFERENCIA</label>
                        <input type="text" name="name" class="form-control" placeholder="Ej: Rotomartillo Industrial Bosch GBH 2-28" required>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-secondary">CLASIFICACIÓN (CATEGORÍA)</label>
                            <select name="category" class="form-select border-secondary">
                                <option value="MAQUINARIA_PESADA">Maquinaria Pesada</option>
                                <option value="HERRAMIENTA_MANO">Herramienta de Mano</option>
                                <option value="EQUIPO_SEGURIDAD">Equipo de Seguridad Industrial</option>
                                <option value="VEHICULO">Vehículo Operativo</option>
                                <option value="OTROS">Otros Activos</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-secondary">ESTADO INICIAL DEL REGISTRO</label>
                            <select name="status" class="form-select border-secondary">
                                <option value="DISPONIBLE">Disponible (En Bodega)</option>
                                <option value="EN_OBRA">Asignado a Obra Inmediata</option>
                                <option value="MANTENIMIENTO">En Mantenimiento Preventivo</option>
                                <option value="AGOTADO">Agotado / Sin Existencias</option>
                            </select>
                        </div>
                    </div>

                    <div class="bg-light p-3 rounded border border-secondary mb-4">
                        <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-calculator me-2"></i>Parámetros de Stock</h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-bold small text-primary">CANTIDAD DE INGRESO (UNIDADES)</label>
                                <input type="number" name="stock_total" class="form-control fw-bold border-primary text-center" value="1" min="1" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small text-danger">PUNTO DE REORDEN (ALERTA MÍNIMA)</label>
                                <input type="number" name="stock_min" class="form-control fw-bold text-center" value="5" min="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold small text-secondary">SOPORTE FOTOGRÁFICO (OPCIONAL)</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted mt-1 d-block">Sube una imagen representativa del equipo para el catálogo.</small>
                    </div>

                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-outline-secondary fw-bold px-4" data-bs-dismiss="modal">Cancelar Operación</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4"><i class="fa-solid fa-cloud-arrow-up me-2"></i>Confirmar Ingreso a Sistema</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalKardex" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-dark text-white border-bottom">
                <h5 class="modal-title fw-bold text-uppercase" style="letter-spacing: 1px;"><i class="fa-solid fa-timeline me-2 text-info"></i>Kardex de Movimientos</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-white">
                
                <div class="d-flex align-items-center p-4 border-bottom bg-light">
                    <img id="kxToolImage" src="" class="rounded shadow border border-secondary me-3 bg-white" width="70" height="70" style="object-fit:contain; padding: 2px;">
                    <div>
                        <h5 id="kxToolName" class="fw-bold mb-1 text-dark">Nombre de Herramienta</h5>
                        <div class="d-flex gap-2 align-items-center mt-2">
                            <span id="kxToolCategory" class="badge bg-secondary px-2 py-1">Categoria</span>
                            <span class="badge border border-primary text-primary bg-white fw-bold px-2 py-1">Unidades Activas: <span id="kxToolStock">0</span></span>
                        </div>
                    </div>
                </div>

                <div class="p-3 bg-white custom-scroll" style="max-height: 450px; overflow-y: auto;">
                    <ul id="timelineContainer" class="timeline">
                        </ul>
                </div>

            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-dark fw-bold px-4 rounded-pill" data-bs-dismiss="modal">Cerrar Historial</button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Inicializar DataTables para el Catálogo de Herramientas
    $(document).ready(function() {
        $('#toolsTable').DataTable({
            responsive: true,
            language: {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
                "emptyTable": "El inventario maestro se encuentra vacío en este momento."
            },
            pageLength: 10, // Mostrar 10 registros por página
            lengthMenu: [ [5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"] ],
            order: [[1, "asc"]], // Ordenar por nombre alfabéticamente por defecto
            columnDefs: [
                { orderable: false, targets: [0, 4] } // Quitar orden a las columnas de foto y acciones
            ]
        });
    });

    // Función para abrir el Kardex y cargar la vida del Activo
    function openKardex(toolId) {
        // Mostrar alerta de carga
        Swal.fire({
            title: 'Consultando Kardex...',
            text: 'Extrayendo historial de movimientos de la base de datos.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        // Petición AJAX al controlador
        $.get('<?= base_url ?>Admin/getToolHistory?id=' + toolId, function(response) {
            if(response.status === 'success') {
                Swal.close();
                
                // Llenar datos de cabecera
                $('#kxToolName').text(response.tool.name);
                $('#kxToolCategory').text(response.tool.category.replace(/_/g, ' '));
                $('#kxToolStock').text(response.tool.stock_available + ' de ' + response.tool.stock_total);
                
                let imgSrc = response.tool.image ? response.tool.image : 'default.png';
                $('#kxToolImage').attr('src', '<?= base_url ?>assets/img/' + imgSrc);
                
                // Construir la línea de tiempo
                let timelineHtml = '';
                
                if(response.data.length > 0) {
                    response.data.forEach(function(mov) {
                        let icon = 'fa-circle-info';
                        let bgClass = 'bg-secondary';
                        let typeLabel = mov.movement_type ? mov.movement_type.replace(/_/g, ' ') : mov.type.replace(/_/g, ' ');
                        let badgeColor = 'bg-secondary';
                        
                        // Sistema de colores inteligente según el tipo de movimiento
                        if(typeLabel.includes('ENTRADA') || typeLabel.includes('INGRESO')) { 
                            icon = 'fa-arrow-down'; bgClass = 'bg-success'; badgeColor = 'bg-success';
                        }
                        else if(typeLabel.includes('SALIDA') || typeLabel.includes('PRESTAMO')) { 
                            icon = 'fa-helmet-safety'; bgClass = 'bg-primary'; badgeColor = 'bg-primary';
                        }
                        else if(typeLabel.includes('DEVOLUCION')) { 
                            icon = 'fa-arrow-rotate-left'; bgClass = 'bg-info text-dark'; badgeColor = 'bg-info text-dark';
                        }
                        else if(typeLabel.includes('BAJA') || typeLabel.includes('DAÑO')) { 
                            icon = 'fa-triangle-exclamation'; bgClass = 'bg-danger'; badgeColor = 'bg-danger';
                        }
                        else if(typeLabel.includes('AJUSTE')) { 
                            icon = 'fa-sliders'; bgClass = 'bg-dark'; badgeColor = 'bg-dark';
                        }
                        
                        let comments = mov.notes ? `<div class="mt-2 p-2 bg-light border rounded small text-muted"><i class="fa-solid fa-quote-left me-1 text-secondary"></i>${mov.notes}</div>` : '';
                        let user = mov.admin_name ? `<strong>${mov.admin_name}</strong> <span class="badge bg-light text-dark border ms-1">Responsable Operación</span>` : '<strong>Administrador de Sistema</strong>';

                        timelineHtml += `
                        <li class="timeline-item">
                            <div class="timeline-icon ${bgClass}"><i class="fa-solid ${icon}"></i></div>
                            <div class="timeline-content shadow-sm">
                                <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                                    <div class="timeline-date"><i class="fa-regular fa-calendar-days me-1"></i> ${mov.created_at_formatted}</div>
                                    <div class="badge ${badgeColor}">${typeLabel}</div>
                                </div>
                                <div class="timeline-title text-primary"><i class="fa-solid fa-cubes me-1"></i> Movimiento de ${mov.quantity} Unidad(es)</div>
                                <div class="timeline-body mt-2">
                                    <div class="mb-1"><i class="fa-solid fa-user-tag text-secondary me-1" style="width: 15px;"></i> ${user}</div>
                                    ${comments}
                                </div>
                            </div>
                        </li>`;
                    });
                } else {
                    timelineHtml = `
                    <div class="text-center py-5 text-muted">
                        <i class="fa-solid fa-box-open fa-3x mb-3 text-secondary opacity-50"></i>
                        <h6 class="fw-bold">Activo sin historial</h6>
                        <p class="small">No se han registrado entradas, préstamos ni devoluciones para esta herramienta todavía.</p>
                    </div>`;
                }
                
                $('#timelineContainer').html(timelineHtml);
                
                // Extraer el modal y ponerlo en el body para evitar el problema de la pantalla gris bloqueada
                document.body.appendChild(document.getElementById('modalKardex'));
                let myModal = new bootstrap.Modal(document.getElementById('modalKardex'));
                myModal.show();
                
            } else {
                Swal.fire('Atención', response.msg, 'error');
            }
        }, 'json').fail(function() {
            Swal.fire('Fallo de Conexión', 'No fue posible conectar con el sistema de auditoría.', 'error');
        });
    }
</script>