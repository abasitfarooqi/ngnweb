<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\MoveCustomerDocumentToSpacesJob;
use App\Models\CustomerDocument;
use App\Models\DocumentType;
use App\Support\CustomerDocumentStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CustomerDocumentController extends Controller
{
    protected function resolveCustomerId(Request $request): ?int
    {
        $customerAuth = $request->user('sanctum') ?: Auth::guard('customer')->user();

        return $customerAuth?->customer_id ?? $customerAuth?->customer?->id;
    }

    public function requirements(Request $request): JsonResponse
    {
        $customerId = $this->resolveCustomerId($request);
        if (! $customerId) {
            return response()->json([
                'message' => 'Customer account is not linked yet.',
            ], 422);
        }

        $context = (string) $request->query('context', 'all');
        $query = DocumentType::query()->orderBy('sort_order');

        if ($context === 'rental') {
            $query->forRental();
        } elseif ($context === 'finance') {
            $query->forFinance();
        }

        $types = $query->get();
        $uploadedDocs = CustomerDocument::query()
            ->where('customer_id', $customerId)
            ->with('documentType')
            ->latest('id')
            ->get()
            ->unique('document_type_id')
            ->keyBy('document_type_id');

        return response()->json([
            'context' => $context,
            'documents' => $types->map(function (DocumentType $type) use ($uploadedDocs) {
                $uploaded = $uploadedDocs->get($type->id);

                return [
                    'id' => $type->id,
                    'name' => $type->name,
                    'slug' => $type->slug,
                    'description' => $type->description,
                    'is_mandatory' => (bool) $type->is_mandatory,
                    'required_for' => $type->required_for,
                    'uploaded' => $uploaded ? [
                        'id' => $uploaded->id,
                        'status' => $uploaded->status ?? 'pending_review',
                        'file_name' => $uploaded->file_name,
                        'file_url' => $uploaded->file_url,
                        'document_number' => $uploaded->document_number,
                        'valid_until' => $uploaded->valid_until,
                        'uploaded_at' => optional($uploaded->created_at)->toIso8601String(),
                    ] : null,
                ];
            })->values(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $customerId = $this->resolveCustomerId($request);
        if (! $customerId) {
            return response()->json([
                'message' => 'Customer account is not linked yet.',
            ], 422);
        }

        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf,webp', 'max:10240'],
            'document_type_id' => ['required', 'integer', 'exists:document_types,id'],
            'document_number' => ['nullable', 'string', 'max:255'],
            'valid_until' => ['nullable', 'date'],
        ]);

        $file = $validated['file'];
        $path = 'customer-documents/'.Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
        CustomerDocumentStorage::put($path, $file->get());

        $existing = CustomerDocument::query()->where([
            'customer_id' => $customerId,
            'document_type_id' => $validated['document_type_id'],
        ])->first();

        $oldPath = $existing?->file_path;

        $attributes = [
            'customer_id' => $customerId,
            'document_type_id' => $validated['document_type_id'],
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_format' => $file->getClientOriginalExtension(),
            'document_number' => $validated['document_number'] ?? '',
            'valid_until' => $validated['valid_until'] ?? null,
        ];
        if (Schema::hasColumn('customer_documents', 'status')) {
            $attributes['status'] = 'pending_review';
        }

        $document = CustomerDocument::updateOrCreate([
            'customer_id' => $customerId,
            'document_type_id' => $validated['document_type_id'],
        ], $attributes);

        if ($oldPath && $oldPath !== $path) {
            CustomerDocumentStorage::delete($oldPath);
        }

        MoveCustomerDocumentToSpacesJob::dispatch($document->id, $path)
            ->delay(now()->addMinutes(10));

        return response()->json([
            'message' => 'Document uploaded successfully. File is staged on site storage and queued for DigitalOcean sync.',
            'document' => [
                'id' => $document->id,
                'status' => $document->status ?? 'pending_review',
                'file_name' => $document->file_name,
                'file_url' => $document->file_url,
                'document_number' => $document->document_number,
                'valid_until' => $document->valid_until,
                'sync_status' => CustomerDocumentStorage::spacesConfigured() ? 'queued_to_spaces' : 'spaces_not_configured',
            ],
        ], 201);
    }
}
