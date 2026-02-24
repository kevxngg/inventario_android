<?php require_once 'views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold text-dark"><i class="fa-solid fa-screwdriver-wrench me-2 text-primary"></i>Centro de Mantenimiento</h2>
        <p class="text-muted mb-0">Control de reparaciones, auditoría de costos y expediente técnico de maquinaria.</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="glass-card p-4 d-flex align-items-center justify-content-between shadow-sm border-start border-warning border-4">
            <div>
                <h6 class="text-muted mb-1 small fw-bold text-uppercase">Activos en Taller</h6>
                <h2 class="fw-bold mb-0 text-dark"><?= $activeCount ?> <span class="fs-6 text-muted">unidades</span></h2>
            </div>
            <div class="icon-box bg-light text-warning rounded p-3 border">
                <i class="fa-solid fa-gears fa-2x"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="glass-card p-4 d-flex align-items-center justify-content-between shadow-sm border-start border-danger border-4">
            <div>
                <h6 class="text-muted mb-1 small fw-bold text-uppercase">Costos Históricos (Reparaciones)</h6>
                <h2 class="fw-bold mb-0 text-danger">$<?= number_format($totalCost, 2) ?> <span class="fs-6 text-muted">USD</span></h2>
            </div>
            <div class="icon-box bg-light text-danger rounded p-3 border">
                <i class="fa-solid fa-money-bill-trend-up fa-2x"></i>
            </div>
        </div>
    </div>
</div>

<ul class="nav nav-tabs mb-4" id="workshopTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-bold text-primary" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>Reparaciones Activas
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold text-secondary" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
            <i class="fa-solid fa-book-journal-whills me-2"></i>Expediente Histórico
        </button>
    </li>
</ul>

<div class="tab-content" id="workshopTabsContent">
    
    <div class="tab-pane fade show active" id="active" role="tabpanel">
        <div class="glass-card p-0 border-0 shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase small text-muted">
                        <tr>
                            <th class="ps-4 py-3">Expediente ID</th>
                            <th>Identificación de Activo</th>
                            <th>Diagnóstico Reportado</th>
                            <th>Fecha de Ingreso</th>
                            <th class="text-end pe-4">Acción Resolutiva</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($activeMaintenance) && $activeMaintenance->num_rows > 0): ?>
                            <?php while($maint = $activeMaintenance->fetch_object()): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-secondary">#REP-<?= str_pad($maint->id, 4, "0", STR_PAD_LEFT) ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?= base_url ?>assets/img/<?= $maint->image ?>" class="rounded shadow-sm border me-3" width="45" height="45" style="object-fit:cover;">
                                        <div>
                                            <div class="fw-bold text-dark"><?= $maint->tool_name ?></div>
                                            <span class="badge bg-warning text-dark border border-warning mt-1"><i class="fa-solid fa-wrench me-1"></i> EN TALLER</span>
                                        </div>
                                    </div>
                                </td>
                                <td><p class="mb-0 small text-secondary fw-semibold" style="max-width: 250px;"><?= $maint->issue_description ?></p></td>
                                <td class="text-muted small fw-bold"><?= date('d/m/Y', strtotime($maint->start_date)) ?></td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-primary shadow-sm fw-bold" onclick="resolveMaintenance(<?= $maint->id ?>, '<?= addslashes($maint->tool_name) ?>')">
                                        <i class="fa-solid fa-clipboard-check me-1"></i> Auditar Reparación
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-check-circle fa-2x mb-3 text-success"></i>
                                    <p class="mb-0 fw-bold">El taller se encuentra libre de incidencias.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="history" role="tabpanel">
        <div class="glass-card p-0 border-0 shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase small text-muted">
                        <tr>
                            <th class="ps-4 py-3">Expediente ID</th>
                            <th>Activo Físico</th>
                            <th>Resolución</th>
                            <th>Costo Financiero</th>
                            <th class="text-end pe-4">Fecha de Cierre</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($historyMaintenance) && $historyMaintenance->num_rows > 0): ?>
                            <?php while($hist = $historyMaintenance->fetch_object()): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-secondary">#REP-<?= str_pad($hist->id, 4, "0", STR_PAD_LEFT) ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= $hist->tool_name ?></div>
                                    <small class="text-muted"><?= $hist->issue_description ?></small>
                                </td>
                                <td>
                                    <?php if($hist->status == 'REPARADO'): ?>
                                        <span class="badge bg-success shadow-sm"><i class="fa-solid fa-check me-1"></i> Operativo / Reparado</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger shadow-sm"><i class="fa-solid fa-xmark me-1"></i> Pérdida Total (Baja)</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="fw-bold <?= $hist->repair_cost > 0 ? 'text-danger' : 'text-muted' ?>">
                                        $<?= number_format($hist->repair_cost, 2) ?> USD
                                    </span>
                                </td>
                                <td class="text-end pe-4 text-muted small fw-bold">
                                    <?= date('d/m/Y', strtotime($hist->end_date)) ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-folder-open fa-2x mb-3 text-secondary"></i>
                                    <p class="mb-0 fw-bold">No hay registros históricos de mantenimiento.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php require_once 'views/layouts/footer.php'; ?>

