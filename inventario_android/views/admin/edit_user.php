<?php require_once 'views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary-dark">✏️ Editar Usuario</h2>
    <a href="<?= base_url ?>Admin/users" class="btn btn-outline-secondary rounded-pill">
        <i class="fa-solid fa-arrow-left me-2"></i> Volver
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="glass-card p-5 fade-in-up">
            
            <form action="<?= base_url ?>Admin/updateUser?id=<?= $user->id ?>" method="POST">
                
                <div class="text-center mb-4">
                    <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem; border-radius: 50%;">
                        <?= strtoupper(substr($user->fullname, 0, 1)) ?>
                    </div>
                    <h5 class="fw-bold"><?= $user->fullname ?></h5>
                    <p class="text-muted small"><?= $user->email ?></p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Nombre Completo</label>
                    <input type="text" name="fullname" class="form-control" value="<?= $user->fullname ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Correo Electrónico</label>
                    <input type="email" name="email" class="form-control" value="<?= $user->email ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Rol en Sistema</label>
                    <select name="role" class="form-select">
                        <option value="USER" <?= $user->role == 'USER' ? 'selected' : '' ?>>👷 Jefe de Obra</option>
                        <option value="ADMIN" <?= $user->role == 'ADMIN' ? 'selected' : '' ?>>👨‍💻 Administrador</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-danger">Cambiar Contraseña</label>
                    <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para NO cambiar">
                    <small class="text-muted">Solo escribe aquí si quieres resetear la clave.</small>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold">
                        <i class="fa-solid fa-save me-2"></i> Guardar Cambios
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>