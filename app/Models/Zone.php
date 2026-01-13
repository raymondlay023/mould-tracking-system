<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Zone extends Model
{
    use HasUuids;

    protected $fillable = ['plant_id','code','name'];

    protected $keyType = 'string';
    public $incrementing = false;

    public function plant() { return $this->belongsTo(Plant::class); }
    public function machines() { return $this->hasMany(Machine::class); }
}
