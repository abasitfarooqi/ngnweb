<?php

namespace App\Http\Controllers\FluxAdmin;

use App\Http\Controllers\Controller;
use App\Models\RentingBooking;
use App\Models\RentingServiceVideo;
use App\Support\UploadLimit;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidatorContract;

class RentalServiceVideoUploadController extends Controller
{
    public function store(Request $request, RentingBooking $booking): RedirectResponse
    {
        $validator = Validator::make($request->all(), $this->videoRules());

        if ($validator->fails()) {
            return $this->backToIssuance($booking, $validator);
        }

        try {
            $this->storeVideo($request, $booking, now());
        } catch (\Throwable $e) {
            return $this->backToIssuance($booking, message: 'Video upload failed: '.$e->getMessage());
        }

        return redirect()
            ->route('flux-admin.rentals.show', ['booking' => $booking->id, 'activeTab' => 'issuance'])
            ->with('status', 'Service video uploaded.');
    }

    public function storeFromForm(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), array_merge([
            'booking_id' => ['required', 'integer', 'exists:renting_bookings,id'],
            'recorded_at' => ['required', 'date'],
        ], $this->videoRules()));

        if ($validator->fails()) {
            return $this->backToCreate($request, $validator);
        }

        $booking = RentingBooking::query()->findOrFail((int) $request->input('booking_id'));

        try {
            $this->storeVideo($request, $booking, Carbon::parse((string) $request->input('recorded_at')));
        } catch (\Throwable $e) {
            return $this->backToCreate($request, message: 'Video upload failed: '.$e->getMessage());
        }

        return redirect()
            ->route('flux-admin.service-videos.index')
            ->with('status', 'Service video uploaded.');
    }

    /** @return array<string, array<int, string>> */
    private function videoRules(): array
    {
        return [
            'video' => ['required', 'file', 'mimes:mp4,mov,avi,wmv,mkv', 'max:'.UploadLimit::maxKilobytes()],
        ];
    }

    private function storeVideo(Request $request, RentingBooking $booking, Carbon $recordedAt): void
    {
        $file = $request->file('video');
        $timestamp = now()->format('Ymd_His');
        $extension = $file->getClientOriginalExtension();
        $fileName = $booking->id.'_'.$timestamp.'.'.$extension;
        $storePath = $file->storeAs('rental_service_videos', $fileName, 'public');

        RentingServiceVideo::create([
            'booking_id' => $booking->id,
            'video_path' => $storePath,
            'recorded_at' => $recordedAt,
        ]);
    }

    private function backToIssuance(RentingBooking $booking, ?ValidatorContract $validator = null, ?string $message = null): RedirectResponse
    {
        $message ??= $validator?->errors()->first('video') ?: 'Video upload failed.';

        $redirect = redirect()
            ->route('flux-admin.rentals.show', ['booking' => $booking->id, 'activeTab' => 'issuance'])
            ->with('error', $message);

        if ($validator) {
            $redirect->withErrors($validator);
        } else {
            $redirect->withErrors(['video' => $message]);
        }

        return $redirect;
    }

    private function backToCreate(Request $request, ?ValidatorContract $validator = null, ?string $message = null): RedirectResponse
    {
        $message ??= $validator?->errors()->first() ?: 'Video upload failed.';

        $redirect = redirect()
            ->route('flux-admin.service-videos.create', array_filter([
                'booking_id' => $request->input('booking_id'),
            ]))
            ->withInput()
            ->with('error', $message);

        if ($validator) {
            $redirect->withErrors($validator);
        } else {
            $redirect->withErrors(['video' => $message]);
        }

        return $redirect;
    }
}
