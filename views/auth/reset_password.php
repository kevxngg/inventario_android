<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña | SICOT ERP</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url ?>assets/css/android-theme.css">
    <style>
        .auth-card { max-width: 450px; width: 100%; }
    </style>
</head>
<body class="android-bg d-flex align-items-center justify-content-center" style="min-height: 100vh;">

    <div class="auth-card p-3">
        <div class="glass-card p-5 shadow-lg border-0 fade-in-up">
            
            <div class="text-center mb-4">
                <div class="btn-icon mx-auto mb-3" style="width: 70px; height: 70px; font-size: 1.8rem; background: var(--success); color: white;">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <h4 class="fw-bold text-dark mb-1">Crear Nueva Contraseña</h4>
                <p class="text-secondary small fw-bold mt-2">Identidad confirmada. Por favor define tu nueva clave de acceso seguro.</p>
            </div>

            <form action="<?= base_url ?>Auth/updateNewPassword" method="POST" id="resetForm">
                
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">NUEVA CONTRASEÑA</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0 text-success"><i class="fa-solid fa-asterisk"></i></span>
                        <input type="password" name="password" id="pass1" class="form-control border-start-0 py-2" placeholder="Mínimo 6 caracteres" minlength="6" required autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-secondary">CONFIRMAR CONTRASEÑA</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0 text-success"><i class="fa-solid fa-check-double"></i></span>
                        <input type="password" id="pass2" class="form-control border-start-0 py-2" placeholder="Repite la contraseña" minlength="6" required>
                    </div>
                    <div id="error-msg" class="text-danger small fw-bold mt-2" style="display:none;"><i class="fa-solid fa-circle-xmark"></i> Las contraseñas no coinciden.</div>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-success btn-lg shadow-sm fw-bold rounded-pill">
                        Guardar y Entrar <i class="fa-solid fa-right-to-bracket ms-2"></i>
                    </button>
                </div>
            </form>
            
        </div>
    </div>

    <script>
        document.getElementById('resetForm').addEventListener('submit', function(e) {
            const p1 = document.getElementById('pass1').value;
            const p2 = document.getElementById('pass2').value;
            if (p1 !== p2) {
                e.preventDefault();
                document.getElementById('error-msg').style.display = 'block';
            }
        });
    </script>
</body>
</html>