<?php

// app\Models\Ecommerce\EcOrder.php

namespace App\Models\Ecommerce;

use App\Mail\Ecommerce\OrderConfirmedAlertMailer;
use App\Mail\Ecommerce\OrderConfirmedMailer;
use App\Mail\Ecommerce\OrderReadyToCollectMailer;
use App\Models\Branch;
use App\Models\CustomerAddress;
use App\Models\CustomerAuth;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Traits\HasRoles;

class EcOrder extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'ec_orders';

    protected $fillable = [
        'order_date',
        'order_status',
        'total_amount',
        'discount',
        'tax',
        'grand_total',
        'shipping_cost',
        'shipping_status',
        'shipping_date',
        'payment_status',
        'currency',
        'payment_date',
        'payment_reference',
        'customer_id',
        'shipping_method_id',
        'payment_method_id',
        'customer_address_id',
        'branch_id',
    ];

    protected $casts = [
        'order_date' => 'datetime',
        'shipping_date' => 'datetime',
        'payment_date' => 'datetime',
        'total_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(CustomerAuth::class, 'customer_id');
    }

    public function shippingMethod(): BelongsTo
    {
        return $this->belongsTo(EcShippingMethod::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(EcPaymentMethod::class);
    }

    public function customerAddress(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(EcOrderItem::class, 'order_id');
    }

    public function shipping(): HasOne
    {
        return $this->hasOne(EcOrderShipping::class, 'order_id');
    }

    public function items()
    {
        return $this->hasMany(EcOrderItem::class, 'order_id');
    }

    protected static function booted(): void
    {
        static::created(function (EcOrder $order): void {
            $id = $order->id;
            DB::afterCommit(function () use ($id): void {
                $fresh = EcOrder::query()->find($id);
                if ($fresh) {
                    static::dispatchOrderStatusEmails($fresh);
                }
            });
        });

        static::updated(function (EcOrder $order): void {
            if (! $order->wasChanged('order_status')) {
                return;
            }
            $id = $order->id;
            DB::afterCommit(function () use ($id): void {
                $fresh = EcOrder::query()->find($id);
                if ($fresh) {
                    static::dispatchOrderStatusEmails($fresh);
                }
            });
        });
    }

    /**
     * Customer-facing emails for the current order_status. Runs after DB commit so line items exist on create.
     * PayPal "In Progress" / process emails are sent from PayPalWebhookController; do not duplicate here.
     */
    protected static function dispatchOrderStatusEmails(EcOrder $order): void
    {
        $newStatus = (string) $order->order_status;
        $norm = strtolower(trim($newStatus));

        Log::info('EcOrder status email hook', [
            'order_id' => $order->id,
            'status' => $newStatus,
            'normalized' => $norm,
        ]);

        try {
            $order->loadMissing(['customer', 'items.product', 'customerAddress', 'shippingMethod', 'branch']);

            if (! $order->customer || ! $order->customer->email) {
                Log::warning('No customer email found for order', ['order_id' => $order->id]);

                return;
            }

            $recipients = [$order->customer->email];

            switch ($norm) {
                case 'confirmed':
                    Mail::to($recipients)
                        ->bcc(['admin@neguinhomotors.co.uk', 'support@neguinhomotors.co.uk'])
                        ->send(new OrderConfirmedMailer($order));

                    Mail::to('customerservice@neguinhomotors.co.uk')
                        ->bcc(['admin@neguinhomotors.co.uk', 'support@neguinhomotors.co.uk'])
                        ->send(new OrderConfirmedAlertMailer($order));
                    break;

                case 'cancelled':
                    if (strtolower(trim((string) $order->payment_status)) === 'refunded') {
                        break;
                    }
                    Mail::raw(
                        "Your order #{$order->id} has been cancelled.\n\nIf you did not request this change, please contact support@neguinhomotors.co.uk.",
                        function ($message) use ($recipients, $order): void {
                            $message
                                ->to($recipients)
                                ->bcc(['admin@neguinhomotors.co.uk', 'support@neguinhomotors.co.uk'])
                                ->subject('Order #'.$order->id.' Cancelled');
                        }
                    );
                    break;

                case 'ready to collect':
                    try {
                        Mail::to($recipients)
                            ->bcc(['admin@neguinhomotors.co.uk', 'support@neguinhomotors.co.uk'])
                            ->send(new OrderReadyToCollectMailer($order));
                    } catch (\Exception $e) {
                        Log::error('Failed to send order ready to collect email', [
                            'order_id' => $order->id,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }
                    break;

                default:
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Failed to send order notification email', [
                'order_id' => $order->id,
                'status' => $newStatus,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
