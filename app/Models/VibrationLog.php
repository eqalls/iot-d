<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VibrationLog extends Model
{
    const LOG_TYPE_DURIAN_FALL = 1;
    const LOG_TYPE_ANIMAL_THREAT = 2;
    
    protected $table = 'vibration_logs';

    public $timestamps = false;

    // Update fillable to include orchard_id and match your database columns
    // Remove timestamp if that column doesn't exist in your database
    protected $fillable = ['device_id', 'vibration_count', 'log_type', 'orchard_id', 'created_at'];

    protected $casts = [
        'created_at' => 'datetime', 
    ];

    public function orchard()
    {
        // Fix the relationship to ensure it matches correctly
        return $this->belongsTo(Orchard::class, 'device_id', 'device_id');
    }
    
    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id', 'id');
    }
    
    // Add an accessor to get farm name through relationships
    public function getFarmNameAttribute()
    {
        if ($this->orchard && $this->orchard->farmer && $this->orchard->farmer->user) {
            return $this->orchard->farmer->user->name;
        }
        
        return 'Unknown Farm';
    }
    
    // Add an accessor for fall count (assuming vibration_count is the fall count)
    public function getFallCountAttribute()
    {
        return $this->vibration_count ?? 0;
    }
    
    // Add a helper method to get orchard name safely
    public function getOrchardNameAttribute()
    {
        return $this->orchard->orchardName ?? 'Unknown Orchard';
    }
    
    public function getLogTypeNameAttribute()
    {
        return match($this->log_type) {
            self::LOG_TYPE_DURIAN_FALL => 'Durian Fall',
            self::LOG_TYPE_ANIMAL_THREAT => 'Animal Threat',
            default => 'Other Alert'
        };
    }
}
