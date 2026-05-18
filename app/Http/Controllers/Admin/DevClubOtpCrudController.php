<?php

namespace App\Http\Controllers\Admin;

use App\Models\ClubMember;
use App\Models\OtpVerification;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class DevClubOtpCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;

    public function setup()
    {
        CRUD::setModel(OtpVerification::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/dev-club-otp');
        CRUD::setEntityNameStrings('club OTP detail', 'club OTP details');

        $this->crud->denyAccess(['create', 'update', 'delete', 'show']);
    }

    protected function setupListOperation()
    {
        $this->crud->query->with('clubMember')->orderByDesc('created_at');

        CRUD::column('id')->label('ID');
        CRUD::addColumn([
            'name' => 'created_at',
            'label' => 'Created',
            'type' => 'datetime',
        ]);
        CRUD::column('club_member_id')->label('Member ID');
        CRUD::column('club_member_name')->label('Club member');
        CRUD::column('club_member_phone')->label('Phone');
        CRUD::column('club_member_email')->label('Email');
        CRUD::column('otp_plain_code')->label('OTP');
        CRUD::column('otp_sms_status')->label('SMS status');
        CRUD::column('otp_sms_to')->label('SMS to');
        CRUD::addColumn([
            'name' => 'otp_sms_body',
            'label' => 'SMS body',
            'type' => 'text',
        ]);
        CRUD::column('otp_sms_error')->label('SMS error');
        CRUD::addColumn([
            'name' => 'otp_sms_created_at',
            'label' => 'SMS created',
            'type' => 'datetime',
        ]);
        CRUD::addColumn([
            'name' => 'expires_at',
            'label' => 'Expires',
            'type' => 'datetime',
        ]);
        CRUD::addColumn([
            'name' => 'is_used',
            'label' => 'Used',
            'type' => 'boolean',
        ]);

        CRUD::addFilter([
            'name' => 'club_customer',
            'type' => 'text',
            'label' => 'Club customer',
        ], false, function ($value) {
            $this->crud->query->whereHas('clubMember', function ($query) use ($value) {
                $query->where(function ($subQuery) use ($value) {
                    $subQuery->where('full_name', 'like', "%{$value}%")
                        ->orWhere('email', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%");

                    if (is_numeric($value)) {
                        $subQuery->orWhere('id', (int) $value);
                    }
                });
            });
        });

        CRUD::addFilter([
            'name' => 'club_member_name',
            'type' => 'text',
            'label' => 'Club member',
        ], false, function ($value) {
            $this->crud->query->whereHas('clubMember', function ($query) use ($value) {
                $query->where('full_name', 'like', "%{$value}%");
            });
        });

        CRUD::addFilter([
            'name' => 'club_member_phone',
            'type' => 'text',
            'label' => 'Phone',
        ], false, function ($value) {
            $this->crud->query->whereHas('clubMember', function ($query) use ($value) {
                $query->where('phone', 'like', "%{$value}%");
            });
        });

        CRUD::addFilter([
            'name' => 'club_member_email',
            'type' => 'text',
            'label' => 'Email',
        ], false, function ($value) {
            $this->crud->query->whereHas('clubMember', function ($query) use ($value) {
                $query->where('email', 'like', "%{$value}%");
            });
        });

        CRUD::addFilter([
            'name' => 'is_used',
            'type' => 'dropdown',
            'label' => 'Used',
        ], [
            1 => 'Yes',
            0 => 'No',
        ], function ($value) {
            $this->crud->query->where('is_used', (bool) $value);
        });

        CRUD::addFilter([
            'name' => 'sort_order',
            'type' => 'dropdown',
            'label' => 'Sort',
        ], [
            'created_desc' => 'Created DESC',
            'created_asc' => 'Created ASC',
            'member_asc' => 'Club customer ASC',
            'member_desc' => 'Club customer DESC',
        ], function ($value) {
            $this->crud->query->reorder();

            if ($value === 'created_asc') {
                $this->crud->query->orderBy('created_at', 'asc');

                return;
            }

            if ($value === 'member_asc' || $value === 'member_desc') {
                $direction = $value === 'member_asc' ? 'asc' : 'desc';
                $this->crud->query->orderBy(
                    ClubMember::select('full_name')
                        ->whereColumn('club_members.id', 'otp_verifications.club_member_id')
                        ->limit(1),
                    $direction
                )->orderBy('created_at', 'desc');

                return;
            }

            $this->crud->query->orderBy('created_at', 'desc');
        });
    }
}
