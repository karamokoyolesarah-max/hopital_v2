<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compte en attente - HospitSIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-yellow-50 to-orange-100 min-h-screen flex items-center justify-center">
    <div class="max-w-2xl mx-4">
        <div class="bg-white rounded-2xl shadow-2xl p-12 text-center">
            <!-- Icône -->
            <div class="bg-yellow-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <!-- Titre -->
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Compte en attente d'approbation</h1>
            
            <!-- Message -->
            <p class="text-gray-600 mb-8 leading-relaxed">
                Merci pour votre inscription, <strong>Dr. {{ Auth::guard('external_doctors')->user()->first_name }} {{ Auth::guard('external_doctors')->user()->last_name }}</strong> !
                <br><br>
                Votre demande d'inscription est actuellement en cours d'examen par notre équipe administrative.
                Vous recevrez un email de confirmation dès que votre compte sera approuvé.
                <br><br>
                <span class="text-sm text-gray-500">Cela peut prendre 24 à 48 heures.</span>
            </p>

            <!-- Informations du compte -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6 text-left">
                <h3 class="font-semibold text-gray-700 mb-3">Informations de votre compte :</h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li><strong>Email :</strong> {{ Auth::guard('external_doctors')->user()->email }}</li>
                    <li><strong>Numero de telephone :</strong> {{ Auth::guard('external_doctors')->user()->phone_number }}</li>
                    <li><strong>Spécialité :</strong> {{ Auth::guard('external_doctors')->user()->speciality }}</li>
                    <li><strong>Licence :</strong> {{ Auth::guard('external_doctors')->user()->license_number }}</li>
                    <li><strong>Statut :</strong> <span class="text-yellow-600 font-semibold">En attente</span></li>
                </ul>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <form method="POST" action="{{ route('external.logout') }}">
                    @csrf
                    <button type="submit" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors">
                        Se déconnecter
                    </button>
                </form>
                <a href="mailto:support@hospitsis.com" class="bg-emerald-600 text-white px-6 py-3 rounded-lg hover:bg-emerald-700 transition-colors inline-block">
                    Contacter le support/0705520080
                </a>
            </div>
        </div>
    </div>
</body>
</html>