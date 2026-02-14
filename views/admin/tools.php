<?php require_once 'views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary-dark">📦 Gestión de Inventario</h2>
    <button class="btn btn-android" data-bs-toggle="modal" data-bs-target="#modalTool">
        <i class="fa-solid fa-plus me-2"></i> Nuevo Ítem
    </button>
</div>

<div class="glass-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle" id="toolsTable">
            <thead class="bg-light">
                <tr>
                    <th style="width: 80px;">Foto</th>
                    <th>Nombre / Categoría</th>
                    <th class="text-center">Stock</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($herramientas)): ?>
                    <?php while($tool = $herramientas->fetch_object()): ?>
                    <tr>
                        <td>
                            <img src="<?= base_url ?>assets/img/<?= $tool->image ?>" class="rounded-3 border" width="50" height="50" style="object-fit:cover;">
                        </td>
                        <td>
                            <div class="fw-bold text-dark"><?= $tool->name ?></div>
                            <span class="badge bg-light text-secondary border mt-1"><?= str_replace('_', ' ', $tool->category) ?></span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex flex-column align-items-center">
                                <span class="fw-bold h5 mb-0 <?= $tool->stock_available <= $tool->stock_min ? 'text-danger' : 'text-success' ?>">
                                    <?= $tool->stock_available ?>
                                </span>
                                <small class="text-muted x-small">de <?= $tool->stock_total ?> total</small>
                            </div>
                        </td>
                        <td>
                            <?php 
                                $badgeClass = 'bg-success';
                                if($tool->status == 'AGOTADO') $badgeClass = 'bg-secondary'; // Gris para agotado
                                if($tool->status == 'MANTENIMIENTO') $badgeClass = 'bg-warning text-dark';
                                if($tool->status == 'EN_OBRA') $badgeClass = 'bg-primary';
                            ?>
                            <span class="badge <?= $badgeClass ?> rounded-pill"><?= $tool->status ?></span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="<?= base_url ?>Admin/editTool?id=<?= $tool->id ?>" class="btn btn-sm btn-outline-primary rounded-circle" title="Editar">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a href="<?= base_url ?>Admin/deleteTool?id=<?= $tool->id ?>" class="btn btn-sm btn-outline-danger rounded-circle btn-delete" title="Eliminar" onclick="return confirm('¿Eliminar este ítem y su historial?');">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalTool" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Registrar Nueva Maquinaria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url ?>Admin/saveTool" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">NOMBRE DEL EQUIPO</label>
                        <input type="text" name="name" class="form-control rounded-pill" placeholder="Ej: Taladro Percutor Industrial" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-muted">CATEGORÍA</label>
                            <select name="category" class="form-select rounded-pill">
                                <option value="MAQUINARIA_PESADA">Maquinaria Pesada</option>
                                <option value="HERRAMIENTA_MANO">Herramienta de Mano</option>
                                <option value="EQUIPO_SEGURIDAD">Equipo de Seguridad</option>
                                <option value="VEHICULO">Vehículo</option>
                                <option value="OTROS">Otros</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-muted">ESTADO INICIAL</label>
                            <select name="status" class="form-select rounded-pill">
                                <option value="DISPONIBLE">🟢 Disponible</option>
                                <option value="EN_OBRA">🔵 En Obra</option>
                                <option value="MANTENIMIENTO">🟠 En Mantenimiento</option>
                                <option value="AGOTADO">🔴 Agotado</option>
                            </select>
                        </div>
                    </div>

                    <div class="row bg-light p-3 rounded mb-3 mx-1">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-primary">CANTIDAD INICIAL</label>
                            <input type="number" name="stock_total" class="form-control fw-bold border-primary" value="1" min="1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small text-danger">ALERTA MÍNIMA</label>
                            <input type="number" name="stock_min" class="form-control fw-bold" value="5" min="0" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">FOTO (OPCIONAL)</label>
                        <input type="file" name="image" class="form-control rounded-pill">
                    </div>

                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-android rounded-pill px-4">Guardar en Sistema</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>