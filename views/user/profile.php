<?php require_once 'views/layouts/header.php'; ?>

<style>
    .profile-preview {
        width: 120px !important; 
        height: 120px !important; 
        border-radius: 50% !important; 
        object-fit: cover !important;
        overflow: hidden !important; 
        border: 4px solid #ffffff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease !important;
        background-color: #f8f9fa;
    }
    .profile-preview:hover {
        transform: scale(1.05);
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-primary-dark"><i class="fa-solid fa-id-card me-2"></i>Mi Perfil Personal</h2>
        <p class="text-muted mb-0">Gestione su información de usuario dentro de la plataforma.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="glass-card p-4 border-0 shadow-sm h-100">
            <h5 class="fw-bold mb-4 text-dark border-bottom pb-2">Mis Datos</h5>
            
            <form action="<?= base_url ?>User/updateProfile" method="POST" enctype="multipart/form-data">
                <div class="row align-items-center mb-4 text-center text-md-start">
                    <div class="col-md-auto mb-3 mb-md-0">
                        <?php 
                            $img = (isset($user->image) && !empty($user->image)) ? $user->image : 'default_user.png';
                        ?>
                        <img src="<?= base_url ?>assets/img/<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>" class="profile-preview rounded-circle" id="previewImg" alt="Foto Perfil" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($user->fullname ?? 'User') ?>&background=0D8ABC&color=fff&rounded=true';">
                    </div>
                    <div class="col-md">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Cambiar mi Foto</label>
                        <input type="file" name="image" class="form-control form-control-sm" accept="image/*" onchange="previewFile()">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase">Nombre y Apellidos</label>
                    <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($user->fullname ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-secondary text-uppercase">Email Corporativo</label>
                    <input type="email" class="form-control bg-light" value="<?= htmlspecialchars($user->email ?? '', ENT_QUOTES, 'UTF-8') ?>" readonly title="El email no puede ser modificado por el técnico.">
                </div>

                <button type="submit" class="btn btn-primary fw-bold px-4 rounded-pill">
                    <i class="fa-solid fa-check-double me-2"></i> Actualizar mi Perfil
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="glass-card p-4 border-0 shadow-sm h-100">
            <h5 class="fw-bold mb-4 text-dark border-bottom pb-2">Acceso Seguro</h5>
            
            <form action="<?= base_url ?>User/updatePassword" method="POST" id="formPass">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase">Nueva Clave de Acceso</label>
                    <input type="password" name="password" id="pass1" class="form-control" placeholder="Mínimo 6 caracteres" minlength="6" required>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-secondary text-uppercase">Repetir Nueva Clave</label>
                    <input type="password" id="pass2" class="form-control" placeholder="Repita su clave" minlength="6" required>
                </div>

                <button type="submit" class="btn btn-dark fw-bold w-100 rounded-pill">
                    <i class="fa-solid fa-lock-open me-2"></i> Cambiar Contraseña
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function previewFile() {
        const preview = document.getElementById('previewImg');
        const file = document.querySelector('input[type=file]').files[0];
        const reader = new FileReader();
        reader.onloadend = () => preview.src = reader.result;
        if (file) reader.readAsDataURL(file);
    }

    document.getElementById('formPass').addEventListener('submit', function(e) {
        const p1 = document.getElementById('pass1').value;
        const p2 = document.getElementById('pass2').value;
        if (p1 !== p2) {
            e.preventDefault();
            Swal.fire('Atención', 'Las contraseñas no coinciden.', 'warning');
        }
    });

    <?php if(isset($_SESSION['alert_message'])): ?>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            icon: '<?= $_SESSION['alert_icon'] ?>',
            title: 'Perfil',
            text: '<?= $_SESSION['alert_message'] ?>',
            confirmButtonColor: '#004b87'
        });
    });
    <?php unset($_SESSION['alert_message']); unset($_SESSION['alert_icon']); ?>
    <?php endif; ?>
</script>

<?php require_once 'views/layouts/footer.php'; ?>