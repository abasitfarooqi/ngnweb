<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\MotorbikeAnnualCompliance;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Vehicle database — Flux Admin')]
class ComplianceIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithExport;
    use WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-services-and-repairs-and-report');
        $this->exportable = true;
        $this->exportFilename = 'vehicle-compliance';
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->with('motorbike:id,reg_no,make,model,year,engine,color')
            ->orderBy('motorbike_annual_compliance.id', 'desc')
            ->paginate($this->perPage);

        return view('flux-admin.pages.motorbikes.compliance-index', ['rows' => $rows]);
    }

    protected function baseQuery(): Builder
    {
        return MotorbikeAnnualCompliance::query()
            ->leftJoin('application_items', 'motorbike_annual_compliance.motorbike_id', '=', 'application_items.motorbike_id')
            ->leftJoin('finance_applications', 'application_items.application_id', '=', 'finance_applications.id')
            ->leftJoin('customers as app_customers', 'finance_applications.customer_id', '=', 'app_customers.id')
            ->leftJoin('renting_booking_items', 'motorbike_annual_compliance.motorbike_id', '=', 'renting_booking_items.motorbike_id')
            ->leftJoin('renting_bookings', 'renting_booking_items.booking_id', '=', 'renting_bookings.id')
            ->leftJoin('customers as rent_customers', 'renting_bookings.customer_id', '=', 'rent_customers.id')
            ->leftJoin('motorbikes_sale', 'motorbike_annual_compliance.motorbike_id', '=', 'motorbikes_sale.motorbike_id')
            ->leftJoin('company_vehicles', 'motorbike_annual_compliance.motorbike_id', '=', 'company_vehicles.motorbike_id')
            ->select('motorbike_annual_compliance.*', 'motorbikes_sale.is_sold', DB::raw('
                CASE
                    WHEN company_vehicles.motorbike_id IS NOT NULL THEN "COMPANY VEHICLE"
                    WHEN application_items.motorbike_id IS NOT NULL AND finance_applications.log_book_sent = true THEN CONCAT("INSTALLMENT TRANSFERRED: ", app_customers.first_name, " ", app_customers.last_name)
                    WHEN application_items.motorbike_id IS NOT NULL THEN CONCAT("INSTALLMENT: ", app_customers.first_name, " ", app_customers.last_name)
                    WHEN renting_booking_items.motorbike_id IS NOT NULL THEN CONCAT("RENTAL: ", rent_customers.first_name, " ", rent_customers.last_name)
                    WHEN motorbikes_sale.motorbike_id IS NOT NULL AND motorbikes_sale.is_sold = true THEN "SOLD"
                    WHEN motorbikes_sale.motorbike_id IS NOT NULL THEN "SALE"
                    ELSE "Unassociated"
                END as association_status'))
            ->when($this->search, function ($q): void {
                $term = $this->search;
                $q->whereHas('motorbike', fn ($q) => $q->where('reg_no', 'like', "%{$term}%")->orWhere('make', 'like', "%{$term}%")->orWhere('model', 'like', "%{$term}%"));
            })
            ->when($this->filter('road_tax_status'), fn ($q, $v) => $q->where('road_tax_status', $v))
            ->when($this->filter('mot_status'), fn ($q, $v) => $q->where('mot_status', $v));
    }

    protected function exportQuery(): Builder { return $this->baseQuery()->with('motorbike:id,reg_no,make,model,year,engine'); }

    protected function exportColumns(): array
    {
        return [
            'Registration' => fn ($r) => $r->motorbike?->reg_no,
            'Make' => fn ($r) => $r->motorbike?->make,
            'Model' => fn ($r) => $r->motorbike?->model,
            'Year' => fn ($r) => $r->motorbike?->year,
            'Engine' => fn ($r) => $r->motorbike?->engine,
            'Road tax' => 'road_tax_status',
            'Tax due' => fn ($r) => $r->tax_due_date ? \Carbon\Carbon::parse($r->tax_due_date)->format('Y-m-d') : '',
            'MOT' => 'mot_status',
            'MOT due' => fn ($r) => $r->mot_due_date ? \Carbon\Carbon::parse($r->mot_due_date)->format('Y-m-d') : '',
            'Status' => 'association_status',
        ];
    }
}
