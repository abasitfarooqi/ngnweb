<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class PurchaseRequestItem extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $fillable = [
        'pr_id',
        'item_name',
        'qty',
        'note',
        'created_by',
        'is_posted',
        'is_approved',
        'brand_name_id',
        'bike_model_id',
        'model',
        'color',
        'year',
        'chassis_no',
        'reg_no',
        'part_number',
        'part_position',
        'link_one',
        'link_two',
        'quantity',
        'image',
    ];

    // Update the relationship to use the new Make model
    public function brandName()
    {
        return $this->belongsTo(Make::class, 'brand_name_id');
    }

    public function bikeModel()
    {
        return $this->belongsTo(BikeModel::class, 'bike_model_id');
    }

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class, 'pr_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approve()
    {
        $this->is_approved = true;
        $this->save();
    }

    public function reject()
    {
        $this->is_approved = false;
        $this->save();
    }

    public function post()
    {
        $this->is_posted = true;
        $this->save();
    }

    public function unpost()
    {
        $this->is_posted = false;
        $this->save();
    }

    public function isApproved()
    {
        return $this->is_approved;
    }
}
