<?php require_once 'views/layouts/header.php'; ?>

<style>
    .admin-edit-avatar {
        width: 110px !important; 
        height: 110px !important; 
        border-radius: 50% !important; 
        object-fit: cover !important;
        overflow: hidden !important; 
        border: 4px solid #ffffff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        background-color: #f8f9fa;
    }
</style>

<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary-dark mb-0"><i class="fa-solid fa-user-pen me-2"></i>Gestión de Personal</h2>
            <p class="text-muted small mt-1">Modificando los credenciales y accesos del sistema.</p>
        </div>
        <a href="<?= base_url ?>Admin/users" class="btn btn-light border shadow-sm rounded-pill fw-bold text-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i> Volver al Listado
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7 col-xl-6">
            <div class="glass-card p-4 p-md-5 border-0 shadow-sm fade-in-up">
                
                <form action="<?= base_url ?>Admin/updateUser?id=<?= $user->id ?>" method="POST">
                    
                    <div class="text-center mb-5 border-bottom pb-4">
                        <?php 
                            // Carga la imagen real del usuario si la tiene
                            $img = (isset($user->image) && !empty($user->image)) ? $user->image : 'default_user.png';
                        ?>
                        <img src="<?= base_url ?>assets/img/<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>" 
                             class="admin-edit-avatar mb-3" 
                             alt="Avatar de <?= htmlspecialchars($user->fullname, ENT_QUOTES, 'UTF-8') ?>"
                             onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($user->fullname ?? 'U') ?>&background=0D8ABC&color=fff&rounded=true';">
                        
                        <h4 class="fw-bold text-dark mb-1"><?= htmlspecialchars($user->fullname, ENT_QUOTES, 'UTF-8') ?></h4>
                        <span class="badge <?= $user->role == 'ADMIN' ? 'bg-primary' : 'bg-secondary' ?> rounded-pill shadow-sm">
                            <i class="fa-solid <?= $user->role == 'ADMIN' ? 'fa-user-shield' : 'fa-helmet-safety' ?> me-1"></i> 
                            <?= $user->role == 'ADMIN' ? 'Administrador' : 'Jefe de Obra' ?>
                        </span>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Nombre Completo del Funcionario</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-primary"><i class="fa-solid fa-signature"></i></span>
                            <input type="text" name="fullname" class="form-control border-start-0 fw-bold" value="<?= htmlspecialchars($user->fullname, ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Correo Corporativo</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-envelope"></i></span>
                            <input type="email" name="email" class="form-control border-start-0" value="<?= htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Nivel de Acceso (Rol)</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-info"><i class="fa-solid fa-sitemap"></i></span>
                            <select name="role" class="form-select border-start-0 fw-bold text-dark cursor-pointer">
                                <option value="USER" <?= $user->role == 'USER' ? 'selected' : '' ?>>Operativo - Jefe de Obra</option>
                                <option value="ADMIN" <?= $user->role == 'ADMIN' ? 'selected' : '' ?>>Máster - Administrador</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-5 p-3 rounded bg-light border border-danger border-opacity-25">
                        <label class="form-label small fw-bold text-danger text-uppercase mb-2"><i class="fa-solid fa-triangle-exclamation me-1"></i> Resetear Contraseña</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-danger"><i class="fa-solid fa-lock-open"></i></span>
                            <input type="password" name="password" id="editPassword" class="form-control border-start-0 border-end-0 border-danger border-opacity-50" placeholder="•••••••• (Dejar en blanco para conservar la actual)">
                            <button class="btn btn-white border border-start-0 border-danger border-opacity-50 text-secondary" type="button" id="togglePasswordBtn">
                                <i class="fa-solid fa-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                        <small class="text-muted d-block mt-2">Si el usuario olvidó su clave, ingrese una nueva aquí. Si no desea alterarla, deje este campo vacío.</small>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm py-3">
                            <i class="fa-solid fa-floppy-disk me-2"></i> Actualizar Registro en Sistema
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const togglePasswordBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('editPassword');
        const togglePasswordIcon = document.getElementById('togglePasswordIcon');

        if(togglePasswordBtn) {
            togglePasswordBtn.addEventListener('click', function() {
                // Alternar el tipo de input (password a text)
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Alternar el icono (ojo abierto a ojo cerrado)
                if(type === 'text') {
                    togglePasswordIcon.classList.remove('fa-eye');
                    togglePasswordIcon.classList.add('fa-eye-slash');
                    togglePasswordIcon.classList.add('text-primary');
                    togglePasswordIcon.classList.remove('text-secondary');
                } else {
                    togglePasswordIcon.classList.remove('fa-eye-slash');
                    togglePasswordIcon.classList.add('fa-eye');
                    togglePasswordIcon.classList.remove('text-primary');
                    togglePasswordIcon.classList.add('text-secondary');
                }
            });
        }
    });
</script>

<?php require_once 'views/layouts/footer.php'; ?>