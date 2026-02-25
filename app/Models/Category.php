<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'parent_id',
    ];

    /**
     * Get all products in this category
     */
    public function oxfords(): BelongsToMany
    {
        return $this->belongsToMany(Oxford::class);
    }

    /**
     * This will give model's Parent
     *
     * @return Belongs To
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * This will give models's Parent, Parent's parent, and so on until root.
     *
     * @return BelongsTo
     */
    public function parentRecursiveFlatten()
    {
        $result = collect();
        $item = $this->parentRecursive;
        if ($item instanceof User) {
            $result->push($item);
            $result = $result->merge($item->parentRecursiveFlatten());
        }

        return $result;
    }

    /**
     * This will give model's Children
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * This will give model's Children, Children's Children and so on until last
     */
    public function ChildrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    public function skus()
    {
        return $this->hasManyThrough(
            Sku::class,
            CategoryProduct::class,
            'category_id',
            'product_id',
            'id',
            'product_id'
        );
    }
}
