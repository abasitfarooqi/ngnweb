<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class AgreementAccess extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $fillable = [
        'customer_id',
        'passcode',
        'expires_at',
        'booking_id',
    ];

    protected $appends = ['link_html'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getLinkHtmlAttribute()
    {
        $url = url("/agreement/{$this->customer_id}/{$this->passcode}");
        $link = '<a href="'.$url.'" target="_blank">'.$url.'</a>';
        \Log::info('Generated Link: '.$link); // Log the generated link

        return $link;
    }

    public function getLink()
    {
        $url = url("/agreement/{$this->customer_id}/{$this->passcode}");

        return $url;
    }
}
