<?php

namespace App\Livewire\FluxAdmin\Pages\Support;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Customer;
use App\Models\SupportConversation;
use App\Support\FluxAdminAccess;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Start customer chat — Flux Admin')]
class ChatStart extends Component
{
    use WithAuthorization;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        if (! FluxAdminAccess::userHasPermission(FluxAdminAccess::user(), 'view-chat') && ! FluxAdminAccess::canManageCommunications()) {
            throw new AuthorizationException('You do not have permission to use customer chat.');
        }
    }

    public function startChat(int $customerAuthId): void
    {
        $customer = Customer::query()
            ->where('is_register', true)
            ->where('is_active', true)
            ->whereHas('customerAuth', fn ($query) => $query->whereKey($customerAuthId)->where('is_active', true))
            ->with('customerAuth')
            ->firstOrFail();

        $conversation = SupportConversation::query()->firstOrCreate(
            ['customer_auth_id' => $customer->customerAuth->id, 'service_booking_id' => null],
            ['title' => 'General chat', 'topic' => 'Staff chat', 'status' => 'open']
        );

        $this->redirect(route('flux-admin.support-inbox.index', ['c' => $conversation->id]), navigate: true);
    }

    public function render()
    {
        $term = trim($this->search);
        $customers = Customer::query()
            ->with('customerAuth')
            ->where('is_register', true)
            ->where('is_active', true)
            ->whereHas('customerAuth', fn ($query) => $query->where('is_active', true))
            ->when($term !== '', function ($query) use ($term): void {
                $query->where(function ($nested) use ($term): void {
                    $like = '%'.$term.'%';
                    $nested->where('first_name', 'like', $like)
                        ->orWhere('last_name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('phone', 'like', $like)
                        ->orWhere('whatsapp', 'like', $like)
                        ->orWhere('license_number', 'like', $like)
                        ->orWhere('username', 'like', $like)
                        ->orWhereHas('customerAuth', fn ($auth) => $auth->where('email', 'like', $like));
                });
            })
            ->orderBy('first_name')->orderBy('last_name')->limit(100)->get();

        return view('flux-admin.pages.support.chat-start', compact('customers', 'term'));
    }
}
