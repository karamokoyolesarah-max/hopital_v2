<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\{Message, Conversation, Notification as NotificationModel};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\SmsService;

class MessagingController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->middleware('auth:patients');
        $this->smsService = $smsService;
    }

    public function index()
    {
        $patient = Auth::guard('patients')->user();
        
        $conversations = Conversation::where('patient_id', $patient->id)
            ->with(['lastMessage', 'doctor', 'externalDoctor'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function($conv) {
                return [
                    'id' => $conv->id,
                    'with_name' => $conv->doctor ? 
                        "Dr {$conv->doctor->first_name} {$conv->doctor->last_name}" : 
                        ($conv->externalDoctor ? "Dr {$conv->externalDoctor->full_name}" : "Hôpital"),
                    'with_type' => $conv->doctor ? 'doctor' : 'external_doctor',
                    'last_message' => $conv->lastMessage?->content ?? 'Aucun message',
                    'last_message_time' => $conv->lastMessage?->created_at->diffForHumans() ?? '',
                    'unread_count' => $conv->unreadCount(),
                    'is_online' => $conv->externalDoctor?->is_online ?? false
                ];
            });

        return view('portal.messages', compact('conversations'));
    }

    public function show($conversationId)
    {
        $patient = Auth::guard('patients')->user();
        
        $conversation = Conversation::where('id', $conversationId)
            ->where('patient_id', $patient->id)
            ->with(['messages' => function($query) {
                $query->orderBy('created_at', 'asc');
            }, 'doctor', 'externalDoctor'])
            ->firstOrFail();

        $conversation->messages()
            ->where('sender_type', '!=', 'patient')
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('portal.conversation', compact('conversation'));
    }

    public function sendMessage(Request $request, $conversationId)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'attachment' => 'nullable|file|mimes:jpg,png,pdf|max:5120'
        ]);

        $patient = Auth::guard('patients')->user();
        
        $conversation = Conversation::where('id', $conversationId)
            ->where('patient_id', $patient->id)
            ->firstOrFail();

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $patient->id,
            'sender_type' => 'patient',
            'content' => $validated['content'],
            'is_read' => false
        ]);

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('messages/attachments', 'public');
            $message->update(['attachment_path' => $path]);
        }

        $conversation->touch();

        $this->notifyRecipient($conversation, $message);

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    public function createConversation(Request $request)
    {
        $validated = $request->validate([
            'with_type' => 'required|in:doctor,external_doctor,hospital',
            'with_id' => 'required|integer',
            'appointment_id' => 'nullable|exists:appointments,id',
            'initial_message' => 'required|string|max:1000'
        ]);

        $patient = Auth::guard('patients')->user();

        $existingConv = Conversation::where('patient_id', $patient->id)
            ->where(function($query) use ($validated) {
                if ($validated['with_type'] === 'doctor') {
                    $query->where('doctor_id', $validated['with_id']);
                } elseif ($validated['with_type'] === 'external_doctor') {
                    $query->where('external_doctor_id', $validated['with_id']);
                }
            })
            ->first();

        if ($existingConv) {
            return redirect()->route('patient.messages.show', $existingConv->id);
        }

        $conversation = Conversation::create([
            'patient_id' => $patient->id,
            'doctor_id' => $validated['with_type'] === 'doctor' ? $validated['with_id'] : null,
            'external_doctor_id' => $validated['with_type'] === 'external_doctor' ? $validated['with_id'] : null,
            'appointment_id' => $validated['appointment_id'] ?? null,
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $patient->id,
            'sender_type' => 'patient',
            'content' => $validated['initial_message'],
            'is_read' => false
        ]);

        return redirect()->route('patient.messages.show', $conversation->id)
            ->with('success', 'Conversation créée avec succès');
    }

    public function notifications()
    {
        $patient = Auth::guard('patients')->user();
        
        $notifications = NotificationModel::where('patient_id', $patient->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('portal.notifications', compact('notifications'));
    }

    public function markNotificationAsRead($notificationId)
    {
        $patient = Auth::guard('patients')->user();
        
        NotificationModel::where('id', $notificationId)
            ->where('patient_id', $patient->id)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function markAllNotificationsAsRead()
    {
        $patient = Auth::guard('patients')->user();
        
        NotificationModel::where('patient_id', $patient->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function unreadNotificationsCount()
    {
        $patient = Auth::guard('patients')->user();
        
        $count = NotificationModel::where('patient_id', $patient->id)
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    private function notifyRecipient($conversation, $message)
    {
        $patient = $conversation->patient;

        if ($conversation->doctor_id) {
            $recipient = $conversation->doctor;
            $recipientType = 'doctor';
        } elseif ($conversation->external_doctor_id) {
            $recipient = $conversation->externalDoctor;
            $recipientType = 'external_doctor';
        } else {
            return;
        }

        NotificationModel::create([
            'external_doctor_id' => $recipientType === 'external_doctor' ? $recipient->id : null,
            'doctor_id' => $recipientType === 'doctor' ? $recipient->id : null,
            'type' => 'new_message',
            'title' => "Nouveau message de {$patient->first_name} {$patient->last_name}",
            'message' => substr($message->content, 0, 100),
            'data' => json_encode([
                'conversation_id' => $conversation->id,
                'message_id' => $message->id
            ]),
            'is_read' => false
        ]);

        if ($recipient->phone) {
            $this->smsService->send(
                $recipient->phone, 
                "Nouveau message de {$patient->first_name} : " . substr($message->content, 0, 80)
            );
        }
    }
}