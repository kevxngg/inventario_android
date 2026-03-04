<?php require_once 'views/layouts/header.php'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>

<style>
    /* Diseño Billetera Industrial - Gafete de Seguridad */
    .wallet-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 2rem;
        padding-bottom: 2rem;
    }

    .asset-card {
        background-color: var(--white);
        border-radius: 8px;
        border: 2px solid var(--panel-dark);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        position: relative;
        display: flex;
        flex-direction: column;
    }

    /* Cinta amarilla de precaución visual arriba */
    .asset-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 6px;
        background: repeating-linear-gradient(
          45deg,
          #eab308,
          #eab308 10px,
          #1e293b 10px,
          #1e293b 20px
        );
        z-index: 10;
    }

    .asset-header {
        padding: 2rem 1.5rem 1.5rem;
        background-color: #f8fafc;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 180px;
        position: relative;
        border-bottom: 1px dashed var(--steel-gray);
    }

    .asset-header img {
        max-height: 90%;
        max-width: 90%;
        object-fit: contain;
        filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
    }

    .status-badge {
        position: absolute;
        top: 20px;
        left: 15px;
        background: var(--panel-dark);
        color: white;
        padding: 4px 10px;
        border-radius: 4px;
        font-family: monospace;
        font-size: 0.8rem;
        font-weight: bold;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        z-index: 2;
    }

    .asset-body {
        padding: 1.5rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .asset-category {
        color: var(--steel-gray);
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.2rem;
    }

    .asset-title {
        color: var(--panel-darker);
        font-weight: 900;
        font-size: 1.2rem;
        margin-bottom: 1rem;
        line-height: 1.2;
    }

    .asset-details {
        background-color: var(--bg-app);
        padding: 0.8rem;
        margin-bottom: 1.5rem;
        border-left: 3px solid var(--safety-orange);
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.3rem;
        font-size: 0.8rem;
        font-family: monospace;
    }
    .detail-row:last-child { margin-bottom: 0; }

    .detail-label { color: var(--steel-gray); font-weight: bold; }
    .detail-value { color: var(--panel-darker); font-weight: bold; text-align: right; }

    .asset-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: auto;
    }

    .btn-wallet {
        border-radius: 4px;
        font-weight: 800;
        padding: 0.8rem;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        text-align: center;
        border: none;
        transition: all 0.2s ease;
    }

    .btn-return { background-color: var(--panel-dark); color: white; }
    .btn-return:hover { background-color: var(--safety-orange); color: white; }

    .btn-report { background-color: #ef4444; color: white; }
    .btn-report:hover { background-color: #dc2626; color: white; }

    /* Modal QR Industrial */
    .qr-container {
        background: white;
        padding: 15px;
        display: inline-block;
        border: 4px solid var(--panel-dark);
        border-radius: 4px;
    }
</style>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold" style="color: var(--panel-darker);"><i class="fa-solid fa-id-card-clip me-2" style="color: var(--safety-orange);"></i>Inventario a Cargo</h2>
            <p class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.85rem; letter-spacing: 1px;">Gestión de activos bajo responsabilidad directa</p>
        </div>
        <div>
            <span class="badge py-2 px-3 fs-6 rounded" style="background-color: var(--panel-dark); color: white; border: 2px solid var(--safety-orange);">
                <i class="fa-solid fa-boxes-stacked me-2" style="color: var(--safety-orange);"></i> 
                Volumen Total: <?= isset($misHerramientas) ? $misHerramientas->num_rows : 0 ?>
            </span>
        </div>
    </div>

    <?php if(isset($misHerramientas) && $misHerramientas->num_rows > 0): ?>
        <div class="wallet-container">
            <?php while($item = $misHerramientas->fetch_object()): ?>
                
                <div class="asset-card">
                    <div class="asset-header">
                        <div class="status-badge">
                            ASN-<?= str_pad($item->id, 5, '0', STR_PAD_LEFT) ?>
                        </div>
                        <?php $imgSrc = !empty($item->image) ? $item->image : 'default.png'; ?>
                        <img src="<?= base_url ?>assets/img/<?= htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8') ?>" alt="Herramienta">
                    </div>
                    
                    <div class="asset-body">
                        <div class="asset-category"><?= str_replace('_', ' ', htmlspecialchars($item->category, ENT_QUOTES, 'UTF-8')) ?></div>
                        <h4 class="asset-title"><?= htmlspecialchars($item->tool_name, ENT_QUOTES, 'UTF-8') ?></h4>
                        
                        <div class="asset-details">
                            <div class="detail-row">
                                <span class="detail-label">COD/REF:</span>
                                <span class="detail-value">#<?= str_pad($item->tool_id, 4, '0', STR_PAD_LEFT) ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">CANTIDAD:</span>
                                <span class="detail-value" style="color: var(--safety-orange);"><?= $item->quantity ?> UDS</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">LOCACIÓN:</span>
                                <span class="detail-value"><?= strtoupper(htmlspecialchars($item->project_name ?? 'USO GENERAL', ENT_QUOTES, 'UTF-8')) ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">DESPACHO:</span>
                                <span class="detail-value"><?= date('d/M/Y', strtotime($item->assigned_at)) ?></span>
                            </div>
                        </div>
                        
                        <div class="asset-actions">
                            <button class="btn-wallet btn-return" onclick="abrirModalQR(<?= $item->id ?>, <?= $item->tool_id ?>, '<?= htmlspecialchars(addslashes($item->tool_name), ENT_QUOTES, 'UTF-8') ?>')">
                                <i class="fa-solid fa-qrcode fs-6"></i> Pase QR
                            </button>
                            
                            <a href="<?= base_url ?>User/reportView?tool_id=<?= $item->tool_id ?>" class="btn-wallet btn-report text-decoration-none">
                                <i class="fa-solid fa-triangle-exclamation fs-6"></i> Alerta Falla
                            </a>
                        </div>
                    </div>
                </div>

            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="col-12 text-center py-5">
            <div class="bg-white p-5 border" style="max-width: 500px; margin: 0 auto; border-radius: 8px;">
                <i class="fa-solid fa-helmet-safety fa-3x mb-3" style="color: var(--steel-gray);"></i>
                <h4 class="fw-bold" style="color: var(--panel-darker);">Sin Asignaciones</h4>
                <p class="text-muted fw-bold small mb-4">No figura maquinaria ni herramientas a su nombre en la base de datos central.</p>
                <a href="<?= base_url ?>User/catalog" class="btn btn-primary fw-bold px-4 shadow-sm" style="border-radius: 4px;">
                    <i class="fa-solid fa-cart-flatbed me-2"></i> Abrir Catálogo
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="qrReturnModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 8px; overflow: hidden; border: 2px solid var(--panel-dark);">
            <div class="modal-header py-3 border-0" style="background-color: var(--panel-dark); color: white;">
                <h6 class="modal-title fw-bold text-uppercase" style="letter-spacing: 1px;"><i class="fa-solid fa-qrcode me-2" style="color: var(--safety-orange);"></i>Pase de Aduana/Bodega</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-4 bg-white">
                <h6 class="fw-bold mb-1" id="qrToolName" style="color: var(--panel-darker);">Nombre Herramienta</h6>
                <p class="text-muted fw-bold" style="font-size: 0.7rem; text-transform: uppercase;">Muestre el código al operario logístico</p>
                
                <div class="qr-container my-3">
                    <canvas id="returnQRCode"></canvas>
                </div>
                
                <div class="badge fs-6 px-3 py-2 mt-2" id="qrAssignmentId" style="background-color: var(--bg-app); color: var(--panel-dark); font-family: monospace; border: 1px dashed var(--steel-gray);">ASN-00000</div>
            </div>
            <div class="modal-footer border-top bg-light justify-content-center">
                <button type="button" class="btn fw-bold px-4 w-100" style="background-color: var(--steel-gray); color: white; border-radius: 4px; text-transform: uppercase; font-size: 0.8rem;" onclick="solicitarRetornoManual()">
                    <i class="fa-solid fa-tower-broadcast me-1"></i> Retorno Manual (Sin Lector)
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let currentAssignmentId = null;
    let currentToolName = '';

    // Función para abrir la tarjeta VIP de retorno (Genera el QR al instante)
    function abrirModalQR(assignmentId, toolId, toolName) {
        currentAssignmentId = assignmentId;
        currentToolName = toolName;

        document.getElementById('qrToolName').innerText = toolName;
        document.getElementById('qrAssignmentId').innerText = 'ASN-' + String(assignmentId).padStart(5, '0');

        // Construimos el Payload (La carga de datos oculta en el QR)
        const payload = JSON.stringify({
            action: 'RETURN_CHECKIN',
            assignment_id: assignmentId,
            tool_id: toolId,
            user_id: <?= $_SESSION['identity']->id ?>
        });

        // Instanciar generador de QR
        var qr = new QRious({
            element: document.getElementById('returnQRCode'),
            value: payload,
            size: 200,
            background: 'white',
            foreground: '#0f172a', // Color Oscuro Industrial para buen escaneo
            level: 'H' // Alta redundancia
        });

        var myModal = new bootstrap.Modal(document.getElementById('qrReturnModal'));
        myModal.show();
    }

    // Botón alternativo por si no hay lector de QR en bodega
    function solicitarRetornoManual() {
        $('#qrReturnModal').modal('hide');
        
        Swal.fire({
            title: 'Protocolo de Entrega',
            html: `Notificando a la central la devolución de:<br><strong>${currentToolName}</strong><br><br><small class="text-danger fw-bold text-uppercase">El administrador debe realizar Check-In para liberar su responsabilidad.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ea580c', // Naranja Seguridad
            cancelButtonColor: '#64748b', // Gris Acero
            confirmButtonText: 'Confirmar Notificación',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Llama correctamente a initiateReturn en el UserController
                $.post('<?= base_url ?>User/initiateReturn', { assignment_id: currentAssignmentId }, function(response) {
                    if(response.status === 'success') {
                        Swal.fire({
                            title: 'Señal Emitida',
                            text: response.msg,
                            icon: 'success',
                            confirmButtonColor: '#1e293b'
                        }).then(() => location.reload()); // Recarga la página
                    } else {
                        Swal.fire({
                            title: 'Error de Sistema',
                            text: response.msg,
                            icon: 'error',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                }, 'json').fail(function() {
                    Swal.fire({
                        title: 'Sin Conexión',
                        text: 'Fallo de enlace con la base de datos central.',
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                });
            }
        });
    }
</script>

<?php require_once 'views/layouts/footer.php'; ?>