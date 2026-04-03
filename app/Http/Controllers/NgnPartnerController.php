<?php

namespace App\Http\Controllers;

use App\Mail\NgnPartnerRegistrationMailer;
use App\Models\NgnPartner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NgnPartnerController extends Controller
{
    public function showSubscribePage()
    {
        return view('livewire.agreements.migrated.frontend.ngnpartner.subscribe');
    }

    public function subscribe(Request $request)
    {



        $request->validate([
            'companyname' => 'required|string|max:50|unique:ngn_partners,companyname',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_address' => 'nullable|string|max:255',
            'company_number' => 'nullable|string|max:255',
            'first_name' => 'nullable|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'website' => 'nullable|string|max:255',
            'fleet_size' => 'nullable|integer|min:0',
            'operating_since' => 'nullable|string|max:8',
            'tc_agreed' => 'required|accepted',
        ]);

        try {
            $data = $request->except(['company_logo', 'tc_agreed']);
            $data['is_approved'] = false;
            $data['mobile'] = $request->mobile;

            if ($request->hasFile('company_logo')) {
                $file = $request->file('company_logo');
                $filename = 'partner-'.Str::slug($request->companyname).'-'.time().'.'.$file->getClientOriginalExtension();
                $path = $file->storeAs('public/partner-logos', $filename);
                $data['company_logo'] = Storage::url($path);
            }

            try {
                $partner = NgnPartner::create($data);
                \Log::info('Partner created successfully: '.$partner->id);

                // Send email notification
                Mail::to('support@neguinhomotors.co.uk')->send(new NgnPartnerRegistrationMailer($data));
                \Log::info('Email sent successfully to support@neguinhomotors.co.uk');

            } catch (\Exception $e) {
                Log::error('Error creating partner: '.$e->getMessage());

                return back()->with('error', 'There was an error processing your registration. Please try again.');
            }

            return redirect()
                ->route('ngnpartner.thankyou')
                ->with('success', 'Thank you for registering! We will review your application and contact you soon.');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'There was an error processing your registration. Please try again.')
                ->withInput();
        }
    }

    public function showThankYouPage()
    {
        return view('livewire.agreements.migrated.frontend.ngnpartner.thankyou');
    }

    public function showTermsPage()
    {
        return view('livewire.agreements.migrated.frontend.ngnpartner.terms');
    }
}
