<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Add this line
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visit extends Model
{
    use HasFactory;

    const STATUS_PROPOSED = 'proposed';
    const STATUS_COMPLETE = 'complete';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EFFECTED = 'effected'; // Or map this to 'completed' if they are synonymous

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'visits';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'visit_id';

    /**
     * The name of the "created at" column.
     *
     * @var string|null
     */
    const CREATED_AT = 'created_at';

    /**
     * The name of the "updated at" column.
     * We use status_updated_at instead of the default updated_at.
     *
     * @var string|null
     */
    const UPDATED_AT = 'status_updated_at';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'visit_type_id',
        'visit_date',
        'assigned_volunteer_id',
        'status',
        // created_at and status_updated_at are handled by timestamps/defaults
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'visit_date' => 'date',
    ];

    /**
     * Get the visit type associated with the visit.
     */
    public function visitType(): BelongsTo
    {
        return $this->belongsTo(VisitType::class, 'visit_type_id', 'visit_type_id');
    }

    /**
     * Get the assigned volunteer (user) for the visit.
     */
    public function assignedVolunteer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_volunteer_id', 'user_id');
    }

    /**
     * Get the registrations for the visit.
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'visit_id', 'visit_id');
    }
}
