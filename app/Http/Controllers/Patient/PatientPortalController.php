<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\{Appointment, MedicalRecord, Prescription, Invoice, Hospital, ExternalDoctor, Notification as NotificationModel, Prestation};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\SmsService;

class PatientPortalController extends Controller 
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->middleware('auth:patients');
        $this->smsService = $smsService;
    }

    public function dashboard()
    {
        $patient = Auth::guard('patients')->user();

        if (!$patient) {
            return redirect()->route('patient.login');
        }

        $patient->load([
            'referringDoctor',
            'prescriptions' => fn($query) => $query->latest()->take(3),
            'medicalRecords' => fn($query) => $query->latest()->take(3),
            'appointments' => fn($query) => $query->latest()->take(5)
        ]);

        $totalAppointments = $patient->appointments()->count();
        $totalPrescriptions = $patient->prescriptions()->count();
        $totalDocuments = $patient->documents()->count();
        $totalInvoices = $patient->invoices()->count();

        $documents = $patient->documents()->latest()->take(5)->get();
        $unreadMessages = \App\Models\Message::whereHas('conversation', function($q) use ($patient) {
            $q->where('patient_id', $patient->id);
        })->where('sender_type', '!=', 'patient')->where('is_read', false)->count();

        $upcomingAppointments = $patient->appointments()
            ->where('appointment_datetime', '>', now())
            ->where('status', 'confirmed')
            ->orderBy('appointment_datetime')
            ->with(['doctor', 'service'])
            ->take(3)
            ->get();

        $recentRecords = $patient->medicalRecords()
            ->with('doctor')
            ->latest()
            ->take(5)
            ->get();

        $recentPrescriptions = $patient->prescriptions()
            ->with('doctor')
            ->latest()
            ->take(3)
            ->get();

        $recentInvoices = $patient->invoices()
            ->latest()
            ->take(3)
            ->get();

        $recentMessages = \App\Models\Message::whereHas('conversation', function($q) use ($patient) {
            $q->where('patient_id', $patient->id);
        })->with('conversation.doctor')
        ->orderBy('created_at', 'desc')
        ->take(3)
        ->get();

        $healthMetrics = $patient->vitals()
            ->latest()
            ->take(5)
            ->get();

        return view('portal.Patientdashboard', compact(
            'patient',
            'upcomingAppointments',
            'recentRecords',
            'recentPrescriptions',
            'recentInvoices',
            'recentMessages',
            'healthMetrics',
            'documents',
            'totalAppointments',
            'totalPrescriptions',
            'totalDocuments',
            'totalInvoices',
            'unreadMessages'
        ));
    }

    public function appointments()
    {
        $patient = Auth::guard('patients')->user();
        $appointments = $patient->appointments()
            ->with(['doctor', 'service', 'externalDoctor'])
            ->latest()
            ->paginate(10);

        return view('portal.appointments', compact('appointments'));
    }

    public function bookAppointment()
    {
        $patient = Auth::guard('patients')->user();
        $hospitals = Hospital::where('is_active', true)->get();

        return view('portal.book-appointment', compact('patient', 'hospitals'));
    }

    public function storeAppointment(Request $request)
    {
        $validated = $request->validate([
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'hospital_id' => 'required|exists:hospitals,id',
            'prestation_id' => 'required|exists:prestations,id',
            'reason' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        $patient = Auth::guard('patients')->user();
        $appointmentDateTime = $validated['appointment_date'] . ' ' . $validated['appointment_time'];

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'appointment_datetime' => $appointmentDateTime,
            'status' => 'pending',
            'reason' => $validated['reason'],
            'notes' => $validated['notes'] ?? null,
            'consultation_type' => 'hospital',
            'hospital_id' => $validated['hospital_id'],
        ]);

        // Attach the prestation to the appointment
        $prestation = \App\Models\Prestation::find($validated['prestation_id']);
        $appointment->prestations()->attach($prestation->id, [
            'quantity' => 1,
            'unit_price' => $prestation->price,
            'total' => $prestation->price,
        ]);

        return redirect()->route('patient.appointments')
            ->with('success', 'Votre rendez-vous à l\'hôpital a été enregistré.');
    }

    public function storeHomeAppointment(Request $request)
    {
        $validated = $request->validate([
            'home_address' => 'required|string|min:10',
            'patient_latitude' => 'required|numeric',
            'patient_longitude' => 'required|numeric',
            'patient_full_address' => 'required|string',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'specialty_requested' => 'required|string',
            'external_doctor_id' => 'nullable|exists:external_doctors,id',
            'proposed_price' => 'nullable|numeric|min:5000',
            'doctor_base_price' => 'nullable|numeric',
            'reason' => 'required|string|max:500',
        ]);

        $patient = Auth::guard('patients')->user();
        
        $time_mapping = [
            'matin' => '09:00:00',
            'apres-midi' => '14:00:00',
            'soir' => '19:00:00'
        ];
        $appointmentTime = $time_mapping[$validated['appointment_time']] ?? '09:00:00';
        $appointmentDateTime = $validated['appointment_date'] . ' ' . $appointmentTime;

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'external_doctor_id' => $validated['external_doctor_id'] ?? null,
            'appointment_datetime' => $appointmentDateTime,
            'home_address' => $validated['home_address'],
            'patient_latitude' => $validated['patient_latitude'],
            'patient_longitude' => $validated['patient_longitude'],
            'patient_full_address' => $validated['patient_full_address'],
            'specialty_requested' => $validated['specialty_requested'],
            'proposed_price' => $validated['proposed_price'] ?? null,
            'doctor_base_price' => $validated['doctor_base_price'] ?? null,
            'price_status' => 'pending',
            'status' => 'pending',
            'home_visit_status' => 'requested',
            'reason' => $validated['reason'],
            'consultation_type' => 'home',
            'hospital_id' => $patient->hospital_id,
        ]);

        if ($validated['external_doctor_id']) {
            $doctor = ExternalDoctor::find($validated['external_doctor_id']);
            if ($doctor && $doctor->has_active_subscription) {
                $this->sendDoctorNotification($doctor, $appointment);
            }
        } else {
            $doctors = ExternalDoctor::where('specialty', $validated['specialty_requested'])
                ->where('has_active_subscription', true)
                ->where('is_online', true)
                ->get();
            
            foreach ($doctors as $doctor) {
                $this->sendDoctorNotification($doctor, $appointment);
            }
        }

        $this->smsService->send(
            $patient->phone,
            "Votre demande de visite à domicile a été enregistrée. Vous recevrez une notification dès qu'un médecin accepte."
        );

        return redirect()->route('patient.appointments')
            ->with('success', 'Votre demande de visite à domicile a été transmise.');
    }

    private function sendDoctorNotification($doctor, $appointment)
    {
        try {
            NotificationModel::create([
                'external_doctor_id' => $doctor->id,
                'type' => 'new_appointment_request',
                'title' => 'Nouvelle demande de visite',
                'message' => "Nouvelle demande de visite à domicile pour le " . $appointment->appointment_datetime->format('d/m/Y'),
                'data' => json_encode([
                    'appointment_id' => $appointment->id,
                    'patient_name' => $appointment->patient->first_name . ' ' . $appointment->patient->last_name
                ])
            ]);

            $this->smsService->send(
                $doctor->phone,
                "Nouvelle demande de visite à domicile. Connectez-vous pour accepter."
            );
            
            Log::info("Notification envoyée au médecin {$doctor->id}");
        } catch (\Exception $e) {
            Log::error("Erreur notification médecin: " . $e->getMessage());
        }
    }

    public function trackDoctor($appointmentId)
    {
        $patient = Auth::guard('patients')->user();
        $appointment = Appointment::where('id', $appointmentId)
            ->where('patient_id', $patient->id)
            ->with('externalDoctor')
            ->firstOrFail();

        return view('portal.track-doctor', compact('appointment'));
    }

    public function getDoctorLocation($appointmentId)
    {
        $patient = Auth::guard('patients')->user();
        $appointment = Appointment::where('id', $appointmentId)
            ->where('patient_id', $patient->id)
            ->first();

        if (!$appointment || !$appointment->doctor_current_latitude) {
            return response()->json(['error' => 'Position non disponible'], 404);
        }

        return response()->json([
            'latitude' => $appointment->doctor_current_latitude,
            'longitude' => $appointment->doctor_current_longitude,
            'updated_at' => $appointment->doctor_location_updated_at,
            'status' => $appointment->home_visit_status
        ]);
    }

    public function profile()
    {
        $patient = Auth::guard('patients')->user();
        return view('portal.profile', compact('patient'));
    }

    public function updateProfile(Request $request)
    {
        $patient = Auth::guard('patients')->user();
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:Homme,Femme,Other',
            'blood_group' => 'nullable|string|max:10',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:patients,email,' . $patient->id,
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'allergies' => 'nullable|string',
             'medical_history' => 'nullable|string',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($patient->profile_image && \Storage::disk('public')->exists($patient->profile_image)) {
                \Storage::disk('public')->delete($patient->profile_image);
            }

            // Store new image
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');
            $validated['profile_image'] = $imagePath;
        }

        // Convert allergies to array if provided
        if (isset($validated['allergies'])) {
            $validated['allergies'] = array_map('trim', explode(',', $validated['allergies']));
        }

        $patient->update($validated);
        return back()->with('success', 'Vos informations ont été mises à jour.');
    }

    public function medicalHistory()
    {

        $patient = Auth::guard('patients')->user();
        $medicalHistory = $patient->medicalRecords()->with('recordedBy')->latest()->paginate(10);
        return view('portal.medical-history', compact('medicalHistory'));
    }

    public function prescriptions()
    {
        $patient = Auth::guard('patients')->user();
        $prescriptions = $patient->prescriptions()->with('doctor')->latest()->paginate(10);
        $prescriptions = auth()->guard('patients')->user()->prescriptions;
        return view('portal.prescriptions', compact('prescriptions'));
    }

    public function invoices()
    {
        $patient = Auth::guard('patients')->user();
        $invoices = Invoice::where('patient_id', $patient->id)->latest()->paginate(10);
        return view('portal.invoices', compact('invoices'));
    }

    public function messaging()
    {
        $conversations = []; 
        return view('portal.messaging', compact('conversations'));
    }

    public function documents()
    {
        $patient = Auth::guard('patients')->user();
        $documents = $patient->documents()->latest()->paginate(10);
        return view('portal.documents', compact('documents'));
    }

    public function getHospitalPrestations($hospitalId)
    {
        try {
            $prestations = Prestation::where('hospital_id', $hospitalId)
                ->where('is_active', true)
                ->where('category', 'consultation')
                ->with('service')
                ->get()
                ->map(function ($prestation) {
                    return [
                        'id' => $prestation->id,
                        'name' => $prestation->name,
                        'price' => $prestation->price,
                        'service_name' => $prestation->service->name ?? 'N/A',
                        'description' => $prestation->description,
                    ];
                });

            \Log::info("Prestations for hospital {$hospitalId}: " . $prestations->count());
            return response()->json($prestations);
        } catch (\Exception $e) {
            \Log::error("Error fetching prestations for hospital {$hospitalId}: " . $e->getMessage());
            return response()->json(['error' => 'Erreur de chargement des prestations'], 500);
        }
    }


}
