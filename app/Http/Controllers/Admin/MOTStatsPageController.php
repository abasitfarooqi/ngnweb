<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use App\Models\NgnMotNotifier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Class MOTStatsPageController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MOTStatsPageController extends Controller
{
    public function index(Request $request)
{
    $query = NgnMotNotifier::query();

    // 🔍 WhatsApp Sent Filter
    if ($request->filled('whatsapp_filter')) {
        $query->where('mot_is_whatsapp_sent', $request->whatsapp_filter);
    }

    // ⚙️ MOT Status Filter
    if ($request->filled('mot_status_filter')) {
        if ($request->mot_status_filter === 'expired') {
            $query->whereDate('mot_due_date', '<', now());
        } elseif ($request->mot_status_filter === 'valid') {
            $query->whereDate('mot_due_date', '>=', now());
        }
    }

    // 🔎 Search (Name / Phone / Reg)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('customer_name', 'like', "%{$search}%")
              ->orWhere('customer_contact', 'like', "%{$search}%")
              ->orWhere('motorbike_reg', 'like', "%{$search}%");
        });
    }

    $notifiers = $query->latest()->get();

    $motList = $notifiers->map(function ($notifier) {
        $customer = Customer::where('email', $notifier->customer_email)->first();
        $customerName = $notifier->customer_name;
        $regNo = $notifier->motorbike_reg;
        $motDueDate = $notifier->mot_due_date ? Carbon::parse($notifier->mot_due_date)->format('d/m/Y') : 'N/A';
        $taxDueDate = $notifier->tax_due_date ? Carbon::parse($notifier->tax_due_date)->format('d/m/Y') : 'N/A';
        $insuranceDueDate = $notifier->insurance_due_date ? Carbon::parse($notifier->insurance_due_date)->format('d/m/Y') : 'N/A';
        $motStatus = Carbon::parse($notifier->mot_due_date)->isPast() ? 'Expired' : 'Valid';
        $phoneNumber = $customer && $customer->whatsapp ? $customer->whatsapp : $notifier->customer_contact;

        // Normalise to +44 format
        $phoneNumber = preg_replace('/\s+|^0/', '', $phoneNumber);
        $phoneNumber = preg_replace('/^(\\+44)+/', '', $phoneNumber);
        $phoneNumber = preg_replace('/^44/', '', $phoneNumber);
        $phoneNumber = '+44'.$phoneNumber;

        $message = "Dear {$customerName}, this is a reminder that your MOT for vehicle {$regNo} is due on {$motDueDate}. Please ensure your MOT is up to date to avoid penalties. If you have already renewed, please contact us at 0208 314 1498 or WhatsApp us on 07951790568, NGN Motors.";
        $url = "https://wa.me/{$phoneNumber}?text=".urlencode($message);

        return [
            'customer_name' => $customerName,
            'reg_no' => $regNo,
            'mot_due_date' => $motDueDate,
            'tax_due_date' => $taxDueDate,
            'insurance_due_date' => $insuranceDueDate,
            'mot_status' => $motStatus,
            'is_whatsapp_sent' => $notifier->mot_is_whatsapp_sent,
            'mot_last_whatsapp_notification_date' => $notifier->mot_last_whatsapp_notification_date ? Carbon::parse($notifier->mot_last_whatsapp_notification_date)->format('d/m/Y') : 'N/A',
            'whatsapp_url' => $url,
            'id' => $notifier->id,
            'customer_email' => $notifier->customer_email,
            'customer_contact' => $notifier->customer_contact,
        ];
    });

    return view('admin.mot_stats_page', [
        'title' => 'M O T Stats Page',
        'motList' => $motList,
    ]);
}


    public function sendReminder($id)
    {
        $notifier = NgnMotNotifier::find($id);
        $notifier->mot_is_whatsapp_sent = true;
        $notifier->mot_last_whatsapp_notification_date = Carbon::now();
        $notifier->save();

        return redirect()->back()->with('success', 'WhatsApp reminder sent successfully.');
    }

    public function ajaxUpdate(Request $request, $id)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'motorbike_reg' => 'required|string|max:50',
            'customer_contact' => 'nullable|string|max:50',
            'customer_email' => 'nullable|email|max:255',
        ]);

        $notifier = NgnMotNotifier::findOrFail($id);
        $notifier->customer_name = $request->customer_name;
        $notifier->motorbike_reg = $request->motorbike_reg;
        $notifier->customer_contact = $request->customer_contact;
        $notifier->customer_email = $request->customer_email;
        $notifier->save();

        return response()->json(['success' => true, 'message' => 'Record updated successfully']);
    }
}
