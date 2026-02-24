<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al Sistema | SICOT ERP</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url ?>assets/css/android-theme.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .login-card {
            max-width: 400px;
            width: 100%;
        }
        .auth-icon {
            width: 70px;
            height: 70px;
            background: rgba(0, 75, 135, 0.1);
            color: #004b87;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            margin: 0 auto 1.5rem auto;
        }
    </style>
</head>
<body class="android-bg d-flex align-items-center justify-content-center" style="min-height: 100vh;">

    <div class="login-card p-3">
        <div class="glass-card p-4 shadow-lg border-0 fade-in-up">
            <div class="text-center mb-4">
                <div class="auth-icon shadow-sm">
                    <i class="fa-solid fa-shield-halved fa-2x"></i>
                </div>
                <h4 class="fw-bold text-dark">SICOT ERP</h4>
                <p class="text-secondary small fw-bold text-uppercase" style="letter-spacing: 1px;">Portal de Autenticación</p>
            </div>

            <form action="<?= base_url ?>Auth/authenticate" method="POST">
                
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">CORREO ELECTRÓNICO INSTITUCIONAL</label>
                    <div class="input-group input-group-android">
                        <span class="input-group-text"><i class="fa-solid fa-at"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="usuario@dominio.com" required>
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label small fw-bold text-secondary">CONTRASEÑA DE ACCESO</label>
                    <div class="input-group input-group-android">
                        <span class="input-group-text"><i class="fa-solid fa-key"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Clave de seguridad" required>
                    </div>
                </div>

                <div class="text-end mb-4">
                    <a href="<?= base_url ?>Auth/forgotPassword" class="text-primary fw-bold text-decoration-none small">
                        <i class="fa-solid fa-circle-question me-1"></i> ¿Olvidaste tu contraseña?
                    </a>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-android btn-lg shadow-sm fw-bold py-3">
                        Validar Credenciales <i class="fa-solid fa-arrow-right-to-bracket ms-2"></i>
                    </button>
                </div>
            </form>

            <div class="text-center mt-4 pt-3 border-top">
                <p class="text-muted small mb-2">¿Requiere acceso al sistema?</p>
                <a href="<?= base_url ?>Auth/register" class="fw-bold text-primary text-decoration-none small">SOLICITAR REGISTRO DE NUEVA CUENTA</a>
            </div>

            <div class="text-center mt-3">
                <a href="<?= base_url ?>" class="text-secondary text-decoration-none x-small fw-bold">
                    <i class="fa-solid fa-chevron-left me-1"></i> REGRESAR AL PORTAL PRINCIPAL
                </a>
            </div>
        </div>

        <div class="text-center mt-4 text-muted x-small fw-bold text-uppercase" style="letter-spacing: 1px;">
            &copy; <?= date('Y') ?> Infraestructura de Software Corporativo
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <?php if(isset($_SESSION['error_login'])): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Fallo de Autenticación',
            text: 'Las credenciales proporcionadas no han sido reconocidas en el directorio activo.',
            confirmButtonColor: '#004b87',
            background: '#ffffff',
            confirmButtonText: 'Reintentar'
        });
    </script>
    <?php unset($_SESSION['error_login']); endif; ?>

    <?php if(isset($_SESSION['register']) && $_SESSION['register'] == 'complete'): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Registro Procesado',
            text: 'La cuenta ha sido dada de alta correctamente. Proceda con el ingreso.',
            confirmButtonColor: '#004b87'
        });
    </script>
    <?php unset($_SESSION['register']); endif; ?>

    <?php if(isset($_SESSION['recovery_success'])): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: '¡Acceso Restablecido!',
            text: '<?= $_SESSION['recovery_success'] ?>',
            confirmButtonColor: '#198754'
        });
    </script>
    <?php unset($_SESSION['recovery_success']); endif; ?>

</body>
</html>