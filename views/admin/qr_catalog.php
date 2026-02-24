<?php require_once 'views/layouts/header.php'; ?>

<style>
    .qr-card {
        background-color: #ffffff;
        border: 1px solid #cbd5e0;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        transition: transform 0.2s;
        height: 100%;
    }
    .qr-card:hover {
        border-color: #004b87;
        box-shadow: 0 5px 15px rgba(0, 75, 135, 0.1);
    }
    .qr-code-canvas {
        margin: 15px auto;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 10px;
        border: 1px dashed #e2e8f0;
        border-radius: 4px;
        background: #f8fafc;
    }
    .qr-code-canvas img {
        margin: 0 auto;
    }
    .asset-ref {
        font-family: 'Courier New', Courier, monospace;
        font-weight: 700;
        color: #004b87;
        font-size: 0.85rem;
    }
    
    /* Configuración exclusiva para la impresión física */
    @media print {
        body { background-color: #ffffff; }
        #sidebar-wrapper, .navbar, .header-nav, .no-print { display: none !important; }
        .page-content, main, .container-fluid { margin-left: 0 !important; padding: 0 !important; width: 100% !important; }
        
        .qr-card { 
            border: 1px dashed #999 !important; 
            box-shadow: none !important; 
            page-break-inside: avoid;
            border-radius: 0;
        }
        .print-grid { display: flex; flex-wrap: wrap; width: 100%; }
        .print-col { width: 25% !important; padding: 5px; box-sizing: border-box; }
        .qr-code-canvas { border: none !important; background: transparent !important; margin: 5px auto; }
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary pb-3 no-print">
    <div>
        <h2 class="fw-bold text-dark"><i class="fa-solid fa-qrcode me-2 text-primary"></i>Motor de Etiquetas QR</h2>
        <p class="text-muted mb-0">Generación de matrices de datos para escaneo mediante terminales móviles Android.</p>
    </div>
    <button onclick="window.print()" class="btn btn-primary fw-bold shadow-sm px-4">
        <i class="fa-solid fa-print me-2"></i> Imprimir Matriz de Etiquetas
    </button>
</div>

<div class="alert alert-light border border-secondary shadow-sm mb-4 no-print">
    <i class="fa-solid fa-circle-info text-primary me-2"></i>
    <strong>Aviso de Arquitectura:</strong> Cada código QR generado contiene un payload JSON encriptado (<code>{"id": ID, "type": "TOOL"}</code>). Este estándar asegura que la futura aplicación móvil Android interprete los datos unívocamente, evitando falsos positivos al escanear.
</div>

<div class="row g-3 print-grid">
    <?php if(isset($herramientas) && $herramientas->num_rows > 0): ?>
        <?php while($tool = $herramientas->fetch_object()): ?>
            
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 print-col">
                <div class="qr-card">
                    <div class="fw-bold text-dark text-truncate" style="font-size: 0.9rem;" title="<?= $tool->name ?>">
                        <?= strtoupper($tool->name) ?>
                    </div>
                    
                    <div class="asset-ref mt-1">REF-<?= str_pad($tool->id, 5, '0', STR_PAD_LEFT) ?></div>
                    
                    <div class="qr-code-canvas" 
                         data-id="<?= $tool->id ?>" 
                         data-name="<?= htmlspecialchars($tool->name, ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    
                    <div class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                        SICOT ERP
                    </div>
                </div>
            </div>

        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12 text-center py-5 no-print">
            <div class="p-5 border rounded bg-light text-muted">
                <i class="fa-solid fa-boxes-stacked fa-3x mb-3"></i>
                <h5 class="fw-bold">No existen activos en el inventario.</h5>
                <p>Debe registrar maquinaria en el sistema para generar las etiquetas de trazabilidad.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Seleccionar todos los contenedores de QR de la vista
    const qrContainers = document.querySelectorAll('.qr-code-canvas');
    
    qrContainers.forEach(function(container) {
        const toolId = container.getAttribute('data-id');
        
        // Estructuración del Payload para el móvil Android
        // Esto le dice a la app Java/Kotlin exactamente qué activo es
        const payload = JSON.stringify({
            sys: "SICOT",
            id: parseInt(toolId),
            type: "TOOL"
        });

        // Instanciar y dibujar el código QR
        new QRCode(container, {
            text: payload,
            width: 120,
            height: 120,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.M // Nivel de corrección de errores Medio (Ideal para ambientes sucios/obras)
        });
    });
});
</script>

<?php require_once 'views/layouts/footer.php'; ?>