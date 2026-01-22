<x-portal-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mon Profil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <div class="flex items-center space-x-6 mb-6">
                        <div class="flex-shrink-0">
                            @if($patient->profile_image)
                                <img class="h-24 w-24 rounded-full object-cover" src="{{ asset('storage/' . $patient->profile_image) }}" alt="Photo de profil">
                            @else
                                <div class="h-24 w-24 rounded-full bg-gray-300 flex items-center justify-center">
                                    <svg class="h-12 w-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $patient->first_name }} {{ $patient->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $patient->email }}</p>
                            <p class="text-sm text-gray-500">IPU: {{ $patient->ipu }}</p>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('patient.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Photo de profil -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Photo de profil</label>
                            <div class="flex items-center space-x-4">
                                <input type="file" name="profile_image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="text-xs text-gray-500">JPG, PNG ou GIF (max. 2MB)</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Informations personnelles -->
                            <div>
                                <h4 class="text-lg font-semibold mb-4">Informations Personnelles</h4>

                                <div class="mb-4">
                                    <label for="first_name" class="block text-sm font-medium text-gray-700">Prénom</label>
                                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $patient->first_name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div class="mb-4">
                                    <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $patient->name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div class="mb-4">
                                    <label for="dob" class="block text-sm font-medium text-gray-700">Date de naissance</label>
                                    <input type="date" name="dob" id="dob" value="{{ old('dob', $patient->dob ? $patient->dob->format('Y-m-d') : '') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div class="mb-4">
                                    <label for="gender" class="block text-sm font-medium text-gray-700">Sexe</label>
                                    <select name="gender" id="gender" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Sélectionner</option>
                                        <option value="Homme" {{ old('gender', $patient->gender) == 'Homme' ? 'selected' : '' }}>Masculin</option>
                                        <option value="Femme" {{ old('gender', $patient->gender) == 'Femme' ? 'selected' : '' }}>Féminin</option>
                                        <option value="Other" {{ old('gender', $patient->gender) == 'Other' ? 'selected' : '' }}>Autre</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="blood_group" class="block text-sm font-medium text-gray-700">Groupe sanguin</label>
                                    <select name="blood_group" id="blood_group" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Sélectionner</option>
                                        <option value="A+" {{ old('blood_group', $patient->blood_group) == 'A+' ? 'selected' : '' }}>A+</option>
                                        <option value="A-" {{ old('blood_group', $patient->blood_group) == 'A-' ? 'selected' : '' }}>A-</option>
                                        <option value="B+" {{ old('blood_group', $patient->blood_group) == 'B+' ? 'selected' : '' }}>B+</option>
                                        <option value="B-" {{ old('blood_group', $patient->blood_group) == 'B-' ? 'selected' : '' }}>B-</option>
                                        <option value="AB+" {{ old('blood_group', $patient->blood_group) == 'AB+' ? 'selected' : '' }}>AB+</option>
                                        <option value="AB-" {{ old('blood_group', $patient->blood_group) == 'AB-' ? 'selected' : '' }}>AB-</option>
                                        <option value="O+" {{ old('blood_group', $patient->blood_group) == 'O+' ? 'selected' : '' }}>O+</option>
                                        <option value="O-" {{ old('blood_group', $patient->blood_group) == 'O-' ? 'selected' : '' }}>O-</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Coordonnées -->
                            <div>
                                <h4 class="text-lg font-semibold mb-4">Coordonnées</h4>

                                <div class="mb-4">
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                                    <input type="tel" name="phone" id="phone" value="{{ old('phone', $patient->phone) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div class="mb-4">
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" name="email" id="email" value="{{ old('email', $patient->email) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div class="mb-4">
                                    <label for="address" class="block text-sm font-medium text-gray-700">Adresse</label>
                                    <textarea name="address" id="address" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('address', $patient->address) }}</textarea>
                                </div>

                                <div class="mb-4">
                                    <label for="city" class="block text-sm font-medium text-gray-700">Ville</label>
                                    <input type="text" name="city" id="city" value="{{ old('city', $patient->city) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div class="mb-4">
                                    <label for="postal_code" class="block text-sm font-medium text-gray-700">Code postal</label>
                                    <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $patient->postal_code) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>

                        <!-- Contact d'urgence -->
                        <div class="mt-6">
                            <h4 class="text-lg font-semibold mb-4">Contact d'Urgence</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="mb-4">
                                    <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700">Nom du contact</label>
                                    <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div class="mb-4">
                                    <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700">Téléphone du contact</label>
                                    <input type="tel" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>

                        <!-- Informations médicales -->
                        <div class="mt-6">
                            <h4 class="text-lg font-semibold mb-4">Informations Médicales</h4>
                            <div class="grid grid-cols-1 gap-6">
                                <div class="mb-4">
                                    <label for="allergies" class="block text-sm font-medium text-gray-700">Allergies</label>
                                    <textarea name="allergies" id="allergies" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('allergies', is_array($patient->allergies) ? implode(', ', $patient->allergies) : $patient->allergies) }}</textarea>
                                </div>

                                <div class="mb-4">
                                    <label for="medical_history" class="block text-sm font-medium text-gray-700">Antécédents médicaux</label>
                                    <textarea name="medical_history" id="medical_history" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('medical_history', $patient->medical_history) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Mettre à jour le profil
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-portal-layout>
