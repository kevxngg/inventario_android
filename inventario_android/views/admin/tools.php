<?php require_once 'views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary-dark">📦 Gestión de Inventario</h2>
    <button class="btn btn-android" data-bs-toggle="modal" data-bs-target="#modalTool">
        <i class="fa-solid fa-plus me-2"></i> Nuevo Ítem
    </button>
</div>

<div class="glass-card p-4">
    <table class="table table-hover align-middle" id="toolsTable">
        <thead class="bg-light">
            <tr>
                <th>Foto</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($tool = $herramientas->fetch_object()): ?>
            <tr>
                <td>
                    <img src="<?= base_url ?>assets/img/<?= $tool->image ?>" class="rounded-3" width="50" height="50" style="object-fit:cover;">
                </td>
                <td>
                    <div class="fw-bold"><?= $tool->name ?></div>
                    <small class="text-muted">ID: #<?= $tool->id ?></small>
                </td>
                <td><span class="badge bg-light text-dark border"><?= $tool->category ?></span></td>
                <td>
                    <?php 
                        $badgeColor = 'bg-success';
                        if($tool->status == 'MANTENIMIENTO') $badgeColor = 'bg-warning text-dark';
                        if($tool->status == 'EN_OBRA') $badgeColor = 'bg-primary';
                    ?>
                    <span class="badge <?= $badgeColor ?> rounded-pill"><?= $tool->status ?></span>
                </td>
                <td>
                    <div class="d-flex gap-2">
                        <a href="<?= base_url ?>Admin/editTool?id=<?= $tool->id ?>" class="btn btn-sm btn-outline-primary rounded-circle" style="width:32px; height:32px; padding:0; display:flex; align-items:center; justify-content:center;">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        <a href="<?= base_url ?>Admin/deleteTool?id=<?= $tool->id ?>" class="btn btn-sm btn-outline-danger rounded-circle btn-delete" style="width:32px; height:32px; padding:0; display:flex; align-items:center; justify-content:center;">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalTool" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Registrar Nueva Maquinaria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url ?>Admin/saveTool" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre del Equipo</label>
                        <input type="text" name="name" class="form-control rounded-pill" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Categoría</label>
                            <select name="category" class="form-select rounded-pill">
                                <option value="MAQUINARIA_PESADA">Maquinaria Pesada</option>
                                <option value="HERRAMIENTA_MANO">Herramienta de Mano</option>
                                <option value="EQUIPO_SEGURIDAD">Equipo de Seguridad</option>
                                <option value="VEHICULO">Vehículo</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estado Inicial</label>
                            <select name="status" class="form-select rounded-pill">
                                <option value="DISPONIBLE">Disponible</option>
                                <option value="MANTENIMIENTO">En Mantenimiento</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto (Opcional)</label>
                        <input type="file" name="image" class="form-control rounded-pill">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-android w-100">Guardar en Sistema</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>