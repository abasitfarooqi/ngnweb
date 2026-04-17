<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CustomerDocumentRequest;
use App\Http\Requests\UpdateCustomerDocumentReviewRequest;
use App\Models\CustomerDocument;
use App\Models\DocumentType;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class CustomerDocumentCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(CustomerDocument::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/customer-document');
        CRUD::setEntityNameStrings('customer document', 'customer documents');
    }

    protected function setupListOperation()
    {
        CRUD::orderBy('id', 'DESC');

        CRUD::addColumn(['name' => 'id', 'type' => 'number', 'label' => 'ID']);

        CRUD::addColumn([
            'name' => 'customer',
            'type' => 'relationship',
            'label' => 'Customer',
            'attribute' => 'full_name',
        ]);

        CRUD::addColumn([
            'name' => 'documentType',
            'type' => 'relationship',
            'label' => 'Document type',
            'attribute' => 'name',
        ]);

        CRUD::addColumn([
            'name' => 'file',
            'label' => 'File',
            'type' => 'closure',
            'function' => function (CustomerDocument $entry): string {
                $url = $entry->file_url;

                return $url
                    ? '<a class="btn btn-sm btn-outline-primary" href="'.e($url).'" target="_blank" rel="noopener">Open</a>'
                    : '<span class="text-muted">—</span>';
            },
            'escaped' => false,
        ]);

        CRUD::addColumn([
            'name' => 'status',
            'label' => 'Status',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'is_verified',
            'label' => 'Verified',
            'type' => 'boolean',
        ]);

        CRUD::addColumn([
            'name' => 'valid_until',
            'label' => 'Valid until',
            'type' => 'date',
        ]);

        CRUD::addColumn([
            'name' => 'document_number',
            'label' => 'Document no.',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'created_at',
            'label' => 'Uploaded',
            'type' => 'datetime',
        ]);

        CRUD::enableExportButtons();

        CRUD::addFilter(
            [
                'name' => 'status',
                'type' => 'dropdown',
                'label' => 'Status',
            ],
            [
                'pending_review' => 'Pending review',
                'uploaded' => 'Uploaded',
                'approved' => 'Approved',
                'rejected' => 'Rejected (reupload)',
                'archived' => 'Archived',
            ],
            function ($value): void {
                $this->crud->addClause('where', 'status', $value);
            }
        );

        CRUD::addFilter(
            [
                'name' => 'section',
                'type' => 'dropdown',
                'label' => 'Section',
            ],
            [
                'rental_general' => 'Rental and general',
                'finance' => 'Finance',
            ],
            function ($value): void {
                $financeIds = DocumentType::query()->forFinance()->pluck('id')->filter()->values();
                if ($value === 'finance') {
                    if ($financeIds->isEmpty()) {
                        $this->crud->addClause('whereRaw', '1 = 0');
                    } else {
                        $this->crud->addClause('whereIn', 'document_type_id', $financeIds->all());
                    }
                } elseif ($value === 'rental_general' && $financeIds->isNotEmpty()) {
                    $this->crud->addClause('whereNotIn', 'document_type_id', $financeIds->all());
                }
            }
        );
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(CustomerDocumentRequest::class);
        CRUD::setFromDb();
    }

    protected function setupUpdateOperation()
    {
        CRUD::setValidation(UpdateCustomerDocumentReviewRequest::class);

        $entry = $this->crud->getCurrentEntry();
        CRUD::addField([
            'name' => 'review_header',
            'type' => 'custom_html',
            'value' => view('admin.customer_documents.review_header', ['entry' => $entry])->render(),
        ]);

        CRUD::field('review_help')
            ->type('custom_html')
            ->value('<p class="help-block">Set <strong>Valid until</strong> when the document carries an expiry. Choose <strong>Approved</strong> to verify (a document number is issued automatically if blank) or <strong>Rejected</strong> to ask for a reupload (reason required).</p>');

        CRUD::field('valid_until')
            ->type('date')
            ->label('Valid until');

        CRUD::field('status')
            ->type('select_from_array')
            ->label('Review status')
            ->options([
                'pending_review' => 'Pending review',
                'uploaded' => 'Uploaded',
                'approved' => 'Approved (verified)',
                'rejected' => 'Rejected — reupload required',
                'archived' => 'Archived',
            ]);

        CRUD::field('rejection_reason')
            ->type('textarea')
            ->label('Rejection / reupload note')
            ->hint('Required when status is Rejected.');
    }

    protected function setupShowOperation()
    {
        CRUD::setFromDb();
    }
}
