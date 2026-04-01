<?php

// app\Models\Ecommerce\EcOrder.php

namespace App\Models\Ecommerce;

use App\Mail\Ecommerce\OrderConfirmedAlertMailer;
use App\Mail\Ecommerce\OrderConfirmedMailer;
use App\Mail\Ecommerce\OrderReadyToCollectMailer;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerAuth;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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

    protected static function booted()
    {
        static::saving(function ($order) {

            if ($order->exists) {
                $originalStatus = $order->getOriginal('order_status');
                $newStatus = $order->order_status;

                // Only proceed if status has changed
                if ($originalStatus !== $newStatus) {
                    Log::info('Order status changing', [
                        'order_id' => $order->id,
                        'from' => $originalStatus,
                        'to' => $newStatus,
                    ]);

                    try {
                        // Make sure all relations are loaded
                        if (! $order->relationLoaded('customer')) {
                            $order->load(['customer', 'items.product', 'customerAddress', 'shippingMethod', 'branch']);
                        }

                        if ($order->customer && $order->customer->email) {
                            $recipients = [$order->customer->email];

                            switch ($newStatus) {
                                case 'Confirmed':

                                    // Email to customer
                                    Mail::to($recipients)
                                        ->bcc(['admin@neguinhomotors.co.uk', 'support@neguinhomotors.co.uk'])
                                        ->send(new OrderConfirmedMailer($order));

                                    // Email to customer service
                                    Mail::to('customerservice@neguinhomotors.co.uk')
                                        ->bcc(['admin@neguinhomotors.co.uk', 'support@neguinhomotors.co.uk'])
                                        ->send(new OrderConfirmedAlertMailer($order));

                                    break;
                                case 'Cancelled':
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

                                case 'Pending':
                                    // Mail::to($recipients)
                                    //     ->send(new OrderPendingMail($order));

                                    break;

                                case 'In Progress':

                                    // Manual Item processing

                                    break;

                                case 'Ready to collect':

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

                                case 'Delivered':

                                    // Assuming Customer has received the order by Store Pickup Method while otherwise we rely on Royal Mail Response
                                    break;
                            }
                        } else {
                            Log::warning('No customer email found for order', ['order_id' => $order->id]);
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
        });
    }
}
