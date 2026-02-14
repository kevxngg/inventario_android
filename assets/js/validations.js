/**
 * VALIDACIONES DE FORMULARIOS - CLIENT SIDE
 */

document.addEventListener("DOMContentLoaded", function() {
    
    // Seleccionar todos los formularios del sistema
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            
            let isValid = true;
            let firstErrorField = null;

            // 1. Validar campos requeridos
            const requiredInputs = form.querySelectorAll('input[required], select[required], textarea[required]');
            
            requiredInputs.forEach(input => {
                // Limpiar errores previos
                input.classList.remove('is-invalid', 'shake-animation');
                
                if (!input.value.trim()) {
                    isValid = false;
                    markError(input);
                    if(!firstErrorField) firstErrorField = input;
                }
            });

            // 2. Validar Email (si existe)
            const emailInputs = form.querySelectorAll('input[type="email"]');
            emailInputs.forEach(input => {
                if (input.value && !validateEmail(input.value)) {
                    isValid = false;
                    markError(input);
                    if(!firstErrorField) firstErrorField = input;
                }
            });

            // 3. Validar Contraseñas (Mínimo 6 caracteres)
            const passInputs = form.querySelectorAll('input[type="password"]');
            passInputs.forEach(input => {
                if (input.value && input.value.length < 6) {
                    isValid = false;
                    markError(input);
                    // Mostrar alerta específica
                    Swal.fire({
                        icon: 'warning',
                        title: 'Contraseña muy corta',
                        text: 'La contraseña debe tener al menos 6 caracteres.',
                        confirmButtonColor: '#0061a4'
                    });
                    if(!firstErrorField) firstErrorField = input;
                }
            });

            // SI HAY ERRORES, DETENER EL ENVÍO
            if (!isValid) {
                event.preventDefault(); // ¡ALTO AHÍ!
                
                // Enfocar el primer error
                if(firstErrorField) firstErrorField.focus();

                // Alerta general si no es password
                if(!form.querySelector('input[type="password"].is-invalid')){
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    Toast.fire({
                        icon: 'error',
                        title: 'Por favor completa los campos obligatorios'
                    });
                }
            }
        });
    });
});

// Función auxiliar para marcar error visualmente
function markError(input) {
    input.classList.add('is-invalid'); // Borde rojo (Bootstrap)
    
    // Agregar animación de sacudida (Shake)
    input.classList.add('shake-animation');
    
    // Remover la animación después de 0.5s para poder repetirla
    setTimeout(() => {
        input.classList.remove('shake-animation');
    }, 500);
}

// Regex simple para email
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}