<?php require_once 'views/layouts/header.php'; ?>

<div class="container p-4">
    
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <div class="text-center mb-4">
                <h2 class="fw-bold text-danger">Reportar Incidente</h2>
                <p class="text-muted">Informa sobre daños, pérdidas o fallas en maquinaria.</p>
            </div>

            <div class="glass-card p-5 border-danger border-top border-4 shadow-lg">
                
                <form action="<?= base_url ?>User/saveReport" method="POST">
                    <input type="hidden" name="type" value="REPORTE_DAÑO">
                    
                    <div class="text-center mb-4">
                        <div class="bg-danger-light text-danger rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 70px; height: 70px; background-color: #ffe6e6;">
                            <i class="fa-solid fa-triangle-exclamation fa-2x"></i>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-secondary">DETALLE DEL PROBLEMA</label>
                        <textarea name="description" class="form-control form-control-lg bg-light border-0" rows="5" placeholder="Describe qué equipo falló, ubicación y situación actual..." required style="resize: none;"></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger btn-lg rounded-pill fw-bold shadow-sm hover-elevate">
                            <i class="fa-solid fa-paper-plane me-2"></i> Enviar Reporte
                        </button>
                        <a href="<?= base_url ?>User/panel" class="btn btn-outline-secondary rounded-pill border-0">
                            Cancelar
                        </a>
                    </div>
                </form>

            </div>
            
            <div class="text-center mt-4 text-muted small">
                <i class="fa-solid fa-info-circle me-1"></i> El administrador recibirá una notificación inmediata.
            </div>

        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>