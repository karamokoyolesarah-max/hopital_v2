<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portail Médecin Externe - HospitSIS</title>
    <script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="bg-gradient-to-br from-emerald-50 to-teal-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-emerald-700 mb-2">Portail Médecin Externe</h1>
            <p class="text-gray-600">Rejoignez notre réseau de professionnels de santé</p>
        </div>

        <!-- Formulaire d'inscription -->
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-2xl p-8">
            <form method="POST" action="{{ route('external.register.submit') }}" class="space-y-6">
                @csrf

                <!-- Titre du formulaire -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800">Inscrivez-vous ici</h1>
                    <h2 class="text-2xl font-semibold text-gray-700 mt-2">Monsieur/Madame</h2>
                    <p class="text-gray-600 mt-2">Veuillez remplir le formulaire ci-dessous pour rejoindre notre réseau</p>
                </div>

                <!-- Informations personnelles -->
               <div class="border-b pb-6">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Informations personnelles</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Prénom *</label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            @error('first_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nom *</label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            @error('last_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone *</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            @error('phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Informations professionnelles -->
                <div class="border-b pb-6">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Informations professionnelles</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Spécialité *</label>
                            <select name="speciality" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                <option value="">Sélectionnez une spécialité</option>
                                <option value="Cardiologie">Cardiologie</option>
                                <option value="Dermatologie">Dermatologie</option>
                                <option value="Pédiatrie">Pédiatrie</option>
                                <option value="Gynécologie">Gynécologie</option>
                                <option value="Neurologie">Neurologie</option>
                                <option value="Ophtalmologie">Ophtalmologie</option>
                                <option value="Psychiatrie">Psychiatrie</option>
                                <option value="Médecine générale">Médecine générale</option>
                                <option value="Autre">Autre</option>
                            </select>
                            @error('speciality')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Numéro de licence *</label>
                            <input type="text" name="license_number" value="{{ old('license_number') }}" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            @error('license_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tarif consultation (FCFA)</label>
                            <input type="number" name="consultation_fee" value="{{ old('consultation_fee') }}" min="0"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            @error('consultation_fee')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Qualifications / Diplômes</label>
                            <textarea name="qualifications" rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">{{ old('qualifications') }}</textarea>
                            @error('qualifications')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Biographie</label>
                            <textarea name="bio" rows="4"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">{{ old('bio') }}</textarea>
                            @error('bio')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Mot de passe -->
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Sécurité</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mot de passe *</label>
                            <input type="password" name="password" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            @error('password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirmer le mot de passe *</label>
                            <input type="password" name="password_confirmation" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Boutons -->
                <div class="flex items-center justify-between pt-6">
                    <a href="{{ route('home') }}" class="text-emerald-600 hover:text-emerald-800 font-medium">
                        ← Retour à l'accueil
                    </a>
                    <button type="submit"
                        class="bg-emerald-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-emerald-700 transition-colors shadow-lg">
                        S'inscrire
                    </button>
                </div>

                <div class="text-center text-sm text-gray-600 pt-4">
                    Vous avez déjà un compte ?
                    <a href="{{ route('external.login') }}" class="text-emerald-600 hover:text-emerald-800 font-medium">
                        Se connecter
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
