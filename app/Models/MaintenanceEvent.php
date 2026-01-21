<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceEvent extends Model
{
    use HasFactory, HasUuids, \Spatie\Activitylog\Traits\LogsActivity;

    protected $table = 'maintenance_events';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'mould_id', 'start_ts', 'end_ts', 'type',
        'description', 'parts_used', 'downtime_min', 'cost',
        'next_due_shot', 'next_due_date',
        'performed_by', 'notes',
        'machine_id', 'plant_id',
        'status',
    ];

    protected $casts = [
        'start_ts' => 'datetime',
        'end_ts' => 'datetime',
        'downtime_min' => 'integer',
        'cost' => 'integer',
        'next_due_shot' => 'integer',
        'next_due_date' => 'date',
    ];

    public function mould()
    {
        return $this->belongsTo(Mould::class);
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('end_ts');
    }
    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->useLogName('maintenance_event')
            ->logOnly([
                'status',
                'start_ts', 'end_ts',
                'description', 'notes',
                'downtime_min', 'cost'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
