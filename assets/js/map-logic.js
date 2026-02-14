/**
 * LÓGICA DEL MAPA LEAFLET
 */

var map; // Variable global del mapa

function initMap(lat = 4.5709, lng = -74.2973) {
    // Verificar si existe el contenedor del mapa
    if (!document.getElementById('map')) return;

    // Inicializar mapa
    map = L.map('map').setView([lat, lng], 6);

    // Cargar capa (Skin del mapa)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Icono personalizado (Opcional - Estilo Android)
    var androidIcon = L.icon({
        iconUrl: 'assets/img/marker.png', // Asegúrate de tener una imagen o usa la default
        iconSize: [38, 38],
        iconAnchor: [19, 38],
        popupAnchor: [0, -30]
    });
}

// Función para viajar suavemente a una obra (FlyTo)
function flyToObra(lat, lng, titulo) {
    if (map) {
        map.flyTo([lat, lng], 15, {
            duration: 1.5,
            easeLinearity: 0.25
        });

        // Crear popup automático al llegar
        L.popup()
            .setLatLng([lat, lng])
            .setContent("<b>" + titulo + "</b><br>Ubicación seleccionada")
            .openOn(map);
    }
}

// Inicializar cuando cargue la página
document.addEventListener("DOMContentLoaded", function() {
    initMap();
});