<script>
function resolveMaintenance(maintId, toolName) {
    Swal.fire({
        title: 'Auditoría de Mantenimiento',
        html: `
            <div class="text-start mb-3">
                <div class="bg-light p-3 rounded border border-secondary mb-3">
                    <p class="mb-1 text-secondary small fw-bold">ACTIVO EN REVISIÓN</p>
                    <p class="mb-0 fw-bold text-dark"><i class="fa-solid fa-toolbox me-2 text-primary"></i>${toolName}</p>
                </div>
                
                <label class="form-label fw-bold small text-secondary">RESOLUCIÓN TÉCNICA</label>
                <select id="resolutionStatus" class="form-select border-primary shadow-sm text-dark fw-bold mb-3" onchange="toggleCostField()">
                    <option value="REPARADO">Reparación Exitosa (Reintegrar a Bodega)</option>
                    <option value="IRREPARABLE">Irreparable (Pérdida Total / Dar de Baja)</option>
                </select>

                <div id="costContainer">
                    <label class="form-label fw-bold small text-secondary">COSTO FINANCIERO DE REPARACIÓN (USD)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white text-danger fw-bold">$</span>
                        <input type="number" id="repairCost" class="form-control text-danger fw-bold text-center" value="0" min="0" step="0.01">
                    </div>
                    <small class="text-muted x-small d-block mt-1">Este valor afectará las métricas de rentabilidad.</small>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#004b87',
        confirmButtonText: '<i class="fa-solid fa-file-signature me-1"></i> Confirmar Auditoría',
        cancelButtonText: 'Cancelar',
        width: '500px',
        preConfirm: () => {
            const resolution = document.getElementById('resolutionStatus').value;
            const cost = document.getElementById('repairCost').value;
            return { maintenance_id: maintId, resolution: resolution, cost: cost };
        }
    }).then((result) => {
        if(result.isConfirmed) {
            $.post('<?= base_url ?>Admin/resolveMaintenance', result.value, function(response){
                if(response.status === 'success'){
                    Swal.fire({
                        title: 'Expediente Cerrado',
                        text: response.msg,
                        icon: 'success',
                        confirmButtonColor: '#004b87'
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Error del Sistema', response.msg, 'error');
                }
            }, 'json');
        }
    });
}

function toggleCostField() {
    const status = document.getElementById('resolutionStatus').value;
    const costDiv = document.getElementById('costContainer');
    if(status === 'IRREPARABLE') {
        costDiv.style.display = 'none';
        document.getElementById('repairCost').value = 0;
    } else {
        costDiv.style.display = 'block';
    }
}
</script>