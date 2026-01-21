<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Place extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'places';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'place_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'location',
        'latitude',
        'longitude',
    ];

    /**
     * Scope a query to only include places within a given radius (km).
     */
    public function scopeWithinDistance($query, $lat, $lng, $radius = 10)
    {
        $haversine = "(6371 * acos(cos(radians($lat)) * cos(radians(latitude)) * cos(radians(longitude) - radians($lng)) + sin(radians($lat)) * sin(radians(latitude))))";
        
        return $query->selectRaw("*, $haversine AS distance")
                     ->having('distance', '<', $radius)
                     ->orderBy('distance');
    }

    /**
     * Get the visit types associated with the place.
     */
    public function visitTypes(): HasMany
    {
        return $this->hasMany(VisitType::class, 'place_id', 'place_id');
    }
}
