<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourSchedule extends Model
{
    use HasFactory;

    protected $primaryKey = 'schedule_id';

    protected $fillable = [
        'visit_type_id',
        'day_of_week',
        'start_time',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
    ];

    public function visitType(): BelongsTo
    {
        return $this->belongsTo(VisitType::class, 'visit_type_id', 'visit_type_id');
    }
}
