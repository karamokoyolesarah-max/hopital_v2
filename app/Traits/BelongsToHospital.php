<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

trait BelongsToHospital
{
    /**
     * Le "boot" du trait s'exécute automatiquement sur le modèle.
     */
    protected static function bootBelongsToHospital()
    {
        // 1. FILTRAGE AUTOMATIQUE (SELECT) via Global Scope
        static::addGlobalScope('hospital_filter', function (Builder $builder) {
            
            // Ne pas filtrer si :
            // - On est en ligne de commande (migrations, seeds)
            // - L'utilisateur est Super Admin
            if (app()->runningInConsole()) {
                return;
            }

            if (Auth::check() && Auth::user()->role === 'super_admin') {
                return;
            }

            // Récupérer l'ID de l'hôpital (Priorité Session, sinon Auth)
            $hospitalId = Session::get('hospital_id') ?? (Auth::check() ? Auth::user()->hospital_id : null);

            if ($hospitalId !== null) {
                $builder->where('hospital_id', $hospitalId);
            }
        });

        // 2. ASSIGNATION AUTOMATIQUE (INSERT) lors de la création
        static::creating(function ($model) {
            if (!$model->hospital_id) {
                $hospitalId = Session::get('hospital_id') ?? (Auth::check() ? Auth::user()->hospital_id : null);
                
                if ($hospitalId) {
                    $model->hospital_id = $hospitalId;
                }
            }
        });
    }

    /**
     * Relation vers l'hôpital
     */
    public function hospital()
    {
        return $this->belongsTo(\App\Models\Hospital::class, 'hospital_id');
    }
}