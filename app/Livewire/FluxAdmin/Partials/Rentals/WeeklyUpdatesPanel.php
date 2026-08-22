<?php

namespace App\Livewire\FluxAdmin\Partials\Rentals;

use App\Mail\RentingInvoiceUpdateReminderMail;
use App\Models\BookingInvoice;
use App\Models\RentingBooking;
use App\Models\RentingWeeklyUpdate;
use App\Models\RentingWeeklyUpdateLog;
use App\Support\FluxAdminAccess;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class WeeklyUpdatesPanel extends Component
{
    public int $bookingId;

    public ?int $invoiceId = null;

    public string $newNote = '';

    public string $newNotedDate = '';

    public string $newNotedTime = '';

    /** @var list<array{id: int|null, note: string}> */
    public array $drafts = [];

    /** @var list<int> */
    public array $removedIds = [];

    public bool $isDirty = false;

    public ?string $flashMessage = null;

    public ?string $flashType = null;

    public function mount(int $bookingId, ?int $invoiceId = null): void
    {
        $this->bookingId = $bookingId;
        $this->invoiceId = $invoiceId;

        if (! $this->isInvoicePanel()) {
            $this->loadDrafts();
        }
    }

    #[On('weekly-updates-changed')]
    public function refreshFromPeer(): void
    {
        if ($this->isInvoicePanel() || $this->isDirty) {
            return;
        }

        $this->loadDrafts();
    }

    public function addDraft(): void
    {
        $this->drafts[] = ['id' => null, 'note' => '', 'invoice_id' => null, 'noted_date' => '', 'noted_time' => ''];
        $this->isDirty = true;
    }

    public function removeDraft(int $index): void
    {
        $row = $this->drafts[$index] ?? null;

        if (! is_array($row)) {
            return;
        }

        if (! empty($row['id'])) {
            $this->removedIds[] = (int) $row['id'];
        }

        array_splice($this->drafts, $index, 1);
        $this->isDirty = true;
    }

    public function saveDrafts(): void
    {
        $this->flashMessage = null;

        if ($this->drafts !== []) {
            $this->validate([
                'drafts.*.note' => ['required', 'string', 'min:1'],
                'drafts.*.noted_date' => ['nullable', 'date'],
                'drafts.*.noted_time' => ['nullable'],
            ], [
                'drafts.*.note.required' => 'Please enter a note.',
            ]);
        }

        $booking = RentingBooking::query()->whereKey($this->bookingId)->firstOrFail();

        foreach (array_unique($this->removedIds) as $id) {
            $this->ownedQuery()->whereKey($id)->first()?->delete();
        }

        $this->removedIds = [];
        $staffId = RentingWeeklyUpdate::staffId();

        foreach ($this->drafts as $row) {
            $note = trim((string) ($row['note'] ?? ''));
            $id = $row['id'] ?? null;

            $notedAt = $this->resolveNotedAt($row['noted_date'] ?? '', $row['noted_time'] ?? '');

            if ($id) {
                $update = $this->ownedQuery()->whereKey($id)->first();

                if ($update && ($update->note !== $note || $this->notedAtChanged($update, $notedAt))) {
                    $update->note = $note;
                    $update->created_at = $notedAt;
                    $update->save();
                }

                continue;
            }

            $update = new RentingWeeklyUpdate([
                'booking_id' => $booking->id,
                'invoice_id' => null,
                'note' => $note,
                'user_id' => $staffId,
            ]);
            $update->created_at = $notedAt;
            $update->save();
        }

        $this->loadDrafts();
        $this->flashMessage = 'Weekly rental updates saved.';
        $this->flashType = 'success';
        $this->dispatch('weekly-updates-changed');
    }

    public function addInvoiceUpdate(): void
    {
        $this->flashMessage = null;

        $this->validate([
            'newNote' => ['required', 'string', 'min:1'],
            'newNotedDate' => ['nullable', 'date'],
            'newNotedTime' => ['nullable'],
        ], [
            'newNote.required' => 'Please enter a note.',
        ]);

        $invoice = BookingInvoice::query()
            ->where('booking_id', $this->bookingId)
            ->whereKey($this->invoiceId)
            ->firstOrFail();

        $update = new RentingWeeklyUpdate([
            'booking_id' => $invoice->booking_id,
            'invoice_id' => $invoice->id,
            'note' => trim($this->newNote),
            'user_id' => RentingWeeklyUpdate::staffId(),
        ]);
        $update->created_at = $this->resolveNotedAt($this->newNotedDate, $this->newNotedTime);
        $update->save();

        $emailed = $this->emailInvoiceUpdate($update, $invoice);

        $this->newNote = '';
        $this->newNotedDate = '';
        $this->newNotedTime = '';
        $this->flashMessage = $emailed
            ? 'Update added. The customer has been emailed, and customer service was copied.'
            : 'Update added. No customer email is on file, so nothing was sent.';
        $this->flashType = 'success';
        $this->dispatch('weekly-updates-changed');
    }

    private function emailInvoiceUpdate(RentingWeeklyUpdate $update, BookingInvoice $invoice): bool
    {
        $booking = RentingBooking::query()
            ->with(['customer', 'rentingBookingItems.motorbike:id,reg_no'])
            ->find($invoice->booking_id);
        $customer = $booking?->customer;
        $email = trim((string) ($customer?->email ?? ''));

        if (! $booking || ! $customer || $email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        Mail::to($email)->send(new RentingInvoiceUpdateReminderMail($update, $booking, $invoice, $customer));

        return true;
    }

    public function removeUpdate(int $id): void
    {
        $this->ownedQuery()->whereKey($id)->first()?->delete();

        if (! $this->isInvoicePanel()) {
            $this->loadDrafts();
        }

        $this->flashMessage = 'Update removed.';
        $this->flashType = 'success';
        $this->dispatch('weekly-updates-changed');
    }

    public function render()
    {
        $updates = $this->ownedQuery()
            ->with(['user:id,first_name,last_name', 'invoice:id,invoice_date'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return view('flux-admin.partials.rentals.weekly-updates-panel', [
            'updates' => $updates,
            'auditLogs' => $this->auditLogs(),
            'isSuperAdmin' => FluxAdminAccess::isSuperAdmin(),
        ]);
    }

    protected function isInvoicePanel(): bool
    {
        return $this->invoiceId !== null;
    }

    protected function ownedQuery()
    {
        $query = RentingWeeklyUpdate::query()->where('booking_id', $this->bookingId);

        if ($this->isInvoicePanel()) {
            $query->where('invoice_id', $this->invoiceId);
        }

        return $query;
    }

    public function loadDrafts(): void
    {
        $this->drafts = $this->ownedQuery()
            ->orderBy('created_at')
            ->orderBy('id')
            ->get(['id', 'note', 'invoice_id', 'created_at'])
            ->map(fn (RentingWeeklyUpdate $update) => [
                'id' => $update->id,
                'note' => $update->note,
                'invoice_id' => $update->invoice_id,
                'noted_date' => $update->created_at?->format('Y-m-d') ?? '',
                'noted_time' => $update->created_at?->format('H:i') ?? '',
            ])
            ->values()
            ->all();

        $this->removedIds = [];
        $this->isDirty = false;
    }

    protected function resolveNotedAt(mixed $date, mixed $time): Carbon
    {
        $date = trim((string) $date);
        $time = trim((string) $time);
        $now = now();

        if ($date === '' && $time === '') {
            return $now;
        }

        if ($date === '') {
            $date = $now->toDateString();
        }

        if ($time === '') {
            $time = $now->format('H:i');
        }

        try {
            return Carbon::parse($date.' '.$time);
        } catch (\Throwable) {
            return $now;
        }
    }

    protected function notedAtChanged(RentingWeeklyUpdate $update, Carbon $notedAt): bool
    {
        return $update->created_at?->format('Y-m-d H:i') !== $notedAt->format('Y-m-d H:i');
    }

    protected function auditLogs(): Collection
    {
        if ($this->isInvoicePanel() || ! FluxAdminAccess::isSuperAdmin()) {
            return collect();
        }

        return RentingWeeklyUpdateLog::query()
            ->with('changer:id,first_name,last_name')
            ->where(function ($query) {
                $bookingId = (string) $this->bookingId;
                $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(old_data, '$.booking_id')) = ?", [$bookingId])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(new_data, '$.booking_id')) = ?", [$bookingId]);
            })
            ->orderByDesc('id')
            ->limit(50)
            ->get();
    }
}
