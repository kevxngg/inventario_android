<?php require_once 'views/layouts/header.php'; ?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-4 border-bottom border-secondary pb-3">
        <h2 class="fw-bold text-dark"><i class="fa-solid fa-address-card me-2 text-primary"></i>Panel Operativo del Personal</h2>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="glass-card p-3 d-flex align-items-center justify-content-between shadow-sm h-100 border-start border-secondary border-4">
                <div>
                    <h6 class="text-muted mb-1 small fw-bold text-uppercase">Solicitudes Emitidas</h6>
                    <h2 class="fw-bold mb-0 text-dark"><?= $totalSolicitudes ?></h2>
                </div>
                <div class="icon-box bg-light text-secondary rounded p-3 border">
                    <i class="fa-solid fa-file-export fa-lg"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-3 d-flex align-items-center justify-content-between shadow-sm h-100 border-start border-success border-4">
                <div>
                    <h6 class="text-muted mb-1 small fw-bold text-uppercase">Autorizadas</h6>
                    <h2 class="fw-bold mb-0 text-success"><?= $aprobados ?></h2>
                </div>
                <div class="icon-box bg-light text-success rounded p-3 border">
                    <i class="fa-solid fa-check-double fa-lg"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-3 d-flex align-items-center justify-content-between shadow-sm h-100 border-start border-warning border-4">
                <div>
                    <h6 class="text-muted mb-1 small fw-bold text-uppercase">En Revisión</h6>
                    <h2 class="fw-bold mb-0 text-warning"><?= $pendientes ?></h2>
                </div>
                <div class="icon-box bg-light text-warning rounded p-3 border">
                    <i class="fa-solid fa-clock-rotate-left fa-lg"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-3 d-flex align-items-center justify-content-between shadow-sm h-100 border-start border-info border-4">
                <div>
                    <h6 class="text-muted mb-1 small fw-bold text-uppercase">Proyectos Vinculados</h6>
                    <h2 class="fw-bold mb-0 text-info"><?= $totalObras ?></h2>
                </div>
                <div class="icon-box bg-light text-info rounded p-3 border">
                    <i class="fa-solid fa-helmet-safety fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7 mb-4">
            <div class="glass-card p-4 h-100 shadow-sm border-0">
                <h6 class="fw-bold mb-4 text-secondary text-uppercase"><i class="fa-solid fa-chart-pie me-2"></i>Desglose de Peticiones</h6>
                <div style="height: 260px;">
                    <canvas id="userStatusChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-5 mb-4">
            <div class="glass-card p-4 h-100 shadow-sm border-0 d-flex flex-column justify-content-center">
                <div class="mb-4 text-center">
                    <div class="d-inline-block bg-light p-4 rounded-circle border shadow-sm">
                        <i class="fa-solid fa-industry text-primary fa-3x"></i>
                    </div>
                </div>
                <h5 class="fw-bold text-dark text-center mb-3">Directrices Operativas</h5>
                <p class="text-muted text-center small mb-4">Mantenga actualizado el registro de novedades en terreno. Toda solicitud de activos o reporte de incidencias está sujeta a auditoría administrativa.</p>
                
                <div class="d-grid gap-2">
                    <a href="<?= base_url ?>User/catalog" class="btn btn-primary fw-bold shadow-sm"><i class="fa-solid fa-list-check me-2"></i>Ir al Catálogo de Activos</a>
                    <a href="<?= base_url ?>User/reportView" class="btn btn-outline-danger fw-bold shadow-sm"><i class="fa-solid fa-triangle-exclamation me-2"></i>Registrar Incidencia</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('userStatusChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Autorizadas', 'En Revisión (Pendientes)', 'Denegadas'],
            datasets: [{
                data: [<?= $aprobados ?>, <?= $pendientes ?>, <?= $rechazados ?>],
                backgroundColor: ['#198754', '#ffc107', '#dc3545'],
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 8 } }
            }
        }
    });
});
</script>

<?php require_once 'views/layouts/footer.php'; ?>