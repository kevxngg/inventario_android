<?php require_once 'views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold text-dark"><i class="fa-solid fa-file-pen me-2 text-primary"></i>Actualización de Registro de Activo</h2>
        <p class="text-muted mb-0">Modificación de parámetros técnicos y control de stock maestro.</p>
    </div>
    <a href="<?= base_url ?>Admin/tools" class="btn btn-outline-secondary fw-bold rounded-pill shadow-sm">
        <i class="fa-solid fa-chevron-left me-2"></i> Retornar al Inventario
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="glass-card p-4 border-0 shadow-sm fade-in-up">
            
            <form action="<?= base_url ?>Admin/updateTool?id=<?= $tool->id ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $tool->id ?>">
                
                <div class="row g-4 align-items-center mb-5 pb-4 border-bottom">
                    <div class="col-md-3 text-center">
                        <label class="form-label d-block small fw-bold text-secondary text-uppercase mb-3">Soporte Visual Actual</label>
                        <div class="position-relative d-inline-block">
                            <img src="<?= base_url ?>assets/img/<?= $tool->image ?>" alt="Ficha técnica" class="rounded shadow-sm border p-1 bg-white" style="width: 140px; height: 140px; object-fit: cover;">
                            <div class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 shadow" title="Activo Registrado">
                                <i class="fa-solid fa-check fs-6"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-secondary text-uppercase">Referencia / Nombre del Equipo</label>
                                <input type="text" name="name" class="form-control form-control-lg fw-bold border-secondary" value="<?= $tool->name ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary text-uppercase">Clasificación Técnica</label>
                                <select name="category" class="form-select border-secondary">
                                    <option value="MAQUINARIA_PESADA" <?= $tool->category == 'MAQUINARIA_PESADA' ? 'selected' : '' ?>>Maquinaria Pesada</option>
                                    <option value="HERRAMIENTA_MANO" <?= $tool->category == 'HERRAMIENTA_MANO' ? 'selected' : '' ?>>Herramienta de Mano</option>
                                    <option value="EQUIPO_SEGURIDAD" <?= $tool->category == 'EQUIPO_SEGURIDAD' ? 'selected' : '' ?>>Equipo de Seguridad Industrial</option>
                                    <option value="VEHICULO" <?= $tool->category == 'VEHICULO' ? 'selected' : '' ?>>Vehículo Operativo</option>
                                    <option value="OTROS" <?= $tool->category == 'OTROS' ? 'selected' : '' ?>>Otros Activos</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary text-uppercase">Estado Operativo Actual</label>
                                <select name="status" class="form-select border-secondary fw-bold">
                                    <option value="DISPONIBLE" <?= $tool->status == 'DISPONIBLE' ? 'selected' : '' ?>>DISPONIBLE (EN BODEGA)</option>
                                    <option value="EN_OBRA" <?= $tool->status == 'EN_OBRA' ? 'selected' : '' ?>>EN OPERACIÓN (OBRA)</option>
                                    <option value="MANTENIMIENTO" <?= $tool->status == 'MANTENIMIENTO' ? 'selected' : '' ?>>MANTENIMIENTO (TALLER)</option>
                                    <option value="AGOTADO" <?= $tool->status == 'AGOTADO' ? 'selected' : '' ?>>AGOTADO / SIN EXISTENCIAS</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-light p-4 rounded border mb-5">
                    <h6 class="fw-bold text-dark mb-4 text-uppercase" style="letter-spacing: 1px;"><i class="fa-solid fa-calculator me-2 text-primary"></i>Gestión de Disponibilidad y Stock</h6>
                    
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Inventario Físico Total</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fa-solid fa-warehouse"></i></span>
                                <input type="number" name="stock_total" id="stock_total" class="form-control fw-bold fs-5 text-center" 
                                       value="<?= $tool->stock_total ?>" data-initial="<?= $tool->stock_total ?>" required min="0">
                            </div>
                            <div class="form-text x-small fw-bold">Unidades totales propiedad de la empresa.</div>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Disponibilidad en Tiempo Real</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fa-solid fa-truck-ramp-box"></i></span>
                                <input type="text" id="stock_available_visual" class="form-control fw-bold fs-5 text-center text-success bg-white" 
                                       value="<?= $tool->stock_available ?>" data-initial="<?= $tool->stock_available ?>" readonly>
                            </div>
                            <div class="form-text x-small fw-bold">Cálculo de unidades listas para despacho.</div>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Alerta de Reabastecimiento</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fa-solid fa-triangle-exclamation text-danger"></i></span>
                                <input type="number" name="stock_min" class="form-control fw-bold fs-5 text-center text-danger" 
                                       value="<?= $tool->stock_min ?>" required min="0">
                            </div>
                            <div class="form-text x-small fw-bold">Notificar cuando el stock sea inferior a esta cifra.</div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-secondary text-uppercase">Actualizar Soporte Fotográfico</label>
                    <input type="file" name="image" class="form-control shadow-sm" accept="image/*">
                    <small class="text-muted mt-2 d-block fw-semibold">Seleccione un nuevo archivo solo si desea sustituir la imagen actual.</small>
                </div>

                <div class="d-grid pt-3 border-top">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm py-3">
                        <i class="fa-solid fa-cloud-arrow-up me-2"></i> Sincronizar Cambios con el Servidor
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const totalInput = document.getElementById('stock_total');
    const availableInput = document.getElementById('stock_available_visual');

    const initialTotal = parseInt(totalInput.getAttribute('data-initial')) || 0;
    const initialAvailable = parseInt(availableInput.getAttribute('data-initial')) || 0;

    totalInput.addEventListener('input', function() {
        let newTotal = parseInt(this.value);
        if(isNaN(newTotal)) newTotal = 0;

        let difference = newTotal - initialTotal;
        let newAvailable = initialAvailable + difference;

        if(newAvailable < 0) newAvailable = 0;

        availableInput.value = newAvailable;
        
        availableInput.classList.remove('text-success', 'text-danger', 'text-primary');
        if(newAvailable > initialAvailable) {
            availableInput.classList.add('text-primary'); 
        } else if (newAvailable < initialAvailable) {
            availableInput.classList.add('text-danger'); 
        } else {
            availableInput.classList.add('text-success'); 
        }
    });
});
</script>

<?php require_once 'views/layouts/footer.php'; ?>