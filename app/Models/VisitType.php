<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Add this line
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VisitType extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'visit_types';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'visit_type_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'place_id',
        'title',
        'description',
        'meeting_point',
        'period_start',
        'period_end',
        'start_time',
        'duration_minutes',
        'requires_ticket',
        'min_participants',
        'max_participants',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'start_time' => 'datetime:H:i', // Cast to time only H:i format
        'requires_ticket' => 'boolean',
    ];

    /**
     * Get the place that this visit type belongs to.
     */
    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'place_id', 'place_id');
    }

    /**
     * Get the volunteers assigned to this visit type.
     */
    public function volunteers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'volunteers_visit_types', 'visit_type_id', 'user_id');
                    // Removed wherePivot('role', 'volunteer') as roles are now managed by Spatie
                    // Filter using Spatie's role scope when querying the relationship if needed:
                    // $visitType->volunteers()->role('volunteer')->get()
    }

    /**
     * Get the visits scheduled for this visit type.
     */
    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class, 'visit_type_id', 'visit_type_id');
    }
}
