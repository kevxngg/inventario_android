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
                
                <div class="mb-4 text-center">
                    <img src="<?= base_url ?>assets/img/<?= $tool->image ?>" alt="Foto actual" class="rounded-3 shadow-sm mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                    <p class="text-muted small">Imagen Actual</p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Nombre del Equipo</label>
                    <input type="text" name="name" class="form-control" value="<?= $tool->name ?>" required>
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
                        <label class="form-label fw-bold">Estado Actual</label>
                        <select name="status" class="form-select">
                            <option value="DISPONIBLE" <?= $tool->status == 'DISPONIBLE' ? 'selected' : '' ?>>🟢 Disponible</option>
                            <option value="EN_OBRA" <?= $tool->status == 'EN_OBRA' ? 'selected' : '' ?>>🔵 En Obra</option>
                            <option value="MANTENIMIENTO" <?= $tool->status == 'MANTENIMIENTO' ? 'selected' : '' ?>>🟠 Mantenimiento</option>
                        </select>
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

<?php require_once 'views/layouts/footer.php'; ?>