@extends('layouts.patient')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">
        <i class="fas fa-comments text-blue-600 mr-3"></i>Mes Messages
    </h1>

    @if($conversations->isEmpty())
        <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
            <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Aucune conversation</h3>
            <p class="text-gray-500">Vos conversations avec les médecins apparaîtront ici.</p>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="divide-y divide-gray-200">
                @foreach($conversations as $conv)
                    <a href="{{ route('patient.messages.show', $conv['id']) }}" class="block p-4 hover:bg-blue-50 transition">
                        <div class="flex items-start space-x-3">
                            <div class="relative">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($conv['with_name']) }}&background=2563eb&color=fff" class="w-12 h-12 rounded-full">
                                @if($conv['is_online'])
                                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <p class="font-semibold text-gray-900 truncate">{{ $conv['with_name'] }}</p>
                                    <span class="text-xs text-gray-500">{{ $conv['last_message_time'] }}</span>
                                </div>
                                <p class="text-sm text-gray-600 truncate mt-1">{{ $conv['last_message'] }}</p>
                                @if($conv['unread_count'] > 0)
                                    <span class="inline-block mt-1 px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded-full font-semibold">
                                        {{ $conv['unread_count'] }} nouveau{{ $conv['unread_count'] > 1 ? 'x' : '' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection