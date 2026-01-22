<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $twilio;
    protected $from;

    public function __construct()
    {
        if (config('services.twilio.enabled')) {
            try {
                $this->twilio = new \Twilio\Rest\Client(
                    config('services.twilio.sid'),
                    config('services.twilio.token')
                );
                $this->from = config('services.twilio.from');
            } catch (\Exception $e) {
                Log::error("Erreur initialisation Twilio: " . $e->getMessage());
            }
        }
    }

    public function send(string $to, string $message): bool
    {
        try {
            if (!config('services.twilio.enabled')) {
                Log::info("SMS (mode test) vers {$to}: {$message}");
                return true;
            }

            $to = $this->formatPhoneNumber($to);

            $this->twilio->messages->create($to, [
                'from' => $this->from,
                'body' => $message
            ]);

            Log::info("SMS envoyé avec succès à {$to}");
            return true;

        } catch (\Exception $e) {
            Log::error("Erreur envoi SMS à {$to}: " . $e->getMessage());
            return false;
        }
    }

    public function sendAppointmentAccepted($patient, $appointment, $doctor)
    {
        $message = "Bonjour {$patient->first_name},\n\n"
                 . "Bonne nouvelle ! Dr {$doctor->full_name} a accepté votre demande de visite à domicile.\n\n"
                 . "📅 Date : {$appointment->appointment_datetime->format('d/m/Y à H:i')}\n"
                 . "💰 Prix : " . number_format($appointment->negotiated_price ?? $appointment->proposed_price, 0, ',', ' ') . " FCFA\n\n"
                 . "Vous pouvez suivre sa position en temps réel via l'application.\n\n"
                 . "HospitSIS";

        return $this->send($patient->phone, $message);
    }

    public function sendDoctorOnTheWay($patient, $doctor, $eta = 30)
    {
        $message = "Bonjour {$patient->first_name},\n\n"
                 . "Dr {$doctor->full_name} est en route vers votre domicile !\n"
                 . "⏰ Arrivée estimée : {$eta} minutes\n\n"
                 . "Suivez sa position en direct sur l'application.\n\n"
                 . "HospitSIS";

        return $this->send($patient->phone, $message);
    }

    public function sendPriceNegotiation($patient, $doctor, $counterPrice)
    {
        $message = "Bonjour {$patient->first_name},\n\n"
                 . "Dr {$doctor->full_name} propose un prix de " 
                 . number_format($counterPrice, 0, ',', ' ') . " FCFA pour la visite.\n\n"
                 . "Connectez-vous à l'application pour accepter ou faire une contre-offre.\n\n"
                 . "HospitSIS";

        return $this->send($patient->phone, $message);
    }

    public function sendDoctorArrived($patient, $doctor)
    {
        $message = "Bonjour {$patient->first_name},\n\n"
                 . "Dr {$doctor->full_name} est arrivé à votre domicile.\n\n"
                 . "HospitSIS";

        return $this->send($patient->phone, $message);
    }

    public function sendAppointmentReminder($patient, $appointment)
    {
        $message = "Rappel de rendez-vous\n\n"
                 . "Bonjour {$patient->first_name},\n\n"
                 . "Votre rendez-vous est prévu demain :\n"
                 . "📅 {$appointment->appointment_datetime->format('d/m/Y à H:i')}\n"
                 . "📍 " . ($appointment->consultation_type === 'home' ? 'À domicile' : 'À l\'hôpital') . "\n\n"
                 . "HospitSIS";

        return $this->send($patient->phone, $message);
    }

    private function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        if (substr($phone, 0, 1) === '0') {
            $phone = '+225' . substr($phone, 1);
        }

        if (substr($phone, 0, 1) !== '+') {
            $phone = '+225' . $phone;
        }

        return $phone;
    }
}