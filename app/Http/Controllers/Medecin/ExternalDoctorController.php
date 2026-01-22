<?php

namespace App\Http\Controllers\Medecin;

use App\Http\Controllers\Controller;
use App\Models\ExternalDoctor;
use App\Models\User;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ExternalDoctorController extends Controller
{
    // Afficher le formulaire d'inscription
    public function showRegistrationForm()
    {
        return view('medecin.external.register');
    }

    // Traiter l'inscription
    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:external_doctors,email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'speciality' => 'required|string|max:255',
            'license_number' => 'required|string|unique:external_doctors,license_number',
            'qualifications' => 'nullable|string',
            'bio' => 'nullable|string',
            'consultation_fee' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // 1. Création dans la table external_doctors
            $doctor = ExternalDoctor::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'speciality' => $validated['speciality'],
                'license_number' => $validated['license_number'],
                'qualifications' => $validated['qualifications'] ?? null,
                'bio' => $validated['bio'] ?? null,
                'consultation_fee' => $validated['consultation_fee'] ?? 0,
                'status' => 'pending',
                'is_active' => false,
            ]);

            // 2. Création du compte miroir dans la table users pour l'auth globale
            User::create([
                'name' => 'Dr. ' . $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'password' => $doctor->password, // Utilise le même hash
                'role' => 'external_doctor',
                'phone' => $validated['phone'],
                'is_active' => false,
            ]);

            DB::commit();

            // Connexion automatique après inscription sur le guard spécifique
            Auth::guard('external_doctors')->login($doctor);

            return redirect()->route('external.pending')
                ->with('success', 'Inscription réussie ! Votre compte est en attente d\'approbation.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Une erreur est survenue lors de l\'inscription.'])->withInput();
        }
    }

    // Afficher le formulaire de connexion
    public function showLoginForm()
    {
        // Attention : Vérifie que le nom du fichier est bien "login.blade.php" (minuscule)
        return view('medecin.external.login');
    }

    // Traiter la connexion
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('external_doctors')->attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            $user = Auth::guard('external_doctors')->user();

            // Vérifier si le compte est approuvé
            if ($user->status !== 'approved' || !$user->is_active) {
                return redirect()->route('external.pending');
            }

            return redirect()->route('external.doctor.dashboard');
        }

        return back()->withErrors([
            'email' => 'Les identifiants ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }

    // Dashboard
    public function dashboard()
    {
        $doctor = Auth::guard('external_doctors')->user();

        $stats = [
            'total_appointments' => $doctor->appointments()->count(),
            'pending_appointments' => $doctor->appointments()->where('status', 'scheduled')->count(),
            'completed_appointments' => $doctor->appointments()->where('status', 'completed')->count(),
            'total_patients' => $doctor->appointments()
                ->withoutGlobalScope('hospital')
                ->distinct('patient_id')
                ->count('patient_id'),
            'home_visits' => $doctor->appointments()->where('consultation_type', 'home')->count(),
            'total_revenue' => 0, // Colonne proposed_price n'existe pas encore
            'unread_notifications' => 0, // Temporairement désactivé
            'active_conversations' => 0, // Temporairement désactivé
        ];

        $upcomingAppointments = $doctor->appointments()
            ->with('patient')
            ->where('appointment_datetime', '>=', now())
            ->orderBy('appointment_datetime')
            ->take(5)
            ->get();

        $recentNotifications = collect(); // Temporairement désactivé

        $recentMessages = collect(); // Temporairement désactivé

        $currentHomeVisits = collect(); // Temporairement désactivé - colonne home_visit_status n'existe pas

        return view('medecin.external.dashboard', compact(
            'doctor',
            'stats',
            'upcomingAppointments',
            'recentNotifications',
            'recentMessages',
            'currentHomeVisits'
        ));
    }

    // Déconnexion
    public function logout(Request $request)
    {
        Auth::guard('external_doctors')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('external.login');
    }

    // Voir tous les rendez-vous
    public function appointments()
    { 
        $doctor = Auth::guard('external_doctors')->user();
        $appointments = $doctor->appointments()
            ->with('patient')
            ->orderBy('appointment_datetime', 'desc')
            ->paginate(20);

        return view('medecin.external.appointments', compact('appointments'));
    }

    // Créer un rendez-vous
    public function createAppointment()
    {
        return view('medecin.external.create-appointment');
    }

    // Enregistrer le rendez-vous
    public function storeAppointment(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'nullable|exists:patients,id',
            'patient_name' => 'required_without:patient_id|string',
            'patient_phone' => 'required_without:patient_id|string',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'reason' => 'required|string',
        ]);

        $doctor = Auth::guard('external_doctors')->user();

        if (!isset($validated['patient_id'])) {
            $nameParts = explode(' ', $validated['patient_name'], 2);
            $patient = Patient::create([
                'first_name' => $nameParts[0],
                'name' => $nameParts[1] ?? '',
                'phone' => $validated['patient_phone'],
                'dob' => '1900-01-01',
                'gender' => 'Other',
                'ipu' => Patient::generateIpu(),
                'is_active' => true,
                'hospital_id' => null,
            ]);
            $patientId = $patient->id;
        } else {
            $patientId = $validated['patient_id'];
        }

        Appointment::create([
            'patient_id' => $patientId,
            'doctor_id' => $doctor->id,
            'appointment_datetime' => $validated['appointment_date'] . ' ' . $validated['appointment_time'],
            'reason' => $validated['reason'],
            'status' => 'scheduled',
            'hospital_id' => null,
        ]);

        return redirect()->route('external.appointments')
            ->with('success', 'Rendez-vous créé avec succès !');
    }

    public function profile()
    {
        $doctor = Auth::guard('external_doctors')->user();
        return view('medecin.external.profile', compact('doctor'));
    }

    public function updateProfile(Request $request)
    {
        $doctor = Auth::guard('external_doctors')->user();
        
        $validated = $request->validate([
            'phone' => 'required|string',
            'bio' => 'nullable|string',
            'consultation_fee' => 'nullable|numeric|min:0',
            'availability' => 'nullable|array',
        ]);

        $doctor->update($validated);

        return back()->with('success', 'Profil mis à jour avec succès !');
    }
}