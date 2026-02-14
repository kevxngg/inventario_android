<?php require_once 'views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold text-primary-dark">🛒 Catálogo de Maquinaria</h2>
        <p class="text-muted mb-0">Solicita equipos disponibles para tus obras.</p>
    </div>
    
    <div class="search-box glass-card py-2 px-3 d-flex align-items-center">
        <i class="fa-solid fa-magnifying-glass text-muted me-2"></i>
        <input type="text" id="searchInput" class="form-control border-0 bg-transparent shadow-none" placeholder="Filtrar por nombre..." style="min-width: 250px;" onkeyup="filterCatalog()">
    </div>
</div>

<div class="row g-4" id="catalogGrid">
    <?php if(isset($disponibles) && $disponibles->num_rows > 0): ?>
        <?php while($tool = $disponibles->fetch_object()): ?>
            
            <?php 
                // LÓGICA VISUAL
                $stock = (int)$tool->stock_available;
                
                // Determinamos si está agotado (Stock 0 o Estado explícito)
                $isAgotado = ($stock <= 0 || $tool->status == 'AGOTADO');
                
                // Configuración Visual por defecto
                $badgeClass = 'bg-success';
                $badgeText = 'Disponible';
                $cardOpacity = '1';
                $grayscale = 'none';
                $btnClass = 'btn-android';
                $btnText = 'Solicitar';
                $btnDisabled = '';

                // Si hay pocas unidades (Alerta Amarilla)
                if($stock > 0 && $stock <= 5){
                    $badgeClass = 'bg-warning text-dark';
                    $badgeText = 'Pocas Unidades';
                }

                // SI ESTÁ AGOTADO (ESTA ES LA PARTE IMPORTANTE)
                if($isAgotado){
                    $badgeClass = 'bg-secondary';
                    $badgeText = 'Agotado';
                    $cardOpacity = '0.75'; // Opacidad para indicar inactividad
                    $grayscale = 'grayscale(100%)'; // Foto en blanco y negro
                    $btnClass = 'btn-secondary';
                    $btnText = 'No disponible';
                    $btnDisabled = 'disabled style="cursor: not-allowed;"';
                }
            ?>

            <div class="col-sm-6 col-md-4 col-xl-3 fade-in-up tool-item" data-search="<?= strtolower($tool->name . ' ' . $tool->category) ?>">
                <div class="glass-card h-100 p-0 overflow-hidden tool-card shadow-sm hover-elevate d-flex flex-column" style="opacity: <?= $cardOpacity ?>;">
                    
                    <div class="position-relative bg-white text-center p-4 d-flex align-items-center justify-content-center" style="height: 200px;">
                        <img src="<?= base_url ?>assets/img/<?= $tool->image ?>" alt="<?= $tool->name ?>" class="img-fluid" style="max-height: 160px; object-fit: contain; filter: <?= $grayscale ?>;">
                        
                        <span class="badge <?= $badgeClass ?> position-absolute top-0 end-0 m-3 rounded-pill shadow-sm">
                            <?= $badgeText ?>
                        </span>

                        <span class="badge bg-light text-dark border position-absolute bottom-0 start-0 m-3 rounded-pill shadow-sm">
                            <b><?= $stock ?></b> uds.
                        </span>
                    </div>

                    <div class="p-4 d-flex flex-column flex-grow-1">
                        <div class="text-uppercase text-muted x-small fw-bold mb-1">
                            <?= str_replace('_', ' ', $tool->category) ?>
                        </div>
                        
                        <h5 class="fw-bold text-dark mb-2"><?= $tool->name ?></h5>
                        
                        <div class="mt-auto d-grid pt-3">
                            <button class="btn <?= $btnClass ?> btn-sm shadow-sm" 
                                    <?= $btnDisabled ?>
                                    onclick="solicitarEquipo(<?= $tool->id ?>, '<?= $tool->name ?>', <?= $stock ?>)">
                                <i class="fa-solid fa-cart-plus me-2"></i> <?= $btnText ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12 text-center py-5">
            <div class="glass-card p-5 d-inline-block">
                <i class="fa-solid fa-box-open text-muted fa-3x mb-3"></i>
                <h4 class="text-muted">No hay herramientas visibles en este momento.</h4>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function solicitarEquipo(id, nombre, stockMax) {
    // Doble validación visual por si acaso
    if(stockMax <= 0) return;

    Swal.fire({
        title: 'Solicitar ' + nombre,
        html: `
            <div class="text-start bg-light p-3 rounded mb-3 border">
                <small class="text-muted d-block fw-bold mb-1">DISPONIBLES</small>
                <span class="fs-4 fw-bold text-primary">${stockMax}</span> <small>unidades</small>
            </div>
            <label class="form-label fw-bold small text-secondary">CANTIDAD A PEDIR</label>
            <input type="number" id="qtyInput" class="form-control form-control-lg text-center fw-bold" 
                   min="1" max="${stockMax}" value="1">
        `,
        showCancelButton: true,
        confirmButtonColor: '#0061a4',
        confirmButtonText: 'Enviar Solicitud',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const qty = document.getElementById('qtyInput').value;
            if (!qty || qty < 1 || parseInt(qty) > stockMax) {
                Swal.showValidationMessage('Cantidad inválida o superior al stock');
                return false;
            }
            return { tool_id: id, quantity: parseInt(qty) };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('<?= base_url ?>User/requestTool', result.value, function(response){
                if(response.status === 'success'){
                    Swal.fire('Enviado', response.msg, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', response.msg, 'error');
                }
            }, 'json');
        }
    });
}

function filterCatalog() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const items = document.getElementsByClassName('tool-item');

    for (let i = 0; i < items.length; i++) {
        const txtValue = items[i].getAttribute('data-search');
        items[i].style.display = txtValue.includes(filter) ? "" : "none";
    }
}
</script>

<?php require_once 'views/layouts/footer.php'; ?>