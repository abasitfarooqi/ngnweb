<?php

namespace App\Support;

use App\Models\Motorbike;
use Illuminate\Database\Eloquent\Builder;

class UsedMotorbikeListing
{
    public static function query(
        string $search = '',
        string $sort = 'default',
        string $availability = 'available',
        string $minPrice = '',
        string $maxPrice = '',
    ): Builder {
        $query = Motorbike::query()
            ->join('motorbikes_sale', 'motorbikes.id', '=', 'motorbikes_sale.motorbike_id')
            ->select(
                'motorbikes.*',
                'motorbikes_sale.price',
                'motorbikes_sale.image_one',
                'motorbikes_sale.mileage as sale_mileage',
                'motorbikes_sale.is_sold',
                'motorbikes_sale.id as sale_id',
            );

        if ($search !== '') {
            $term = '%'.$search.'%';
            $query->where(function ($inner) use ($term) {
                $inner->where('motorbikes.make', 'like', $term)
                    ->orWhere('motorbikes.model', 'like', $term)
                    ->orWhere('motorbikes.reg_no', 'like', $term);
            });
        }

        if ($availability === 'sold') {
            $query->where('motorbikes_sale.is_sold', 1);
        } elseif ($availability === 'available') {
            $query->where('motorbikes_sale.is_sold', 0);
        }

        if ($minPrice !== '' && is_numeric($minPrice)) {
            $query->where('motorbikes_sale.price', '>=', (float) $minPrice);
        }

        if ($maxPrice !== '' && is_numeric($maxPrice)) {
            $query->where('motorbikes_sale.price', '<=', (float) $maxPrice);
        }

        match ($sort) {
            'price_asc' => $query->orderBy('motorbikes_sale.price'),
            'price_desc' => $query->orderByDesc('motorbikes_sale.price'),
            'year_asc' => $query->orderBy('motorbikes.year'),
            'year_desc' => $query->orderByDesc('motorbikes.year'),
            default => $query->orderByDesc('motorbikes.created_at'),
        };

        return $query;
    }
}
