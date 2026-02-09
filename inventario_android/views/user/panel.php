<?php require_once 'views/layouts/header.php'; ?>

<div class="container-fluid p-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Hola, <?= $_SESSION['identity']->fullname ?> 👋</h2>
            <p class="text-muted">Panel de Control</p>
        </div>
        <span class="badge bg-primary px-3 py-2 rounded-pill">👷 Técnico / Usuario</span>
    </div>

    <h4 class="fw-bold mb-3">🚧 Mis Proyectos Asignados</h4>

    <div class="row mb-5">
        <div class="col-md-8 mb-3">
            <div class="card border-0 shadow-sm bg-primary text-white h-100" style="background: linear-gradient(135deg, #0d6efd 0%, #e7f1ff 100%); min-height: 120px;">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-helmet-safety fa-lg"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0 text-dark">Obras a Cargo</h4>
                            <small class="text-muted">Actualmente bajo tu supervisión</small>
                        </div>
                    </div>
                    <div class="text-end">
                        <h1 class="fw-bold display-4 m-0 text-dark"><?= isset($misObras) ? $misObras->num_rows : 0 ?></h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <h5 class="fw-bold text-danger mb-3">¿Incidente en Obra?</h5>
                    <button class="btn btn-outline-danger fw-bold px-4 py-2 w-100" data-bs-toggle="modal" data-bs-target="#modalReport">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i> Reportar Daño
                    </button>
                </div>
            </div>
        </div>
    </div>

    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-list-check me-2"></i>Historial de Solicitudes y Reportes</h5>
    
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="ps-4">DETALLE</th>
                            <th>FECHA</th>
                            <th class="text-end pe-4">ESTADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($misSolicitudes) && $misSolicitudes->num_rows > 0): ?>
                            <?php while($req = $misSolicitudes->fetch_object()): ?>
                            <tr>
                                <td class="ps-4">
                                    <?php if($req->type == 'SOLICITUD_HERRAMIENTA'): ?>
                                        <span class="badge bg-info text-dark me-2">Pedido</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger me-2">Reporte</span>
                                    <?php endif; ?>
                                    <span class="fw-bold text-dark"><?= $req->description ?></span>
                                </td>
                                <td class="text-muted"><?= date('d/m/Y', strtotime($req->created_at)) ?></td>
                                <td class="text-end pe-4">
                                    <?php if($req->status == 'PENDIENTE'): ?>
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    <?php elseif($req->status == 'APROBADO'): ?>
                                        <span class="badge bg-success">Aprobado</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Rechazado</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">
                                    No hay registros recientes.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalReport" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-triangle-exclamation me-2"></i>Reportar Daño o Pérdida</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url ?>User/saveReport" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="type" value="REPORTE_DAÑO">

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">DESCRIBE EL PROBLEMA</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Ej: El taladro Bosch dejó de funcionar, huele a quemado..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light text-muted" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger px-4 rounded-pill">Enviar Reporte</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>