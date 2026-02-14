<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Constructora</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url ?>assets/css/android-theme.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body class="android-bg d-flex align-items-center justify-content-center" style="min-height: 100vh;">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                
                <div class="glass-card p-4 fade-in-up">
                    <div class="text-center mb-4">
                        <div class="avatar-icon mb-3">
                            <i class="fa-solid fa-user-astronaut fa-3x text-primary-dark"></i>
                        </div>
                        <h3 class="fw-bold text-primary-dark">Bienvenido</h3>
                        <p class="text-muted small">Ingresa tus credenciales para acceder</p>
                    </div>

                    <form action="<?= base_url ?>Auth/authenticate" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Correo Electrónico</label>
                            <div class="input-group input-group-android">
                                <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="ejemplo@constructora.com" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Contraseña</label>
                            <div class="input-group input-group-android">
                                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-android btn-lg shadow-sm">
                                Ingresar <i class="fa-solid fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <span class="text-muted small">¿No tienes cuenta?</span>
                        <a href="<?= base_url ?>Auth/register" class="fw-bold text-primary-dark text-decoration-none">Regístrate aquí</a>
                    </div>

                    <div class="text-center mt-3 pt-3 border-top">
                        <a href="<?= base_url ?>" class="text-secondary text-decoration-none small">
                            <i class="fa-solid fa-house me-1"></i> Volver al Inicio
                        </a>
                    </div>
                </div>

                <div class="text-center mt-3 text-muted x-small">
                    &copy; 2026 Sistema de Gestión Avanzado
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <?php if(isset($_SESSION['error_login'])): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Acceso Denegado',
            text: '<?= $_SESSION['error_login'] ?>',
            confirmButtonColor: '#0061a4',
            background: '#f0f7ff',
            backdrop: `rgba(0,0,123,0.1)`
        });
    </script>
    <?php unset($_SESSION['error_login']); endif; ?>

    <?php if(isset($_SESSION['register']) && $_SESSION['register'] == 'complete'): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: '¡Registro Exitoso!',
            text: 'Ya puedes iniciar sesión con tu cuenta.',
            confirmButtonColor: '#0061a4'
        });
    </script>
    <?php unset($_SESSION['register']); endif; ?>

</body>
</html>