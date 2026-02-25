<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class UploadDocumentAccess extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $fillable = [
        'customer_id',
        'booking_id',
        'passcode',
        'expires_at',
    ];

    protected $appends = ['link_html'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function booking()
    {
        return $this->belongsTo(RentingBooking::class);
    }

    public function getCustomerName()
    {
        return $this->customer ? $this->customer->first_name.' '.$this->customer->last_name : 'No customer';
    }

    public function getLinkHtmlAttribute()
    {
        $url = url("/upload-doc/{$this->customer_id}/{$this->passcode}");
        $link = '<a href="'.$url.'" target="_blank">'.$url.'</a>';
        \Log::info('Generated Link: '.$link); // Log the generated link

        return $link;
    }

    public function getLink()
    {
        $url = url("/upload-doc/{$this->customer_id}/{$this->passcode}");

        return $url;
    }
}
