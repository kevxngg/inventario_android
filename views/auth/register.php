<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Personal | SICOT ERP</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url ?>assets/css/android-theme.css">
    <style>
        .register-card {
            max-width: 500px;
            width: 100%;
        }
    </style>
</head>
<body class="android-bg d-flex align-items-center justify-content-center" style="min-height: 100vh;">

    <div class="register-card p-3">
        <div class="glass-card p-4 shadow-lg border-0 fade-in-up">
            <div class="text-center mb-4">
                <h4 class="fw-bold text-dark mb-1">Formulario de Alta</h4>
                <p class="text-secondary small fw-bold text-uppercase" style="letter-spacing: 1px;">Registro de Personal Operativo</p>
            </div>

            <form action="<?= base_url ?>Auth/save" method="POST">
                
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">NOMBRE Y APELLIDO COMPLETO</label>
                    <div class="input-group input-group-android">
                        <span class="input-group-text"><i class="fa-solid fa-address-card"></i></span>
                        <input type="text" name="fullname" class="form-control" placeholder="Identificación del funcionario" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">ORGANIZACIÓN / CONSTRUCTORA</label>
                    <div class="input-group input-group-android">
                        <span class="input-group-text"><i class="fa-solid fa-building"></i></span>
                        <input type="text" name="company_name" class="form-control" placeholder="Razón Social">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">CORREO ELECTRÓNICO</label>
                    <div class="input-group input-group-android">
                        <span class="input-group-text"><i class="fa-solid fa-envelope-open"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="Dirección de contacto corporativo" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-secondary">DEFINIR CONTRASEÑA</label>
                    <div class="input-group input-group-android">
                        <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres de seguridad" required minlength="6">
                    </div>
                    <div class="form-text x-small ps-1 fw-bold text-muted mt-2">La clave debe cumplir con los protocolos de seguridad de la empresa.</div>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-android btn-lg shadow-sm fw-bold">
                        Confirmar Alta de Usuario <i class="fa-solid fa-user-plus ms-2"></i>
                    </button>
                </div>
            </form>

            <div class="text-center mt-4 pt-3 border-top">
                <span class="text-muted small">¿Ya posee una cuenta activa?</span>
                <a href="<?= base_url ?>Auth/login" class="fw-bold text-primary text-decoration-none small ms-1">INICIAR SESIÓN</a>
            </div>

            <div class="text-center mt-3">
                <a href="<?= base_url ?>" class="text-secondary text-decoration-none x-small fw-bold uppercase">
                    <i class="fa-solid fa-house me-1"></i> RETORNAR AL INICIO
                </a>
            </div>
        </div>
    </div>

</body>
</html>