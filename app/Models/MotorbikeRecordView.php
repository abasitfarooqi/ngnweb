<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class MotorbikeRecordView extends Model
{
    use CrudTrait;
    use HasRoles;

    protected $table = 'motorbike_records_view';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'MID',
        'VRM',
        'DATABASE',
        'DOC_ID',
        'PERSON',
        'START_DATE',
        'END_DATE',
    ];
}
