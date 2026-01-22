{{-- resources/views/users/external-doctor-show.blade.php --}}
@extends('layouts.app')

@section('title', 'Détails du Médecin Externe')

@section('content')
<div class="p-6">
    <div class="max-w-4xl mx-auto">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('external-doctors.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-800">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Retour à la liste
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Détails du Médecin Externe</h1>
            </div>

            <!-- Actions -->
            <div class="flex space-x-3">
                @if($externalDoctor->status === 'pending')
                <form method="POST" action="{{ route('external-doctors.approve', $externalDoctor) }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Approuver
                    </button>
                </form>
                <form method="POST" action="{{ route('external-doctors.reject', $externalDoctor) }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Rejeter
                    </button>
                </form>
                @endif
            </div>
        </div>

        <!-- Status Badge -->
        <div class="mb-6">
            @php
                $statusColors = [
                    'pending' => 'yellow',
                    'approved' => 'green',
                    'rejected' => 'red'
                ];
                $statusLabels = [
                    'pending' => 'En attente',
                    'approved' => 'Approuvé',
                    'rejected' => 'Rejeté'
                ];
                $color = $statusColors[$externalDoctor->status] ?? 'gray';
                $label = $statusLabels[$externalDoctor->status] ?? 'Inconnu';
            @endphp
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $color }}-100 text-{{ $color }}-800">
                {{ $label }}
            </span>
        </div>

        <!-- Informations Personnelles -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Informations Personnelles</h3>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nom complet</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $externalDoctor->first_name }} {{ $externalDoctor->last_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $externalDoctor->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Téléphone</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $externalDoctor->phone ?? 'Non fourni' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Spécialité</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $externalDoctor->speciality ?? 'Non spécifiée' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Numéro de licence</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $externalDoctor->license_number }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date d'inscription</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $externalDoctor->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents -->
        @if($externalDoctor->documents && count($externalDoctor->documents) > 0)
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Documents Fournis</h3>
            </div>
            <div class="px-6 py-4">
                <div class="space-y-3">
                    @foreach($externalDoctor->documents as $document)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-8 h-8 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $document->name }}</p>
                                <p class="text-xs text-gray-500">{{ $document->type }}</p>
                            </div>
                        </div>
                        <a href="{{ route('documents.download', $document) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            Télécharger
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Historique des Actions -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Historique des Actions</h3>
            </div>
            <div class="px-6 py-4">
                @if($externalDoctor->auditLogs && count($externalDoctor->auditLogs) > 0)
                <div class="space-y-4">
                    @foreach($externalDoctor->auditLogs as $log)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            @if($log->action === 'approve')
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            @elseif($log->action === 'reject')
                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                            @else
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900">{{ $log->description }}</p>
                            <p class="text-xs text-gray-500">
                                Par {{ $log->user->name ?? 'Système' }} le {{ $log->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-500">Aucune action enregistrée pour ce médecin externe.</p>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
