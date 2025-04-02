<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Add this line
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrecludedDate extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'precluded_dates';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'precluded_date';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string'; // Date keys are treated as strings

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     * We only have 'set_at' which is handled by the database default.
     *
     * @var bool
     */
    public $timestamps = false; // Only 'set_at' exists, handled by DB

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'precluded_date',
        'reason',
        'set_by_user_id',
        // 'set_at' is set by the database
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'precluded_date' => 'date',
    ];

    /**
     * Get the user who set this precluded date.
     */
    public function setter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'set_by_user_id', 'user_id');
    }
}
