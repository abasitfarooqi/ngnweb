<?php

namespace App\Models;

use App\Http\Controllers\Admin\RecoveredMotorbikeCrudController;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class RecoveredMotorbike extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'recovered_motorbikes';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    protected $fillable = ['case_date', 'user_id', 'branch_id', 'motorbike_id', 'notes', 'returned_date'];

    protected $hidden = ['user_id'];

    protected $casts = [
        'case_date' => 'datetime',
        'returned_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function motorbike()
    {
        return $this->belongsTo(Motorbike::class);
    }

    protected static function booted()
    {
        static::saving(function ($recoveredMotorbike) {
            \App::make(RecoveredMotorbikeCrudController::class)->post_precheck($recoveredMotorbike);
        });
    }
}
