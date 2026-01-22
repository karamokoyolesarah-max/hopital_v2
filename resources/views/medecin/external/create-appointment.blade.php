<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau rendez-vous - HospitSIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-8">
                    <h1 class="text-2xl font-bold text-emerald-700">HospitSIS</h1>
                    <div class="hidden md:flex space-x-4">
                        <a href="{{ route('external.doctor.dashboard') }}" class="text-gray-600 hover:text-emerald-600 px-3 py-2 rounded-lg hover:bg-gray-100">
                            Tableau de bord
                        </a>
                        <a href="{{ route('external.appointments') }}" class="text-gray-600 hover:text-emerald-600 px-3 py-2 rounded-lg hover:bg-gray-100">
                            Rendez-vous
                        </a>
                        <a href="{{ route('external.profile') }}" class="text-gray-600 hover:text-emerald-600 px-3 py-2 rounded-lg hover:bg-gray-100">
                            Mon profil
                        </a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-700">Dr. {{ auth()->guard('external_doctors')->user()->first_name }} {{ auth()->guard('external_doctors')->user()->last_name }}</p>
                        <p class="text-xs text-gray-500">{{ auth()->guard('external_doctors')->user()->speciality }}</p>
                    </div>
                    <form method="POST" action="{{ route('external.logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 text-sm">
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-800">Nouveau rendez-vous</h2>
                <p class="text-gray-600 mt-2">Planifiez une consultation pour un patient</p>
            </div>
            <a href="{{ route('external.appointments') }}" class="text-emerald-600 hover:text-emerald-800 font-medium">
                ← Retour aux rendez-vous
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Appointment Form -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <form method="POST" action="{{ route('external.appointments.store') }}">
                @csrf

                <!-- Patient Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informations du patient</h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="patient_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nom complet du patient *
                            </label>
                            <input type="text" id="patient_name" name="patient_name"
                                   value="{{ old('patient_name') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500"
                                   placeholder="Ex: Dupont Jean">
                        </div>

                        <div>
                            <label for="patient_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Téléphone du patient *
                            </label>
                            <input type="tel" id="patient_phone" name="patient_phone"
                                   value="{{ old('patient_phone') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500"
                                   placeholder="+225 XX XX XX XX">
                        </div>
                    </div>
                </div>

                <!-- Appointment Details -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Détails du rendez-vous</h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="appointment_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Date du rendez-vous *
                            </label>
                            <input type="date" id="appointment_date" name="appointment_date"
                                   value="{{ old('appointment_date') }}"
                                   min="{{ date('Y-m-d') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div>
                            <label for="appointment_time" class="block text-sm font-medium text-gray-700 mb-2">
                                Heure du rendez-vous *
                            </label>
                            <input type="time" id="appointment_time" name="appointment_time"
                                   value="{{ old('appointment_time') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Motif de la consultation *
                        </label>
                        <textarea id="reason" name="reason" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500"
                                  placeholder="Décrivez brièvement le motif de la consultation...">{{ old('reason') }}</textarea>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end pt-6 border-t border-gray-200">
                    <a href="{{ route('external.appointments') }}" class="mr-4 px-6 py-3 text-gray-600 hover:text-gray-800 font-medium">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-medium">
                        Créer le rendez-vous
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
