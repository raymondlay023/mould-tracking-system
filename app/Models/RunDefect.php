<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class RunDefect extends Model
{
    use HasUuids;

    protected $table = 'run_defects';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['run_id','defect_code','qty'];

    protected $casts = ['qty' => 'integer'];

    public function run() { return $this->belongsTo(ProductionRun::class, 'run_id'); }
}
