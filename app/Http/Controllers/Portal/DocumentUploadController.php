<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Support\PortalDocumentUpload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class DocumentUploadController extends Controller
{
    public function store(Request $request, PortalDocumentUpload $uploader): RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'max:10240'],
            'document_type_id' => ['required', 'integer', 'exists:document_types,id'],
            'valid_until' => ['nullable', 'date'],
            'tab' => ['nullable', 'in:rental,finance'],
            'booking_id' => ['nullable', 'integer'],
        ]);

        $customerAuth = Auth::guard('customer')->user();
        $profile = $customerAuth?->customer;

        if (! $profile) {
            return redirect()
                ->route('account.documents', $this->returnQuery($validated))
                ->with('error', 'Your account is not linked to a customer record yet.');
        }

        try {
            $result = $uploader->store(
                $profile,
                (int) $validated['document_type_id'],
                $request->file('file'),
                $validated['valid_until'] ?? null,
            );
        } catch (Throwable $e) {
            return redirect()
                ->route('account.documents', $this->returnQuery($validated))
                ->with('error', $e->getMessage());
        }

        $message = $result['synced_now']
            ? 'Document uploaded and synced to storage.'
            : 'Document uploaded successfully.';

        return redirect()
            ->route('account.documents', $this->returnQuery($validated))
            ->with('success', $message);
    }

    /** @param  array<string, mixed>  $validated */
    private function returnQuery(array $validated): array
    {
        $query = array_filter([
            'tab' => $validated['tab'] ?? null,
            'booking_id' => $validated['booking_id'] ?? null,
        ], fn ($value) => $value !== null && $value !== '');

        return $query === [] ? [] : $query;
    }
}
