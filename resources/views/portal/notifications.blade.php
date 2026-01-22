@extends('layouts.patient')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-bell text-blue-600 mr-3"></i>Notifications
        </h1>
        <form action="{{ route('patient.notifications.mark-all-read') }}" method="POST">
            @csrf
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-check-double mr-2"></i>Tout marquer comme lu
            </button>
        </form>
    </div>

    @if($notifications->isEmpty())
        <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
            <i class="fas fa-bell-slash text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Aucune notification</h3>
            <p class="text-gray-500">Vous n'avez pas encore reçu de notifications.</p>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden divide-y divide-gray-200">
            @foreach($notifications as $notif)
                <div class="p-4 hover:bg-blue-50 transition {{ $notif->is_read ? 'bg-gray-50' : 'bg-white' }}">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0
                            {{ $notif->type === 'appointment_accepted' ? 'bg-green-100' : '' }}
                            {{ $notif->type === 'price_negotiation' ? 'bg-orange-100' : '' }}
                            {{ $notif->type === 'new_message' ? 'bg-blue-100' : '' }}
                            {{ $notif->type === 'doctor_on_the_way' ? 'bg-purple-100' : '' }}">
                            @if($notif->type === 'appointment_accepted')
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            @elseif($notif->type === 'price_negotiation')
                                <i class="fas fa-hand-holding-usd text-orange-600 text-xl"></i>
                            @elseif($notif->type === 'new_message')
                                <i class="fas fa-envelope text-blue-600 text-xl"></i>
                            @elseif($notif->type === 'doctor_on_the_way')
                                <i class="fas fa-car text-purple-600 text-xl"></i>
                            @else
                                <i class="fas fa-info-circle text-gray-600 text-xl"></i>
                            @endif
                        </div>
                        
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <h3 class="font-semibold text-gray-900">{{ $notif->title }}</h3>
                                @if(!$notif->is_read)
                                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 mt-1">{{ $notif->message }}</p>
                            <p class="text-xs text-gray-400 mt-2">{{ $notif->created_at->diffForHumans() }}</p>
                            
                            @if($notif->type === 'appointment_accepted' && $notif->data)
                                @php $data = json_decode($notif->data, true); @endphp
                                @if(isset($data['appointment_id']))
                                    <a href="{{ route('patient.appointments.track', $data['appointment_id']) }}" class="inline-block mt-2 px-4 py-2 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition">
                                        <i class="fas fa-map-marked-alt mr-1"></i>Suivre en temps réel
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection