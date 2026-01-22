<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ExternalDoctor extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'speciality',
        'license_number',
        'qualifications',
        'bio',
        'profile_photo',
        'status',
        'is_active',
        'availability',
        'consultation_fee',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'availability' => 'array',
        'consultation_fee' => 'decimal:2',
        'is_active' => 'boolean',
        'location_updated_at' => 'datetime',
        'subscription_start_date' => 'date',
        'subscription_end_date' => 'date',
        'is_online' => 'boolean',
        'has_active_subscription' => 'boolean',
   
    ];


    // Relations
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id')->withoutGlobalScope('hospital_filter');
        return $this->hasMany(Appointment::class, 'external_doctor_id');
   
    }
    

    public function patients()
    {
        return $this->hasManyThrough(Patient::class, Appointment::class, 'doctor_id', 'id', 'id', 'patient_id')->withoutGlobalScope('hospital_filter');
    }
        
    public function conversations()
   {
        return $this->hasMany(Conversation::class);
   }

    public function notifications()
   {
       return $this->hasMany(Notification::class);
   }
      
}