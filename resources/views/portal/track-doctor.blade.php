<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivre le médecin en temps réel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        #tracking-map { height: 70vh; border-radius: 1rem; }
        .pulse { animation: pulse 2s infinite; }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>
<body class="bg-gray-50">
    
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('patient.appointments') }}" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <h1 class="text-lg font-bold text-gray-900">Suivi en temps réel</h1>
                </div>
                <div id="status-badge" class="px-4 py-2 rounded-full text-sm font-semibold"></div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Médecin</p>
                    <p class="font-bold text-gray-900">{{ $appointment->externalDoctor->full_name ?? 'En attente' }}</p>
                    <p class="text-sm text-gray-600">{{ $appointment->specialty_requested }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Date et heure</p>
                    <p class="font-bold text-gray-900">{{ $appointment->appointment_datetime->format('d/m/Y') }}</p>
                    <p class="text-sm text-gray-600">{{ $appointment->appointment_datetime->format('H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Prix convenu</p>
                    <p class="font-bold text-green-600">
                        {{ number_format($appointment->negotiated_price ?? $appointment->proposed_price ?? $appointment->doctor_base_price, 0, ',', ' ') }} FCFA
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-900">
                    <i class="fas fa-map-marked-alt text-blue-600 mr-2"></i>
                    Position en temps réel
                </h2>
                <button onclick="centerMap()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-crosshairs mr-2"></i>Recentrer
                </button>
            </div>
            <div id="tracking-map"></div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Statut de la visite</h3>
            <div class="space-y-4">
                <div class="flex items-center" id="status-requested">
                    <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center text-white mr-4">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Demande envoyée</p>
                        <p class="text-sm text-gray-500">{{ $appointment->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                <div class="flex items-center" id="status-assigned">
                    <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-white mr-4">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-600">Médecin assigné</p>
                        <p class="text-sm text-gray-400">En attente</p>
                    </div>
                </div>

                <div class="flex items-center" id="status-on-the-way">
                    <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-white mr-4">
                        <i class="fas fa-car"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-600">En route</p>
                        <p class="text-sm text-gray-400">En attente</p>
                    </div>
                </div>

                <div class="flex items-center" id="status-arrived">
                    <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-white mr-4">
                        <i class="fas fa-home"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-600">Arrivé</p>
                        <p class="text-sm text-gray-400">En attente</p>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script>
        let map, patientMarker, doctorMarker;
        const appointmentId = {{ $appointment->id }};
        const patientLat = {{ $appointment->patient_latitude }};
        const patientLon = {{ $appointment->patient_longitude }};

        function initMap() {
            map = L.map('tracking-map').setView([patientLat, patientLon], 14);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            const homeIcon = L.divIcon({
                html: '<div style="background: #10b981; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;"><i class="fas fa-home text-white"></i></div>',
                iconSize: [40, 40],
                className: ''
            });
            patientMarker = L.marker([patientLat, patientLon], {icon: homeIcon}).addTo(map);
            patientMarker.bindPopup("<b>Votre domicile</b>");

            startTracking();
        }

        function startTracking() {
            updateDoctorLocation();
            setInterval(updateDoctorLocation, 10000);
        }

        async function updateDoctorLocation() {
            try {
                const response = await fetch(`/api/v1/appointments/${appointmentId}/track`);
                if (!response.ok) return;

                const data = await response.json();
                
                if (data.latitude && data.longitude) {
                    const doctorLat = parseFloat(data.latitude);
                    const doctorLon = parseFloat(data.longitude);

                    if (!doctorMarker) {
                        const carIcon = L.divIcon({
                            html: '<div style="background: #3b82f6; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;" class="pulse"><i class="fas fa-car text-white"></i></div>',
                            iconSize: [40, 40],
                            className: ''
                        });
                        doctorMarker = L.marker([doctorLat, doctorLon], {icon: carIcon}).addTo(map);
                        doctorMarker.bindPopup("<b>Dr {{ $appointment->externalDoctor->full_name ?? 'Médecin' }}</b><br>En route...");
                    } else {
                        doctorMarker.setLatLng([doctorLat, doctorLon]);
                    }

                    if (window.routeLine) {
                        map.removeLayer(window.routeLine);
                    }
                    window.routeLine = L.polyline([
                        [doctorLat, doctorLon],
                        [patientLat, patientLon]
                    ], {color: '#3b82f6', weight: 3, dashArray: '10, 10'}).addTo(map);

                    updateStatus(data.status);
                }
            } catch (error) {
                console.error('Erreur lors de la récupération de la position:', error);
            }
        }

        function updateStatus(status) {
            const statusMap = {
                'doctor_assigned': {elem: 'status-assigned', color: 'bg-blue-500', text: 'Médecin assigné'},
                'on_the_way': {elem: 'status-on-the-way', color: 'bg-orange-500', text: 'En route'},
                'arrived': {elem: 'status-arrived', color: 'bg-green-500', text: 'Arrivé'}
            };

            if (statusMap[status]) {
                const elem = document.getElementById(statusMap[status].elem);
                const circle = elem.querySelector('div');
                circle.className = `w-10 h-10 rounded-full ${statusMap[status].color} flex items-center justify-center text-white mr-4`;
                
                const badge = document.getElementById('status-badge');
                badge.textContent = statusMap[status].text;
                badge.className = `px-4 py-2 rounded-full text-sm font-semibold ${statusMap[status].color} text-white`;
            }
        }

        function centerMap() {
            if (doctorMarker && patientMarker) {
                const bounds = L.latLngBounds([patientMarker.getLatLng(), doctorMarker.getLatLng()]);
                map.fitBounds(bounds, {padding: [50, 50]});
            } else {
                map.setView([patientLat, patientLon], 14);
            }
        }

        document.addEventListener('DOMContentLoaded', initMap);
    </script>
</body>
</html>