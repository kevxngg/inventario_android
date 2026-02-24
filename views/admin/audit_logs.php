<?php require_once 'views/layouts/header.php'; ?>

<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold text-dark mb-0"><i class="fa-solid fa-server me-2 text-danger"></i>Caja Negra de Trazabilidad</h2>
            <p class="text-muted small mt-1">Registro inmutable de todas las acciones ejecutadas por los usuarios administrativos.</p>
        </div>
        <div>
            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger p-2 px-3 rounded-pill shadow-sm">
                <i class="fa-solid fa-shield-halved me-1"></i> Sistema Anti-Fraude Activo
            </span>
        </div>
    </div>

    <div class="glass-card p-0 border-0 shadow-sm fade-in-up">
        <div class="table-responsive" style="max-height: 700px; overflow-y: auto;">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-dark text-white text-uppercase small" style="position: sticky; top: 0; z-index: 1;">
                    <tr>
                        <th class="ps-4 py-3">Timestamp / IP</th>
                        <th>Usuario Responsable</th>
                        <th>Módulo Afectado</th>
                        <th>Tipo de Acción</th>
                        <th class="pe-4">Detalles Técnicos del Evento</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <?php if(isset($logs) && $logs->num_rows > 0): ?>
                        <?php while($log = $logs->fetch_object()): ?>
                        <tr class="border-bottom">
                            <td class="ps-4">
                                <div class="fw-bold text-dark" style="font-family: monospace;"><?= date('d/m/Y - H:i:s', strtotime($log->created_at)) ?></div>
                                <span class="badge bg-light text-secondary border x-small"><i class="fa-solid fa-network-wired me-1"></i> <?= $log->ip_address ?></span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php $img = !empty($log->image) ? $log->image : 'default_user.png'; ?>
                                    <img src="<?= base_url ?>assets/img/<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>" class="rounded-circle shadow-sm me-2 object-fit-cover" width="35" height="35" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($log->fullname ?? 'U') ?>&background=0D8ABC&color=fff&rounded=true';">
                                    <div>
                                        <div class="fw-bold text-dark small"><?= htmlspecialchars($log->fullname, ENT_QUOTES, 'UTF-8') ?></div>
                                        <div class="text-muted" style="font-size: 0.70rem;"><?= $log->role ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary rounded-pill shadow-sm px-3"><i class="fa-solid fa-cube me-1"></i> <?= $log->module ?></span>
                            </td>
                            <td>
                                <?php 
                                    $color = 'primary';
                                    if(strpos($log->action_type, 'ELIMINACION') !== false || strpos($log->action_type, 'BAJA') !== false || strpos($log->action_type, 'RECHAZO') !== false) $color = 'danger';
                                    if(strpos($log->action_type, 'CREACION') !== false || strpos($log->action_type, 'APROBACION') !== false) $color = 'success';
                                    if(strpos($log->action_type, 'EDICION') !== false || strpos($log->action_type, 'MODIFICACION') !== false) $color = 'warning text-dark';
                                ?>
                                <span class="badge bg-<?= $color ?> bg-opacity-10 border border-<?= $color ?> text-<?= $color ?> text-uppercase" style="font-size: 0.75rem;">
                                    <?= str_replace('_', ' ', $log->action_type) ?>
                                </span>
                            </td>
                            <td class="pe-4">
                                <p class="mb-0 text-dark small fw-medium" style="max-width: 400px; white-space: normal;">
                                    <?= htmlspecialchars($log->details, ENT_QUOTES, 'UTF-8') ?>
                                </p>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-shield-cat fa-3x mb-3 text-secondary opacity-50"></i>
                                <h5 class="fw-bold text-dark">La Caja Negra está vacía</h5>
                                <p class="mb-0">Aún no se han registrado eventos administrativos de impacto en el sistema.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>