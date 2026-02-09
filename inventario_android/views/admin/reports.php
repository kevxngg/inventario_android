<?php require_once 'views/layouts/header.php'; ?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Historial de Reportes</h2>
            <p class="text-muted">Visualiza el historial. Acepta o rechaza desde las notificaciones.</p>
        </div>
        <div class="bg-white p-2 rounded shadow-sm border">
            <span class="text-muted small fw-bold">TOTAL:</span>
            <span class="fw-bold text-primary ms-2"><?= $reportes->num_rows ?></span>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase small text-muted">
                        <tr>
                            <th class="ps-4 py-3">Usuario</th>
                            <th>Tipo</th>
                            <th>Detalle</th>
                            <th>Fecha</th>
                            <th class="text-end pe-4">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($reportes) && $reportes->num_rows > 0): ?>
                            <?php while($row = $reportes->fetch_object()): ?>
                            <tr>
                                <td class="ps-4">
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