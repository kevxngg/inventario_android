<?php require_once 'views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary-dark">👥 Gestión de Usuarios</h2>
    <button class="btn btn-android" data-bs-toggle="modal" data-bs-target="#modalUser">
        <i class="fa-solid fa-user-plus me-2"></i> Nuevo Usuario
    </button>
</div>

<div class="glass-card p-4">
    <table class="table table-hover align-middle">
        <thead class="bg-light">
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Fecha Registro</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($user = $listaUsuarios->fetch_object()): ?>
            <tr>
                <td>#<?= $user->id ?></td>
                <td>
                    <div class="fw-bold"><?= $user->fullname ?></div>
                    <small class="text-muted"><?= $user->email ?></small>
                </td>
                <td>
                    <?php if($user->role == 'ADMIN'): ?>
                        <span class="badge bg-primary rounded-pill">Administrador</span>
                    <?php else: ?>
                        <span class="badge bg-secondary rounded-pill">Jefe de Obra</span>
                    <?php endif; ?>
                </td>
                <td><?= $user->created_at ?></td>
                <td>
                    <div class="d-flex gap-2">
                        <a href="<?= base_url ?>Admin/editUser?id=<?= $user->id ?>" class="btn btn-sm btn-outline-primary rounded-circle" style="width:32px; height:32px; padding:0; display:flex; align-items:center; justify-content:center;">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        <?php if($user->id != $_SESSION['identity']->id): ?>
                        <a href="<?= base_url ?>Admin/deleteUser?id=<?= $user->id ?>" class="btn btn-sm btn-outline-danger rounded-circle btn-delete" style="width:32px; height:32px; padding:0; display:flex; align-items:center; justify-content:center;">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalUser" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Registrar Nuevo Empleado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url ?>Admin/saveUser" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre Completo</label>
                        <input type="text" name="fullname" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Rol / Permisos</label>
                            <select name="role" class="form-select">
                                <option value="USER">Jefe de Obra (Básico)</option>
                                <option value="ADMIN">Administrador (Total)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-android w-100">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>