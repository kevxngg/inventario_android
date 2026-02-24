<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validar Código | SICOT ERP</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url ?>assets/css/android-theme.css">
    <style>
        .auth-card { max-width: 450px; width: 100%; }
        .code-input {
            font-size: 2rem;
            letter-spacing: 15px;
            text-align: center;
            font-weight: bold;
            color: var(--danger);
            background: #fff5f5;
        }
    </style>
</head>
<body class="android-bg d-flex align-items-center justify-content-center" style="min-height: 100vh;">

    <div class="auth-card p-3">
        <div class="glass-card p-5 shadow-lg border-0 fade-in-up text-center">
            
            <div class="mb-4">
                <div class="btn-icon mx-auto mb-3" style="width: 70px; height: 70px; font-size: 1.8rem; background: var(--danger); color: white;">
                    <i class="fa-solid fa-shield-cat"></i>
                </div>
                <h4 class="fw-bold text-dark mb-1">Validar Identidad</h4>
                <p class="text-secondary small fw-bold mt-2">
                    Ingresa el código que acabamos de enviar a:<br>
                    <span class="text-danger"><?= $_SESSION['recovery_email'] ?></span>
                </p>
                <small class="text-danger fw-bold"><i class="fa-solid fa-stopwatch"></i> Expira en 10 minutos.</small>
            </div>

            <?php if(isset($_SESSION['error_verify'])): ?>
                <div class="alert alert-danger p-2 small fw-bold border-0 shadow-sm rounded-pill mb-4">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> <?= $_SESSION['error_verify'] ?>
                </div>
                <?php unset($_SESSION['error_verify']); ?>
            <?php endif; ?>

            <form action="<?= base_url ?>Auth/processRecoveryCode" method="POST">
                <div class="mb-4">
                    <input type="text" name="code" class="form-control form-control-lg code-input rounded-3 shadow-sm border-danger border-opacity-25" placeholder="000000" maxlength="6" pattern="\d{6}" required autocomplete="off" autofocus>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-danger btn-lg shadow-sm fw-bold rounded-pill">
                        <i class="fa-solid fa-check-double me-2"></i> Verificar Código
                    </button>
                </div>
            </form>

            <div class="mt-4 pt-3 border-top">
                <a href="<?= base_url ?>Auth/login" class="text-muted text-decoration-none small fw-bold">
                    <i class="fa-solid fa-arrow-left me-1"></i> Volver al Login
                </a>
            </div>
            
        </div>
    </div>

</body>
</html>