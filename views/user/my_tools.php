<?php require_once 'views/layouts/header.php'; ?>

<style>
    .inventory-card {
        background-color: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .inventory-card:hover {
        box-shadow: 0 10px 25px rgba(0, 75, 135, 0.1);
        border-color: #004b87;
        transform: translateY(-5px);
    }
    .img-wrapper {
        height: 180px;
        background-color: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        border-bottom: 1px solid #e2e8f0;
        padding: 15px;
    }
    .img-wrapper img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
    }
    .card-meta {
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }
</style>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary pb-3 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold text-dark"><i class="fa-solid fa-toolbox me-2 text-primary"></i>Inventario a mi Cargo</h2>
            <p class="text-muted mb-0">Listado de activos y maquinaria bajo su responsabilidad operativa en terreno.</p>
        </div>
    </div>

    <div class="row g-4">
        <?php if(isset($misHerramientas) && $misHerramientas->num_rows > 0): ?>
            <?php while($item = $misHerramientas->fetch_object()): ?>
                
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="inventory-card shadow-sm">
                        <div class="img-wrapper position-relative">
                            <span class="position-absolute top-0 start-0 m-2 badge bg-dark opacity-75">
                                REF-<?= str_pad($item->tool_id, 4, '0', STR_PAD_LEFT) ?>
                            </span>
                            
                            <?php $imgSrc = !empty($item->image) ? $item->image : 'default.png'; ?>
                            <img src="<?= base_url ?>assets/img/<?= htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($item->tool_name, ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        
                        <div class="p-4 flex-grow-1 d-flex flex-column">
                            <div class="card-meta mb-1"><?= str_replace('_', ' ', htmlspecialchars($item->category, ENT_QUOTES, 'UTF-8')) ?></div>
                            <h5 class="fw-bold text-dark mb-3"><?= htmlspecialchars($item->tool_name, ENT_QUOTES, 'UTF-8') ?></h5>
                            
                            <ul class="list-unstyled small text-muted mb-4 flex-grow-1">
                                <li class="mb-2">
                                    <i class="fa-solid fa-layer-group text-primary me-2"></i> 
                                    <strong>Cantidad Asignada:</strong> <?= $item->quantity ?> Unidad(es)
                                </li>
                                <li class="mb-2">
                                    <i class="fa-solid fa-map-pin text-danger me-2"></i> 
                                    <strong>Destino:</strong> <?= htmlspecialchars($item->project_name ?? 'Bodega / General', ENT_QUOTES, 'UTF-8') ?>
                                </li>
                                <li>
                                    <i class="fa-regular fa-calendar text-secondary me-2"></i> 
                                    <strong>Entregado el:</strong> <?= date('d/m/Y', strtotime($item->assigned_at)) ?>
                                </li>
                            </ul>
                            
                            <button class="btn btn-outline-primary fw-bold w-100 mt-auto" onclick="solicitarDevolucion(<?= $item->id ?>, '<?= htmlspecialchars(addslashes($item->tool_name), ENT_QUOTES, 'UTF-8') ?>')">
                                <i class="fa-solid fa-right-left me-1"></i> Entregar a Bodega
                            </button>
                        </div>
                    </div>
                </div>

            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="bg-white p-5 rounded-4 border shadow-sm" style="max-width: 500px; margin: 0 auto;">
                    <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
                        <i class="fa-solid fa-hands-bubbles fa-3x text-secondary"></i>
                    </div>
                    <h4 class="fw-bold text-dark">Libre de Responsabilidades</h4>
                    <p class="text-muted mb-4">Actualmente no tienes ninguna herramienta o maquinaria asignada a tu nombre en las obras.</p>
                    <a href="<?= base_url ?>User/catalog" class="btn btn-primary fw-bold px-4 rounded-pill">
                        <i class="fa-solid fa-list-check me-2"></i>Ir al Catálogo de Activos
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function solicitarDevolucion(assignmentId, toolName) {
        Swal.fire({
            title: '¿Confirmar Retorno?',
            html: `Está a punto de notificar la entrega de <strong>${toolName}</strong>.<br><br><small class="text-muted">El administrador deberá confirmar el estado físico (Check-In) en bodega para liberar su responsabilidad en el sistema.</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#004b87',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, notificar entrega',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Petición AJAX segura hacia el controlador
                $.post('<?= base_url ?>User/initiateReturn', { assignment_id: assignmentId }, function(response) {
                    if(response.status === 'success') {
                        Swal.fire({
                            title: 'Notificación Enviada',
                            text: response.msg,
                            icon: 'success',
                            confirmButtonColor: '#004b87'
                        }).then(() => {
                            // Recargar la página para actualizar la vista
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error de Procesamiento',
                            text: response.msg,
                            icon: 'error',
                            confirmButtonColor: '#004b87'
                        });
                    }
                }, 'json').fail(function() {
                    // Manejo de errores de conexión HTTP
                    Swal.fire({
                        title: 'Fallo de Conexión',
                        text: 'No se pudo contactar con el servidor. Revise su conexión a internet e intente nuevamente.',
                        icon: 'error',
                        confirmButtonColor: '#004b87'
                    });
                });
            }
        });
    }
</script>

<?php require_once 'views/layouts/footer.php'; ?>