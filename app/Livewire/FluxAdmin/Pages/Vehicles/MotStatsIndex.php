<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\Customer;
use App\Models\NgnMotNotifier;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('MOT notifier stats — Flux Admin')]
class MotStatsIndex extends Component
{
    use WithAuthorization, WithDataTable, WithPagination;

    public function mount(): void { $this->authorizeModule('see-menu-commons'); }

    public function markWhatsappSent(int $id): void
    {
        NgnMotNotifier::where('id', $id)->update([
            'mot_is_whatsapp_sent' => true,
            'mot_last_whatsapp_notification_date' => now(),
        ]);
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Marked sent.');
    }

    public function render()
    {
        $query = NgnMotNotifier::query()
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('customer_name', 'like', "%{$v}%")->orWhere('customer_contact', 'like', "%{$v}%")->orWhere('motorbike_reg', 'like', "%{$v}%")))
            ->when($this->filter('whatsapp') !== null && $this->filter('whatsapp') !== '', fn ($q) => $q->where('mot_is_whatsapp_sent', (bool) $this->filter('whatsapp')))
            ->when($this->filter('status') === 'expired', fn ($q) => $q->whereDate('mot_due_date', '<', now()))
            ->when($this->filter('status') === 'valid', fn ($q) => $q->whereDate('mot_due_date', '>=', now()));

        $stats = [
            'total' => (clone $query)->count(),
            'expired' => (clone $query)->whereDate('mot_due_date', '<', now())->count(),
            'upcoming_30' => (clone $query)->whereBetween('mot_due_date', [now(), now()->addDays(30)])->count(),
            'whatsapp_sent' => (clone $query)->where('mot_is_whatsapp_sent', true)->count(),
        ];

        $notifiers = $query->latest()->paginate($this->perPage);

        $rows = $notifiers->through(function ($n) {
            $customer = Customer::where('email', $n->customer_email)->first();
            $phone = $customer && $customer->whatsapp ? $customer->whatsapp : $n->customer_contact;
            $phone = preg_replace('/\s+|^0/', '', (string) $phone);
            $phone = preg_replace('/^(\+44)+/', '', $phone);
            $phone = preg_replace('/^44/', '', $phone);
            $phone = '+44'.$phone;

            $motDue = $n->mot_due_date ? Carbon::parse($n->mot_due_date)->format('d/m/Y') : 'N/A';
            $message = "Dear {$n->customer_name}, this is a reminder that your MOT for vehicle {$n->motorbike_reg} is due on {$motDue}. Please ensure your MOT is up to date to avoid penalties. Call 0208 314 1498 or WhatsApp 07951790568, NGN Motors.";

            $n->whatsapp_url = "https://wa.me/{$phone}?text=".rawurlencode($message);
            $n->mot_status = $n->mot_due_date && Carbon::parse($n->mot_due_date)->isPast() ? 'Expired' : 'Valid';

            return $n;
        });

        return view('flux-admin.pages.vehicles.mot-stats-index', compact('rows', 'stats'));
    }
}
