<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Plant extends Model
{
    use HasUuids;

    protected $fillable = ['name'];

    protected $keyType = 'string';
    public $incrementing = false;

    public function zones() { return $this->hasMany(Zone::class); }
    public function machines() { return $this->hasMany(Machine::class); }
}
