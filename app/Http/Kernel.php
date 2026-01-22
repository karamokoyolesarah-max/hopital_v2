<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Ajouter tous vos middlewares personnalisés
        $middleware->alias([
            'patient' => \App\Http\Middleware\PatientMiddleware::class,
            'role' => \App\Http\Middleware\CheckRole::class,
            'active_user' => \App\Http\Middleware\CheckActiveUser::class, // ✅ Si vous l'avez
            'external_doctor.status' => \App\Http\Middleware\CheckExternalDoctorStatus::class, // ✅ AJOUTÉ
            'superadmin.verified' => \App\Http\Middleware\EnsureSuperAdminVerified::class, // ✅ AJOUTÉ
        ]);

        // Exclure la route d'inscription du CSRF pour les tests
        $middleware->validateCsrfTokens(except: [
            'register/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();