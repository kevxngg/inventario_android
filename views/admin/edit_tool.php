<?php require_once 'views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary-dark">✏️ Editar Herramienta</h2>
    <a href="<?= base_url ?>Admin/tools" class="btn btn-outline-secondary rounded-pill">
        <i class="fa-solid fa-arrow-left me-2"></i> Volver
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="glass-card p-5 fade-in-up">
            
            <form action="<?= base_url ?>Admin/updateTool?id=<?= $tool->id ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $tool->id ?>">
                
                <div class="mb-4 text-center">
                    <img src="<?= base_url ?>assets/img/<?= $tool->image ?>" alt="Foto actual" class="rounded-3 shadow-sm mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                    <p class="text-muted small">Imagen Actual</p>
                </div>

                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label fw-bold">Nombre del Equipo</label>
                        <input type="text" name="name" class="form-control" value="<?= $tool->name ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Código / SKU (Opcional)</label>
                        <input type="text" name="sku" class="form-control" value="<?= $tool->sku ?? '' ?>" placeholder="EJ: TAL-001">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Categoría</label>
                        <select name="category" class="form-select">
                            <option value="MAQUINARIA_PESADA" <?= $tool->category == 'MAQUINARIA_PESADA' ? 'selected' : '' ?>>Maquinaria Pesada</option>
                            <option value="HERRAMIENTA_MANO" <?= $tool->category == 'HERRAMIENTA_MANO' ? 'selected' : '' ?>>Herramienta de Mano</option>
                            <option value="EQUIPO_SEGURIDAD" <?= $tool->category == 'EQUIPO_SEGURIDAD' ? 'selected' : '' ?>>Equipo de Seguridad</option>
                            <option value="VEHICULO" <?= $tool->category == 'VEHICULO' ? 'selected' : '' ?>>Vehículo</option>
                            <option value="OTROS" <?= $tool->category == 'OTROS' ? 'selected' : '' ?>>Otros</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Estado General</label>
                        <select name="status" class="form-select">
                            <option value="DISPONIBLE" <?= $tool->status == 'DISPONIBLE' ? 'selected' : '' ?>>🟢 Disponible</option>
                            <option value="EN_OBRA" <?= $tool->status == 'EN_OBRA' ? 'selected' : '' ?>>🔵 En Obra</option>
                            <option value="MANTENIMIENTO" <?= $tool->status == 'MANTENIMIENTO' ? 'selected' : '' ?>>🟠 Mantenimiento</option>
                            <option value="AGOTADO" <?= $tool->status == 'AGOTADO' ? 'selected' : '' ?>>🔴 Agotado</option>
                        </select>
                    </div>
                </div>

                <hr class="my-4">
                <h6 class="text-primary fw-bold mb-3"><i class="fa-solid fa-boxes-stacked me-2"></i>Control de Inventario</h6>

                <div class="row bg-light p-3 rounded mb-4">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold small text-muted">STOCK TOTAL FÍSICO</label>
                        <input type="number" 
                               name="stock_total" 
                               id="stock_total"
                               class="form-control fw-bold text-dark" 
                               value="<?= $tool->stock_total ?>" 
                               data-initial="<?= $tool->stock_total ?>"
                               required min="0">
                        <div class="form-text x-small">Modifica esto si compraste o perdiste unidades.</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold small text-muted">STOCK DISPONIBLE</label>
                        <input type="text" 
                               id="stock_available_visual"
                               class="form-control-plaintext fw-bold text-success" 
                               value="<?= $tool->stock_available ?>" 
                               data-initial="<?= $tool->stock_available ?>"
                               readonly>
                        <div class="form-text x-small">Calculado automáticamente.</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold small text-muted">ALERTA STOCK MÍNIMO</label>
                        <input type="number" name="stock_min" class="form-control text-danger fw-bold" value="<?= $tool->stock_min ?>" required min="0">
                        <div class="form-text x-small">Avisar si baja de esta cantidad.</div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Cambiar Imagen (Opcional)</label>
                    <input type="file" name="image" class="form-control">
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold">
                        <i class="fa-solid fa-save me-2"></i> Guardar Cambios
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

    // Guardamos los valores iniciales que vienen de la Base de Datos
    const initialTotal = parseInt(totalInput.getAttribute('data-initial')) || 0;
    const initialAvailable = parseInt(availableInput.getAttribute('data-initial')) || 0;

    // Escuchamos cuando el usuario escribe en "Stock Total"
    totalInput.addEventListener('input', function() {
        // 1. Obtenemos el nuevo valor que el usuario está escribiendo
        let newTotal = parseInt(this.value);
        if(isNaN(newTotal)) newTotal = 0;

        // 2. Calculamos la diferencia (¿Compró o perdió?)
        // Ejemplo: Tenía 30, puso 35. Diferencia = +5
        let difference = newTotal - initialTotal;

        // 3. Sumamos esa diferencia al disponible original
        // Ejemplo: Tenía 23 disponibles + 5 nuevos = 28 disponibles
        let newAvailable = initialAvailable + difference;

        // Evitamos negativos visuales
        if(newAvailable < 0) newAvailable = 0;

        // 4. Mostramos el resultado en pantalla inmediatamente
        availableInput.value = newAvailable;
        
        // Cambio de color visual
        if(newAvailable > initialAvailable) {
            availableInput.classList.remove('text-success', 'text-danger');
            availableInput.classList.add('text-primary'); // Azul si aumentó
        } else if (newAvailable < initialAvailable) {
            availableInput.classList.remove('text-success', 'text-primary');
            availableInput.classList.add('text-danger'); // Rojo si disminuyó
        } else {
            availableInput.classList.remove('text-primary', 'text-danger');
            availableInput.classList.add('text-success'); // Verde si es igual
        }
    });
});
</script>

<?php require_once 'views/layouts/footer.php'; ?>