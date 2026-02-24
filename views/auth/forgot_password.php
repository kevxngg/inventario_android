<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña | SICOT ERP</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url ?>assets/css/android-theme.css">
    <style>
        .auth-card { max-width: 450px; width: 100%; }
    </style>
</head>
<body class="android-bg d-flex align-items-center justify-content-center" style="min-height: 100vh;">

    <div class="auth-card p-3">
        <div class="glass-card p-5 shadow-lg border-0 fade-in-up text-center">
            
            <div class="mb-4">
                <div class="btn-icon mx-auto mb-3" style="width: 70px; height: 70px; font-size: 1.8rem; background: var(--danger); color: white;">
                    <i class="fa-solid fa-key"></i>
                </div>
                <h4 class="fw-bold text-dark mb-1">Recuperación de Acceso</h4>
                <p class="text-secondary small fw-bold mt-2">
                    Ingresa tu correo corporativo y te enviaremos un código de seguridad para restablecer tu contraseña.
                </p>
            </div>

            <?php if(isset($_SESSION['error_recovery'])): ?>
                <div class="alert alert-danger p-2 small fw-bold border-0 shadow-sm rounded-pill mb-4">
                    <i class="fa-solid fa-circle-exclamation me-1"></i> <?= $_SESSION['error_recovery'] ?>
                </div>
                <?php unset($_SESSION['error_recovery']); ?>
            <?php endif; ?>

            <form action="<?= base_url ?>Auth/sendRecoveryCode" method="POST">
                <div class="mb-4 text-start">
                    <label class="form-label small fw-bold text-secondary">CORREO ELECTRÓNICO</label>
                    <div class="input-group input-group-android shadow-sm">
                        <span class="input-group-text bg-white border-end-0 text-danger"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control border-start-0 py-2" placeholder="ejemplo@constructora.com" required autofocus>
                    </div>
                </div>

                <div class="d-grid mb-4">
                    <button type="submit" class="btn btn-danger btn-lg shadow-sm fw-bold rounded-pill">
                        Enviar Código de Seguridad <i class="fa-solid fa-paper-plane ms-2"></i>
                    </button>
                </div>
            </form>

            <div class="pt-3 border-top">
                <a href="<?= base_url ?>Auth/login" class="text-muted text-decoration-none small fw-bold">
                    <i class="fa-solid fa-arrow-left me-1"></i> Cancelar y volver al Login
                </a>
            </div>
            
        </div>
    </div>

</body>
</html>