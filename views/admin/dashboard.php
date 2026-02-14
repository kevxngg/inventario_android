<?php require_once 'views/layouts/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-primary-dark">Panel de Control</h1>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="glass-card p-3 d-flex align-items-center justify-content-between shadow-sm h-100">
            <div>
                <h6 class="text-muted mb-0 small">Total Inventario</h6>
                <h2 class="fw-bold mb-0 text-primary-dark"><?= $totalTools ?></h2>
            </div>
            <div class="icon-box bg-primary-light text-primary rounded-circle p-3">
                <i class="fa-solid fa-toolbox fa-lg"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="glass-card p-3 d-flex align-items-center justify-content-between shadow-sm h-100">
            <div>
                <h6 class="text-muted mb-0 small">Obras Activas</h6>
                <h2 class="fw-bold mb-0 text-dark"><?= $activeProjects ?></h2>
            </div>
            <div class="icon-box bg-white border text-dark rounded-circle p-3">
                <i class="fa-solid fa-helmet-safety fa-lg"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="glass-card p-3 d-flex align-items-center justify-content-between shadow-sm border-warning h-100">
            <div>
                <h6 class="text-muted mb-0 small">En Mantenimiento</h6>
                <h2 class="fw-bold mb-0 text-warning"><?= $maintenance ?></h2>
            </div>
            <div class="icon-box bg-warning-light text-warning rounded-circle p-3">
                <i class="fa-solid fa-screwdriver-wrench fa-lg"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="glass-card p-3 d-flex align-items-center justify-content-between shadow-sm h-100" style="background: linear-gradient(135deg, #e3f2fd 0%, #ffffff 100%);">
            <div>
                <h6 class="text-primary mb-0 small fw-bold">Disponibles</h6>
                <h2 class="fw-bold mb-0 text-primary"><?= $available ?></h2>
            </div>
            <div class="icon-box bg-primary text-white rounded-circle p-3 shadow-sm">
                <i class="fa-solid fa-check fa-lg"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mb-4">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4 text-secondary"><i class="fa-solid fa-chart-column me-2"></i>Distribución por Categoría</h5>
            <canvas id="categoryChart" height="150"></canvas>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4 text-secondary"><i class="fa-solid fa-chart-pie me-2"></i>Estado del Stock</h5>
            <div style="position: relative; height: 250px;">
                <canvas id="stockChart"></canvas>
            </div>
            <div class="mt-4 text-center">
                <span class="badge bg-success me-1">Disponible: <?= $available ?></span>
                <span class="badge bg-primary me-1">En Obra: <?= $inUse ?></span>
                <span class="badge bg-warning text-dark">Taller: <?= $maintenance ?></span>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // --- 1. GRÁFICA DE BARRAS: CATEGORÍAS (REAL) ---
    // PHP inyecta los datos reales aquí desde el Controlador
    const catLabels = <?= json_encode($catLabels) ?>;
    const catData = <?= json_encode($catData) ?>;

    new Chart(document.getElementById('categoryChart'), {
        type: 'bar',
        data: {
            labels: catLabels.length > 0 ? catLabels : ['Sin datos'],
            datasets: [{
                label: 'Cantidad de Equipos',
                data: catData.length > 0 ? catData : [0],
                backgroundColor: 'rgba(0, 97, 164, 0.7)', // Azul primario translúcido
                borderColor: '#0061a4',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            },
            plugins: {
                legend: { display: false } // Ocultar leyenda para limpieza
            }
        }
    });

    // --- 2. GRÁFICA DE DONA: ESTADO STOCK (REAL) ---
    const available = <?= $available ?>;
    const inUse = <?= $inUse ?>;
    const maintenance = <?= $maintenance ?>;

    new Chart(document.getElementById('stockChart'), {
        type: 'doughnut',
        data: {
            labels: ['Disponible', 'En Obra', 'Mantenimiento'],
            datasets: [{
                data: [available, inUse, maintenance],
                backgroundColor: [
                    '#2e7d32', // Verde (Disponible)
                    '#0061a4', // Azul (En Obra)
                    '#ffc107'  // Amarillo (Mantenimiento)
                ],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%', // Dona más delgada y elegante
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } }
            }
        }
    });
});
</script>

<?php if(isset($_SESSION['alert_message'])): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            title: '¡Procesado!',
            text: '<?= $_SESSION['alert_message'] ?>',
            icon: '<?= $_SESSION['alert_icon'] ?>',
            confirmButtonColor: '#0061a4',
            timer: 3000,
            timerProgressBar: true
        });
    });
</script>
<?php 
    // Borrar el mensaje de la sesión
    unset($_SESSION['alert_message']); 
    unset($_SESSION['alert_icon']); 
?>
<?php endif; ?>

<?php require_once 'views/layouts/footer.php'; ?>