<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Farmer;
use App\Models\Orchard;
use App\Models\Durian;
use App\Models\Storage;

class HarvestLog extends Model
{
    protected $table = 'harvest_logs';

    protected $casts = [
        'harvest_date' => 'date:Y-m-d',
    ];

    protected $fillable = [
        'farmer_id',
        'orchard_id',
        'durian_id',
        'harvest_date',
        'total_harvested',
        'status',
        'grade',
        'condition',
        'storage_location'
    ];

    // Relationships
    public function orchard()
    {
        return $this->belongsTo(Orchard::class);
    }

    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }

    public function durian()
    {
        return $this->belongsTo(Durian::class);
    }

    public function storage()
    {
        return $this->belongsTo(Storage::class, 'storage_location');
    }

    public function scopeForFarmer($query, $userId)
    {
        return $query->whereHas('farmer', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->with(['orchard', 'durian']);
    }
}
