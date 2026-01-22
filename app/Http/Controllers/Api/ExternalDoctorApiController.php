<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{ExternalDoctor, Appointment, Notification as NotificationModel};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\SmsService;

class ExternalDoctorApiController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function getAvailableDoctors(Request $request)
    {
        $specialty = $request->query('specialty');
        
        $doctors = ExternalDoctor::where('specialty', $specialty)
            ->where('status', 'approved')
            ->where('has_active_subscription', true)
            ->select([
                'id', 'full_name', 'specialty', 'consultation_fee', 
                'address', 'phone', 'email', 'is_online',
                'latitude', 'longitude'
            ])
            ->get();

        return response()->json($doctors);
    }

    public function getDoctorLocation($id)
    {
        $doctor = ExternalDoctor::findOrFail($id);
        
        return response()->json([
            'id' => $doctor->id,
            'name' => $doctor->full_name,
            'latitude' => $doctor->latitude,
            'longitude' => $doctor->longitude,
            'is_online' => $doctor->is_online,
            'updated_at' => $doctor->location_updated_at
        ]);
    }

    public function updateLocation(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        $doctor = Auth::guard('external_doctors')->user();
        
        $doctor->update([
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'location_updated_at' => now()
        ]);

        Appointment::where('external_doctor_id', $doctor->id)
            ->whereIn('home_visit_status', ['doctor_assigned', 'on_the_way'])
            ->update([
                'doctor_current_latitude' => $validated['latitude'],
                'doctor_current_longitude' => $validated['longitude'],
                'doctor_location_updated_at' => now()
            ]);

        return response()->json(['success' => true, 'message' => 'Position mise à jour']);
    }

    public function getPendingAppointments()
    {
        $doctor = Auth::guard('external_doctors')->user();
        
        if (!$doctor->has_active_subscription) {
            return response()->json([
                'error' => 'Vous devez souscrire à un forfait pour recevoir des rendez-vous'
            ], 403);
        }

        $appointments = Appointment::where(function($query) use ($doctor) {
                $query->where('external_doctor_id', $doctor->id)
                      ->orWhere('specialty_requested', $doctor->specialty);
            })
            ->where('consultation_type', 'home')
            ->where('status', 'pending')
            ->with('patient')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($apt) {
                return [
                    'id' => $apt->id,
                    'patient_name' => $apt->patient->first_name . ' ' . $apt->patient->last_name,
                    'patient_phone' => $apt->patient->phone,
                    'patient_address' => $apt->patient_full_address,
                    'patient_latitude' => $apt->patient_latitude,
                    'patient_longitude' => $apt->patient_longitude,
                    'appointment_date' => $apt->appointment_datetime,
                    'reason' => $apt->reason,
                    'proposed_price' => $apt->proposed_price,
                    'doctor_base_price' => $apt->doctor_base_price,
                    'specialty' => $apt->specialty_requested,
                    'created_at' => $apt->created_at->diffForHumans()
                ];
            });

        return response()->json($appointments);
    }

    public function acceptAppointment($id)
    {
        $doctor = Auth::guard('external_doctors')->user();
        $appointment = Appointment::findOrFail($id);

        if ($appointment->external_doctor_id && $appointment->external_doctor_id != $doctor->id) {
            return response()->json(['error' => 'Ce rendez-vous a déjà été accepté par un autre médecin'], 403);
        }

        $appointment->update([
            'external_doctor_id' => $doctor->id,
            'status' => 'confirmed',
            'home_visit_status' => 'doctor_assigned',
            'patient_notified' => false
        ]);

        $patient = $appointment->patient;
        NotificationModel::create([
            'patient_id' => $patient->id,
            'type' => 'appointment_accepted',
            'title' => 'Rendez-vous accepté !',
            'message' => "Dr {$doctor->full_name} a accepté votre demande de visite à domicile.",
            'data' => json_encode(['appointment_id' => $appointment->id])
        ]);

        $this->smsService->send(
            $patient->phone,
            "Bonne nouvelle ! Dr {$doctor->full_name} a accepté votre demande de visite à domicile pour le " . $appointment->appointment_datetime->format('d/m/Y à H:i')
        );

        return response()->json([
            'success' => true, 
            'message' => 'Rendez-vous accepté. Le patient a été notifié.',
            'appointment' => $appointment
        ]);
    }

    public function rejectAppointment($id)
    {
        $doctor = Auth::guard('external_doctors')->user();
        $appointment = Appointment::findOrFail($id);

        if ($appointment->external_doctor_id != $doctor->id) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $appointment->update([
            'external_doctor_id' => null,
            'status' => 'rejected'
        ]);

        return response()->json(['success' => true, 'message' => 'Rendez-vous rejeté']);
    }

    public function negotiatePrice(Request $request, $id)
    {
        $validated = $request->validate([
            'counter_price' => 'required|numeric|min:5000'
        ]);

        $doctor = Auth::guard('external_doctors')->user();
        $appointment = Appointment::findOrFail($id);

        if ($appointment->external_doctor_id != $doctor->id) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $appointment->update([
            'negotiated_price' => $validated['counter_price'],
            'price_status' => 'negotiating'
        ]);

        $patient = $appointment->patient;
        NotificationModel::create([
            'patient_id' => $patient->id,
            'type' => 'price_negotiation',
            'title' => 'Contre-proposition de prix',
            'message' => "Dr {$doctor->full_name} propose " . number_format($validated['counter_price'], 0, ',', ' ') . " FCFA.",
            'data' => json_encode(['appointment_id' => $appointment->id, 'price' => $validated['counter_price']])
        ]);

        $this->smsService->send(
            $patient->phone,
            "Dr {$doctor->full_name} propose " . number_format($validated['counter_price'], 0, ',', ' ') . " FCFA pour la visite. Connectez-vous pour répondre."
        );

        return response()->json([
            'success' => true,
            'message' => 'Contre-proposition envoyée au patient',
            'negotiated_price' => $validated['counter_price']
        ]);
    }
}