<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Registration extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'registrations';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'registration_id';

    /**
     * The name of the "created at" column.
     *
     * @var string|null
     */
    const CREATED_AT = 'registered_at';

    /**
     * The name of the "updated at" column.
     * We use cancelled_at instead of the default updated_at.
     *
     * @var string|null
     */
    const UPDATED_AT = 'cancelled_at';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'visit_id',
        'user_id',
        'num_participants',
        'booking_code',
        // registered_at and cancelled_at are handled by timestamps
    ];

    /**
     * Get the visit that this registration belongs to.
     */
    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class, 'visit_id', 'visit_id');
    }

    /**
     * Get the user (fruitore) that this registration belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
