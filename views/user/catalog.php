<?php require_once 'views/layouts/header.php'; ?>

<?php 
// Preparación de las opciones de Proyectos/Obras para el menú desplegable
$optionsHtml = '';
if(isset($obras) && $obras->num_rows > 0) {
    while($obra = $obras->fetch_object()){
        if($obra->status == 'EN_EJECUCION' || $obra->status == 'PLANIFICACION') {
            $optionsHtml .= '<option value="'.$obra->id.'">'.htmlspecialchars($obra->name, ENT_QUOTES, 'UTF-8').'</option>';
        }
    }
}
if($optionsHtml == ''){
    $optionsHtml = '<option value="" disabled>No hay frentes de obra activos en el sistema</option>';
}

// Configurar fecha mínima (hoy) para los inputs de fecha
$today = date('Y-m-d');
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold text-primary-dark"><i class="fa-solid fa-list-check me-2"></i>Catálogo de Activos</h2>
        <p class="text-muted mb-0">Selecciona las herramientas y agrégalas a tu orden de pedido logística.</p>
    </div>
    
    <div class="search-box glass-card py-2 px-3 d-flex align-items-center">
        <i class="fa-solid fa-magnifying-glass text-muted me-2"></i>
        <input type="text" id="searchInput" class="form-control border-0 bg-transparent shadow-none" placeholder="Buscar maquinaria..." style="min-width: 250px;" onkeyup="filterCatalog()">
    </div>
</div>

<div class="row g-4" id="catalogGrid">
    <?php if(isset($disponibles) && $disponibles->num_rows > 0): ?>
        <?php while($tool = $disponibles->fetch_object()): ?>
            
            <?php 
                $stock = (int)$tool->stock_available;
                $isAgotado = ($stock <= 0 || $tool->status == 'AGOTADO');
                
                $badgeClass = 'bg-success';
                $badgeText = 'Disponible';
                $cardOpacity = '1';
                $grayscale = 'none';
                $btnClass = 'btn-android';
                $btnText = 'Agregar a la Orden';
                $btnDisabled = '';

                if($stock > 0 && $stock <= 5){
                    $badgeClass = 'bg-warning text-dark';
                    $badgeText = 'Inventario Crítico';
                }

                if($isAgotado){
                    $badgeClass = 'bg-secondary';
                    $badgeText = 'Sin Existencias';
                    $cardOpacity = '0.75'; 
                    $grayscale = 'grayscale(100%)'; 
                    $btnClass = 'btn-secondary';
                    $btnText = 'No Disponible';
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
                            <button class="btn <?= $btnClass ?> btn-sm shadow-sm fw-bold" 
                                    <?= $btnDisabled ?>
                                    onclick="addToCart(<?= $tool->id ?>, '<?= addslashes($tool->name) ?>', <?= $stock ?>, '<?= $tool->image ?>')">
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
                <i class="fa-solid fa-server text-muted fa-3x mb-3"></i>
                <h4 class="text-muted">No existen registros de activos en la base de datos.</h4>
            </div>
        </div>
    <?php endif; ?>
</div>

<button class="btn btn-primary shadow-lg position-fixed d-flex align-items-center justify-content-center" 
        style="bottom: 30px; right: 30px; width: 65px; height: 65px; border-radius: 50%; z-index: 1000;"
        data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas">
    <i class="fa-solid fa-file-invoice fs-4"></i>
    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" id="cartCount" style="font-size: 0.8rem;">
        0
    </span>
</button>

<div class="offcanvas offcanvas-end shadow" tabindex="-1" id="cartOffcanvas" style="width: 450px;">
    <div class="offcanvas-header bg-primary text-white border-bottom shadow-sm">
        <h5 class="offcanvas-title fw-bold"><i class="fa-solid fa-truck-fast me-2"></i> Orden de Despacho</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    
    <div class="offcanvas-body d-flex flex-column bg-light p-0">
        
        <ul class="nav nav-tabs bg-white px-3 pt-2 shadow-sm" id="cartTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold text-primary" id="items-tab" data-bs-toggle="tab" data-bs-target="#items-pane" type="button" role="tab">1. Artículos</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-secondary" id="logistics-tab" data-bs-toggle="tab" data-bs-target="#logistics-pane" type="button" role="tab">2. Logística</button>
            </li>
        </ul>

        <div class="tab-content flex-grow-1 overflow-auto" id="cartTabsContent">
            
            <div class="tab-pane fade show active p-3" id="items-pane" role="tabpanel">
                <div id="cartItemsContainer">
                    </div>
            </div>

            <div class="tab-pane fade p-4" id="logistics-pane" role="tabpanel">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">FRENTE DE OBRA (DESTINO) <span class="text-danger">*</span></label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-primary"><i class="fa-solid fa-location-dot text-primary"></i></span>
                        <select id="cartProjectInput" class="form-select border-primary" required>
                            <option value="" disabled selected>Seleccione destino...</option>
                            <?= $optionsHtml ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">FECHA REQUERIDA (ENTREGA) <span class="text-danger">*</span></label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-primary"><i class="fa-solid fa-calendar-check text-primary"></i></span>
                        <input type="date" id="expectedDate" class="form-control border-primary" min="<?= $today ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">FECHA ESTIMADA DE DEVOLUCIÓN</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white"><i class="fa-solid fa-calendar-xmark text-secondary"></i></span>
                        <input type="date" id="returnDate" class="form-control" min="<?= $today ?>">
                    </div>
                    <small class="text-muted" style="font-size: 0.70rem;">Opcional. Ayuda a planificar el stock futuro.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">OBSERVACIONES / NOTAS DE DESPACHO</label>
                    <textarea id="orderNotes" class="form-control shadow-sm" rows="3" placeholder="Ej: Favor incluir extensiones eléctricas, enviar con conductor Juan..."></textarea>
                </div>
            </div>
        </div>

        <div class="bg-white p-3 border-top shadow-lg z-1">
            <button class="btn btn-primary w-100 fw-bold py-2 shadow-sm rounded-pill mb-2" onclick="submitCart()" id="btnSubmitCart">
                <i class="fa-solid fa-file-signature me-2"></i> Confirmar y Enviar Orden
            </button>
            <button class="btn btn-outline-danger w-100 btn-sm rounded-pill fw-bold" onclick="clearCart()">
                <i class="fa-solid fa-trash me-1"></i> Vaciar Orden
            </button>
        </div>
    </div>
</div>

<script>
// --- LÓGICA DEL CARRITO PROFESIONAL ---
let cart = JSON.parse(localStorage.getItem('sicot_cart')) || [];

document.addEventListener('DOMContentLoaded', updateCartUI);

function addToCart(id, name, maxStock, image) {
    let existingItem = cart.find(item => item.id === id);
    if (existingItem) {
        if(existingItem.qty < maxStock) {
            existingItem.qty++;
            Swal.fire({ toast:true, position:'top-end', icon:'success', title:'Cantidad actualizada', showConfirmButton:false, timer:2000 });
        } else {
            Swal.fire({ toast:true, position:'top-end', icon:'warning', title:'No hay más stock', showConfirmButton:false, timer:2500 });
        }
    } else {
        cart.push({ id: id, name: name, maxStock: maxStock, image: image, qty: 1 });
        Swal.fire({ toast:true, position:'top-end', icon:'success', title:'Añadido a la orden', showConfirmButton:false, timer:2000 });
    }
    saveCart();
}

function updateItemQty(id, change) {
    let item = cart.find(i => i.id === id);
    if(item) {
        let newQty = item.qty + change;
        if(newQty > 0 && newQty <= item.maxStock) {
            item.qty = newQty;
            saveCart();
        } else if (newQty === 0) { removeItem(id); }
    }
}

function removeItem(id) {
    cart = cart.filter(item => item.id !== id);
    saveCart();
}

function clearCart() {
    cart = [];
    document.getElementById('cartProjectInput').value = '';
    document.getElementById('expectedDate').value = '';
    document.getElementById('returnDate').value = '';
    document.getElementById('orderNotes').value = '';
    saveCart();
}

function saveCart() {
    localStorage.setItem('sicot_cart', JSON.stringify(cart));
    updateCartUI();
}

function updateCartUI() {
    const totalItems = cart.reduce((sum, item) => sum + item.qty, 0);
    document.getElementById('cartCount').innerText = totalItems;
    
    const container = document.getElementById('cartItemsContainer');
    container.innerHTML = '';
    
    if(cart.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted mt-5 py-5">
                <i class="fa-solid fa-box-open fa-3x mb-3 opacity-25"></i>
                <h6>La orden está vacía</h6>
                <p class="small">Añade equipos desde el catálogo.</p>
            </div>`;
        document.getElementById('btnSubmitCart').disabled = true;
        return;
    }

    document.getElementById('btnSubmitCart').disabled = false;

    cart.forEach(item => {
        container.innerHTML += `
            <div class="card border-0 shadow-sm mb-3 rounded-3 border-start border-primary border-4">
                <div class="card-body p-2 d-flex align-items-center">
                    <img src="<?= base_url ?>assets/img/${item.image}" alt="" style="width: 50px; height: 50px; object-fit: contain;" class="bg-light rounded p-1 me-3">
                    <div class="flex-grow-1">
                        <div class="fw-bold text-dark mb-2" style="font-size: 0.85rem; line-height:1.2;">${item.name}</div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center border rounded bg-light px-2 py-1">
                                <button class="btn btn-sm btn-link text-danger p-0" onclick="updateItemQty(${item.id}, -1)"><i class="fa-solid fa-minus"></i></button>
                                <span class="mx-3 fw-bold small">${item.qty}</span>
                                <button class="btn btn-sm btn-link text-success p-0" onclick="updateItemQty(${item.id}, 1)"><i class="fa-solid fa-plus"></i></button>
                            </div>
                            <button class="btn btn-sm btn-outline-danger border-0" onclick="removeItem(${item.id})"><i class="fa-regular fa-trash-can"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
}

function submitCart() {
    if(cart.length === 0) return;

    const projectId = document.getElementById('cartProjectInput').value;
    const expectedDate = document.getElementById('expectedDate').value;
    const returnDate = document.getElementById('returnDate').value;
    const orderNotes = document.getElementById('orderNotes').value;
    
    // Validaciones Logísticas
    if(!projectId) {
        Swal.fire('Atención', 'Debes seleccionar la obra destino en la pestaña Logística.', 'warning');
        const triggerEl = document.querySelector('#logistics-tab');
        bootstrap.Tab.getOrCreateInstance(triggerEl).show();
        return;
    }
    if(!expectedDate) {
        Swal.fire('Atención', 'Debes indicar la fecha en que necesitas los equipos.', 'warning');
        const triggerEl = document.querySelector('#logistics-tab');
        bootstrap.Tab.getOrCreateInstance(triggerEl).show();
        return;
    }
    if(returnDate && returnDate < expectedDate) {
        Swal.fire('Error en Fechas', 'La fecha de devolución no puede ser anterior a la fecha de entrega.', 'error');
        return;
    }

    Swal.fire({
        title: 'Procesando Orden',
        text: 'Enviando formulario de despacho al Administrador...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    const payload = {
        cart: cart,
        project_id: projectId,
        expected_date: expectedDate,
        return_date: returnDate,
        order_notes: orderNotes
    };

    fetch('<?= base_url ?>User/requestCart', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            clearCart();
            Swal.fire({
                title: 'Orden Generada', 
                text: data.msg, 
                icon: 'success',
                confirmButtonColor: '#004b87'
            }).then(() => location.reload());
        } else { Swal.fire('Error Operativo', data.msg, 'error'); }
    }).catch(error => {
        Swal.fire('Fallo de Red', 'No se pudo enviar la orden logística.', 'error');
    });
}

// Filtro de búsqueda visual
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