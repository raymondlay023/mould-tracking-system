<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plant extends Model
{
    public function zones()
    {
        return $this->hasMany(Zone::class);
    }

    public function machines()
    {
        return $this->hasMany(Machine::class);
    }
}
