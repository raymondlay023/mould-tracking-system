<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }

    public function machines()
    {
        return $this->hasMany(Machine::class);
    }
}
