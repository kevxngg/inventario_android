/**
 * LÓGICA DE UI E INTERACCIÓN - ANDROID LIQUID
 */

document.addEventListener("DOMContentLoaded", function() {
    
    // 1. SELECTORES
    const wrapper = document.getElementById("wrapper");
    const menuToggle = document.getElementById("menu-toggle");
    
    // Crear dinámicamente el overlay si no existe
    let overlay = document.getElementById("overlay");
    if (!overlay) {
        overlay = document.createElement("div");
        overlay.id = "overlay";
        document.body.appendChild(overlay);
    }

    // 2. FUNCIÓN TOGGLE (ABRIR/CERRAR MENÚ)
    if(menuToggle){
        menuToggle.addEventListener("click", function(e) {
            e.preventDefault();
            wrapper.classList.toggle("toggled");
            toggleOverlay();
        });
    }

    // 3. CERRAR AL TOCAR EL OVERLAY (UX MÓVIL)
    overlay.addEventListener("click", function() {
        wrapper.classList.remove("toggled");
        toggleOverlay();
    });

    function toggleOverlay() {
        // En móvil (pantalla < 992px)
        if (window.innerWidth <= 992) {
            if (wrapper.classList.contains("toggled")) {
                overlay.classList.add("active");
            } else {
                overlay.classList.remove("active");
            }
        }
    }

    // 4. ANIMACIONES DE ENTRADA (Intersection Observer)
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add("visible");
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.fade-in-up').forEach((el) => {
        el.style.opacity = "0";
        el.style.transform = "translateY(30px)";
        el.style.transition = "all 0.6s cubic-bezier(0.25, 1, 0.5, 1)"; // Easing suave
        observer.observe(el);
    });
    
    // CSS class para el estado visible
    const style = document.createElement('style');
    style.innerHTML = `
        .fade-in-up.visible { opacity: 1 !important; transform: translateY(0) !important; }
    `;
    document.head.appendChild(style);

    // 5. CONFIRMACIONES SWEETALERT (Estilizadas)
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');

            Swal.fire({
                title: '¿Eliminar registro?',
                text: "Esta acción es irreversible",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ba1a1a', // Rojo Android
                cancelButtonColor: '#535f70',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                // Estilo personalizado del modal
                customClass: {
                    popup: 'glass-card'
                },
                background: 'rgba(255, 255, 255, 0.95)',
                backdrop: `rgba(0, 60, 100, 0.4) backdrop-filter: blur(4px)`
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });
    });
});