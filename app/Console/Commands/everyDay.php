<?php

namespace App\Console\Commands;

use App\Mail\RentalDue;
use App\Models\Motorcycle;
use App\Models\RentalPayment;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class everyDay extends Command
{
    protected $signature = 'daily:updates';

    protected $description = 'Daily updates including payment data';

    public function handle(): void
    {
        info('Cron Job running at '.now());

        $today = Carbon::now('Europe/London');
        $tomorrow = $today->addDay();
        $nextPayDate = Carbon::now()->addDays(7);
        $motorcycles = Motorcycle::where('next_payment_date', '=', $tomorrow->toDateString())->get();

        foreach ($motorcycles as $key => $motorcycle) {
            return;
            // Set next payment date
            Motorcycle::findOrFail($motorcycle->id)->update([
                'next_payment_date' => Carbon::now()->addDays(8),
            ]);

            // Send renter email reminder for next day payment
            $user = User::where('id', $motorcycle->user_id)->first();
            Mail::to($user->email)->send(new RentalDue($user));

            $rentalPrice = $motorcycle->rental_price;

            $payment = new RentalPayment;
            $payment->payment_type = 'rental';
            $payment->payment_due_date = $nextPayDate;
            $payment->payment_date = null;
            $payment->rental_price = $rentalPrice;
            $payment->registration = $motorcycle->registration;
            $payment->received = 0.00;
            $payment->outstanding = $rentalPrice;
            $payment->user_id = $motorcycle->user_id;
            $payment->created_at = $today;
            $payment->motorcycle_id = $motorcycle->id;
            $payment->save();

            // Apply late payment fees if applicaable
            // Day 1 £10
            // Day 2 £30
            // Day 3 £50
            // Day 4 £70
            // Day 5 £90
            // Day 6 £110
            // Day 7 130
        }
    }
}
