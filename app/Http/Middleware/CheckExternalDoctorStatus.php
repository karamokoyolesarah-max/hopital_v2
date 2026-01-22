<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckExternalDoctorStatus
{
    public function handle($request, Closure $next)
    {
        if (Auth::guard('external_doctors')->check()) {
            $doctor = Auth::guard('external_doctors')->user();
         
            if (auth('external_doctors')->check() && auth('external_doctors')->user()->status !== 'approved') {
             // Si pas encore approuvé, on force vers la page d'attente
             return redirect()->route('external.pending');
            }

            

            // Vérifier si le compte est actif
            if (!$doctor->is_active) {
                Auth::guard('external_doctors')->logout();
                return redirect()->route('external.login')
                    ->with('error', 'Votre compte a été désactivé.');
            }

            // Vérifier si le compte est approuvé
            if ($doctor->status !== 'approved') {
                return redirect()->route('external.pending')
                    ->with('warning', 'Votre compte est en attente d\'approbation par l\'administration.');
            }
        }

        return $next($request);
    }
}