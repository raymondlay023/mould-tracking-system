<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TrialEvent extends Model
{
    use HasUuids;

    protected $table = 'trial_events';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'mould_id','machine_id',
        'start_ts','end_ts',
        'purpose','parameters','notes',
        'approved','approved_go','approved_by','approved_at'
    ];

    protected $casts = [
        'start_ts' => 'datetime',
        'end_ts' => 'datetime',
        'approved' => 'boolean',
        'approved_go' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function mould() { return $this->belongsTo(Mould::class); }
    public function machine() { return $this->belongsTo(Machine::class); }
}
