<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Mould extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    protected $table = 'moulds';

    // kalau PK kamu uuid 'id'
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'code',
        'name',
        'cavities',
        'customer',
        'resin',
        'min_tonnage_t',
        'max_tonnage_t',
        'pm_interval_shot',
        'pm_interval_days',
        'commissioned_at',
        'rmp_last_at',
        'rmp_approved_by',
        'status',
    ];

    protected $casts = [
        'commissioned_at' => 'date',
        'rmp_last_at' => 'datetime',
        'cavities' => 'integer',
        'min_tonnage_t' => 'integer',
        'max_tonnage_t' => 'integer',
        'pm_interval_shot' => 'integer',
        'pm_interval_days' => 'integer',
        'status' => \App\Enums\MouldStatus::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('mould')
            ->logOnly([
                'code','name','cavities','customer','resin',
                'min_tonnage_t','max_tonnage_t',
                'pm_interval_shot','pm_interval_days',
                'commissioned_at','status',
                'rmp_last_at','rmp_approved_by',
            ])
            ->logOnlyDirty()              // cuma field yang berubah
            ->dontSubmitEmptyLogs();      // jangan bikin log kalau gak ada perubahan
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return match ($eventName) {
            'created' => 'Created mould',
            'updated' => 'Updated mould',
            'deleted' => 'Deleted mould',
            default => $eventName,
        };
    }
}
