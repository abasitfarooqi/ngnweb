<?php

namespace App\Livewire\Portal\Finance;

use App\Models\Branch;
use App\Models\Motorbike;
use App\Models\Motorcycle;
use Livewire\Component;

class Browse extends Component
{
    public string $search = '';

    public string $branch_id = '';

    public string $condition = '';

    public int $minDeposit = 0;

    public function calculateMonthlyPayment(float $price, float $deposit): float
    {
        $finance = max(0, $price - $deposit);

        return $finance > 0 ? round($finance / 52, 2) : 0;
    }

    public function render()
    {
        $branches = Branch::orderBy('name')->get();
        $motorbikes = collect();

        try {
            $query = Motorbike::with(['branch', 'images'])
                ->join('motorbikes_sale', 'motorbikes.id', '=', 'motorbikes_sale.motorbike_id')
                ->select('motorbikes.*', 'motorbikes_sale.price as sale_price', 'motorbikes_sale.id as sale_id', 'motorbikes_sale.condition as condition', 'motorbikes_sale.image_one as sale_image_one')
                ->where('motorbikes_sale.is_sold', 0);

            if ($this->search) {
                $s = $this->search;
                $query->where(function ($q) use ($s) {
                    $q->where('motorbikes.make', 'like', '%'.$s.'%')
                        ->orWhere('motorbikes.model', 'like', '%'.$s.'%');
                });
            }

            if ($this->branch_id) {
                $query->where('motorbikes.branch_id', $this->branch_id);
            }

            if ($this->condition !== '') {
                $query->where('motorbikes_sale.condition', $this->condition);
            }

            $motorbikes = $query->orderBy('motorbikes_sale.price', 'asc')->get();
        } catch (\Exception $e) {
        }

        $newForFinance = Motorcycle::query()
            ->where('availability', 'for sale')
            ->orderByDesc('id')
            ->get();

        $usedForFinance = Motorbike::query()
            ->join('motorbikes_sale', 'motorbikes.id', '=', 'motorbikes_sale.motorbike_id')
            ->select(
                'motorbikes.*',
                'motorbikes_sale.price',
                'motorbikes_sale.image_one',
                'motorbikes_sale.mileage as sale_mileage',
            )
            ->where('motorbikes_sale.is_sold', 0)
            ->where(function ($q) {
                $q->where('motorbikes.is_ebike', false)->orWhereNull('motorbikes.is_ebike');
            })
            ->orderByDesc('motorbikes.created_at')
            ->get();

        $ebikesForFinance = Motorbike::query()
            ->join('motorbikes_sale', 'motorbikes.id', '=', 'motorbikes_sale.motorbike_id')
            ->select(
                'motorbikes.*',
                'motorbikes_sale.price',
                'motorbikes_sale.image_one',
                'motorbikes_sale.mileage as sale_mileage',
            )
            ->where('motorbikes_sale.is_sold', 0)
            ->where('motorbikes.is_ebike', true)
            ->orderByDesc('motorbikes.created_at')
            ->get();

        return view('livewire.portal.finance.browse', compact(
            'motorbikes',
            'branches',
            'newForFinance',
            'usedForFinance',
            'ebikesForFinance',
        ))->layout('components.layouts.portal', ['title' => 'Finance enquiry | My Account']);
    }
}
