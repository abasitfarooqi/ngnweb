@extends('layouts.admin')

@section('content')

@section('content')
    <style>
        .selected-row {
            background-color: #464040 !important;
            color: white !important;
        }
    </style>

    <div class="content-page">
        <div class="content">
            <div class="card">
                <div class="card-body">

                    <!-- Start Content-->
                    <div class="container-fluid">
                        <h1>ROTA</h1>
                        <table style="padding: 10px; width: 100%; font-size: 11px; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="padding: 10px; cursor: pointer; border: 0.4px solid black;"
                                        onclick="sortTable(0)">EMPLOYEE ID</th>
                                    <th style="padding: 10px; cursor: pointer; border: 0.4px solid black;"
                                        onclick="sortTable(1)">FIRST NAME</th>
                                    <th style="padding: 10px; cursor: pointer; border: 0.4px solid black;"
                                        onclick="sortTable(2)">DAY</th>
                                    <th style="padding: 10px; cursor: pointer; border: 0.4px solid black;"
                                        onclick="sortTable(3)">DAY OFF</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employeeSchedules as $index => $schedule)
                                    <tr class="employee_row"
                                        style="background-color: {{ $index % 2 == 0 ? '#f2f2f2' : 'white' }};">
                                        <td style="padding: 10px; border: 0.4px solid black;">{{ $schedule['employee_id'] }}
                                        </td>
                                        <td style="padding: 10px; border: 0.4px solid black;">{{ $schedule['first_name'] }}
                                        </td>
                                        <td style="padding: 10px; border: 0.4px solid black;">{{ $schedule['day_of_week'] }}
                                        </td>
                                        <td style="padding: 10px; border: 0.4px solid black;">{{ $schedule['off_day'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> <!-- container -->
        </div> <!-- content -->
    </div>

    <script>
        function sortTable(columnIndex) {
            let table = document.querySelector('table');
            let rows = Array.from(table.querySelectorAll('tbody tr'));
            rows.sort((a, b) => {
                let aValue = parseFloat(a.cells[columnIndex].textContent);
                let bValue = parseFloat(b.cells[columnIndex].textContent);
                return aValue - bValue;
            });
            table.querySelector('tbody').innerHTML = '';
            rows.forEach(row => {
                table.querySelector('tbody').appendChild(row);
            });
        }

        $(document).ready(function() {
            $('.employee_row').on('click', function() {
                $(this).toggleClass('selected-row');
            });
        });
    </script>
@endsection
