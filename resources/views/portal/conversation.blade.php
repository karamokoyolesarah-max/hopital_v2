@extends('layouts.patient')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden h-[calc(100vh-12rem)] flex flex-col">
        
        <!-- En-tête conversation -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-4 text-white flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('patient.messages') }}" class="text-white hover:bg-white/20 p-2 rounded-full">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <img src="https://ui-avatars.com/api/?name={{ urlencode($conversation->doctor ? 'Dr ' . $conversation->doctor->first_name : 'Dr ' . $conversation->externalDoctor->full_name) }}&background=fff&color=2563eb" class="w-10 h-10 rounded-full">
                <div>
                    <p class="font-bold">
                        {{ $conversation->doctor ? 'Dr ' . $conversation->doctor->first_name . ' ' . $conversation->doctor->last_name : 'Dr ' . $conversation->externalDoctor->full_name }}
                    </p>
                    @if($conversation->externalDoctor && $conversation->externalDoctor->is_online)
                        <p class="text-xs text-blue-100">
                            <i class="fas fa-circle text-green-400 mr-1"></i>En ligne
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Messages -->
        <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50" id="messages-container">
            @forelse($conversation->messages as $message)
                @if($message->sender_type === 'patient')
                    <!-- Message envoyé -->
                    <div class="flex items-end justify-end space-x-3">
                        <div class="max-w-sm">
                            <div class="bg-blue-600 text-white rounded-2xl rounded-tr-none p-4 shadow-sm">
                                <p>{{ $message->content }}</p>
                            </div>
                            <p class="text-xs text-gray-500 mt-1 mr-2 text-right">{{ $message->created_at->format('H:i') }}</p>
                        </div>
                    </div>
                @else
                    <!-- Message reçu -->
                    <div class="flex items-start space-x-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($conversation->doctor ? 'Dr ' . $conversation->doctor->first_name : 'Dr ' . $conversation->externalDoctor->full_name) }}&background=2563eb&color=fff" class="w-8 h-8 rounded-full flex-shrink-0">
                        <div class="max-w-sm">
                            <div class="bg-white rounded-2xl rounded-tl-none p-4 shadow-sm">
                                <p class="text-gray-800">{{ $message->content }}</p>
                            </div>
                            <p class="text-xs text-gray-500 mt-1 ml-2">{{ $message->created_at->format('H:i') }}</p>
                        </div>
                    </div>
                @endif
            @empty
                <div class="text-center text-gray-500 py-8">
                    <i class="fas fa-comments text-4xl mb-2"></i>
                    <p>Aucun message. Commencez la conversation !</p>
                </div>
            @endforelse
        </div>

        <!-- Zone de saisie -->
        <div class="border-t border-gray-200 p-4 bg-white">
            <form action="{{ route('patient.messages.send', $conversation->id) }}" method="POST" class="flex items-center space-x-3" id="message-form">
                @csrf
                <button type="button" class="text-gray-500 hover:text-gray-700 transition">
                    <i class="fas fa-paperclip text-xl"></i>
                </button>
                <input type="text" name="content" required placeholder="Écrire un message..." class="flex-1 px-4 py-3 border border-gray-300 rounded-full focus:ring-2 focus:ring-blue-500 outline-none">
                <button type="submit" class="bg-blue-600 text-white p-3 rounded-full hover:bg-blue-700 transition shadow-lg">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>

    </div>
</div>

<script>
// Scroll vers le bas automatiquement
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('messages-container');
    container.scrollTop = container.scrollHeight;
});

// Soumettre le formulaire en AJAX
document.getElementById('message-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const content = formData.get('content');
    
    if (!content.trim()) return;
    
    try {
        const response = await fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        });
        
        if (response.ok) {
            this.querySelector('input[name="content"]').value = '';
            // Recharger la page pour afficher le nouveau message
            window.location.reload();
        }
    } catch (error) {
        console.error('Erreur:', error);
    }
});
</script>
@endsection