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

<style>
    /* Diseño Industrial para el Catálogo */
    .tool-card {
        background-color: var(--white);
        border: 1px solid var(--border-color);
        border-radius: 8px; /* Menos redondeado, más rudo */
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
    }
    .tool-card:hover {
        border-color: var(--panel-dark);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.15);
    }
    .tool-image-container {
        background-color: #f8fafc;
        border-bottom: 2px solid var(--border-color);
        position: relative;
    }
    
    /* Input Search Fuerte */
    .search-box {
        background-color: var(--white);
        border: 2px solid var(--panel-dark);
        border-radius: 8px;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
    }
    .search-box input { font-weight: bold; color: var(--panel-darker); }
    .search-box input:focus { box-shadow: none; background: transparent; }
    
    /* Botón Flotante Carrito */
    .btn-cart-float {
        background-color: var(--panel-darker) !important;
        border: 2px solid var(--safety-orange) !important;
        color: white !important;
    }
    .btn-cart-float:hover { background-color: var(--safety-orange) !important; border-color: var(--panel-darker) !important; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold" style="color: var(--panel-darker);"><i class="fa-solid fa-list-check me-2" style="color: var(--safety-orange);"></i>Catálogo de Activos</h2>
        <p class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.85rem; letter-spacing: 1px;">Añadir herramientas a la orden de despacho</p>
    </div>
    
    <div class="search-box py-2 px-3 d-flex align-items-center">
        <i class="fa-solid fa-magnifying-glass text-dark me-2"></i>
        <input type="text" id="searchInput" class="form-control border-0 bg-transparent shadow-none" placeholder="Buscar por referencia o tipo..." style="min-width: 250px;" onkeyup="filterCatalog()">
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
                $btnClass = 'btn-primary'; // Naranja (Estilo Industrial)
                $btnText = 'Añadir a Despacho';
                $btnDisabled = '';

                if($stock > 0 && $stock <= 5){
                    $badgeClass = 'text-dark';
                    $badgeStyle = 'background-color: #eab308;'; // Amarillo Alerta
                    $badgeText = 'Inventario Crítico';
                } else { $badgeStyle = ''; }

                if($isAgotado){
                    $badgeClass = 'bg-secondary';
                    $badgeStyle = '';
                    $badgeText = 'Sin Existencias';
                    $cardOpacity = '0.6'; 
                    $grayscale = 'grayscale(100%) opacity(0.7)'; 
                    $btnClass = 'btn-dark';
                    $btnText = 'Activo Agotado';
                    $btnDisabled = 'disabled style="cursor: not-allowed;"';
                }
            ?>

            <div class="col-sm-6 col-md-4 col-xl-3 fade-in-up tool-item" data-search="<?= strtolower($tool->name . ' ' . $tool->category) ?>">
                <div class="tool-card h-100 d-flex flex-column" style="opacity: <?= $cardOpacity ?>;">
                    
                    <div class="tool-image-container text-center p-4 d-flex align-items-center justify-content-center" style="height: 180px;">
                        <img src="<?= base_url ?>assets/img/<?= $tool->image ?>" alt="<?= htmlspecialchars($tool->name, ENT_QUOTES) ?>" class="img-fluid" style="max-height: 140px; object-fit: contain; filter: <?= $grayscale ?>;">
                        
                        <span class="badge <?= $badgeClass ?> position-absolute top-0 end-0 m-2 rounded shadow-sm px-2 py-1" style="<?= $badgeStyle ?> font-family: monospace; font-size: 0.75rem;">
                            <?= $badgeText ?>
                        </span>

                        <span class="badge border border-dark text-dark position-absolute bottom-0 start-0 m-2 px-2 py-1 shadow-sm" style="background-color: rgba(255,255,255,0.9); font-family: monospace;">
                            STOCK: <b><?= $stock ?></b>
                        </span>
                    </div>

                    <div class="p-3 d-flex flex-column flex-grow-1 bg-white">
                        <div class="text-uppercase fw-bold mb-1" style="font-size: 0.7rem; color: var(--steel-gray); letter-spacing: 1px;">
                            <i class="fa-solid fa-tag me-1"></i> <?= str_replace('_', ' ', htmlspecialchars($tool->category, ENT_QUOTES)) ?>
                        </div>
                        
                        <h6 class="fw-bold text-dark mb-3 lh-sm" style="font-size: 1.1rem;"><?= htmlspecialchars($tool->name, ENT_QUOTES) ?></h6>
                        
                        <div class="mt-auto d-grid">
                            <button class="btn <?= $btnClass ?> btn-sm shadow-sm fw-bold text-uppercase" style="letter-spacing: 0.5px; border-radius: 4px;"
                                    <?= $btnDisabled ?>
                                    onclick="addToCart(<?= $tool->id ?>, '<?= addslashes(htmlspecialchars($tool->name, ENT_QUOTES)) ?>', <?= $stock ?>, '<?= $tool->image ?>')">
                                <i class="fa-solid fa-truck-ramp-box me-2"></i> <?= $btnText ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12 text-center py-5">
            <div class="bg-white p-5 d-inline-block rounded-4 border shadow-sm">
                <i class="fa-solid fa-boxes-stacked fa-3x mb-3" style="color: var(--steel-gray);"></i>
                <h4 class="fw-bold" style="color: var(--panel-darker);">Catálogo Vacío</h4>
                <p class="text-muted">No existen registros de activos operativos en la base de datos en este momento.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<button class="btn btn-cart-float shadow-lg position-fixed d-flex align-items-center justify-content-center" 
        style="bottom: 30px; right: 30px; width: 70px; height: 70px; border-radius: 50%; z-index: 1000;"
        data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas">
    <i class="fa-solid fa-clipboard-list fs-3"></i>
    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill shadow" id="cartCount" style="font-size: 0.9rem; background-color: var(--safety-orange); border: 2px solid white;">
        0
    </span>
</button>

<div class="offcanvas offcanvas-end shadow-lg" tabindex="-1" id="cartOffcanvas" style="width: 450px; border-left: 5px solid var(--panel-dark);">
    <div class="offcanvas-header text-white border-bottom" style="background-color: var(--panel-dark);">
        <h5 class="offcanvas-title fw-bold text-uppercase" style="letter-spacing: 1px;"><i class="fa-solid fa-truck-fast me-2" style="color: var(--safety-orange);"></i> Orden de Despacho</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    
    <div class="offcanvas-body d-flex flex-column bg-light p-0">
        
        <ul class="nav nav-tabs bg-white px-3 pt-2 shadow-sm" id="cartTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold" id="items-tab" data-bs-toggle="tab" data-bs-target="#items-pane" type="button" role="tab" style="color: var(--panel-darker);">1. Equipos Requeridos</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-secondary" id="logistics-tab" data-bs-toggle="tab" data-bs-target="#logistics-pane" type="button" role="tab">2. Datos Logísticos</button>
            </li>
        </ul>

        <div class="tab-content flex-grow-1 overflow-auto" id="cartTabsContent">
            
            <div class="tab-pane fade show active p-3" id="items-pane" role="tabpanel">
                <div id="cartItemsContainer">
                    </div>
            </div>

            <div class="tab-pane fade p-4 bg-white h-100" id="logistics-pane" role="tabpanel">
                <div class="alert mb-4" style="background-color: rgba(234, 88, 12, 0.1); border-left: 4px solid var(--safety-orange);">
                    <i class="fa-solid fa-circle-info me-2" style="color: var(--safety-orange);"></i>
                    <small class="fw-bold text-dark">Todos los traslados están sujetos a validación de bodega.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.8rem;">FRENTE DE OBRA DESTINO <span class="text-danger">*</span></label>
                    <select id="cartProjectInput" class="form-select border-dark fw-bold shadow-sm" required>
                        <option value="" disabled selected>Seleccione la ubicación de destino...</option>
                        <?= $optionsHtml ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.8rem;">FECHA DE ENTREGA EXIGIDA <span class="text-danger">*</span></label>
                    <input type="date" id="expectedDate" class="form-control border-dark fw-bold shadow-sm" min="<?= $today ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary" style="font-size: 0.8rem;">FECHA ESTIMADA DE RETORNO (OPCIONAL)</label>
                    <input type="date" id="returnDate" class="form-control border-secondary shadow-sm" min="<?= $today ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.8rem;">INSTRUCCIONES DE TRANSPORTE</label>
                    <textarea id="orderNotes" class="form-control border-secondary shadow-sm" rows="3" placeholder="Ej: Enviar equipo con el conductor del camión de las 2:00 PM."></textarea>
                </div>
            </div>
        </div>

        <div class="bg-white p-3 border-top shadow-lg z-1">
            <button class="btn btn-primary w-100 fw-bold py-3 shadow-sm rounded mb-2 text-uppercase" onclick="submitCart()" id="btnSubmitCart" style="letter-spacing: 1px;">
                <i class="fa-solid fa-file-signature me-2"></i> Emitir Orden Oficial
            </button>
            <button class="btn w-100 btn-sm rounded fw-bold" style="background-color: #f1f5f9; color: #ef4444; border: 1px solid #cbd5e1;" onclick="clearCart()">
                <i class="fa-solid fa-trash me-1"></i> Descartar Formulario
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// --- LÓGICA DEL CARRITO (TOTALMENTE INTACTA, SOLO ESTILOS ACTUALIZADOS) ---
let cart = JSON.parse(localStorage.getItem('sicot_cart')) || [];

document.addEventListener('DOMContentLoaded', updateCartUI);

function addToCart(id, name, maxStock, image) {
    let existingItem = cart.find(item => item.id === id);
    if (existingItem) {
        if(existingItem.qty < maxStock) {
            existingItem.qty++;
            Swal.fire({ toast:true, position:'top-end', icon:'success', title:'Unidad sumada al lote', showConfirmButton:false, timer:2000, iconColor: '#ea580c' });
        } else {
            Swal.fire({ toast:true, position:'top-end', icon:'warning', title:'Límite de stock físico alcanzado', showConfirmButton:false, timer:2500 });
        }
    } else {
        cart.push({ id: id, name: name, maxStock: maxStock, image: image, qty: 1 });
        Swal.fire({ toast:true, position:'top-end', icon:'success', title:'Añadido a la orden logística', showConfirmButton:false, timer:2000, iconColor: '#ea580c' });
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
                <i class="fa-solid fa-clipboard-list fa-3x mb-3 opacity-25"></i>
                <h6 class="fw-bold text-uppercase">Orden Vacía</h6>
                <p class="small">Seleccione equipos del catálogo principal.</p>
            </div>`;
        document.getElementById('btnSubmitCart').disabled = true;
        return;
    }

    document.getElementById('btnSubmitCart').disabled = false;

    cart.forEach(item => {
        container.innerHTML += `
            <div class="card border-0 shadow-sm mb-2 rounded bg-white" style="border-left: 4px solid var(--safety-orange) !important;">
                <div class="card-body p-2 d-flex align-items-center">
                    <img src="<?= base_url ?>assets/img/${item.image}" alt="" style="width: 50px; height: 50px; object-fit: contain;" class="bg-light rounded p-1 me-3 border">
                    <div class="flex-grow-1">
                        <div class="fw-bold text-dark mb-2" style="font-size: 0.85rem; line-height:1.2;">${item.name}</div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center border rounded bg-light px-2 py-1 border-secondary">
                                <button class="btn btn-sm btn-link text-dark p-0 text-decoration-none" onclick="updateItemQty(${item.id}, -1)"><i class="fa-solid fa-minus"></i></button>
                                <span class="mx-3 fw-bold small font-monospace">${item.qty}</span>
                                <button class="btn btn-sm btn-link p-0 text-decoration-none" style="color: var(--safety-orange);" onclick="updateItemQty(${item.id}, 1)"><i class="fa-solid fa-plus"></i></button>
                            </div>
                            <button class="btn btn-sm text-danger border-0 fw-bold" onclick="removeItem(${item.id})"><i class="fa-regular fa-trash-can"></i></button>
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
    
    if(!projectId) {
        Swal.fire('Requisito Logístico', 'Debe especificar el frente de obra destino en la pestaña Logística.', 'warning');
        const triggerEl = document.querySelector('#logistics-tab');
        bootstrap.Tab.getOrCreateInstance(triggerEl).show();
        return;
    }
    if(!expectedDate) {
        Swal.fire('Requisito Logístico', 'Debe indicar la fecha exacta en la que exige los equipos.', 'warning');
        const triggerEl = document.querySelector('#logistics-tab');
        bootstrap.Tab.getOrCreateInstance(triggerEl).show();
        return;
    }
    if(returnDate && returnDate < expectedDate) {
        Swal.fire('Error Temporal', 'La fecha estimada de retorno no puede ser anterior a la entrega.', 'error');
        return;
    }

    Swal.fire({
        title: 'Transmitiendo Orden',
        text: 'Enviando requerimiento a la central logística...',
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
                title: 'Orden Procesada', 
                text: data.msg, 
                icon: 'success',
                confirmButtonColor: '#1e293b'
            }).then(() => location.reload());
        } else { Swal.fire('Bloqueo Operativo', data.msg, 'error'); }
    }).catch(error => {
        Swal.fire('Fallo de Comunicación', 'No se pudo contactar con el servidor central.', 'error');
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