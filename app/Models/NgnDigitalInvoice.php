<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class NgnDigitalInvoice extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'ngn_digital_invoices';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'invoice_number',
        'invoice_type',
        'invoice_category',
        'template',
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'whatsapp',
        'motorbike_id',
        'registration_number',
        'total_paid',
        'vin',
        'make',
        'model',
        'year',
        'issue_date',
        'due_date',
        'amount',
        'total_paid',
        'booking_invoice_id',
        'internal_notes',
        'notes',
        'status',
        'created_by',
    ];

    public static function generateNumber(): string
    {
        $prefix = 'INV-'.now()->format('Y');
        $last = static::where('invoice_number', 'like', $prefix.'-%')
            ->orderByDesc('id')->value('invoice_number');

        $n = 1;
        if ($last && preg_match('/-(\d+)$/', $last, $m)) {
            $n = intval($m[1]) + 1;
        }

        return sprintf('%s-%04d', $prefix, $n);
    }

    public function items()
    {
        return $this->hasMany(NgnDigitalInvoiceItem::class, 'invoice_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function motorbike()
    {
        return $this->belongsTo(Motorbike::class, 'motorbike_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Fallback to manual entry if no customer is selected
    public function getCustomerNameAttribute()
    {
        return $this->customer
            ? $this->customer->first_name.' '.$this->customer->last_name
            : $this->attributes['customer_name'] ?? '';
    }

    public function getCustomerEmailAttribute()
    {
        return $this->customer
            ? $this->customer->email
            : $this->attributes['customer_email'] ?? '';
    }

    public function getCustomerPhoneAttribute()
    {
        return $this->customer
            ? $this->customer->phone
            : $this->attributes['customer_phone'] ?? '';
    }

    public function getWhatsappAttribute()
    {
        return $this->customer
            ? $this->customer->whatsapp
            : $this->attributes['whatsapp'] ?? '';
    }

    public function getItemsRepeatableAttribute()
    {
        return $this->items
            ? $this->items->map(function ($item) {
                return [
                    'item_name' => $item->item_name,
                    'sku' => $item->sku,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->total,
                    'notes' => $item->notes,
                ];
            })->toArray()
            : [];
    }

    public function getMotorbikeDetailAttribute()
    {
        return $this->motorbike->reg_no.' | '.$this->motorbike->make.' '.$this->motorbike->model.' | '.$this->motorbike->year.' | '.$this->motorbike->vin_number;
    }

    /**
     * Get the invoice detail attribute
     */
    public function getDetailAttribute()
    {
        return $this->invoice_number.' | '.$this->customer->detail.' | £'.number_format($this->total, 2);
    }

    public function printPdfButton()
    {
        return '<a class="btn btn-sm btn-link" target="_blank" href="'.url('admin/bookings/invoices/'.$this->id.'/print').'" data-toggle="tooltip" title="Print Invoice"><i class="la la-print"></i> Print</a>';
    }

    public function scopeInvoiceType($q, $type)
    {
        return $q->where('invoice_type', $type);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = static::generateNumber();
            }
        });

        static::saving(function ($invoice) {
            // Auto-calc totals from items
            $invoice->total = $invoice->items->sum('total');
        });
    }

    public function bookingInvoice()
    {
        return $this->belongsTo(BookingInvoice::class, 'booking_invoice_id');
    }
}
