<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Orchard extends Model
{
    use HasFactory;

    protected $fillable = [
        'orchardName', 
        'numTree', 
        'device_id', 
        'durian_id',
        'orchardSize',
        'location'
        // user_id removed as it's not in the database
    ];
    // Each Orchard belongs to one Durian
    public function durian()
    {
        return $this->belongsTo(Durian::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id');
    }
    
    public function vibrationLogs()
    {
        return $this->hasMany(VibrationLog::class, 'device_id', 'device_id');
    }
    
    public function farmer()
    {
        return $this->belongsTo(Farmer::class, 'user_id');
    }
}
