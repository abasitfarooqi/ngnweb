<?php

namespace App\Http\Controllers\FluxAdmin;

use App\Http\Controllers\Controller;
use App\Models\RentingBooking;
use App\Models\RentingServiceVideo;
use App\Support\UploadLimit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RentalServiceVideoUploadController extends Controller
{
    public function store(Request $request, RentingBooking $booking): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'video' => ['required', 'file', 'mimes:mp4,mov,avi,wmv,mkv', 'max:'.UploadLimit::maxKilobytes()],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('flux-admin.rentals.show', ['booking' => $booking->id, 'activeTab' => 'issuance'])
                ->withErrors($validator)
                ->with('error', $validator->errors()->first('video'));
        }

        try {
            $file = $request->file('video');
            $timestamp = now()->format('Ymd_His');
            $extension = $file->getClientOriginalExtension();
            $fileName = $booking->id.'_'.$timestamp.'.'.$extension;
            $storePath = $file->storeAs('rental_service_videos', $fileName, 'public');

            RentingServiceVideo::create([
                'booking_id' => $booking->id,
                'video_path' => $storePath,
                'recorded_at' => now(),
            ]);
        } catch (\Throwable $e) {
            return redirect()
                ->route('flux-admin.rentals.show', ['booking' => $booking->id, 'activeTab' => 'issuance'])
                ->withErrors(['video' => 'Video upload failed: '.$e->getMessage()])
                ->with('error', 'Video upload failed: '.$e->getMessage());
        }

        return redirect()
            ->route('flux-admin.rentals.show', ['booking' => $booking->id, 'activeTab' => 'issuance'])
            ->with('status', 'Service video uploaded.');
    }
}
