<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Machine extends Model
{
    use HasUuids;

    protected $fillable = [
        'plant_id','zone_id','code','name','tonnage_t','plc_connected'
    ];

    protected $casts = [
        'tonnage_t' => 'integer',
        'plc_connected' => 'boolean',
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    public function plant() { return $this->belongsTo(Plant::class); }
    public function zone() { return $this->belongsTo(Zone::class); }
}
