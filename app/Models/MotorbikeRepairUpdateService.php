<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
// use Backpack\CRUD\app\Models\Traits\CrudTrait;

// class MotorbikeRepairUpdateService extends Model
// {
//     use CrudTrait, HasFactory;

//     protected $table = 'motorbike_repair_update_services';

//     protected $fillable = [
//         'motorbike_repair_update_id',
//         'motorbike_repair_services_list_id',
//     ];

//     public function update()
//     {
//         return $this->belongsTo(MotorbikeRepairUpdate::class, 'motorbike_repair_update_id');
//     }

//     public function service()
//     {
//         return $this->belongsTo(MotorbikeRepairServicesList::class, 'motorbike_repair_services_list_id');
//     }
// }
