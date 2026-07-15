<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\AgreementAccess;
use App\Models\ContractAccess;
use App\Models\CustomerAuth;
use App\Models\UploadDocumentAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Native discovery of pending agreement / contract / document-upload links
 * for the authenticated customer, so the app can surface "documents to
 * review" without waiting on the SMS/email link. The app should open the
 * returned `url` in a secure in-app browser/WebView — signing itself still
 * happens on the existing web flow (signature capture, PDF generation).
 */
class MobileAgreementsController extends Controller
{
    public function pending(Request $request): JsonResponse
    {
        $actor = $this->customer($request);
        if (! $actor) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $profile = $actor->customer;
        if (! $profile) {
            return response()->json(['data' => []]);
        }

        $items = [];

        AgreementAccess::query()
            ->where('customer_id', $profile->id)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->latest('id')
            ->get()
            ->each(function (AgreementAccess $access) use (&$items) {
                $items[] = [
                    'type' => 'rental_agreement',
                    'title' => 'Rental agreement to sign',
                    'url' => AgreementAccess::customerSigningUrl((int) $access->customer_id, (string) $access->passcode),
                    'booking_id' => $access->booking_id,
                    'expires_at' => optional($access->expires_at)->toIso8601String(),
                ];
            });

        ContractAccess::query()
            ->where('customer_id', $profile->id)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->latest('id')
            ->get()
            ->each(function (ContractAccess $access) use (&$items) {
                $items[] = [
                    'type' => 'finance_agreement',
                    'title' => 'Finance agreement to sign',
                    'url' => route('finance.show', ['customer_id' => $access->customer_id, 'passcode' => $access->passcode]),
                    'application_id' => $access->application_id,
                    'expires_at' => optional($access->expires_at)->toIso8601String(),
                ];
            });

        UploadDocumentAccess::query()
            ->where('customer_id', $profile->id)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->latest('id')
            ->get()
            ->each(function (UploadDocumentAccess $access) use (&$items) {
                $items[] = [
                    'type' => 'document_upload',
                    'title' => 'Documents requested',
                    'url' => route('uploaddoc.showUploadDocPage.show', ['customer_id' => $access->customer_id, 'passcode' => $access->passcode]),
                    'booking_id' => $access->booking_id,
                    'expires_at' => optional($access->expires_at)->toIso8601String(),
                ];
            });

        return response()->json(['data' => $items]);
    }

    private function customer(Request $request): ?CustomerAuth
    {
        $actor = $request->user('customer') ?: $request->user('sanctum');

        return $actor instanceof CustomerAuth ? $actor : null;
    }
}
