<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedecinExterne extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'external_doctors';

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'specialite',
        'numero_ordre',
        'adresse_cabinet',
        'password',
        'statut',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isActive(): bool
    {
        return $this->statut === 'actif';
    }

    public function getFullNameAttribute(): string
    {
        return $this->nom . ' ' . $this->prenom;
    }
}
