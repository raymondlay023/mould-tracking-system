<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
