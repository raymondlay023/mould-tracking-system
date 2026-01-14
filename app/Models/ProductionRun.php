<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProductionRun extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    protected $table = 'production_runs';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'mould_id', 'machine_id',
        'start_ts', 'end_ts',
        'cavities_snapshot',
        'shot_total', 'part_total', 'ok_part', 'ng_part',
        'cycle_time_avg_sec', 'operator_name', 'notes',
    ];

    protected $casts = [
        'start_ts' => 'datetime',
        'end_ts' => 'datetime',
        'shot_total' => 'integer',
        'part_total' => 'integer',
        'ok_part' => 'integer',
        'ng_part' => 'integer',
        'cavities_snapshot' => 'integer',
        'cycle_time_avg_sec' => 'integer',
    ];

    public function mould()
    {
        return $this->belongsTo(Mould::class);
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function defects()
    {
        return $this->hasMany(RunDefect::class, 'run_id');
    }

    public function scopeActive($q)
    {
        return $q->whereNull('end_ts');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('run')
            ->logOnly([
                'mould_id', 'machine_id', 'start_ts', 'end_ts',
                'shot_total', 'part_total', 'ok_part', 'ng_part', 'cycle_time_avg_sec',
                'operator_name',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function booted()
    {
        static::creating(function ($run) {
            if (empty($run->cavities_snapshot) && $run->mould_id) {
                $cav = \App\Models\Mould::where('id', $run->mould_id)->value('cavities');
                $run->cavities_snapshot = (int) ($cav ?? 1);
            }
        });
    }
}
