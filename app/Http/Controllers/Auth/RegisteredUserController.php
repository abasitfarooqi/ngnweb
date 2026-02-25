<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return redirect('/accountinformation/register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            // 'first_name' => ['required', 'string', 'max:255'],
            // 'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'is_client' => $request->is_client,
            'role_id' => $request->role_id,
            'rating' => $request->rating,
            'username' => $request->email,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Create the corresponding customer record
        Customer::create([
            'first_name' => '',
            'last_name' => '',
            'email' => $request->email, // Optional: if you want to store email in customers table
            'phone' => '', // Add any other fields you want to store
            'dob' => '1970-01-01', // Set to NULL or use '1970-01-01' as a placeholder
            'address' => '',
            'postcode' => '',
            'city' => '',
            'country' => '',
            // Add other customer fields as necessary
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Redirect to the dashboard instead of the default route
        return redirect()->intended('account'); // Assuming 'account' is the route to the dashboard
    }
}
