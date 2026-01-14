<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plant extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name'];

    protected $keyType = 'string';
    public $incrementing = false;

    public function zones() { return $this->hasMany(Zone::class); }
    public function machines() { return $this->hasMany(Machine::class); }
}
