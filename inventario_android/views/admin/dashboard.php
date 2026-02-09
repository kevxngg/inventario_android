<?php require_once 'views/layouts/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-primary-dark">Panel de Control</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
            <i class="fa-solid fa-calendar me-2"></i> Esta semana
        </button>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="glass-card p-3 d-flex align-items-center justify-content-between shadow-sm">
            <div>
                <h6 class="text-muted mb-0">Total Inventario</h6>
                <h2 class="fw-bold mb-0"><?= $totalTools ?></h2>
            </div>
            <div class="icon-box bg-primary-light text-primary rounded-circle p-3">
                <i class="fa-solid fa-toolbox fa-2x"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="glass-card p-3 d-flex align-items-center justify-content-between shadow-sm">
            <div>
                <h6 class="text-muted mb-0">Obras en Ejecución</h6>
                <h2 class="fw-bold mb-0"><?= $activeProjects ?></h2>
            </div>
            <div class="icon-box bg-success-light text-success rounded-circle p-3">
                <i class="fa-solid fa-helmet-safety fa-2x"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="glass-card p-3 d-flex align-items-center justify-content-between shadow-sm border-warning">
            <div>
                <h6 class="text-muted mb-0">En Mantenimiento</h6>
                <h2 class="fw-bold mb-0 text-warning"><?= $maintenance ?></h2>
            </div>
            <div class="icon-box bg-warning-light text-warning rounded-circle p-3">
                <i class="fa-solid fa-triangle-exclamation fa-2x"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4">Uso de Maquinaria vs Tiempo</h5>
            <canvas id="usageChart" height="150"></canvas>
        </div>
    </div>
    <div class="col-md-4">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4">Estado del Stock</h5>
            <canvas id="stockChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Gráfico de Barras
    new Chart(document.getElementById('usageChart'), {
        type: 'line',
        data: {
            labels: ['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
            datasets: [{
                label: 'Horas de uso',
                data: [12, 19, 3, 5, 2, 3],
                borderColor: '#0061a4',
                tension: 0.4
            }]
        }
    });

    // Gráfico de Dona
    new Chart(document.getElementById('stockChart'), {
        type: 'doughnut',
        data: {
            labels: ['Disponible', 'En Uso', 'Mantenimiento'],
            datasets: [{
                data: [300, 50, 100],
                backgroundColor: ['#cce5ff', '#0061a4', '#ffc107']
            }]
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
    // Borrar el mensaje de la sesión para que no salga siempre
    unset($_SESSION['alert_message']); 
    unset($_SESSION['alert_icon']); 
?>
<?php endif; ?>

<?php require_once 'views/layouts/footer.php'; ?>