<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Part extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    protected $table = 'parts';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'mould_id',
        'part_number',
        'part_name',
        'cavity_number',
    ];

    protected $casts = [
        'cavity_number' => 'integer',
    ];

    public function mould()
    {
        return $this->belongsTo(Mould::class, 'mould_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('part')
            ->logOnly([
                'mould_id',
                'part_number',
                'part_name',
                'cavity_number',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return match ($eventName) {
            'created' => 'Created part',
            'updated' => 'Updated part',
            'deleted' => 'Deleted part',
            default => $eventName,
        };
    }
}
