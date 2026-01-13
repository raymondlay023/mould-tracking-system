<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SetupEvent extends Model
{
    use HasUuids;

    protected $table = 'setup_events';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'mould_id','machine_id',
        'start_ts','end_ts',
        'target_min','actual_min',
        'loss_reason','operator_name','notes'
    ];

    protected $casts = [
        'start_ts' => 'datetime',
        'end_ts' => 'datetime',
        'target_min' => 'integer',
        'actual_min' => 'integer',
    ];

    public function mould() { return $this->belongsTo(Mould::class); }
    public function machine() { return $this->belongsTo(Machine::class); }
}
