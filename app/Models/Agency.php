<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agency extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'agency_id';

    protected $fillable = ['name', 'contact_email', 'website'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'agency_id');
    }

    public function visitTypes(): HasMany
    {
        return $this->hasMany(VisitType::class, 'agency_id');
    }
}
