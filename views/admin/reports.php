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

<style>
    /* ==============================================
       ESTILOS INDUSTRIALES - LOGÍSTICA Y ACTAS
       ============================================== */
    .nav-tabs .nav-link {
        color: var(--steel-gray);
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
        border-bottom: 3px solid transparent;
        border-radius: 0;
        padding: 15px 20px;
    }
    .nav-tabs .nav-link:hover { color: var(--panel-darker); border-color: #e2e8f0; }
    .nav-tabs .nav-link.active {
        color: var(--safety-orange);
        background-color: transparent;
        border-color: var(--safety-orange);
    }
    
    .table thead { background-color: var(--panel-dark) !important; color: white !important; }
    .table thead th { border-bottom: none; font-weight: 600; letter-spacing: 1px; }

    /* Sub-pestañas de filtrado (Pills) */
    .nav-pills .nav-link {
        color: var(--panel-dark);
        font-weight: bold;
        font-size: 0.8rem;
        padding: 8px 16px;
        border: 1px solid var(--border-color);
        margin-right: 10px;
        border-radius: 20px;
    }
    .nav-pills .nav-link.active {
        background-color: var(--panel-dark);
        color: white;
        border-color: var(--panel-dark);
    }
</style>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold" style="color: var(--panel-darker);"><i class="fa-solid fa-clipboard-check me-2" style="color: var(--safety-orange);"></i>Gestión de Actas y Bodega</h2>
            <p class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.85rem; letter-spacing: 1px;">Control de retornos físicos y documentos legales de despacho.</p>
        </div>
        
        <div class="d-flex gap-2">
            <button id="btnDeleteSelected" class="btn btn-danger rounded fw-bold px-4 shadow-sm text-uppercase" style="display: none; letter-spacing: 1px;" onclick="deleteSelected()">
                <i class="fa-solid fa-trash-can me-2"></i> Purgar Historial
            </button>
        </div>
    </div>

    <ul class="nav nav-tabs mb-4 border-bottom" id="auditoriaTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="checkin-tab" data-bs-toggle="tab" data-bs-target="#checkin" type="button" role="tab">
                <i class="fa-solid fa-right-left me-2"></i>Retorno Físico (Check-In)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
                <i class="fa-solid fa-folder-open me-2"></i>Expedientes y Actas
            </button>
        </li>
    </ul>

    <div class="tab-content" id="auditoriaTabsContent">
        
        <div class="tab-pane fade show active" id="checkin" role="tabpanel">
            <div class="card border border-secondary shadow-sm rounded overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 bg-white">
                            <thead class="text-uppercase small">
                                <tr>
                                    <th class="ps-4 py-3">ID Remisión</th>
                                    <th>Activo Físico</th>
                                    <th>Funcionario Responsable</th>
                                    <th>Locación Asignada</th>
                                    <th>Volumen</th>
                                    <th class="text-end pe-4">Acción Requerida</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(isset($activosEnObra) && $activosEnObra->num_rows > 0): ?>
                                    <?php while($activo = $activosEnObra->fetch_object()): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-secondary font-monospace">ASN-<?= str_pad($activo->id, 5, "0", STR_PAD_LEFT) ?></td>
                                        <td>
                                            <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($activo->tool_name, ENT_QUOTES) ?></div>
                                            <small class="text-muted fw-bold">Despachado: <?= date('d/m/Y', strtotime($activo->assigned_at)) ?></small>
                                        </td>
                                        <td><div class="d-flex align-items-center"><i class="fa-solid fa-user-helmet me-2" style="color: var(--safety-orange);"></i><span class="fw-bold text-dark"><?= htmlspecialchars($activo->fullname, ENT_QUOTES) ?></span></div></td>
                                        <td><span class="badge bg-light text-dark border border-secondary fw-bold shadow-sm"><i class="fa-solid fa-location-dot me-1"></i> <?= htmlspecialchars($activo->project_name ?? 'USO GENERAL', ENT_QUOTES) ?></span></td>
                                        <td><span class="badge" style="background-color: var(--panel-dark); font-size: 0.85rem;"><?= $activo->quantity ?> Uds.</span></td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-sm text-white shadow-sm fw-bold px-3 text-uppercase" style="background-color: var(--safety-orange); letter-spacing: 0.5px;" onclick="processCheckIn(<?= $activo->id ?>, '<?= htmlspecialchars(addslashes($activo->tool_name), ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($activo->fullname), ENT_QUOTES) ?>')">
                                                <i class="fa-solid fa-boxes-packing me-1"></i> Recibir Activo
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="text-center py-5 text-muted bg-light"><i class="fa-solid fa-box-open fa-3x mb-3 opacity-25"></i><p class="mb-0 fw-bold text-uppercase" style="letter-spacing: 1px;">Sin activos en terreno para retornar.</p></td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="history" role="tabpanel">
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <ul class="nav nav-pills" id="filterPills" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-filter="all" onclick="filterTable('all')">Todos</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-filter="despacho" onclick="filterTable('despacho')">Solo Despachos</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-filter="falla" onclick="filterTable('falla')">Solo Fallas/Incidencias</button>
                    </li>
                </ul>
            </div>

            <div class="card border border-secondary shadow-sm rounded overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 bg-white" id="historyTable">
                            <thead class="text-uppercase small">
                                <tr>
                                    <th class="ps-4 py-3" style="width: 40px;"><input type="checkbox" id="selectAll" class="form-check-input border-secondary" style="cursor: pointer;"></th>
                                    <th>Solicitante</th>
                                    <th>Tipo / Clasificación</th>
                                    <th>Detalle del Expediente</th>
                                    <th>Fecha de Emisión</th>
                                    <th class="text-end pe-4">Estatus Legal/Logístico</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(isset($reportes) && $reportes->num_rows > 0): ?>
                                    <?php while($row = $reportes->fetch_object()): ?>
                                    
                                    <?php 
                                        // Clasificar fila para el filtro JS
                                        $filterClass = ($row->type == 'SOLICITUD_HERRAMIENTA') ? 'row-despacho' : 'row-falla'; 
                                        $safe_desc = str_replace(["\r", "\n"], " ", htmlspecialchars($row->description, ENT_QUOTES));
                                    ?>
                                    
                                    <tr class="history-row <?= $filterClass ?>" id="row-<?= $row->request_unique_id ?>">
                                        <td class="ps-4"><input type="checkbox" class="form-check-input border-secondary row-checkbox" value="<?= $row->request_unique_id ?>"></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded text-white d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 35px; height: 35px; background-color: var(--panel-dark);">
                                                    <i class="fa-solid fa-user"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark" style="font-size: 0.9rem; line-height: 1.1;"><?= htmlspecialchars($row->fullname, ENT_QUOTES) ?></div>
                                                    <span class="badge bg-light text-secondary border mt-1" style="font-size: 0.6rem;"><?= $row->role ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if($row->type == 'SOLICITUD_HERRAMIENTA'): ?>
                                                <span class="badge bg-light text-dark border shadow-sm px-2"><i class="fa-solid fa-truck-ramp-box me-1" style="color: var(--safety-orange);"></i> Despacho</span>
                                            <?php else: ?>
                                                <span class="badge border border-danger text-danger bg-light shadow-sm px-2"><i class="fa-solid fa-triangle-exclamation me-1"></i> Falla / Incidencia</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <p class="mb-0 text-secondary fw-bold font-monospace" style="max-width: 250px; font-size: 0.8rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($row->description, ENT_QUOTES) ?></p>
                                        </td>
                                        <td class="text-muted fw-bold font-monospace" style="font-size: 0.8rem;"><?= date('d/M/Y', strtotime($row->created_at)) ?></td>
                                        <td class="text-end pe-4">
                                            
                                            <?php if($row->type == 'REPORTE_DAÑO' || strpos($row->type, 'REPORTE') !== false): ?>
                                                <div class="d-flex flex-column align-items-end gap-2">
                                                    <?php if($row->status == 'RESUELTO'): ?>
                                                        <span class="badge shadow-sm px-2" style="background-color: var(--panel-dark);"><i class="fa-solid fa-lock me-1"></i> CASO CERRADO</span>
                                                    <?php else: ?>
                                                        <span class="badge text-dark shadow-sm px-2" style="background-color: #eab308;"><i class="fa-solid fa-triangle-exclamation me-1"></i> EN REVISIÓN TÉCNICA</span>
                                                    <?php endif; ?>
                                                    <small class="text-muted font-monospace" style="font-size: 0.65rem;">Gestionado en C. Incidencias</small>
                                                </div>

                                            <?php else: ?>
                                                <?php if($row->status == 'PENDIENTE'): ?>
                                                    <div class="btn-group shadow-sm">
                                                        <a href="<?= base_url ?>Admin/changeStatus?id=<?= $row->request_unique_id ?>&status=APROBADO" class="btn btn-sm btn-success fw-bold" title="Aprobar Despacho"><i class="fa-solid fa-check"></i> Autorizar</a>
                                                        <a href="<?= base_url ?>Admin/changeStatus?id=<?= $row->request_unique_id ?>&status=RECHAZADO" class="btn btn-sm btn-danger fw-bold" title="Rechazar Despacho"><i class="fa-solid fa-xmark"></i></a>
                                                    </div>
                                                <?php elseif($row->status == 'APROBADO'): ?>
                                                    <div class="d-flex flex-column align-items-end gap-2">
                                                        <span class="badge bg-success shadow-sm px-2 py-1"><i class="fa-solid fa-check-double me-1"></i> DESPACHO AUTORIZADO</span>
                                                        <button class="btn btn-sm fw-bold px-3 shadow-sm text-uppercase" style="background-color: var(--white); border: 2px solid var(--panel-dark); color: var(--panel-darker); font-size: 0.75rem;" onclick="imprimirActa('<?= $row->request_unique_id ?>', '<?= htmlspecialchars(addslashes($row->fullname), ENT_QUOTES) ?>', '<?= addslashes($safe_desc) ?>', '<?= date('d/m/Y h:i A', strtotime($row->created_at)) ?>')">
                                                            <i class="fa-solid fa-file-contract me-1" style="color: var(--safety-orange);"></i> Imprimir Acta
                                                        </button>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="badge bg-danger shadow-sm px-2 py-1">SOLICITUD DENEGADA</span>
                                                <?php endif; ?>

                                            <?php endif; ?>
                                            
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="text-center py-5 text-muted bg-light"><i class="fa-solid fa-folder-open fa-3x mb-3 opacity-25"></i><p class="mb-0 fw-bold text-uppercase" style="letter-spacing: 1px;">No existen registros en el histórico.</p></td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // =================================================================
    // MOTOR DE GENERACIÓN DE ACTAS EN PDF (DISEÑO CORPORATIVO ESTRICTO)
    // =================================================================
    function imprimirActa(id, usuario, descripcion, fecha) {
        const w = window.open('', '_blank', 'width=850,height=700');
        w.document.write(`
            <html>
                <head>
                    <title>Acta de Entrega ASN-${id} - SICOT ERP</title>
                    <style>
                        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700;900&display=swap');
                        body { font-family: 'Roboto', Arial, sans-serif; color: #1e293b; margin: 0; padding: 40px; background: #fff; }
                        .ticket-box { border: 2px solid #0f172a; padding: 50px; max-width: 700px; margin: auto; position: relative; }
                        .header { display: flex; justify-content: space-between; border-bottom: 4px solid #ea580c; padding-bottom: 20px; margin-bottom: 40px; align-items: flex-end; }
                        .logo { font-size: 36px; font-weight: 900; color: #0f172a; letter-spacing: -1px; line-height: 1; }
                        .logo span { font-weight: 400; color: #64748b; font-size: 20px; display: block; letter-spacing: 2px; margin-top: 5px; }
                        .meta-data { text-align: right; font-size: 12px; line-height: 1.8; color: #334155; }
                        .meta-data strong { color: #0f172a; }
                        .title { text-align: center; font-size: 18px; font-weight: 900; margin-bottom: 40px; text-transform: uppercase; letter-spacing: 1px; background: #f8fafc; padding: 10px; border: 1px solid #e2e8f0; }
                        
                        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
                        .info-table th, .info-table td { padding: 12px; border: 1px solid #cbd5e1; font-size: 13px; }
                        .info-table th { background-color: #f1f5f9; text-transform: uppercase; width: 40%; text-align: left; color: #475569; }
                        .info-table td { font-weight: 700; color: #0f172a; }

                        .legal-text { margin-top: 30px; font-size: 11px; color: #475569; text-align: justify; line-height: 1.6; padding: 15px; border-left: 4px solid #eab308; background: #f8fafc; }
                        
                        .signatures { display: flex; justify-content: space-between; margin-top: 100px; text-align: center; }
                        .sig-block { width: 45%; }
                        .sig-line { border-top: 1px solid #0f172a; padding-top: 10px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
                        
                        .watermark { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 120px; color: rgba(15, 23, 42, 0.03); z-index: -1; white-space: nowrap; font-weight: 900; letter-spacing: 5px; }
                        
                        @media print { 
                            .ticket-box { border: none; padding: 0; }
                            body { padding: 0; }
                            .legal-text { border-left: 4px solid #000 !important; background: transparent; }
                        }
                    </style>
                </head>
                <body>
                    <div class="ticket-box">
                        <div class="watermark">CONFIDENCIAL</div>
                        
                        <div class="header">
                            <div class="logo">SICOT <span>SISTEMA LOGÍSTICO</span></div>
                            <div class="meta-data">
                                <strong style="font-size: 16px;">ACTA N°: ASN-${String(id).padStart(5, '0')}</strong><br>
                                Fecha de Emisión: <strong>${fecha}</strong><br>
                                Autoridad: <strong>Dirección de Operaciones</strong>
                            </div>
                        </div>
                        
                        <div class="title">DOCUMENTO OFICIAL DE DESPACHO Y ASIGNACIÓN</div>
                        
                        <table class="info-table">
                            <tr><th>Funcionario Receptor</th><td>${usuario.toUpperCase()}</td></tr>
                            <tr><th>Especificación del Activo</th><td>${descripcion.toUpperCase()}</td></tr>
                            <tr><th>Estado de Transacción</th><td>AUTORIZADO Y VERIFICADO EN BODEGA</td></tr>
                            <tr><th>Condición Operativa</th><td>ÓPTIMAS CONDICIONES DE USO</td></tr>
                        </table>
                        
                        <div class="legal-text">
                            <strong>CLÁUSULA DE RESPONSABILIDAD:</strong><br>
                            Mediante el presente documento corporativo, el área de administración certifica la entrega del activo físico descrito en favor del funcionario responsable. 
                            El receptor asume la total custodia, garantizando el mantenimiento preventivo y el buen uso operativo del equipo en el frente de obra asignado.<br><br>
                            En caso de pérdida, hurto o daño atribuible a negligencia, el costo de reposición será asumido según los estatutos de la compañía. 
                            La devolución y el relevo de responsabilidad solo se harán efectivos cuando el equipo sea escaneado (Check-In) en bodega mediante el código QR de la billetera digital del funcionario.
                        </div>

                        <div class="signatures">
                            <div class="sig-block"><div class="sig-line">Firma Jefe de Operaciones / Bodega</div></div>
                            <div class="sig-block"><div class="sig-line">Firma de Aceptación Funcionario<br><span style="font-weight:normal; font-size:10px; margin-top:5px; display:block;">C.C. / ID: ______________________</span></div></div>
                        </div>
                    </div>
                    <script>
                        window.onload = function() { 
                            window.print(); 
                            setTimeout(function(){ window.close(); }, 500); 
                        }
                    <\/script>
                </body>
            </html>
        `);
        w.document.close();
    }

    // =================================================================
    // LÓGICA DE FILTRADO POR PESTAÑAS (PILLS)
    // =================================================================
    function filterTable(filterType) {
        // Actualizar UI de los botones
        document.querySelectorAll('#filterPills .nav-link').forEach(btn => {
            btn.classList.remove('active');
            if (btn.getAttribute('data-filter') === filterType) {
                btn.classList.add('active');
            }
        });

        // Filtrar filas
        const rows = document.querySelectorAll('.history-row');
        rows.forEach(row => {
            if (filterType === 'all') {
                row.style.display = '';
            } else if (filterType === 'despacho' && row.classList.contains('row-despacho')) {
                row.style.display = '';
            } else if (filterType === 'falla' && row.classList.contains('row-falla')) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // =================================================================
    // LÓGICA CHECK-IN Y PURGA
    // =================================================================
    function processCheckIn(id, tool, user) {
        Swal.fire({
            title: 'Protocolo de Recepción',
            html: `<div class="text-start">
                    <p class="mb-1 small font-monospace"><b>ACTIVO:</b> ${tool}</p>
                    <p class="mb-3 small font-monospace"><b>RESPONSABLE:</b> ${user}</p>
                    <label class="form-label small fw-bold">EVALUACIÓN FÍSICA</label>
                    <select id="retCond" class="form-select border-dark fw-bold">
                        <option value="BUENO">ÓPTIMO (Regresar a Bodega)</option>
                        <option value="DAÑADO">AVERÍA (Enviar a Taller)</option>
                        <option value="PERDIDO">PÉRDIDA / DESTRUCCIÓN TOTAL</option>
                    </select>
                   </div>`,
            showCancelButton: true,
            confirmButtonColor: '#ea580c',
            confirmButtonText: 'Ejecutar Check-In',
            cancelButtonText: 'Cancelar'
        }).then((res) => { 
            if(res.isConfirmed) { 
                $.post('<?= base_url ?>Admin/processReturn', {assignment_id: id, return_condition: document.getElementById('retCond').value}, () => location.reload()); 
            } 
        });
    }

    function deleteSelected() {
        let ids = []; document.querySelectorAll('.row-checkbox:checked').forEach(cb => ids.push(cb.value));
        Swal.fire({ 
            title: '¿Purgar Expedientes?', 
            text: 'La acción es irreversible. Quedará registrada en la Caja Negra.', 
            icon: 'warning', 
            showCancelButton: true, 
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Sí, Purgar',
            cancelButtonText: 'Cancelar'
        }).then((res) => {
            if(res.isConfirmed) { $.post('<?= base_url ?>Admin/deleteReports', {ids: ids}, () => location.reload()); }
        });
    }

    document.getElementById('selectAll')?.addEventListener('change', function() {
        document.querySelectorAll('.row-checkbox').forEach(cb => {
            // Solo seleccionar los que están visibles actualmente (por el filtro)
            if (cb.closest('tr').style.display !== 'none') {
                cb.checked = this.checked;
            }
        });
        document.getElementById('btnDeleteSelected').style.display = document.querySelectorAll('.row-checkbox:checked').length > 0 ? 'inline-block' : 'none';
    });

    // Actualizar botón de eliminar al clickear checkboxes individuales
    document.querySelectorAll('.row-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            document.getElementById('btnDeleteSelected').style.display = document.querySelectorAll('.row-checkbox:checked').length > 0 ? 'inline-block' : 'none';
        });
    });
</script>