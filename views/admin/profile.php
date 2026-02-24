<?php require_once 'views/layouts/header.php'; ?>

<style>
    .profile-preview {
        width: 120px !important; 
        height: 120px !important; 
        border-radius: 50% !important; 
        object-fit: cover !important;
        overflow: hidden !important; /* La clave para que no salgan esquinas en el Hover */
        border: 4px solid #ffffff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease !important;
        background-color: #f8f9fa;
    }
    .profile-preview:hover {
        transform: scale(1.05); /* Efecto hover elegante sin perder redondez */
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-primary-dark"><i class="fa-solid fa-user-shield me-2"></i>Mi Perfil de Administrador</h2>
        <p class="text-muted mb-0">Gestione su información personal y credenciales de acceso maestro.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="glass-card p-4 border-0 shadow-sm h-100 fade-in-up">
            <h5 class="fw-bold mb-4 text-dark border-bottom pb-2">Datos Personales</h5>
            
            <form action="<?= base_url ?>Admin/updateProfile" method="POST" enctype="multipart/form-data">
                <div class="row align-items-center mb-4">
                    <div class="col-md-auto text-center mb-3 mb-md-0">
                        <?php 
                            $img = (isset($user->image) && !empty($user->image)) ? $user->image : 'default_user.png';
                        ?>
                        <img src="<?= base_url ?>assets/img/<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>" class="profile-preview rounded-circle" id="previewImg" alt="Foto Perfil" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($user->fullname ?? 'Admin') ?>&background=0D8ABC&color=fff&rounded=true';">
                    </div>
                    <div class="col-md">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Actualizar Fotografía</label>
                        <input type="file" name="image" class="form-control form-control-sm border-secondary" accept="image/*" onchange="previewFile()">
                        <small class="text-muted mt-1 d-block">Formatos soportados: JPG, PNG o WEBP.</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase">Nombre Completo</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-signature text-primary"></i></span>
                        <input type="text" name="fullname" class="form-control border-start-0 fw-bold" value="<?= htmlspecialchars($user->fullname ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-secondary text-uppercase">Correo Electrónico (No editable)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-envelope text-muted"></i></span>
                        <input type="email" class="form-control border-start-0 bg-light text-muted" value="<?= htmlspecialchars($user->email ?? '', ENT_QUOTES, 'UTF-8') ?>" readonly>
                    </div>
                    <small class="text-info mt-1 d-block"><i class="fa-solid fa-circle-info me-1"></i> Por seguridad, el email de administrador no puede cambiarse desde aquí.</small>
                </div>

                <button type="submit" class="btn btn-primary fw-bold px-4 rounded-pill shadow-sm">
                    <i class="fa-solid fa-floppy-disk me-2"></i> Guardar Cambios de Perfil
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="glass-card p-4 border-0 shadow-sm h-100 fade-in-up" style="animation-delay: 0.1s;">
            <h5 class="fw-bold mb-4 text-dark border-bottom pb-2">Seguridad de la Cuenta</h5>
            
            <form action="<?= base_url ?>Admin/updatePassword" method="POST" id="formPass">
                <p class="small text-muted mb-4">Para cambiar su clave de acceso maestro, ingrese la nueva contraseña a continuación. Se cerrará su sesión en otros dispositivos.</p>
                
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase">Nueva Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-warning"></i></span>
                        <input type="password" name="password" id="pass1" class="form-control border-start-0" placeholder="Mínimo 6 caracteres" minlength="6" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-secondary text-uppercase">Confirmar Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-shield-check text-warning"></i></span>
                        <input type="password" id="pass2" class="form-control border-start-0" placeholder="Repita la clave" minlength="6" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-dark fw-bold w-100 rounded-pill shadow-sm py-2">
                    <i class="fa-solid fa-key me-2"></i> Actualizar Contraseña
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // Previsualización redonda forzada
    function previewFile() {
        const preview = document.getElementById('previewImg');
        const file = document.querySelector('input[type=file]').files[0];
        const reader = new FileReader();
        reader.onloadend = function () { 
            preview.src = reader.result; 
        }
        if (file) { reader.readAsDataURL(file); }
    }

    document.getElementById('formPass').addEventListener('submit', function(e) {
        const p1 = document.getElementById('pass1').value;
        const p2 = document.getElementById('pass2').value;
        if (p1 !== p2) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error de coincidencia',
                text: 'Las contraseñas ingresadas no son iguales. Por favor, verifíquelas.',
                confirmButtonColor: '#004b87'
            });
        }
    });

    <?php if(isset($_SESSION['alert_message'])): ?>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            icon: '<?= $_SESSION['alert_icon'] ?>',
            title: 'Gestión de Perfil',
            text: '<?= $_SESSION['alert_message'] ?>',
            confirmButtonColor: '#004b87'
        });
    });
    <?php unset($_SESSION['alert_message']); unset($_SESSION['alert_icon']); ?>
    <?php endif; ?>
</script>

<?php require_once 'views/layouts/footer.php'; ?>