<?php

namespace App\Rules;

use App\Support\BookingSchedule;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BookableTimeSlot implements ValidationRule
{
    public function __construct(private ?string $date) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! filled($this->date) || ! filled($value)) {
            return;
        }

        if (! BookingSchedule::isSlotBookable($this->date, (string) $value)) {
            $fail('Choose a time at least '.BookingSchedule::leadMinutes().' minutes from now.');
        }
    }
}
