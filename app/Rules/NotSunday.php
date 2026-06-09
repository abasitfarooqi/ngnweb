<?php

namespace App\Rules;

use App\Support\BookingSchedule;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NotSunday implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (BookingSchedule::isSunday($value)) {
            $fail('We are closed on Sundays. Please choose another day.');
        }
    }
}
