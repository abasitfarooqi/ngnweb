<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class MOTBooking extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_BOOKED = 'booked';

    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $fillable = [
        'branch_id',
        'title',
        'vehicle_registration',
        'vehicle_chassis',
        'vehicle_color',
        'date_of_appointment',
        'start',
        'end',
        'customer_name',
        'customer_contact',
        'customer_email',
        'payment_link',
        'is_paid',
        'status',
        'notes',
        'background_color',
        'text_color',
        'all_day',
        'is_validate',
        'payment_method',
        'payment_notes',
        'user_id',
    ];

    protected $table = 'mot_bookings';

    protected $casts = [
        'date_of_appointment' => 'datetime',
        'start' => 'datetime',
        'end' => 'datetime',
        'all_day' => 'boolean',
        'is_paid' => 'boolean',
        'is_validate' => 'boolean',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function getRouteKeyName()
    {
        return 'vehicle_registration';
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->save();
    }

    public function markAsCancelled(?string $note = null)
    {
        $this->status = 'cancelled';

        if (is_string($note) && trim($note) !== '') {
            $this->appendNotes(trim($note));
        }

        $this->save();
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value ?: $this->buildTitle();
    }

    public function getFormattedDateOfAppointmentAttribute()
    {
        return Carbon::parse($this->date_of_appointment)->format('d-m-Y');
    }

    protected static function booted()
    {
        static::saving(function ($booking) {

            $booking->vehicle_registration = strtoupper($booking->vehicle_registration);

            $needsVehicleLookup = $booking->isDirty('vehicle_registration') || ! $booking->exists;

            if ($needsVehicleLookup && ! Motorbike::isRegNoExists($booking->vehicle_registration)) {

                $booking->vehicle_registration = str_replace(' ', '', $booking->vehicle_registration);

                $response = Http::withHeaders([
                    'x-api-key' => '5i0qXnN6SY3blfoFeWvlu9sTQCSdrf548nMS8vVO',
                    'Content-Type' => 'application/json',
                ])->post('https://driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1/vehicles', [
                    'registrationNumber' => $booking->vehicle_registration,
                ]);

                if ($response->successful()) {
                    Log::info('IT SUCCEEDS');

                    $vehicleDetails = json_decode($response->body(), true);

                    $vehicleDetails->taxStatus ?? null;
                    if ($vehicleDetails['taxStatus'] != 'SORN') {
                        $vehicleDetails->taxDueDate ?? null;
                    }
                    $vehicleDetails->make ?? null;
                    $vehicleDetails->yearOfManufacture ?? null;
                    $vehicleDetails->engineCapacity ?? null;
                    $vehicleDetails->co2Emissions ?? null;
                    $vehicleDetails->fuelType ?? null;
                    $vehicleDetails->colour ?? null;
                    $vehicleDetails->typeApproval ?? null;
                    $vehicleDetails->dateOfLastV5CIssued ?? null;
                    $vehicleDetails->wheelplan ?? null;
                    $vehicleDetails->monthOfFirstRegistration ?? null;

                    if (! empty($vehicleDetails['monthOfFirstRegistration']) && strlen($vehicleDetails['monthOfFirstRegistration']) == 7) {
                        $vehicleDetails['monthOfFirstRegistration'] .= '-01';
                    }

                    $motorbike = new Motorbike([
                        'reg_no' => $booking->vehicle_registration,
                        'vin_number' => rand(0, 3).'0'.rand(1, 14).'0'.rand(5, 14).'0'.rand(7, 14).'0'.rand(9, 14),
                        'model' => $vehicleDetails['make'] ?? '',
                        'make' => $vehicleDetails['make'] ?? '',
                        'year' => $vehicleDetails['yearOfManufacture'] ?? '',
                        'engine' => $vehicleDetails['engineCapacity'] ?? '',
                        'reg_no' => $vehicleDetails['registrationNumber'] ?? '',
                        'co2_emissions' => $vehicleDetails['co2Emissions'] ?? '',
                        'fuel_type' => $vehicleDetails['fuelType'] ?? null,
                        'color' => $vehicleDetails['colour'] ?? '',
                        'type_approval' => $vehicleDetails['typeApproval'] ?? null,
                        'date_of_last_v5c_issued' => $vehicleDetails['dateOfLastV5CIssued'] ?? null,
                        'wheel_plan' => $vehicleDetails['wheelplan'] ?? null,
                        'mot_status' => $vehicleDetails['motStatus'] ?? null,
                        'month_of_first_registration' => $vehicleDetails['monthOfFirstRegistration'] ?? null,
                        'vehicle_profile_id' => 2,
                    ]);

                    $motorbike->save();

                    MotorbikeRegistration::create([
                        'motorbike_id' => $motorbike->id,
                        'registration_number' => $vehicleDetails['registrationNumber'],
                        'start_date' => Carbon::now(),
                        'end_date' => Carbon::now(),
                    ]);

                    MotorbikeAnnualCompliance::create([
                        'motorbike_id' => $motorbike->id,
                        'year' => date('Y'),
                        'mot_status' => $vehicleDetails['motStatus'] ?? '',
                        'road_tax_status' => $vehicleDetails['taxStatus'],
                        'insurance_status' => 'N/A',
                        'tax_due_date' => $vehicleDetails['taxDueDate'] ?? null,
                        'insurance_due_date' => Carbon::now(),
                        'mot_due_date' => $vehicleDetails['motExpiryDate'] ?? null,
                    ]);

                } else {

                    Log::info('IT NOT SUCCEEDS');

                    $errors = json_decode($response->body())->errors;

                    foreach ($errors as $error) {
                        Log::error('DVLA API Error: '.$error->detail);
                    }

                    $booking->is_validate = false;
                }
            }

            $user = $booking->user;
            $userName = $user ? trim($user->first_name.' '.$user->last_name) : null;
            $userName = $userName ?: $booking->user_id; // Use user_id if name is not available

            $booking->title = $booking->status.' MOT '.$booking->vehicle_registration.' '.$booking->customer_name.' '.$booking->customer_contact.' '.$booking->customer_email.' - By Staff Name: '.$userName;

            if ($booking->status !== self::STATUS_CANCELLED) {
                \App::make(\App\Http\Controllers\Admin\MOTBookingCrudController::class)
                    ->generateAgreementAccess($booking);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function motTimeSlots(): array
    {
        return [
            '09:00' => '09:00 AM',
            '09:30' => '09:30 AM',
            '10:00' => '10:00 AM',
            '10:30' => '10:30 AM',
            '11:00' => '11:00 AM',
            '11:30' => '11:30 AM',
            '12:00' => '12:00 PM',
            '12:30' => '12:30 PM',
            '13:00' => '01:00 PM',
            '13:30' => '01:30 PM',
            '14:00' => '02:00 PM',
            '14:30' => '02:30 PM',
            '15:00' => '03:00 PM',
            '15:30' => '03:30 PM',
            '16:00' => '04:00 PM',
            '16:30' => '04:30 PM',
        ];
    }

    public static function catfordBranchId(): ?int
    {
        return Branch::query()
            ->where('name', 'like', '%Catford%')
            ->orderBy('id')
            ->value('id');
    }

    public static function appointmentStart(string $date, string $time): Carbon
    {
        return Carbon::parse(trim($date.' '.$time));
    }

    public static function reservedTimeSlotsForDate(int $branchId, string $date, ?int $ignoreId = null): array
    {
        $query = static::query()
            ->where('branch_id', $branchId)
            ->whereDate('date_of_appointment', $date)
            ->where('status', '!=', self::STATUS_CANCELLED);

        if ($ignoreId !== null) {
            $query->whereKeyNot($ignoreId);
        }

        return $query
            ->get(['id', 'start'])
            ->mapWithKeys(function (self $booking): array {
                if (! $booking->start) {
                    return [];
                }

                return [Carbon::parse($booking->start)->format('H:i') => $booking->id];
            })
            ->all();
    }

    public static function availableTimeSlotsForDate(int $branchId, string $date, ?int $ignoreId = null): array
    {
        $reserved = array_keys(self::reservedTimeSlotsForDate($branchId, $date, $ignoreId));

        return array_filter(
            self::motTimeSlots(),
            fn (string $label, string $value): bool => ! in_array($value, $reserved, true),
            ARRAY_FILTER_USE_BOTH
        );
    }

    public function appendNotes(string $line): void
    {
        $line = trim($line);
        if ($line === '') {
            return;
        }

        $notes = trim((string) $this->notes);
        $this->notes = $notes === '' ? $line : $notes."\n".$line;
    }

    public function buildTitle(): string
    {
        $staffName = trim((string) (($this->user?->first_name ?? '').' '.($this->user?->last_name ?? '')));
        $source = $this->user_id
            ? 'By Staff Name: '.($staffName !== '' ? $staffName : $this->user_id)
            : 'By Website User';

        return trim(implode(' ', array_filter([
            $this->status ?: self::STATUS_PENDING,
            'MOT',
            strtoupper((string) $this->vehicle_registration),
            $this->customer_name,
            $this->customer_contact,
            $this->customer_email,
            $source,
        ])));
    }
}
