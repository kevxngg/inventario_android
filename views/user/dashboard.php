<?php require_once 'views/layouts/header.php'; ?>

<div class="container-fluid p-4">
    <h2 class="fw-bold text-dark mb-4">Resumen de Actividad</h2>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="glass-card p-3 d-flex align-items-center justify-content-between shadow-sm h-100">
                <div>
                    <h6 class="text-muted mb-0 small">Mis Solicitudes</h6>
                    <h2 class="fw-bold mb-0 text-primary"><?= $totalSolicitudes ?></h2>
                </div>
                <div class="icon-box bg-primary-light text-primary rounded-circle p-3">
                    <i class="fa-solid fa-clipboard-list fa-lg"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-3 d-flex align-items-center justify-content-between shadow-sm h-100 border-success">
                <div>
                    <h6 class="text-muted mb-0 small">Aprobadas</h6>
                    <h2 class="fw-bold mb-0 text-success"><?= $aprobados ?></h2>
                </div>
                <div class="icon-box bg-success-light text-success rounded-circle p-3">
                    <i class="fa-solid fa-check-double fa-lg"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-3 d-flex align-items-center justify-content-between shadow-sm h-100 border-warning">
                <div>
                    <h6 class="text-muted mb-0 small">Pendientes</h6>
                    <h2 class="fw-bold mb-0 text-warning"><?= $pendientes ?></h2>
                </div>
                <div class="icon-box bg-warning-light text-warning rounded-circle p-3">
                    <i class="fa-solid fa-hourglass-half fa-lg"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-3 d-flex align-items-center justify-content-between shadow-sm h-100">
                <div>
                    <h6 class="text-muted mb-0 small">Obras Activas</h6>
                    <h2 class="fw-bold mb-0 text-dark"><?= $totalObras ?></h2>
                </div>
                <div class="icon-box bg-light text-dark rounded-circle p-3 border">
                    <i class="fa-solid fa-helmet-safety fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="glass-card p-4 h-100">
                <h5 class="fw-bold mb-4 text-secondary">Estado de mis Solicitudes</h5>
                <div style="height: 250px;">
                    <canvas id="userStatusChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="glass-card p-4 h-100 d-flex flex-column justify-content-center align-items-center text-center">
                <div class="mb-3">
                    <i class="fa-solid fa-hard-hat text-primary" style="font-size: 80px; opacity: 0.2;"></i>
                </div>
                <h4>¡Manos a la obra!</h4>
                <p class="text-muted">Recuerda reportar cualquier incidente a tiempo.</p>
                <a href="<?= base_url ?>User/panel" class="btn btn-primary rounded-pill px-4">Ir a Mis Proyectos</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('userStatusChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Aprobados', 'Pendientes', 'Rechazados'],
            datasets: [{
                data: [<?= $aprobados ?>, <?= $pendientes ?>, <?= $rechazados ?>],
                backgroundColor: ['#198754', '#ffc107', '#dc3545'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
});
</script>

<?php require_once 'views/layouts/footer.php'; ?>