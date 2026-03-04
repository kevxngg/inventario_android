<?php require_once 'views/layouts/header.php'; ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

<style>
    /* Personalización para que DataTables encaje con el diseño corporativo */
    .dataTables_wrapper .row { align-items: center; margin-bottom: 1.5rem; }
    .dataTables_filter input {
        border-radius: 20px;
        border: 1px solid #ced4da;
        padding: 6px 15px;
        outline: none;
        transition: all 0.3s ease;
    }
    .dataTables_filter input:focus { 
        border-color: #004b87; 
        box-shadow: 0 0 0 0.2rem rgba(0, 75, 135, 0.25); 
    }
    .page-item.active .page-link { background-color: #004b87; border-color: #004b87; }
    .page-link { color: #004b87; border-radius: 8px; margin: 0 3px; border: none; font-weight: 600; }
    .page-link:hover { background-color: #f1f5f9; color: #003666; }
    table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control:before { background-color: #004b87; }
    
    /* Efecto hover premium para las filas */
    #tablaUsuarios tbody tr { transition: background-color 0.2s ease; }
    #tablaUsuarios tbody tr:hover { background-color: #f8fafc !important; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold text-dark"><i class="fa-solid fa-users-gear me-2 text-primary"></i>Consola de Gestión de Identidades</h2>
        <p class="text-muted mb-0">Administración de credenciales, roles y permisos de acceso al sistema.</p>
    </div>
    <button class="btn btn-android shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalUser">
        <i class="fa-solid fa-user-plus me-2"></i> Alta de Nuevo Funcionario
    </button>
</div>

<div class="glass-card p-4 border-0 shadow-sm rounded-4 overflow-hidden bg-white">
    <div class="table-responsive">
        <table id="tablaUsuarios" class="table align-middle mb-0 w-100 border-top">
            <thead class="bg-light text-uppercase small text-muted">
                <tr>
                    <th class="ps-3 py-3">ID Interno</th>
                    <th>Funcionario</th>
                    <th>Nivel de Acceso</th>
                    <th>Fecha de Registro</th>
                    <th class="text-end pe-3">Acciones Administrativas</th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($listaUsuarios)): ?>
                    <?php while($user = $listaUsuarios->fetch_object()): ?>
                    <tr>
                        <td class="ps-3 fw-bold text-secondary">#<?= str_pad($user->id, 4, "0", STR_PAD_LEFT) ?></td>
                        <td>
                            <div class="d-flex align-items-center py-1">
                                <?php 
                                    $userImg = !empty($user->image) ? $user->image : 'default_user.png'; 
                                ?>
                                <img src="<?= base_url ?>assets/img/<?= htmlspecialchars($userImg, ENT_QUOTES) ?>" 
                                     class="rounded-circle border shadow-sm me-3" 
                                     style="width: 45px; height: 45px; object-fit: cover;"
                                     onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($user->fullname) ?>&background=e2e8f0&color=004b87';">
                                <div>
                                    <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($user->fullname, ENT_QUOTES) ?></div>
                                    <div class="text-muted small fw-semibold"><i class="fa-solid fa-envelope me-1 opacity-50"></i><?= htmlspecialchars($user->email, ENT_QUOTES) ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if($user->role == 'ADMIN'): ?>
                                <span class="badge bg-primary-dark shadow-sm px-3 py-2 rounded-pill">
                                    <i class="fa-solid fa-user-shield me-1"></i> Administrador
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary shadow-sm px-3 py-2 rounded-pill">
                                    <i class="fa-solid fa-user-helmet text-white me-1"></i> Jefe de Obra
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted small fw-bold">
                            <i class="fa-regular fa-calendar-days me-1"></i> <?= date('d / m / Y', strtotime($user->created_at)) ?>
                        </td>
                        <td class="text-end pe-3">
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="<?= base_url ?>Admin/editUser?id=<?= $user->id ?>" class="btn btn-sm btn-light border text-primary shadow-sm" title="Modificar Perfil">
                                    <i class="fa-solid fa-user-pen"></i>
                                </a>
                                <?php if($user->id != $_SESSION['identity']->id): ?>
                                <a href="<?= base_url ?>Admin/deleteUser?id=<?= $user->id ?>" class="btn btn-sm btn-light border text-danger shadow-sm btn-delete" title="Inhabilitar Usuario" onclick="return confirm('ATENCIÓN: ¿Confirma la eliminación definitiva de este funcionario y sus permisos de acceso?');">
                                    <i class="fa-solid fa-user-slash"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-dark text-white py-3 border-0">
                <h6 class="modal-title fw-bold text-uppercase" style="letter-spacing: 1px;"><i class="fa-solid fa-user-plus me-2 text-primary"></i>Alta de Personal Operativo</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url ?>Admin/saveUser" method="POST">
                <div class="modal-body p-4 bg-light">
                    <div class="bg-white p-4 rounded-3 shadow-sm border mb-3">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary text-uppercase">Nombre y Apellido</label>
                            <input type="text" name="fullname" class="form-control fw-bold border-secondary" placeholder="Ej: Carlos Mendoza" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary text-uppercase">Correo Institucional</label>
                            <input type="email" name="email" class="form-control border-secondary" placeholder="usuario@constructora.com" required>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-secondary text-uppercase">Contraseña Inicial</label>
                                <input type="password" name="password" class="form-control border-secondary" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-secondary text-uppercase">Rol Asignado</label>
                                <select name="role" class="form-select fw-bold border-secondary">
                                    <option value="USER">Personal de Obra</option>
                                    <option value="ADMIN">Administrador de Sistema</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="text-muted small text-center">
                        <i class="fa-solid fa-circle-info me-1 text-primary"></i> El usuario podrá cambiar su avatar y contraseña desde su panel.
                    </div>
                </div>
                <div class="modal-footer bg-white border-0 py-3">
                    <button type="button" class="btn btn-outline-secondary fw-bold rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm rounded-pill"><i class="fa-solid fa-check me-2"></i> Confirmar Registro</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Inicialización del motor DataTables
    $(document).ready(function() {
        $('#tablaUsuarios').DataTable({
            responsive: true,
            language: {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
                "emptyTable": "No hay usuarios registrados en el sistema."
            },
            pageLength: 10,
            lengthMenu: [ [5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"] ],
            order: [[0, "desc"]], // Ordena por el ID más reciente primero
            columnDefs: [
                { orderable: false, targets: [4] } // Desactiva el ordenamiento en la columna de Acciones
            ]
        });
    });
</script>

<?php if(isset($_SESSION['alert_message'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '<?= $_SESSION['alert_icon'] == "success" ? "Operación Exitosa" : "Acción Denegada" ?>',
                text: '<?= addslashes($_SESSION['alert_message']) ?>',
                icon: '<?= $_SESSION['alert_icon'] ?>',
                confirmButtonColor: '#004b87'
            });
        });
    </script>
    <?php 
        // Es vital limpiar la variable después de mostrarla, sino saldrá cada vez que recargue
        unset($_SESSION['alert_message']); 
        unset($_SESSION['alert_icon']); 
    ?>
<?php endif; ?>