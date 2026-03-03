<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Motorbike;
use App\Models\MotorbikeRegistration;
use App\Models\RentingBookingItem;
use App\Models\RentingPricing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EbikeManagerController extends Controller
{
    public function index(Request $request)
    {
        $q = Motorbike::query()
            ->where('is_ebike', true)
            ->with([
                'registrations' => fn ($x) => $x->orderByDesc('start_date'),
                'rentingPricings' => fn ($x) => $x->where('iscurrent', true)->orderByDesc('update_date'),
            ]);

        if ($request->filled('make')) {
            $q->where('make', 'like', '%'.$request->make.'%');
        }

        if ($request->filled('reg')) {
            $reg = str_replace(' ', '', $request->reg);
            $q->whereHas('registrations', function ($r) use ($reg) {
                $r->whereRaw("REPLACE(registration_number,' ','') like ?", ['%'.$reg.'%']);
            });
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $q->where(function ($w) use ($s) {
                $w->where('make', 'like', '%'.$s.'%')
                    ->orWhere('model', 'like', '%'.$s.'%')
                    ->orWhere('vin_number', 'like', '%'.$s.'%');
            });
        }

        $ebikes = $q->orderByDesc('id')->get();

        return view('olders.admin.ebike_manager', [
            'title' => 'Ebike Manager',
            'breadcrumbs' => [
                trans('backpack::crud.admin') => backpack_url('dashboard'),
                'Ebike Manager' => false,
            ],
            'ebikes' => $ebikes,
            'filters' => $request->only(['make', 'reg', 'search']),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'make' => ['required','string','max:255'],
            'model' => ['required','string','max:255'],
            'year' => ['required','integer','min:1900','max:'.(date('Y')+1)],
            'engine' => ['nullable','string','max:255'],
            'color' => ['required','string','max:255'],
            'vehicle_profile_id' => ['required','integer'],
            'vin_number' => ['required','string','max:255'],

            'registration_number' => [
                'required','string','max:255',
                function ($attribute, $value, $fail) {
                    $regNo = strtoupper(str_replace(' ', '', $value));

                    $existsInMotorbikes = Motorbike::whereRaw("REPLACE(UPPER(reg_no),' ','') = ?", [$regNo])->exists();
                    $existsInRegs = MotorbikeRegistration::whereRaw("REPLACE(UPPER(registration_number),' ','') = ?", [$regNo])->exists();

                    if ($existsInMotorbikes || $existsInRegs) {
                        $fail('This registration number already exists. Please use a unique registration.');
                    }
                }
            ],

            'weekly_price' => ['required','numeric','min:0'],
            'minimum_deposit' => ['required','numeric','min:0'],
        ], [
            'year.required' => 'Year is required.',
            'color.required' => 'Colour is required.',
            'vin_number.required' => 'VIN number is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        try {
            DB::transaction(function () use ($data) {
                $regNo = strtoupper(str_replace(' ', '', $data['registration_number']));

                $bike = Motorbike::create([
                    'vin_number' => $data['vin_number'],
                    'make' => $data['make'],
                    'model' => $data['model'],
                    'year' => $data['year'],
                    'engine' => $data['engine'] ?? 'Electric',
                    'color' => $data['color'],
                    'vehicle_profile_id' => $data['vehicle_profile_id'],
                    'is_ebike' => true,
                    'reg_no' => $regNo,
                ]);

                MotorbikeRegistration::create([
                    'motorbike_id' => $bike->id,
                    'registration_number' => $data['registration_number'],
                    'start_date' => now()->toDateString(),
                    'end_date' => null,
                ]);

                RentingPricing::where('motorbike_id', $bike->id)->update(['iscurrent' => false]);

                RentingPricing::create([
                    'motorbike_id' => $bike->id,
                    'user_id' => backpack_user()->id ?? null,
                    'iscurrent' => true,
                    'weekly_price' => $data['weekly_price'],
                    'minimum_deposit' => $data['minimum_deposit'],
                    'update_date' => now(),
                ]);
            });

            return redirect()->route('page.ebike_manager.index')
                ->with('success', 'E-bike created with registration + current pricing.');
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'E-bike creation failed. Please verify all required fields and try again. Note: Registration number and VIN must be unique.')
                ->withInput();
        }
    }



    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'make' => ['required','string','max:255'],
            'model' => ['required','string','max:255'],
            'year' => ['required','integer','min:1900','max:'.(date('Y')+1)],
            'engine' => ['nullable','string','max:255'],
            'color' => ['required','string','max:255'],
            'vehicle_profile_id' => ['required','integer'],
            'vin_number' => ['required','string','max:255'],

            'registration_number' => ['required','string','max:255'],
            'weekly_price' => ['required','numeric','min:0'],
            'minimum_deposit' => ['required','numeric','min:0'],
        ], [
            'year.required' => 'Year is required.',
            'color.required' => 'Colour is required.',
        ]);

        // ✅ Normalise reg for comparison
        $regNo = strtoupper(str_replace(' ', '', $data['registration_number']));

        // ✅ Block duplicate reg in motorbikes (except this bike)
        $existsInMotorbikes = Motorbike::whereRaw("REPLACE(UPPER(reg_no),' ','') = ?", [$regNo])
            ->where('id', '!=', $id)
            ->exists();

        // ✅ Block duplicate reg in motorbike_registrations (except this bike)
        $existsInRegs = MotorbikeRegistration::whereRaw("REPLACE(UPPER(registration_number),' ','') = ?", [$regNo])
            ->where('motorbike_id', '!=', $id)
            ->exists();

        if ($existsInMotorbikes || $existsInRegs) {
            return redirect()->back()
                ->withErrors(['registration_number' => 'This registration number already exists. Please use a unique registration.'])
                ->withInput();
        }

        DB::transaction(function () use ($data, $id, $regNo) {

            $bike = Motorbike::where('is_ebike', true)->findOrFail($id);

            $bike->update([
                'vin_number' => $data['vin_number'],
                'make' => $data['make'],
                'model' => $data['model'],
                'year' => $data['year'],
                'engine' => $data['engine'] ?? 'Electric',
                'color' => $data['color'],
                'vehicle_profile_id' => $data['vehicle_profile_id'],
                'is_ebike' => true,

                // keep synced
                'reg_no' => $regNo,
            ]);

            $reg = MotorbikeRegistration::where('motorbike_id', $bike->id)
                ->orderByDesc('start_date')
                ->first();

            if ($reg) {
                $reg->update(['registration_number' => $data['registration_number']]);
            } else {
                MotorbikeRegistration::create([
                    'motorbike_id' => $bike->id,
                    'registration_number' => $data['registration_number'],
                    'start_date' => now()->toDateString(),
                    'end_date' => null,
                ]);
            }

            RentingPricing::where('motorbike_id', $bike->id)->update(['iscurrent' => false]);

            RentingPricing::create([
                'motorbike_id' => $bike->id,
                'user_id' => backpack_user()->id ?? null,
                'iscurrent' => true,
                'weekly_price' => $data['weekly_price'],
                'minimum_deposit' => $data['minimum_deposit'],
                'update_date' => now(),
            ]);
        });

        return redirect()->route('page.ebike_manager.index')
            ->with('success', 'E-bike updated + new current pricing saved.');
    }



    public function destroy(int $id)
    {
        $bike = Motorbike::where('is_ebike', true)->findOrFail($id);

        $hasActiveRental = RentingBookingItem::query()
            ->where('motorbike_id', $bike->id)
            ->where('is_posted', true)
            ->whereNull('end_date')
            ->exists();

        if ($hasActiveRental) {
            return redirect()->route('page.ebike_manager.index')
                ->with('error', 'Cannot delete this e-bike because it is currently in an active rental.');
        }

        DB::transaction(function () use ($bike) {
            MotorbikeRegistration::where('motorbike_id', $bike->id)->delete();

            // If you want to keep pricing history, remove this line
            RentingPricing::where('motorbike_id', $bike->id)->delete();

            $bike->delete();
        });

        return redirect()->route('page.ebike_manager.index')->with('success', 'E-bike deleted.');
    }
}
