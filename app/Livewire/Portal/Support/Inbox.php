<?php

namespace App\Livewire\Portal\Support;

use App\Models\ServiceBooking;
use App\Models\SupportConversation;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Inbox extends Component
{
    public string $statusFilter = 'all';

    public string $typeFilter = 'all';

    public string $search = '';

    protected array $queryString = [
        'statusFilter' => ['except' => 'all'],
        'typeFilter' => ['except' => 'all'],
        'search' => ['except' => ''],
    ];

    public function startGeneralChat(): void
    {
        $customerAuth = Auth::guard('customer')->user();
        if (! $customerAuth) {
            abort(403);
        }

        $conversation = SupportConversation::query()->create([
            'customer_auth_id' => $customerAuth->id,
            'title' => 'General enquiry',
            'topic' => 'General enquiry',
            'status' => 'open',
        ]);

        $this->redirectRoute('account.support.thread', ['conversationUuid' => $conversation->uuid], navigate: true);
    }

    public function startFromEnquiry(int $serviceBookingId): void
    {
        $customerAuth = Auth::guard('customer')->user();
        if (! $customerAuth) {
            abort(403);
        }

        $booking = ServiceBooking::query()
            ->forPortalCustomer($customerAuth)
            ->findOrFail($serviceBookingId);

        $conversation = SupportConversation::query()->firstOrCreate(
            [
                'service_booking_id' => $booking->id,
            ],
            [
                'customer_auth_id' => $customerAuth->id,
                'title' => $booking->service_type ?: 'Service enquiry',
                'topic' => $booking->subject ?: $booking->service_type ?: 'Service enquiry',
                'status' => 'open',
            ]
        );

        if (! $booking->conversation_id) {
            $booking->forceFill(['conversation_id' => $conversation->id])->save();
        }

        $this->redirectRoute('account.support.thread', ['conversationUuid' => $conversation->uuid], navigate: true);
    }

    public function render()
    {
        $customerAuth = Auth::guard('customer')->user();
        if (! $customerAuth) {
            abort(403);
        }

        $conversations = SupportConversation::query()
            ->where('customer_auth_id', $customerAuth->id)
            ->with(['serviceBooking', 'assignedBackpackUser'])
            ->when($this->typeFilter === 'general', fn ($q) => $q->whereNull('service_booking_id'))
            ->when($this->typeFilter === 'enquiry', fn ($q) => $q->whereNotNull('service_booking_id'))
            ->when($this->statusFilter !== 'all', function ($q) {
                if ($this->statusFilter === 'waiting_for_staff') {
                    $q->whereIn('status', ['waiting_for_staff', 'awaiting_staff']);

                    return;
                }

                $q->where('status', $this->statusFilter);
            })
            ->when($this->search !== '', function ($q) {
                $term = trim($this->search);
                $q->where(function ($nested) use ($term): void {
                    $nested
                        ->where('title', 'like', '%'.$term.'%')
                        ->orWhere('topic', 'like', '%'.$term.'%')
                        ->orWhere('uuid', 'like', '%'.$term.'%')
                        ->orWhereHas('serviceBooking', function ($bookingQuery) use ($term): void {
                            $bookingQuery
                                ->where('service_type', 'like', '%'.$term.'%')
                                ->orWhere('subject', 'like', '%'.$term.'%');
                        });
                });
            })
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->get();

        $recentEnquiries = ServiceBooking::query()
            ->forPortalCustomer($customerAuth)
            ->latest()
            ->limit(8)
            ->get();

        return view('livewire.portal.support.inbox', compact('conversations', 'recentEnquiries'))
            ->layout('components.layouts.portal', [
                'title' => 'Conversations | My Account',
            ]);
    }
}
