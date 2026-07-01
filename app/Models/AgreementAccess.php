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

    public static function rentalUrlsFor(int $customerId, string $passcode): array
    {
        $params = ['customer_id' => $customerId, 'passcode' => $passcode];

        return [
            'standard' => route('agreement.show.v6', $params),
            'ins' => route('agreement.show.ins.v6', $params),
        ];
    }

    public function rentalAgreementUrls(): array
    {
        return self::rentalUrlsFor($this->customer_id, $this->passcode);
    }

    public function getLinkHtmlAttribute()
    {
        $url = $this->rentalAgreementUrls()['ins'];

        return '<a href="'.$url.'" target="_blank">'.$url.'</a>';
    }

    public function getLink()
    {
        return $this->rentalAgreementUrls()['ins'];
    }
}
