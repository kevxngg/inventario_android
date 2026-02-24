<?php require_once 'views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold text-dark"><i class="fa-solid fa-users-gear me-2 text-primary"></i>Consola de Gestión de Identidades</h2>
        <p class="text-muted mb-0">Administración de credenciales, roles y permisos de acceso al sistema.</p>
    </div>
    <button class="btn btn-android shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalUser">
        <i class="fa-solid fa-user-plus me-2"></i> Alta de Nuevo Funcionario
    </button>
</div>

<div class="glass-card p-0 border-0 shadow-sm overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-uppercase small text-muted">
                <tr>
                    <th class="ps-4 py-3">ID Interno</th>
                    <th>Funcionario</th>
                    <th>Nivel de Acceso</th>
                    <th>Fecha de Registro</th>
                    <th class="text-end pe-4">Acciones Administrativas</th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($listaUsuarios)): ?>
                    <?php while($user = $listaUsuarios->fetch_object()): ?>
                    <tr>
                        <td class="ps-4 fw-bold text-secondary">#<?= str_pad($user->id, 4, "0", STR_PAD_LEFT) ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-light border text-primary d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 40px; height: 40px; font-weight: 800;">
                                    <?= strtoupper(substr($user->fullname, 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark"><?= $user->fullname ?></div>
                                    <div class="text-muted small fw-semibold"><?= $user->email ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if($user->role == 'ADMIN'): ?>
                                <span class="badge bg-primary-dark shadow-sm px-2 py-1">
                                    <i class="fa-solid fa-user-shield me-1"></i> Administrador
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary shadow-sm px-2 py-1">
                                    <i class="fa-solid fa-user-helmet text-white me-1"></i> Jefe de Obra
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted small fw-bold">
                            <?= date('d/m/Y', strtotime($user->created_at)) ?>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="<?= base_url ?>Admin/editUser?id=<?= $user->id ?>" class="btn btn-sm btn-light border text-primary shadow-sm" title="Modificar Perfil">
                                    <i class="fa-solid fa-user-pen"></i>
                                </a>
                                <?php if($user->id != $_SESSION['identity']->id): ?>
                                <a href="<?= base_url ?>Admin/deleteUser?id=<?= $user->id ?>" class="btn btn-sm btn-light border text-danger shadow-sm btn-delete" title="Inhabilitar Usuario" onclick="return confirm('¿Confirma la eliminación definitiva de este funcionario y sus permisos de acceso?');">
                                    <i class="fa-solid fa-user-slash"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-dark text-white py-3">
                <h6 class="modal-title fw-bold text-uppercase" style="letter-spacing: 1px;"><i class="fa-solid fa-user-plus me-2 text-primary"></i>Alta de Personal Operativo</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url ?>Admin/saveUser" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Nombre y Apellido</label>
                        <input type="text" name="fullname" class="form-control fw-bold" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Correo Institucional</label>
                        <input type="email" name="email" class="form-control" placeholder="usuario@dominio.com" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-secondary text-uppercase">Contraseña</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-secondary text-uppercase">Rol Asignado</label>
                            <select name="role" class="form-select fw-bold">
                                <option value="USER">Personal de Obra</option>
                                <option value="ADMIN">Administrador de Sistema</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">Confirmar Registro</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>