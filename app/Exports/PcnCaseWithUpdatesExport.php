<?php

namespace App\Exports;

use App\Models\PcnCase;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PcnCaseWithUpdatesExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Get all PCN cases with their relationships and updates
        $pcnCases = PcnCase::with(['motorbike', 'customer', 'user', 'updates.user'])->get();

        // Create a new collection to store flattened data
        $flattenedData = new Collection;

        foreach ($pcnCases as $pcnCase) {
            if ($pcnCase->updates->isEmpty()) {
                // If no updates, add one row with PCN data and empty update fields
                $flattenedData->push((object) [
                    'pcn_case' => $pcnCase,
                    'update' => null,
                ]);
            } else {
                // If has updates, add one row for each update
                foreach ($pcnCase->updates as $update) {
                    $flattenedData->push((object) [
                        'pcn_case' => $pcnCase,
                        'update' => $update,
                    ]);
                }
            }
        }

        return $flattenedData;
    }

    public function map($row): array
    {
        $pcnCase = $row->pcn_case;
        $update = $row->update;

        $customer = $pcnCase->customer;
        $motorbike = $pcnCase->motorbike;

        return [
            // PCN Case Data
            $pcnCase->id,
            $pcnCase->pcn_number,
            $pcnCase->date_of_contravention,
            $pcnCase->time_of_contravention,
            $motorbike ? $motorbike->reg_no : '',
            $customer ? ($customer->first_name.' '.$customer->last_name) : '',
            $customer ? $customer->email : '',
            $pcnCase->isClosed ? 'Yes' : 'No',
            $pcnCase->full_amount,
            $pcnCase->reduced_amount,
            $pcnCase->council_link ?? '',
            $pcnCase->is_police ? 'Yes' : 'No',
            $pcnCase->note,

            // Update Data
            $update ? $update->update_date : '',
            $update ? ($update->is_appealed ? 'Yes' : 'No') : '',
            $update ? ($update->is_paid_by_owner ? 'Yes' : 'No') : '',
            $update ? ($update->is_paid_by_keeper ? 'Yes' : 'No') : '',
            $update ? ($update->is_cancled ? 'Yes' : 'No') : '',
            $update ? $update->additional_fee : '',
            $update ? $update->note : '',
            $update && $update->user ? ($update->user->first_name.' '.$update->user->last_name) : '',
            $update ? $update->created_at : '',
        ];
    }

    public function headings(): array
    {
        return [
            // PCN Case Headers
            'Case ID',
            'PCN Number',
            'Date of Contravention',
            'Time of Contravention',
            'Vehicle Reg No',
            'Customer Name',
            'Customer Email',
            'Case Closed',
            'Full Amount',
            'Reduced Amount',
            'Council Link',
            'Is Police Case',
            'Case Note',

            // Update Headers
            'Update Date',
            'Is Appealed',
            'Paid by NGN',
            'Paid by Keeper',
            'Is Cancelled',
            'Additional Fee',
            'Update Note',
            'Updated By',
            'Update Created At',
        ];
    }
}

