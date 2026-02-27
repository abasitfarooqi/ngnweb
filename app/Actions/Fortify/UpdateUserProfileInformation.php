<?php

namespace App\Actions\Fortify;

use App\Models\CustomerAuth;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    public function update(mixed $user, array $input): void
    {
        if (! $user instanceof CustomerAuth || ! $user->customer) {
            return;
        }

        $name = $input['name'] ?? '';
        $parts = preg_split('/\s+/', trim($name), 2);
        $firstName = $parts[0] ?? $user->customer->first_name;
        $lastName = $parts[1] ?? $user->customer->last_name;

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('customer_auths')->ignore($user->id),
            ],
        ])->validateWithBag('updateProfileInformation');

        $user->customer->forceFill([
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'email'      => $input['email'],
        ])->save();

        if ($input['email'] !== $user->email && $user instanceof MustVerifyEmail) {
            $user->forceFill(['email_verified_at' => null])->save();
            $user->sendEmailVerificationNotification();
        } else {
            $user->forceFill(['email' => $input['email']])->save();
        }
    }
}
