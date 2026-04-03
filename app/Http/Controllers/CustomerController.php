<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerAgreement;
use App\Models\CustomerDocument;
use App\Models\DocumentType;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function index()
    {
        // dashboard of customers all points of interest will be address
    }

    public function customers()
    {
        $customers = Customer::all();

        return view('livewire.agreements.migrated.admin.customers.customers', compact('customers'));
    }

    public function create()
    {
        $customers = Customer::all();

        return view('livewire.agreements.migrated.admin.customers.create', compact('customers'));
    }

    // deleteDocument
    public function deleteDocument(Request $request)
    {

        \Log::info($request);

        $document = CustomerDocument::where('id', $request->documentTypeId)
            ->where('customer_id', $request->customerId)
            ->where('booking_id', $request->bookingId)
            ->where('id_deleted', false)
            ->first();

        if (! $document) {
            return response()->json(['error' => 'Document not found'], 404);
        } else {
            $document->id_deleted = true;
            $document->save();
        }

        return response()->json(['success' => 'Document deleted']);
    }

    public function verifyDocument(Request $request, $documentTypeId)
    {
        // $validatedData = $request->validate([
        //     'document_id' => 'required|exists:CustomerDocument,id',
        // ]);

        $document = CustomerDocument::where('id', $request->documentTypeId)
            ->where('id_deleted', false)
            ->first();

        if (! $document) {
            return response()->json(['error' => 'Document not found'], 404);
        }

        $document->is_verified = true;
        $document->save();

        return response()->json(['success' => 'Document Verified']);
    }

    public function verifyAgreementDocument(Request $request, $documentTypeId)
    {

        \Log::info('Received request to verify agreement document', $request->all());
        // $validatedData = $request->validate([
        //     'document_id' => 'required|exists:CustomerDocument,id',
        // ]);

        $document = CustomerAgreement::find($request->documentTypeId);

        if (! $document) {
            return response()->json(['error' => 'Document not found'], 404);
        }

        $document->is_verified = true;
        $document->save();

        return response()->json(['success' => 'Document Verified']);
    }

    public function store(Request $request)
    {

        if ($request->id === null) {

            try {
                \Log::info('Received Customers store request:', $request->all());
                $customer = new Customer($request->all());
                $customer->save();

                return response()->json(['success' => 'Customer added successfully.', 'id' => $customer->id]);
            } catch (QueryException $e) {
                $errorCode = $e->errorInfo[1];
                if ($errorCode == 1062) {
                    // Duplicate entry
                    \Log::error(__FILE__.' at line '.__LINE__.'Duplicate entry detected:', ['message' => $e->getMessage()]);

                    return response()->json(['error' => 'A customer with the same email already exists.'], 409); // 409 Conflict
                }
                // Other SQL errors
                \Log::error(__FILE__.' at line '.__LINE__.'An SQL error occurred:', ['message' => $e->getMessage()]);

                return response()->json(['error' => 'An error occurred while adding the customer.', 'message' => $e->getMessage()], 500);
            } catch (\Exception $e) {
                \Log::error(__FILE__.' at line '.__LINE__.'An error occurred while adding the customer:', ['message' => $e->getMessage()]);

                return response()->json(['error' => 'An unexpected error occurred.', 'message' => $e->getMessage()], 500);
            }
        } else {
            $customer = Customer::find($request->id);
            if (! $customer) {
                return response()->json(['error' => 'Customer not found'], 404);
            }

            try {
                \Log::info('Received Customers update request:', $request->all());
                $customer->fill($request->all());
                $customer->save();

                return response()->json(['success' => 'Customer updated successfully.', 'id' => $customer->id]);
            } catch (QueryException $e) {
                $errorCode = $e->errorInfo[1];
                if ($errorCode == 1062) {
                    // Duplicate entry
                    \Log::error(__FILE__.' at line '.__LINE__.'Duplicate entry detected:', ['message' => $e->getMessage()]);

                    return response()->json(['error' => 'A customer with the same email already exists.'], 409); // 409 Conflict
                }
                // Other SQL errors
                \Log::error(__FILE__.' at line '.__LINE__.'An SQL error occurred:', ['message' => $e->getMessage()]);

                return response()->json(['error' => 'An error occurred while updating the customer.', 'message' => $e->getMessage()], 500);
            } catch (\Exception $e) {
                \Log::error(__FILE__.' at line '.__LINE__.'An error occurred while updating the customer:', ['message' => $e->getMessage()]);

                return response()->json(['error' => 'An unexpected error occurred.', 'message' => $e->getMessage()], 500);
            }
        }
    }

    public function uploadCustomerDocument(Request $request, $customerId)
    {
        $request->validate([
            'document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:65536',
        ]);

        $file = $request->file('document');
        $timestamp = time();
        $extension = $file->getClientOriginalExtension();
        $documentFriendlyName = str_slug($request->input('name', 'document'));

        $path = $file->storeAs(
            "customers/{$customerId}/documents",
            "{$documentFriendlyName}-{$timestamp}.{$extension}",
            'local'
        );

        // LOGGING & SFTP SYNC LOGIC (Add these lines)
        $absoluteLocalPath = storage_path('app/'.$path);
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        // --- SFTP Sync Logic ---
        $absolutePath = storage_path('app/'.$path);
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absolutePath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        if (! $path) {
            return back()->with('error', 'Document upload failed.');
        }

        \Log::info('Document uploaded:', ['path' => $path, 'name' => $documentFriendlyName, 'timestamp' => $timestamp, 'extension' => $extension]);

        if (! $success) {
            \Log::warning("uploaded file locally but failed to sync to remote domain: $absolutePath");
        }

        return back()->with('success', 'Document uploaded successfully.');
    }

    public function getCustomerDocument($customerId, $fileName)
    {
        // Authorization check to ensure the user has the right to access this customer's documents
        if (! auth()->user()->canAccessCustomerDocuments($customerId)) {
            abort(403);
        }

        $path = "customers/{$customerId}/documents/{$fileName}";

        if (! Storage::exists($path)) {
            abort(404);
        }

        return Storage::download($path);
    }

    public function upload(Request $request, $customer_id)
    {
        $request->validate([
            'document' => 'file|mimes:jpg,jpeg,png,pdf|max:65536',
        ]);

        \Log::info("Received Customer upload request for customer_id {$customer_id}: ", $request->all());

        $file = $request->file('document');
        $documentTypeCode = $request->input('documentTypeCode');
        $motorbikeID = $request->input('motorbikeID');
        $bookingID = $request->input('bookingID');
        $documentType = DocumentType::where('code', $documentTypeCode)->firstOrFail();

        $rrand = rand(10, 999);
        $date = now()->format('Y-m-d');
        $extension = $file->getClientOriginalExtension();
        $filename = "{$documentTypeCode}-{$rrand}-{$date}.{$extension}";

        $path = $file->storeAs("customers/{$customer_id}/documents", $filename);

        // $path = $file->storeAs("customers/{$customer_id}/documents", $filename, 'public');

        $customerDocument = new CustomerDocument([
            'customer_id' => $customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => $filename,
            'file_path' => $path,
            'file_format' => $extension,
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => true,
            'motorbike_id' => $motorbikeID,
            'booking_id' => $bookingID,
        ]);

        $customerDocument->save();

        // LOGGING & SFTP SYNC LOGIC (Add these lines)
        $absoluteLocalPath = storage_path('app/'.$path);
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        // --- SFTP Sync Logic ---
        $absolutePath = storage_path('app/'.$path);
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absolutePath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        \Log::info("Document stored at: {$path}");

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully.',
            'path' => $path,
        ]);
    }

    public function uploadCustomerViaLink(Request $request, $customer_id)
    {
        $request->validate([
            'document' => 'file|mimes:jpg,jpeg,png,pdf|max:65536',
        ]);

        \Log::info("Received Customer upload request for customer_id {$customer_id}: ", $request->all());

        $file = $request->file('document');
        $documentTypeCode = $request->input('documentTypeCode');
        $motorbikeID = $request->input('motorbikeID');
        $bookingID = $request->input('bookingID');
        $documentType = DocumentType::where('code', $documentTypeCode)->firstOrFail();

        $rrand = rand(10, 999);
        $date = now()->format('Y-m-d');
        $extension = $file->getClientOriginalExtension();
        $filename = "{$documentTypeCode}-{$rrand}-{$date}.{$extension}";

        $path = $file->storeAs("customers/{$customer_id}/documents", $filename);

        // $path = $file->storeAs("customers/{$customer_id}/documents", $filename, 'public');

        // Insert document details into customer_documents table
        $customerDocument = new CustomerDocument([
            'customer_id' => $customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => $filename,
            'file_path' => $path,
            'file_format' => $extension,
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'motorbike_id' => $motorbikeID,
            'booking_id' => $bookingID,
        ]);

        $customerDocument->save();

        \Log::info("Document stored at: {$path}");

        // LOGGING & SFTP SYNC LOGIC (Add these lines)
        $absoluteLocalPath = storage_path('app/'.$path);
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        // --- SFTP Sync Logic ---
        $absolutePath = storage_path('app/'.$path);
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absolutePath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        if (! $success) {
            \Log::warning("uploaded file locally but failed to sync to remote domain: $absolutePath");
        }

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully.',
            'path' => $path,
        ]);
    }

    // 2.1.3.2 & 2.2.1 - booking-new.blade.php
    public function uploadLeftDocument(Request $request)
    {
        // log
        \Log::info('Received request to upload left documents', $request->all());
        $customerId = $request->customer_id; // Get customer_id from request

        $query = DB::table('document_types as DT')
            ->select('DT.name', 'DT.code', 'DT.is_active', 'DT.is_required', 'CD.customer_id', 'CD.file_name', 'CD.file_path', 'CD.is_verified')
            ->leftJoin('customer_documents AS CD', function ($join) use ($customerId) {
                $join->on('DT.id', '=', 'CD.document_type_id')
                    ->where('CD.customer_id', '=', $customerId)
                    ->where('CD.is_verified', '=', true);
            })
            ->where('DT.is_motorbike', '=', false)
            ->get();

        return response()->json($query);
    }

    // 2.1.3.3 & 2.2.2 - booking-new.blade.php
    // admin -> Route::post('/customers/documents/left', [CustomerController::class, 'uploadLeftDocument'])->name('customers.documents.left');
    public function uploadLeftMotorbikeDocument(Request $request)
    {
        \Log::info('Received request to upload left motorbike documents', [$request->all()]);

        $query = DB::table('document_types as DT')
            ->select('DT.name', 'DT.code', 'DT.is_active', 'DT.is_required')
            ->where('DT.is_motorbike', '=', true)
            ->get();

        \Log::info('Motorbike documents:', [$query]);

        return response()->json($query);
    }

    // Route -> Route::post('/customers/documents/left', [CustomerController::class, 'uploadLeftDocumentViaLink'])->name('customers.documents.left.link');
    // public function uploadLeftDocumentViaLink(Request $request)
    // {
    //     // log
    //     \Log::info('Received request to upload left documents', $request->all());
    //     $customerId = $request->customer_id; // Get customer_id from request

    //     $query = DB::table('document_types as DT')
    //         ->select('DT.name', 'DT.code', 'DT.is_active', 'DT.is_required', 'CD.customer_id', 'CD.file_name', 'CD.file_path', 'CD.is_verified')
    //         ->leftJoin('customer_documents AS CD', function ($join) use ($customerId) {
    //             $join->on('DT.id', '=', 'CD.document_type_id')
    //                 ->where('CD.customer_id', '=', $customerId)
    //                 ->where('CD.id_deleted', '=', false);
    //         })
    //         ->where('DT.is_motorbike', '=', false)
    //         ->where('DT.name', '!=', 'Rental Agreement')
    //         ->get();

    //     return response()->json($query);
    // }

    public function uploadLeftDocumentViaLink(Request $request)
    {
        $customerId = $request->customer_id; // Get customer_id from request

        $query = DB::table('document_types as DT')
            ->select(
                'DT.id',
                'DT.name',
                'DT.code',
                'DT.is_active',
                'DT.is_required',
                'CD.customer_id',
                'CD.file_name',
                'CD.file_path',
                'CD.is_verified'
            )
            ->leftJoin('customer_documents AS CD', function ($join) use ($customerId) {
                $join->on('DT.id', '=', 'CD.document_type_id')
                    ->where('CD.customer_id', '=', $customerId)
                    ->where('CD.id_deleted', '=', false);
            })
            ->where(function ($q) {
                $q->where('DT.is_motorbike', '=', false) // normal customer docs
                    ->orWhere('DT.id', '=', 14);          // include “Other Document (person)”
            })
            ->where('DT.name', '!=', 'Rental Agreement') // exclude rental agreement
            ->where('DT.code', '!=', 'loyalty_scheme_policy') // exclude loyalty scheme policy
            ->get();

        return response()->json($query);
    }

    // Route::post('/customers/documents/motorbikeleft', [CustomerController::class, 'uploadLeftMotorbikeDocumentViaLink'])->name('customers.motorbike.documents.left.link');
    public function uploadLeftMotorbikeDocumentViaLink(Request $request)
    {
        $customerId = $request->customer_id;

        $query = DB::table('document_types as DT')
            ->select('DT.name', 'DT.code', 'DT.is_active', 'DT.is_required')
            ->where('DT.is_motorbike', '=', true)
            ->get();

        return response()->json($query);
    }

    // 2.1.3.2 & 2.2.1 - booking-new.blade.php
    public function uploadListDocument(Request $request)
    {

        $validated = $request->validate([
            'customer_id' => 'required|integer',
            'booking_id' => 'required|integer',
        ]);

        $customerId = $request->customer_id;
        $bookingID = $request->booking_id;

        // customer_documents table
        $customer_docs = DB::table('document_types as DT')
            ->select('CD.id', 'DT.name', 'DT.code', 'DT.is_active', 'DT.is_required', 'CD.customer_id', 'CD.file_name', 'CD.file_path', 'CD.is_verified')
            ->leftJoin('customer_documents AS CD', function ($join) use ($customerId) {
                $join->on('DT.id', '=', 'CD.document_type_id')
                    ->where('CD.customer_id', '=', $customerId)
                    ->where('CD.id_deleted', '=', false);
            })
            ->where('DT.is_motorbike', '=', false)
            ->where('DT.name', '!=', 'Rental Agreement')
            ->get();

        // motorbike_documents table ONLY MOTORBIKES
        $motorbike_docs = DB::table('document_types as DT')
            ->select('CD.id', 'DT.name', 'DT.code', 'DT.is_active', 'DT.is_required', 'CD.customer_id', 'CD.file_name', 'CD.file_path', 'CD.is_verified')
            ->leftJoin('customer_documents AS CD', function ($join) use ($customerId, $bookingID) {
                $join->on('DT.id', '=', 'CD.document_type_id')
                    ->where('CD.customer_id', '=', $customerId)
                    ->where('CD.booking_id', '=', $bookingID);
            })
            ->where('DT.is_motorbike', '=', true)
            ->where('DT.name', '!=', 'Rental Agreement')
            ->get();

        // customer_agreements table
        $customer_agreement = DB::table('document_types as DT')
            ->select('CD.id', 'DT.name', 'DT.code', 'DT.is_active', 'DT.is_required', 'CD.customer_id', 'CD.file_name', 'CD.file_path', 'CD.is_verified')
            ->leftJoin('customer_agreements AS CD', function ($join) use ($customerId, $bookingID) {
                $join->on('DT.id', '=', 'CD.document_type_id')
                    ->where('CD.customer_id', '=', $customerId)
                    ->where('CD.booking_id', '=', $bookingID);
            })
            ->where('DT.name', '=', 'Rental Agreement')
            ->get();

        return response()->json(['customer_docs' => $customer_docs, 'motorbike_docs' => $motorbike_docs, 'customer_agreement' => $customer_agreement]);
    }
}
