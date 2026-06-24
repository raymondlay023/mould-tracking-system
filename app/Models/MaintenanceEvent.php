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
        'mould_id', 'start_ts', 'end_ts', 'type', 'pm_subtype',
        'description', 'parts_used', 'downtime_min', 'cost',
        'next_due_shot', 'next_due_date',
        'performed_by', 'notes',
        'machine_id', 'plant_id',
        'status', 'checklist_data',
    ];

    protected $casts = [
        'start_ts' => 'datetime',
        'end_ts' => 'datetime',
        'downtime_min' => 'integer',
        'cost' => 'integer',
        'next_due_shot' => 'integer',
        'next_due_date' => 'date',
        'checklist_data' => 'array',
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
                'type', 'pm_subtype',
                'start_ts', 'end_ts',
                'description', 'notes',
                'downtime_min', 'cost',
                'checklist_data'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function booted()
    {
        static::creating(function ($event) {
            if ($event->type === 'PM' && empty($event->checklist_data)) {
                if ($event->pm_subtype === 'DAILY') {
                    $event->checklist_data = [
                        ['task' => 'Cooling circulation', 'cleaning' => false, 'lubricate' => false, 'remark' => ''],
                        ['task' => 'Surface cavity', 'cleaning' => false, 'lubricate' => false, 'remark' => ''],
                        ['task' => 'Surface core', 'cleaning' => false, 'lubricate' => false, 'remark' => ''],
                        ['task' => 'Slider slide plate', 'cleaning' => false, 'lubricate' => false, 'remark' => ''],
                        ['task' => 'Angular pin / angular cams', 'cleaning' => false, 'lubricate' => false, 'remark' => ''],
                        ['task' => 'Limit switch / sensor', 'cleaning' => false, 'lubricate' => false, 'remark' => ''],
                        ['task' => 'Cek hot runner', 'cleaning' => false, 'lubricate' => false, 'remark' => ''],
                    ];
                } elseif ($event->pm_subtype === 'WEEKLY') {
                    $event->checklist_data = [
                        ['task' => 'Cooling circulation', 'cleaning' => false, 'lubricate' => false, 'remark' => ''],
                        ['task' => 'Ejector pin', 'cleaning' => false, 'lubricate' => false, 'remark' => ''],
                        ['task' => 'Ejector block', 'cleaning' => false, 'lubricate' => false, 'remark' => ''],
                        ['task' => 'Slider', 'cleaning' => false, 'lubricate' => false, 'remark' => ''],
                        ['task' => 'Angular pin / angular cams', 'cleaning' => false, 'lubricate' => false, 'remark' => ''],
                        ['task' => 'Lifter', 'cleaning' => false, 'lubricate' => false, 'remark' => ''],
                        ['task' => 'Gas spring / spring / slinder', 'cleaning' => false, 'lubricate' => false, 'remark' => ''],
                        ['task' => 'Air vent', 'cleaning' => false, 'lubricate' => false, 'remark' => ''],
                        ['task' => 'Limit switch / sensor', 'cleaning' => false, 'lubricate' => false, 'remark' => ''],
                        ['task' => 'Ejector leader pin', 'cleaning' => false, 'lubricate' => false, 'remark' => ''],
                        ['task' => 'Support pin', 'cleaning' => false, 'lubricate' => false, 'remark' => ''],
                        ['task' => 'Leader pin', 'cleaning' => false, 'lubricate' => false, 'remark' => ''],
                        ['task' => 'Puller bolts / stop bolts', 'cleaning' => false, 'lubricate' => false, 'remark' => ''],
                        ['task' => 'Guide rail', 'cleaning' => false, 'lubricate' => false, 'remark' => ''],
                        ['task' => 'Cek visual core dan cavity', 'cleaning' => false, 'lubricate' => false, 'remark' => ''],
                        ['task' => 'Pemberian anti rush core dan cavity', 'cleaning' => false, 'lubricate' => false, 'remark' => ''],
                    ];
                } elseif ($event->pm_subtype === 'PPM') {
                    $event->checklist_data = [
                        // Cavity Side
                        ['group' => 'Cavity Side', 'task' => 'Surface - eching or mirror', 'methode_check' => 'Visual Check', 'standard_value' => 'No Scratch/Dented', 'status' => '', 'remark' => ''],
                        ['group' => 'Cavity Side', 'task' => 'Guide bush/pin', 'methode_check' => 'Ukur Diameter', 'standard_value' => 'Toleransi 0,2 mm', 'status' => '', 'remark' => ''],
                        ['group' => 'Cavity Side', 'task' => 'Stopper pin slider', 'methode_check' => 'Ukur Diameter', 'standard_value' => 'Toleransi 0,2 mm', 'status' => '', 'remark' => ''],
                        ['group' => 'Cavity Side', 'task' => 'Heater / termocopel', 'methode_check' => 'Cek dengan Ohm m', 'standard_value' => 'No Broken', 'status' => '', 'remark' => ''],
                        ['group' => 'Cavity Side', 'task' => 'Sprubush for nozel', 'methode_check' => 'Ukur radius', 'standard_value' => 'Nozle, sprue bush match', 'status' => '', 'remark' => ''],
                        ['group' => 'Cavity Side', 'task' => 'Hose/cable/nipple', 'methode_check' => 'Visual Check', 'standard_value' => 'No Broken', 'status' => '', 'remark' => ''],
                        ['group' => 'Cavity Side', 'task' => 'Runner gate', 'methode_check' => 'Visual Check', 'standard_value' => 'No Undercut/Dented', 'status' => '', 'remark' => ''],
                        ['group' => 'Cavity Side', 'task' => 'Valve gate', 'methode_check' => 'Test timer gate', 'standard_value' => 'Pin gate valve open', 'status' => '', 'remark' => ''],
                        ['group' => 'Cavity Side', 'task' => 'Circulation cooling sistem', 'methode_check' => 'Sirkulasi dg mc', 'standard_value' => 'No Stuck', 'status' => '', 'remark' => ''],
                        ['group' => 'Cavity Side', 'task' => 'Parting line', 'methode_check' => 'Visual Check', 'standard_value' => 'No Rusty/Dirty', 'status' => '', 'remark' => ''],
                        ['group' => 'Cavity Side', 'task' => 'Lock / safety mold', 'methode_check' => 'Visual Check', 'standard_value' => 'No Broken/lose', 'status' => '', 'remark' => ''],
                        ['group' => 'Cavity Side', 'task' => 'Locketring mold', 'methode_check' => 'Ukur Diameter', 'standard_value' => 'Toleransi 0,5 mm', 'status' => '', 'remark' => ''],
                        
                        // Core Side
                        ['group' => 'Core Side', 'task' => 'Surface-eching or mirror', 'methode_check' => 'Visual Check', 'standard_value' => 'No Scratch/Dented', 'status' => '', 'remark' => ''],
                        ['group' => 'Core Side', 'task' => 'Ejector pin', 'methode_check' => 'Ukur diameter', 'standard_value' => 'Toleransi 0,2 mm', 'status' => '', 'remark' => ''],
                        ['group' => 'Core Side', 'task' => 'Center sleep pin', 'methode_check' => 'Ukur diameter', 'standard_value' => 'Toleransi 0,2 mm', 'status' => '', 'remark' => ''],
                        ['group' => 'Core Side', 'task' => 'Airvent/parting line', 'methode_check' => 'Visual Check', 'standard_value' => 'No Rusty/Dirty', 'status' => '', 'remark' => ''],
                        ['group' => 'Core Side', 'task' => 'Hose/cable/nipple', 'methode_check' => 'Visual Check', 'standard_value' => 'No Broken/lose', 'status' => '', 'remark' => ''],
                        ['group' => 'Core Side', 'task' => 'Limit switch/sensor cable/conector', 'methode_check' => 'Kontak point', 'standard_value' => 'Open close sesuai std', 'status' => '', 'remark' => ''],
                        ['group' => 'Core Side', 'task' => 'Hole ejector root (position)', 'methode_check' => 'Visual Check', 'standard_value' => 'No Undercut/No same', 'status' => '', 'remark' => ''],
                        ['group' => 'Core Side', 'task' => 'Check all screw for tight', 'methode_check' => 'Visual Check', 'standard_value' => 'No Broken/No Tight', 'status' => '', 'remark' => ''],
                        ['group' => 'Core Side', 'task' => 'Guide bush / pin', 'methode_check' => 'Ukur diameter', 'standard_value' => 'Toleransi 0,2 mm', 'status' => '', 'remark' => ''],
                        ['group' => 'Core Side', 'task' => 'Slider', 'methode_check' => 'Ukur diameter', 'standard_value' => 'Toleransi 0,2 mm', 'status' => '', 'remark' => ''],
                        ['group' => 'Core Side', 'task' => 'Guide rail', 'methode_check' => 'Ukur diameter', 'standard_value' => 'Toleransi 0,2 mm', 'status' => '', 'remark' => ''],
                        ['group' => 'Core Side', 'task' => 'Angular pin', 'methode_check' => 'Ukur diameter', 'standard_value' => 'Toleransi 0,2 mm', 'status' => '', 'remark' => ''],
                        ['group' => 'Core Side', 'task' => 'Slide block', 'methode_check' => 'Ukur diameter', 'standard_value' => 'Toleransi 0,2 mm', 'status' => '', 'remark' => ''],
                        ['group' => 'Core Side', 'task' => 'Lifter/ejector block (screw)', 'methode_check' => 'Ukur diameter', 'standard_value' => 'Toleransi 0,2 mm', 'status' => '', 'remark' => ''],
                        ['group' => 'Core Side', 'task' => 'Nipple/coupler water', 'methode_check' => 'Visual Check', 'standard_value' => 'No Broken/lose', 'status' => '', 'remark' => ''],
                        ['group' => 'Core Side', 'task' => 'Cooling system', 'methode_check' => 'Test Pump', 'standard_value' => 'No Stuck', 'status' => '', 'remark' => ''],
                        ['group' => 'Core Side', 'task' => 'Coil spring/gas spring', 'methode_check' => 'Pressure Manual &', 'standard_value' => 'No Broken/No Tight', 'status' => '', 'remark' => ''],
                        ['group' => 'Core Side', 'task' => 'Check all insert in EJ lifter/block', 'methode_check' => 'Visual Check', 'standard_value' => 'No Crack/Broken', 'status' => '', 'remark' => ''],
                    ];
                } else {
                    // not yet defined
                }
            }
        });
    }
}
