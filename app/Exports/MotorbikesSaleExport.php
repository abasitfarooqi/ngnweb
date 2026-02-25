<?php

namespace App\Exports;

use App\Models\MotorbikesSale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MotorbikesSaleExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return MotorbikesSale::all(); // Customize the query as needed
    }

    public function headings(): array
    {
        return [
            'ID',
            'Motorbike ID',
            'Condition',
            'Image One',
            'Image Two',
            'Image Three',
            'Image Four',
            'Is Sold',
            'Mileage',
            'Price',
            'Engine',
            'Suspension',
            'Brakes',
            'Belt',
            'Electrical',
            'Tires',
            'Note',
            'V5 Available',
        ];
    }
}
