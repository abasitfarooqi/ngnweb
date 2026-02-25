<?php

namespace App\Exports;

use App\Models\PcnCase;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PcnCaseExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Summary export: one row per PCN case
        return PcnCase::with(['motorbike', 'customer', 'user'])->get();
    }

    public function map($row): array
    {
        /** @var \App\Models\PcnCase $pcnCase */
        $pcnCase = $row;
        $customer = $pcnCase->customer;

        return [
            // Summary PCN data (one row per case)
            $pcnCase->created_at,
            $pcnCase->date_of_letter_issued,
            $pcnCase->pcn_number,
            $pcnCase->date_of_contravention,
            $pcnCase->motorbike->reg_no ?? '',
            $pcnCase->customer_id,
            $customer ? ($customer->first_name.' '.$customer->last_name) : '',
            $customer ? $customer->email : '',
            $pcnCase->isClosed ? 'TRUE' : 'FALSE',
            $pcnCase->full_amount,
            $pcnCase->reduced_amount,
            optional($pcnCase->user)->first_name ?? '',
            $pcnCase->note,
            $pcnCase->council_link ?? '',
            $pcnCase->is_police ? 'Yes' : 'No',
        ];
    }

    public function headings(): array
    {
        return [
            'Date Created',
            'Date of Letter Issued',
            'PCN Number',
            'Date of Contravention',
            'Vehicle Registration Number',
            'Customer ID',
            'Customer Name',
            'Customer Email',
            'Case Closed',
            'Full Amount',
            'Reduced Amount',
            'Updated By',
            'Case Note',
            'Council Link',
            'Police Case',
        ];
    }

    public function columnFormats(): array
    {
        // Columns: A = Date Created, B = Date of Letter Issued, D = Date of Contravention
        return [
            'A' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
