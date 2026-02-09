<?php require_once 'views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold text-primary-dark">🛒 Catálogo de Maquinaria</h2>
        <p class="text-muted mb-0">Solicita equipos disponibles para tus obras.</p>
    </div>
    
    <div class="search-box glass-card py-2 px-3 d-flex align-items-center">
        <i class="fa-solid fa-magnifying-glass text-muted me-2"></i>
        <input type="text" class="form-control border-0 bg-transparent shadow-none" placeholder="Buscar taladro, grúa..." style="min-width: 250px;">
    </div>
</div>

<div class="row g-4">
    <?php while($tool = $disponibles->fetch_object()): ?>
    <div class="col-sm-6 col-md-4 col-xl-3 fade-in-up">
        <div class="glass-card h-100 p-0 overflow-hidden tool-card shadow-sm hover-elevate">
            
            <div class="position-relative bg-white text-center p-4" style="height: 200px;">
                <img src="<?= base_url ?>assets/img/<?= $tool->image ?>" alt="<?= $tool->name ?>" class="img-fluid" style="max-height: 100%; object-fit: contain;">
                
                <span class="badge bg-success position-absolute bottom-0 start-0 m-3 rounded-pill">
                    <i class="fa-solid fa-check me-1"></i> Disponible
                </span>
            </div>

            <div class="p-4">
                <div class="text-uppercase text-muted x-small fw-bold mb-1"><?= $tool->category ?></div>
                <h5 class="fw-bold text-dark mb-2"><?= $tool->name ?></h5>
                
                <p class="text-muted small mb-3 text-truncate">
                    <?= $tool->description ?? 'Equipo estándar de alta durabilidad para construcción pesada.' ?>
                </p>

                <div class="d-grid">
                    <button class="btn btn-android btn-sm" onclick="solicitarEquipo(<?= $tool->id ?>, '<?= $tool->name ?>')">
                        <i class="fa-solid fa-plus me-2"></i> Solicitar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<script>
function solicitarEquipo(id, nombre) {
    Swal.fire({
        title: '¿Solicitar ' + nombre + '?',
        text: "Se notificará al administrador.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0061a4',
        confirmButtonText: 'Sí, solicitar'
    }).then((result) => {
        if (result.isConfirmed) {
            // PETICIÓN AJAX REAL
            $.post('<?= base_url ?>User/requestTool', {tool_id: id}, function(response){
                Swal.fire('¡Solicitud Enviada!', 'El admin revisará tu petición.', 'success');
            });
        }
    })
}
</script>

<style>
.hover-elevate {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-elevate:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,97,164,0.15) !important;
}
</style>

<?php require_once 'views/layouts/footer.php'; ?>