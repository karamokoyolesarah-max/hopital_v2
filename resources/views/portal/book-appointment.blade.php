<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prendre un Rendez-vous</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        .fade-in { animation: fadeIn 0.4s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        #map { height: 300px; border-radius: 1rem; }
        .price-negotiation { border: 2px dashed #10b981; background: #f0fdf4; }
    </style>
</head>
<body class="bg-gray-50">
    
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('patient.dashboard') }}" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <h1 class="text-lg font-bold text-gray-900">Prendre un rendez-vous</h1>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div id="selection-container" class="fade-in">
            <h2 class="text-2xl font-bold text-gray-800 mb-8 text-center">Où souhaitez-vous consulter ?</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div onclick="openForm('hospital')" class="bg-white p-8 rounded-3xl shadow-sm border-2 border-transparent hover:border-blue-500 hover:shadow-xl transition-all cursor-pointer group text-center">
                    <div class="w-20 h-20 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                        <i class="fas fa-hospital text-blue-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">À l'hôpital</h3>
                    <p class="text-gray-500 text-sm mb-6">Consultation dans nos locaux avec tout l'équipement médical.</p>
                    <span class="bg-blue-600 text-white px-6 py-2 rounded-full font-medium">Choisir</span>
                </div>

                <div onclick="openForm('home')" class="bg-white p-8 rounded-3xl shadow-sm border-2 border-transparent hover:border-green-500 hover:shadow-xl transition-all cursor-pointer group text-center">
                    <div class="w-20 h-20 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                        <i class="fas fa-home text-green-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">À domicile</h3>
                    <p class="text-gray-500 text-sm mb-6">Un spécialiste se déplace directement chez vous.</p>
                    <span class="bg-green-600 text-white px-6 py-2 rounded-full font-medium">Choisir</span>
                </div>
            </div>
        </div>

        <!-- FORMULAIRE HÔPITAL -->
        <div id="form-hospital" class="hidden fade-in">
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-blue-600 p-6 text-white flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold">Rendez-vous à l'hôpital</h2>
                        <p class="text-blue-100 text-sm">Veuillez remplir les informations suivantes</p>
                    </div>
                    <button onclick="showSelection()" class="text-white hover:bg-blue-700 p-2 rounded-full">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form method="POST" action="{{ route('patient.book-appointment.store') }}" class="p-8 space-y-6">
                    @csrf
                    <input type="hidden" name="consultation_type" value="hospital">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Choisir un hôpital</label>
                        <select name="hospital_id" id="hospital_id" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">-- Sélectionner un hôpital --</option>
                            @foreach($hospitals as $hospital)
                                <option value="{{ $hospital->id }}">{{ $hospital->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Choisir une prestation</label>
                        <select name="prestation_id" id="prestation_id" required disabled class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">-- Sélectionner d'abord un hôpital --</option>
                        </select>
                    </div>

                    <div id="price-display" class="bg-green-50 p-4 rounded-xl border border-green-200 hidden">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-green-800">Prix de la consultation:</span>
                            <span id="prestation-price" class="text-lg font-bold text-green-600"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Date du RDV</label>
                            <input type="date" name="appointment_date" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Heure</label>
                            <select name="appointment_time" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                                <option value="08:00">08:00</option>
                                <option value="09:00">09:00</option>
                                <option value="10:00">10:00</option>
                                <option value="11:00">11:00</option>
                                <option value="14:00">14:00</option>
                                <option value="15:00">15:00</option>
                                <option value="16:00">16:00</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Spécialité médicale</label>
                        <select name="service_id" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">-- Choisir une spécialité --</option>
                            <option value="1">Médecine générale</option>
                            <option value="2">Cardiologie</option>
                            <option value="3">Neurologie</option>
                            <option value="4">Pédiatrie</option>
                            <option value="5">Gynécologie</option>
                            <option value="6">Dermatologie</option>
                            <option value="7">ORL</option>
                            <option value="8">Ophtalmologie</option>
                            <option value="9">Orthopédie</option>
                            <option value="10">Psychiatrie</option>
                            <option value="11">Radiologie</option>
                            <option value="12">Urgences</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Motif de consultation</label>
                        <textarea name="reason" rows="3" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Décrivez brièvement votre besoin..."></textarea>
                    </div>

                    <button type="submit" class="w-full py-4 bg-blue-600 text-white rounded-2xl font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all">
                        Confirmer le rendez-vous
                    </button>
                </form>
            </div>
        </div>

        <!-- FORMULAIRE DOMICILE -->
        <div id="form-home" class="hidden fade-in">
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-green-600 p-6 text-white flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold">Rendez-vous à domicile</h2>
                        <p class="text-green-100 text-sm">Un spécialiste se déplacera à votre adresse</p>
                    </div>
                    <button onclick="showSelection()" class="text-white hover:bg-green-700 p-2 rounded-full">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form method="POST" action="{{ route('patient.book-appointment.home.store') }}" id="homeAppointmentForm" class="p-8 space-y-6">
                    @csrf
                    <input type="hidden" name="consultation_type" value="home">
                    <input type="hidden" name="patient_latitude" id="patient_latitude">
                    <input type="hidden" name="patient_longitude" id="patient_longitude">
                    <input type="hidden" name="patient_full_address" id="patient_full_address">
                    
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 rounded-2xl border border-green-200">
                        <h3 class="font-bold text-green-800 mb-4 flex items-center">
                            <i class="fas fa-user-circle mr-2"></i> Vos informations
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nom complet</label>
                                <input type="text" value="{{ $patient->first_name }} {{ $patient->last_name }}" readonly class="w-full px-4 py-2 bg-white border border-green-200 rounded-xl">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Téléphone</label>
                                <input type="text" value="{{ $patient->phone }}" readonly class="w-full px-4 py-2 bg-white border border-green-200 rounded-xl">
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 p-6 rounded-2xl border border-yellow-200">
                        <label class="block text-sm font-bold text-yellow-800 mb-3 flex items-center">
                            <i class="fas fa-map-marker-alt mr-2"></i>Votre localisation (obligatoire)
                        </label>
                        <button type="button" onclick="getUserLocation()" class="w-full mb-3 py-3 bg-yellow-500 text-white rounded-xl font-semibold hover:bg-yellow-600 transition">
                            <i class="fas fa-crosshairs mr-2"></i>Me géolocaliser automatiquement
                        </button>
                        <div id="map" class="mb-3"></div>
                        <textarea name="home_address" id="home_address" required rows="2" class="w-full px-4 py-3 border border-yellow-300 rounded-xl focus:ring-2 focus:ring-yellow-500 outline-none" placeholder="Adresse complète : Rue, Quartier, N° de porte, Ville..."></textarea>
                        <p class="text-xs text-yellow-700 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Cette adresse permettra au médecin de vous localiser facilement
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-stethoscope mr-2 text-green-600"></i>Spécialité recherchée
                        </label>
                        <select name="specialty_requested" id="specialty_requested" required onchange="loadAvailableDoctors()" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none">
                            <option value="">-- Choisir une spécialité --</option>
                            <option value="Médecine générale">Médecine générale</option>
                            <option value="Cardiologie">Cardiologie</option>
                            <option value="Neurologie">Neurologie</option>
                            <option value="Pédiatrie">Pédiatrie</option>
                            <option value="Gynécologie">Gynécologie</option>
                            <option value="Infirmier">Infirmier(ère)</option>
                            <option value="Sage-femme">Sage-femme</option>
                            <option value="Kinésithérapie">Kinésithérapie</option>
                            <option value="Psychiatrie">Psychiatrie</option>
                        </select>
                    </div>

                    <div id="available-doctors" class="hidden">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Spécialistes disponibles</label>
                        <div id="doctors-list" class="space-y-3"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Date souhaitée</label>
                            <input type="date" name="appointment_date" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tranche horaire</label>
                            <select name="appointment_time" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none">
                                <option value="matin">Matin (08h - 12h)</option>
                                <option value="apres-midi">Après-midi (14h - 18h)</option>
                                <option value="soir">Soir (18h - 21h)</option>
                            </select>
                        </div>
                    </div>

                    <div id="price-negotiation" class="price-negotiation p-6 rounded-2xl hidden">
                        <h3 class="font-bold text-green-800 mb-4 flex items-center">
                            <i class="fas fa-coins mr-2"></i>Proposer un prix
                        </h3>
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-2">Prix de base du médecin : <span id="doctor-base-price" class="font-bold text-green-700"></span> FCFA</p>
                            <p class="text-xs text-gray-500">Vous pouvez proposer un prix. Le médecin pourra accepter ou contre-proposer.</p>
                        </div>
                        <input type="number" name="proposed_price" id="proposed_price" min="10000" step="1000" placeholder="Votre proposition (FCFA)" class="w-full px-4 py-3 border border-green-300 rounded-xl focus:ring-2 focus:ring-green-500 outline-none">
                        <input type="hidden" name="doctor_base_price" id="doctor_base_price_hidden">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Motif de consultation</label>
                        <textarea name="reason" rows="4" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none" placeholder="Décrivez vos symptômes ou votre besoin médical..."></textarea>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-xl border border-blue-200">
                        <h4 class="font-bold text-blue-800 mb-2 flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>Informations importantes
                        </h4>
                        <ul class="text-sm text-blue-700 space-y-1">
                            <li>✓ Vous recevrez une notification dès qu'un médecin accepte votre demande</li>
                            <li>✓ Vous pourrez suivre sa position en temps réel lors du déplacement</li>
                            <li>✓ Le paiement se fera après les soins</li>
                        </ul>
                    </div>

                    <button type="submit" class="w-full py-4 bg-green-600 text-white rounded-2xl font-bold hover:bg-green-700 shadow-lg shadow-green-200 transition-all">
                        <i class="fas fa-paper-plane mr-2"></i>Demander une visite à domicile
                    </button>
                </form>
            </div>
        </div>

    </main>

    <script>
        let map, marker;
        let userLatitude, userLongitude;

        function openForm(type) {
            document.getElementById('selection-container').classList.add('hidden');
            if(type === 'hospital') {
                document.getElementById('form-hospital').classList.remove('hidden');
            } else {
                document.getElementById('form-home').classList.remove('hidden');
                setTimeout(initMap, 300);
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function showSelection() {
            document.getElementById('form-hospital').classList.add('hidden');
            document.getElementById('form-home').classList.add('hidden');
            document.getElementById('selection-container').classList.remove('hidden');
        }

        function initMap() {
            map = L.map('map').setView([5.3600, -4.0083], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
        }

        function getUserLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        userLatitude = position.coords.latitude;
                        userLongitude = position.coords.longitude;

                        document.getElementById('patient_latitude').value = userLatitude;
                        document.getElementById('patient_longitude').value = userLongitude;

                        if (marker) map.removeLayer(marker);
                        marker = L.marker([userLatitude, userLongitude]).addTo(map);
                        map.setView([userLatitude, userLongitude], 15);

                        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${userLatitude}&lon=${userLongitude}&format=json`)
                            .then(res => res.json())
                            .then(data => {
                                const address = data.display_name;
                                document.getElementById('home_address').value = address;
                                document.getElementById('patient_full_address').value = address;
                            });

                        alert('Votre position a été enregistrée avec succès !');
                    },
                    function(error) {
                        alert('Impossible d\'obtenir votre position. Veuillez autoriser la géolocalisation.');
                    }
                );
            } else {
                alert('Votre navigateur ne supporte pas la géolocalisation.');
            }
        }

        async function loadAvailableDoctors() {
            const specialty = document.getElementById('specialty_requested').value;
            if (!specialty) return;

            try {
                const response = await fetch(`/api/v1/external-doctors/available?specialty=${specialty}`);
                const doctors = await response.json();

                const container = document.getElementById('doctors-list');
                const availableSection = document.getElementById('available-doctors');

                if (doctors.length === 0) {
                    container.innerHTML = '<p class="text-gray-500">Aucun spécialiste disponible pour cette spécialité.</p>';
                    availableSection.classList.remove('hidden');
                    return;
                }

                container.innerHTML = doctors.map(doctor => `
                    <div class="border-2 border-green-200 rounded-xl p-4 hover:border-green-500 transition cursor-pointer" onclick="selectDoctor(${doctor.id}, '${doctor.full_name}', ${doctor.consultation_fee})">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-bold text-gray-800">${doctor.full_name}</h4>
                                <p class="text-sm text-gray-600">${doctor.specialty}</p>
                                <p class="text-xs text-gray-500 mt-1"><i class="fas fa-map-marker-alt"></i> ${doctor.address || 'Adresse non spécifiée'}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-green-600">${doctor.consultation_fee.toLocaleString()} FCFA</p>
                                <span class="text-xs ${doctor.is_online ? 'text-green-600' : 'text-gray-400'}">
                                    <i class="fas fa-circle"></i> ${doctor.is_online ? 'En ligne' : 'Hors ligne'}
                                </span>
                            </div>
                        </div>
                    </div>
                `).join('');

                availableSection.classList.remove('hidden');
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        function selectDoctor(doctorId, doctorName, basePrice) {
            document.getElementById('doctor-base-price').textContent = basePrice.toLocaleString();
            document.getElementById('doctor_base_price_hidden').value = basePrice;
            document.getElementById('price-negotiation').classList.remove('hidden');
            
            const form = document.getElementById('homeAppointmentForm');
            let doctorInput = form.querySelector('input[name="external_doctor_id"]');
            if (!doctorInput) {
                doctorInput = document.createElement('input');
                doctorInput.type = 'hidden';
                doctorInput.name = 'external_doctor_id';
                form.appendChild(doctorInput);
            }
            doctorInput.value = doctorId;

            alert(`Vous avez sélectionné ${doctorName}. Vous pouvez proposer un prix ci-dessous.`);
        }

        // Hospital and Prestation selection functionality
        document.getElementById('hospital_id').addEventListener('change', async function() {
            const hospitalId = this.value;
            const prestationSelect = document.getElementById('prestation_id');
            const priceDisplay = document.getElementById('price-display');

            if (!hospitalId) {
                prestationSelect.innerHTML = '<option value="">-- Sélectionner d\'abord un hôpital --</option>';
                prestationSelect.disabled = true;
                priceDisplay.classList.add('hidden');
                return;
            }

            try {
                const response = await fetch(`/portal/api/hospitals/${hospitalId}/prestations`);
                const prestations = await response.json();

                if (prestations.length > 0) {
                    prestationSelect.innerHTML = '<option value="">-- Choisir une prestation --</option>' +
                        prestations.map(p => `<option value="${p.id}" data-price="${p.price}">${p.name} (${p.service_name})</option>`).join('');
                    prestationSelect.disabled = false;
                } else {
                    prestationSelect.innerHTML = '<option value="">Aucune prestation disponible</option>';
                    prestationSelect.disabled = true;
                }
            } catch (error) {
                console.error('Erreur:', error);
                prestationSelect.innerHTML = '<option value="">Erreur de chargement</option>';
                prestationSelect.disabled = true;
            }
        });

        document.getElementById('prestation_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const priceDisplay = document.getElementById('price-display');
            const priceSpan = document.getElementById('prestation-price');

            if (selectedOption.value && selectedOption.dataset.price) {
                const price = parseFloat(selectedOption.dataset.price);
                priceSpan.textContent = price.toLocaleString('fr-FR') + ' FCFA';
                priceDisplay.classList.remove('hidden');
            } else {
                priceDisplay.classList.add('hidden');
            }
        });

        async function findNearestHospital() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(async function(position) {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;

                    try {
                        const response = await fetch(`/api/v1/hospitals/nearest?lat=${lat}&lon=${lon}`);
                        const hospitals = await response.json();

                        const resultDiv = document.getElementById('nearest-hospital-result');
                        if (hospitals.length > 0) {
                            resultDiv.innerHTML = hospitals.map((h, index) => `
                                <div class="mt-2 p-3 bg-white rounded-lg border border-blue-200">
                                    <p class="font-semibold text-blue-800">${index + 1}. ${h.name}</p>
                                    <p class="text-sm text-gray-600">${h.address}</p>
                                    <p class="text-xs text-blue-600 mt-1">Distance : ${h.distance} km</p>
                                </div>
                            `).join('');
                            resultDiv.classList.remove('hidden');
                        } else {
                            resultDiv.innerHTML = '<p class="text-sm text-gray-500 mt-2">Aucun hôpital trouvé à proximité.</p>';
                            resultDiv.classList.remove('hidden');
                        }
                    } catch (error) {
                        alert('Erreur lors de la recherche d\'hôpitaux.');
                    }
                });
            } else {
                alert('Géolocalisation non supportée par votre navigateur.');
            }
        }
    </script>
</body>
</html>