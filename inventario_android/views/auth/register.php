<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | Constructora</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url ?>assets/css/android-theme.css">
</head>
<body class="android-bg d-flex align-items-center justify-content-center" style="min-height: 100vh;">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                
                <div class="glass-card p-4 fade-in-up">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold text-primary-dark">Crear Cuenta</h3>
                        <p class="text-muted small">Únete para gestionar tus obras y equipos</p>
                    </div>

                    <form action="<?= base_url ?>Auth/save" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Nombre Completo</label>
                            <div class="input-group input-group-android">
                                <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                                <input type="text" name="fullname" class="form-control" placeholder="Ej: Kevin Desarrollador" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Nombre de tu Constructora / Empresa</label>
                            <div class="input-group input-group-android">
                                <span class="input-group-text"><i class="fa-solid fa-helmet-safety"></i></span>
                                <input type="text" name="company_name" class="form-control" placeholder="Ej: Construcciones SAS">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Correo Electrónico</label>
                            <div class="input-group input-group-android">
                                <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="correo@ejemplo.com" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Contraseña</label>
                            <div class="input-group input-group-android">
                                <span class="input-group-text"><i class="fa-solid fa-key"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="Crea una contraseña segura" required minlength="6">
                            </div>
                            <div class="form-text x-small ps-2">Mínimo 6 caracteres</div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-android btn-lg shadow-sm">
                                Registrarse <i class="fa-solid fa-check-circle ms-2"></i>
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <span class="text-muted small">¿Ya tienes cuenta?</span>
                        <a href="<?= base_url ?>Auth/login" class="fw-bold text-primary-dark text-decoration-none">Inicia Sesión</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>