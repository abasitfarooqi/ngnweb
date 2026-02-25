<?php

namespace App\Models;

use App\Services\FtpSyncService;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mews\Purifier\Facades\Purifier;
use Spatie\Permission\Traits\HasRoles;

class NgnProduct extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'ngn_products';

    protected $primaryKey = 'id';

    protected $fillable = [
        'sku',
        'ean',
        'image_url',
        'name',
        'variation',
        'description',
        'extended_description',
        'colour',
        'pos_variant_id',
        'pos_product_id',
        'brand_id',
        'category_id',
        'model_id',
        'normal_price',
        'pos_price',
        'pos_vat',
        'global_stock',
        'vatable',
        'is_oxford',
        'dead',
        'sorting_code',
        'slug',
        'meta_title',
        'meta_description',
        'is_ecommerce',
    ];

    public function brand()
    {
        return $this->belongsTo(NgnBrand::class, 'brand_id');
    }

    public function category()
    {
        return $this->belongsTo(NgnCategory::class, 'category_id');
    }

    public function getDetailAttribute()
    {
        return $this->name.' | '.$this->brand->name.' | '.$this->category->name.' | '.$this->model->name.' | '.$this->sku;
    }

    public function model()
    {
        return $this->belongsTo(NgnModel::class, 'model_id');
    }

    public function productModel()
    {
        return $this->belongsTo(NgnModel::class, 'model_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(NgnStockMovement::class, 'product_id');
    }

    public function orderItems()
    {
        return $this->hasMany(EcOrderItem::class, 'product_id');
    }

    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = Purifier::clean($value);
    }

    public function setExtendedDescriptionAttribute($value)
    {
        $this->attributes['extended_description'] = Purifier::clean($value);
    }

    // -- For StockHandlerCrudcontroller

    public function getCatfordStockAttribute()
    {
        return NgnStockMovement::where('product_id', $this->id)
            ->where('branch_id', 1)
            ->sum('in') - NgnStockMovement::where('product_id', $this->id)
            ->where('branch_id', 1)
            ->sum('out');
    }

    // Define accessor for Tooting Stock
    public function getTootingStockAttribute()
    {
        return NgnStockMovement::where('product_id', $this->id)
            ->where('branch_id', 2)
            ->sum('in') - NgnStockMovement::where('product_id', $this->id)
            ->where('branch_id', 2)
            ->sum('out');
    }

    public function product()
    {
        return $this->belongsTo(NgnProduct::class, 'pos_product_id'); // Ensure the correct foreign key
    }

    public function productImages()
    {
        return $this->hasMany(NgnProductImage::class, 'product_id');
    }

    // In NgnProduct model
    public function scopeFilterCategory($query, $categories)
    {
        if (! empty($categories)) {
            return $query->whereIn('category_id', $categories);
        }

        return $query;
    }

    public function scopeFilterBrand($query, $brands)
    {
        if (! empty($brands)) {
            return $query->whereIn('brand_id', $brands);
        }

        return $query;
    }

    public function scopeSearch($query, $search)
    {
        if (! empty($search)) {
            // Implement full-text search or optimized search here
        }

        return $query;
    }

    public function getStockByBranch($branchId)
    {
        return NgnStockMovement::where('product_id', $this->id)
            ->where('branch_id', $branchId)
            ->selectRaw('SUM(`in`) - SUM(`out`) as available_stock')
            ->value('available_stock') ?? 0;
    }

    public function getStockLocationText()
    {
        $catfordStock = $this->getStockByBranch(1);
        $tootingStock = $this->getStockByBranch(2);

        $locations = [];
        if ($catfordStock > 0) {
            $locations[] = "Catford: {$catfordStock}";
        }
        if ($tootingStock > 0) {
            $locations[] = "Tooting: {$tootingStock}";
        }

        return ! empty($locations) ? implode(', ', $locations) : 'Out of Stock after this order';
    }

    protected static function booted()
    {
        static::saved(function ($model) {
            // Sync all productImages files after save
            if ($model->productImages) {
                foreach ($model->productImages as $image) {
                    $filePath = $image->image_url; // Adjust if your field name is different
                    if ($filePath && Storage::disk('product_images')->exists($filePath)) {
                        $fullLocalPath = Storage::disk('product_images')->path($filePath);

                        Log::info("[📦 NgnProduct Sync] Syncing file: $fullLocalPath");

                        $success = app(FtpSyncService::class)->uploadFile($fullLocalPath);

                        Log::info('[📦 NgnProduct Sync] Upload result: '.($success ? '✅ success' : '❌ failure'));
                    } else {
                        Log::warning("[📦 NgnProduct Sync] File does not exist or path empty: $filePath");
                    }
                }
            }
        });
    }
}
