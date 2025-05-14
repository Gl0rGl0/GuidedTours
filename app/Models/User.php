<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Add this use statement
use Illuminate\Database\Eloquent\Relations\HasMany; // Import HasMany
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo
use Spatie\Permission\Traits\HasRoles; // Add Spatie HasRoles trait

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles; // Add HasRoles trait here

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'password',
        'first_name',
        'last_name',
        'birth_date',
    ];    

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed'
        ];
    }

    /**
     * The visit types that the user (volunteer) can guide.
     */
    public function visitTypes(): BelongsToMany
    {
        return $this->belongsToMany(VisitType::class, 'volunteers_visit_types', 'user_id', 'visit_type_id');
    }

    /**
     * Get the volunteer availabilities for the user.
     */
    public function volunteerAvailabilities(): HasMany
    {
        return $this->hasMany(VolunteerAvailability::class, 'user_id', 'user_id');
    }

    /**
     * Get the visits assigned to the user (volunteer).
     */
    public function assignedVisits(): HasMany
    {
        return $this->hasMany(Visit::class, 'assigned_volunteer_id', 'user_id');
    }

    /**
     * Get the registrations made by the user (fruitore).
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'user_id', 'user_id');
    }
}
