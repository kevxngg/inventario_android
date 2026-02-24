<?php require_once 'views/layouts/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom border-secondary">
    <h2 class="fw-bold text-dark"><i class="fa-solid fa-chart-line me-2 text-primary"></i>Panel de Control Maestro</h2>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="glass-card p-3 d-flex align-items-center justify-content-between shadow-sm h-100 border-start border-primary border-4">
            <div>
                <h6 class="text-muted mb-1 small fw-bold text-uppercase">Inventario Global</h6>
                <h2 class="fw-bold mb-0 text-dark"><?= $totalTools ?> <span class="fs-6 text-muted">uds</span></h2>
            </div>
            <div class="icon-box bg-light text-primary rounded p-3 border">
                <i class="fa-solid fa-boxes-stacked fa-lg"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="glass-card p-3 d-flex align-items-center justify-content-between shadow-sm h-100 border-start border-info border-4">
            <div>
                <h6 class="text-muted mb-1 small fw-bold text-uppercase">Frentes de Obra</h6>
                <h2 class="fw-bold mb-0 text-dark"><?= $activeProjects ?></h2>
            </div>
            <div class="icon-box bg-light text-info rounded p-3 border">
                <i class="fa-solid fa-map-location-dot fa-lg"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="glass-card p-3 d-flex align-items-center justify-content-between shadow-sm h-100 border-start border-warning border-4">
            <div>
                <h6 class="text-muted mb-1 small fw-bold text-uppercase">En Mantenimiento</h6>
                <h2 class="fw-bold mb-0 text-dark"><?= $maintenance ?> <span class="fs-6 text-muted">uds</span></h2>
            </div>
            <div class="icon-box bg-light text-warning rounded p-3 border">
                <i class="fa-solid fa-screwdriver-wrench fa-lg"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="glass-card p-3 d-flex align-items-center justify-content-between shadow-sm h-100 border-start border-success border-4" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
            <div>
                <h6 class="text-success mb-1 small fw-bold text-uppercase">Stock Disponible</h6>
                <h2 class="fw-bold mb-0 text-success"><?= $available ?> <span class="fs-6 text-muted">uds</span></h2>
            </div>
            <div class="icon-box bg-success text-white rounded p-3 shadow-sm">
                <i class="fa-solid fa-check-double fa-lg"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mb-4">
        <div class="glass-card p-4 h-100 shadow-sm border-0">
            <h6 class="fw-bold mb-4 text-secondary text-uppercase"><i class="fa-solid fa-chart-column me-2"></i>Distribución de Activos por Categoría</h6>
            <canvas id="categoryChart" height="120"></canvas>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="glass-card p-4 h-100 shadow-sm border-0">
            <h6 class="fw-bold mb-4 text-secondary text-uppercase"><i class="fa-solid fa-chart-pie me-2"></i>Estado Operativo Global</h6>
            <div style="position: relative; height: 220px;">
                <canvas id="stockChart"></canvas>
            </div>
            <div class="mt-4 text-center">
                <span class="badge bg-success shadow-sm me-1 px-2 py-2">Disponible: <?= $available ?></span>
                <span class="badge bg-primary shadow-sm me-1 px-2 py-2">En Obra: <?= $inUse ?></span>
                <span class="badge bg-warning text-dark shadow-sm px-2 py-2">Taller: <?= $maintenance ?></span>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // --- 1. GRÁFICA DE BARRAS: CATEGORÍAS ---
    const catLabels = <?= json_encode($catLabels) ?>;
    const catData = <?= json_encode($catData) ?>;

    new Chart(document.getElementById('categoryChart'), {
        type: 'bar',
        data: {
            labels: catLabels.length > 0 ? catLabels : ['Sin registros'],
            datasets: [{
                label: 'Volumen de Activos',
                data: catData.length > 0 ? catData : [0],
                backgroundColor: 'rgba(0, 75, 135, 0.85)', 
                borderColor: '#004b87',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                x: { grid: { display: false } }
            },
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: 'rgba(0,0,0,0.8)' }
            }
        }
    });

    // --- 2. GRÁFICA DE DONA: ESTADO STOCK ---
    const available = <?= $available ?>;
    const inUse = <?= $inUse ?>;
    const maintenance = <?= $maintenance ?>;

    new Chart(document.getElementById('stockChart'), {
        type: 'doughnut',
        data: {
            labels: ['Disponible (Bodega)', 'En Operación (Obra)', 'Mantenimiento (Taller)'],
            datasets: [{
                data: [available, inUse, maintenance],
                backgroundColor: [
                    '#198754', // Verde corporativo
                    '#004b87', // Azul corporativo
                    '#ffc107'  // Amarillo alerta
                ],
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%', 
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, font: { size: 11 } } }
            }
        }
    });
});
</script>

<?php if(isset($_SESSION['alert_message'])): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            title: 'Notificación del Sistema',
            text: '<?= $_SESSION['alert_message'] ?>',
            icon: '<?= $_SESSION['alert_icon'] ?>',
            confirmButtonColor: '#004b87',
            timer: 3000,
            timerProgressBar: true
        });
    });
</script>
<?php 
    unset($_SESSION['alert_message']); 
    unset($_SESSION['alert_icon']); 
?>
<?php endif; ?>

<?php require_once 'views/layouts/footer.php'; ?>