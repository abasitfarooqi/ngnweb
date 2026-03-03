<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\NgnPartnerRequest;
use App\Models\ClubMember;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class NgnPartnerCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class NgnPartnerCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitStore;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {
        update as traitUpdate;
    }

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\NgnPartner::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/ngn-partner');
        CRUD::setEntityNameStrings('ngn partner', 'ngn partners');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     *
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::setFromDb(); // set columns from db columns.

        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     *
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(NgnPartnerRequest::class);
        CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     *
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $response = $this->traitStore();
        $entry = $this->crud->entry ?? null;

        if ($entry) {
            $entry->refresh();
            $this->syncClubMember($entry);
        }

        return $response;
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update()
    {
        $response = $this->traitUpdate();
        $entry = $this->crud->entry ?? null;

        if ($entry) {
            $entry->refresh();
            $this->syncClubMember($entry);
        }

        return $response;
    }

    /**
     * Sync ClubMember record based on partner approval status.
     *
     * @param \App\Models\NgnPartner $partner
     * @return void
     */
    private function syncClubMember($partner)
    {
        if ($partner->is_approved) {
            // Partner is approved - set is_partner = true
            $this->handleApprovedPartner($partner);
        } else {
            // Partner is not approved - set is_partner = false
            $this->handleUnapprovedPartner($partner);
        }
    }

    /**
     * Handle approved partner - create or update ClubMember with is_partner = true.
     *
     * @param \App\Models\NgnPartner $partner
     * @return void
     */
    private function handleApprovedPartner($partner)
    {
        // Validate required fields exist
        if (empty($partner->phone) || empty($partner->first_name) || empty($partner->last_name) || empty($partner->email)) {
            return;
        }

        // First priority: Check if ClubMember already exists with this partner's ID
        // This handles cases where phone number was updated
        $existingMemberByPartnerId = ClubMember::where('ngn_partner_id', $partner->id)->first();

        if ($existingMemberByPartnerId) {
            // ClubMember exists with this partner ID - update details
            // DO NOT modify is_active - preserve existing status
            $updateData = [
                'full_name' => trim($partner->first_name.' '.$partner->last_name),
                'email' => $partner->email,
                'phone' => $partner->phone,
                'is_partner' => true,
            ];

            // Set default passkey if null or empty
            if (empty($existingMemberByPartnerId->passkey)) {
                $updateData['passkey'] = '012345';
            }

            $existingMemberByPartnerId->update($updateData);
            return;
        }

        // Second priority: Search for existing ClubMember by phone number
        $existingMemberByPhone = ClubMember::where('phone', $partner->phone)->first();

        if ($existingMemberByPhone) {
            // ClubMember exists with this phone number
            if ($existingMemberByPhone->ngn_partner_id == $partner->id) {
                // Already associated with this partner - ensure is_partner = true
                // DO NOT modify is_active - preserve existing status
                $updateData = [];
                if (! $existingMemberByPhone->is_partner) {
                    $updateData['is_partner'] = true;
                }
                // Set default passkey if null or empty
                if (empty($existingMemberByPhone->passkey)) {
                    $updateData['passkey'] = '012345';
                }
                if (! empty($updateData)) {
                    $existingMemberByPhone->update($updateData);
                }
            }
            // If ngn_partner_id doesn't match or is null, skip (do nothing)
        } else {
            // ClubMember doesn't exist - create new record
            ClubMember::create([
                'full_name' => trim($partner->first_name.' '.$partner->last_name),
                'email' => $partner->email,
                'phone' => $partner->phone,
                'is_active' => true,
                'tc_agreed' => true,
                'is_partner' => true,
                'ngn_partner_id' => $partner->id,
                'passkey' => '012345',
            ]);
        }
    }

    /**
     * Handle unapproved partner - set is_partner = false for associated ClubMembers.
     *
     * @param \App\Models\NgnPartner $partner
     * @return void
     */
    private function handleUnapprovedPartner($partner)
    {
        // Find all ClubMembers associated with this partner
        $associatedMembers = ClubMember::where('ngn_partner_id', $partner->id)
            ->where('is_partner', true)
            ->get();

        // Set is_partner = false for all associated members
        foreach ($associatedMembers as $member) {
            $member->update(['is_partner' => false]);
        }
    }
}
