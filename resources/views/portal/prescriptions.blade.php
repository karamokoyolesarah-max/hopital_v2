<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Ordonnances - Portail Patient</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800">

    <header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ url('/portal/dashboard') }}" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <h1 class="text-lg font-bold text-gray-900">Mes Ordonnances</h1>
                </div>
                <div class="flex items-center">
                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">
                        {{ count($prescriptions ?? []) }} Prescription(s)
                    </span>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Médecin</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Hôpital</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($prescriptions ?? [] as $prescription)
                            <tr>
                                <td class="px-6 py-4">{{ $prescription->date }}</td>
                                <td class="px-6 py-4">{{ $prescription->doctor_name }}</td>
                                <td class="px-6 py-4">{{ $prescription->hospital_name }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="#" class="text-blue-600 hover:underline">Télécharger</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-16 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-file-medical text-gray-200 text-5xl mb-4"></i>
                                        <p>Aucune ordonnance n'est encore disponible.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-8 p-4 bg-blue-50 rounded-xl flex items-start space-x-3">
            <i class="fas fa-info-circle text-blue-500 mt-1"></i>
            <p class="text-sm text-blue-800">Les ordonnances s'affichent ici après validation médicale.</p>
        </div>
    </main>
</body>
</html>