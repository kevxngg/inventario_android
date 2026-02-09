/**
 * EFECTOS VISUALES Y ANIMACIONES - ANDROID 16 STYLE
 */

document.addEventListener("DOMContentLoaded", function() {
    
    // 1. ANIMACIÓN DE ENTRADA (FADE IN)
    // Busca todos los elementos con la clase .fade-in-up y los activa en cascada
    const observerOptions = {
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = "1";
                entry.target.style.transform = "translateY(0)";
                observer.unobserve(entry.target); // Solo animar una vez
            }
        });
    }, observerOptions);

    document.querySelectorAll('.fade-in-up').forEach((el) => {
        // Estado inicial (si no está definido en CSS)
        el.style.opacity = "0";
        el.style.transform = "translateY(20px)";
        el.style.transition = "opacity 0.6s ease-out, transform 0.6s ease-out";
        observer.observe(el);
    });

    // 2. LÓGICA DEL SIDEBAR (MENÚ LATERAL)
    const wrapper = document.getElementById("wrapper");
    const menuToggle = document.getElementById("menu-toggle");

    if(wrapper && menuToggle){
        menuToggle.addEventListener("click", function(e) {
            e.preventDefault();
            wrapper.classList.toggle("toggled");
        });
    }

    // 3. CONFIRMACIONES CON SWEETALERT (GENÉRICO)
    // Detecta cualquier botón con la clase .btn-delete y pide confirmación
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');

            Swal.fire({
                title: '¿Estás seguro?',
                text: "No podrás revertir esta acción",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#0061a4',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                background: '#f0f7ff',
                borderRadius: '20px'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });
    });
});