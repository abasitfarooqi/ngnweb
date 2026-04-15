<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NgnClubController;
use Illuminate\Http\Request;

class MobileClubLegacyController extends Controller
{
    public function __construct(private readonly NgnClubController $legacyClubController) {}

    public function storePurchase(Request $request)
    {
        return $this->legacyClubController->store($request);
    }

    public function storePurchaseMb(Request $request)
    {
        return $this->legacyClubController->storeMB($request);
    }

    public function storeSpending(Request $request)
    {
        return $this->legacyClubController->storeSpending($request);
    }

    public function listSpending(Request $request)
    {
        return $this->legacyClubController->listCustomerSpending($request);
    }

    public function deleteSpending(Request $request)
    {
        return $this->legacyClubController->deleteCustomerSpending($request);
    }

    public function recordSpendingPayment(Request $request)
    {
        return $this->legacyClubController->recordSpendingPayment($request);
    }

    public function spendingPaymentHistory(Request $request)
    {
        return $this->legacyClubController->getSpendingPaymentHistory($request);
    }

    public function creditStatus(Request $request)
    {
        return $this->legacyClubController->creditLookup($request);
    }

    public function creditStatusWithTime(Request $request)
    {
        return $this->legacyClubController->creditLookupGetTime($request);
    }

    public function initiateRedeem(Request $request)
    {
        return $this->legacyClubController->initiateRedeem($request);
    }

    public function verifyOtpAndRedeem(Request $request)
    {
        return $this->legacyClubController->verifyOtpAndRedeem($request);
    }

    public function updateRedeemInvoice(Request $request)
    {
        return $this->legacyClubController->updateRedeemInvoice($request);
    }

    public function referralStatus(Request $request)
    {
        return $this->legacyClubController->referralStatus($request);
    }
}
