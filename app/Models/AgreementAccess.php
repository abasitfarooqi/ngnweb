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

    /**
     * Customer-facing rental signing URL (V6 first priority).
     * Used for automated email and the staff copy-paste link.
     */
    public static function customerSigningUrl(int $customerId, string $passcode): string
    {
        return route('agreement.show.ins.v6', [
            'customer_id' => $customerId,
            'passcode' => $passcode,
        ]);
    }

    /**
     * Optional Loyalty Scheme Policy signing URL (same passcode as rental access).
     */
    public static function loyaltySchemeSigningUrl(int $customerId, string $passcode): string
    {
        return route('loyalty.scheme.show', [
            'customer_id' => $customerId,
            'passcode' => $passcode,
        ]);
    }

    public static function rentalUrlsFor(int $customerId, string $passcode): array
    {
        $params = ['customer_id' => $customerId, 'passcode' => $passcode];
        $customer = self::customerSigningUrl($customerId, $passcode);

        return [
            'customer' => $customer,
            'standard' => route('agreement.show.v6', $params),
            'ins' => $customer,
            'loyalty' => self::loyaltySchemeSigningUrl($customerId, $passcode),
        ];
    }

    public function rentalAgreementUrls(): array
    {
        return self::rentalUrlsFor($this->customer_id, $this->passcode);
    }

    public function getLinkHtmlAttribute()
    {
        $url = $this->getLink();

        return '<a href="'.$url.'" target="_blank">'.$url.'</a>';
    }

    public function getLink()
    {
        return self::customerSigningUrl((int) $this->customer_id, (string) $this->passcode);
    }
}
