<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Add this use statement
use Illuminate\Database\Eloquent\Relations\HasMany; // Import HasMany
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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
        'password', // Hashing is handled by the 'hashed' cast below
        'role',
        'first_login',
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
            // 'email_verified_at' => 'datetime', // Removed as per schema
            'password' => 'hashed',
            'first_login' => 'boolean', // Cast first_login to boolean
        ];
    }

    /**
     * The visit types that the user (volunteer) can guide.
     */
    public function visitTypes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany // Use the fully qualified name here or just BelongsToMany if use statement is added
    {
        // Ensure this relationship is only relevant for volunteers
        return $this->belongsToMany(VisitType::class, 'volunteers_visit_types', 'user_id', 'visit_type_id')
                    ->where('role', 'volunteer'); // Redundant check, but good practice
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
