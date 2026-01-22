<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            // Si l'utilisateur est connecté via un guard
            if (Auth::guard($guard)->check()) {
                
                // 1. Priorité au Médecin Externe (Guard spécifique)
                if ($guard === 'external_doctors') {
                    return redirect()->route('external.doctor.dashboard');
                }

                // 2. Gestion pour le Staff (Guard 'web')
                $user = Auth::guard($guard)->user();
                
                return match($user->role) {
                    'doctor'          => redirect('/medecin/dashboard'),
                    'internal_doctor' => redirect()->route('doctor.internal.dashboard'),
                    'external_doctor' => redirect()->route('external.doctor.dashboard'), 
                    'nurse'           => redirect('/nurse/dashboard'),
                    'cashier'         => redirect('/cashier/dashboard'),
                    'admin'           => redirect('/dashboard'),
                    default           => redirect('/dashboard'),
                };
            }
        }

        return $next($request);
    }
}