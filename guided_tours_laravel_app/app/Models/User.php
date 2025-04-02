<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Add this use statement

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

    // Add other relationships as needed later (e.g., availability, assigned visits, registrations)

}
