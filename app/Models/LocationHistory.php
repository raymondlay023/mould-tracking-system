<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class LocationHistory extends Model
{
    use HasUuids;

    protected $table = 'location_histories';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'mould_id','plant_id','machine_id','location',
        'start_ts','end_ts','moved_by','note'
    ];

    protected $casts = [
        'start_ts' => 'datetime',
        'end_ts' => 'datetime',
    ];

    public function mould() { return $this->belongsTo(Mould::class); }
    public function plant() { return $this->belongsTo(Plant::class); }
    public function machine() { return $this->belongsTo(Machine::class); }

    public function scopeCurrent($q)
    {
        return $q->whereNull('end_ts');
    }
}
