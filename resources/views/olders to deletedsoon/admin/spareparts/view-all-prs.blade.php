@extends('layouts.admin')

@section('content')
    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-fluid">
                <h4>ALL QUOTE REQUESTS</h4>
                <div class="card" style="padding: 4px;">
                    @php
                        $currentPrNumber = null;
                    @endphp

                    @foreach ($formattedData as $data)
                        @if ($data['pr_number'] !== $currentPrNumber)
                            @if (!is_null($currentPrNumber))
                                </tbody>
                                </table>
                                <hr>
                            @endif

                            <h5>PR Number: {{ $data['pr_number'] }}
                                <br>
                                Date: {{ $data['pr_date'] }}
                            </h5>
                            <table style="padding: 10px; font-size: 11px; border: 0.4px solid rgba(0, 0, 0, 0.4);">
                                <thead>
                                    <tr>
                                        <th style="width: 4%;text-align: left;">EMPLOYEE ID</th>
                                        <th style="width: 4%;text-align: left;">MAKE</th>
                                        <th style="width: 8%;text-align: left;">MODEL</th>
                                        <th style="width: 8%;text-align: left;">COLOR</th>
                                        <th style="width: 3%;text-align: left;">YEAR</th>
                                        <th style="width: 8%;text-align: left;">CHASSIS NO</th>
                                        <th style="width: 4%;text-align: left;">REG NO</th>
                                        <th style="width: 8%;text-align: left;">PART NUMBER</th>
                                        <th style="width: 8%;text-align: left;">PLACE</th>
                                        <th style="width: 4%;text-align: left;">QUANTITY</th>
                                        <th style="width: 4%;text-align: left;">LINK</th>
                                        {{-- <th style="text-align: left;">IS POSTED</th> --}}
                                        <th>Image</th>
                                    </tr>
                                </thead>
                                <tbody>
                        @endif

                        <tr style="padding: 4px;">
                            <td style="border: 0.4px solid rgba(0, 0, 0, 0.4);text-align: left; padding: 4px;">
                                {{ $data['employee_id'] }}
                            </td>
                            <td style="border: 0.4px solid rgba(0, 0, 0, 0.4);text-align: left; padding: 4px;">
                                {{ $data['brand_name'] }}
                            </td>
                            <td style="border: 0.4px solid rgba(0, 0, 0, 0.4);text-align: left; padding: 4px;">
                                {{ $data['bike_model'] }}
                            </td>
                            <td style="border: 0.4px solid rgba(0, 0, 0, 0.4);text-align: left; padding: 4px;">
                                {{ $data['color'] }}</td>
                            <td style="border: 0.4px solid rgba(0, 0, 0, 0.4);text-align: left; padding: 4px;">
                                {{ $data['year'] }}</td>
                            <td style="border: 0.4px solid rgba(0, 0, 0, 0.4);text-align: left; padding: 4px;">
                                {{ $data['chassis_no'] }}
                            </td>
                            <td style="border: 0.4px solid rgba(0, 0, 0, 0.4);text-align: left; padding: 4px;">
                                {{ $data['reg_no'] }}</td>
                            <td style="border: 0.4px solid rgba(0, 0, 0, 0.4);text-align: left; padding: 4px;">
                                {{ $data['part_number'] }}
                            </td>
                            <td style="border: 0.4px solid rgba(0, 0, 0, 0.4);text-align: left; padding: 4px;">
                                {{ $data['part_position'] }}
                            </td>
                            <td style="border: 0.4px solid rgba(0, 0, 0, 0.4);text-align: left; padding: 4px;">
                                {{ $data['quantity'] }}
                            </td>
                            <td style="border: 0.4px solid rgba(0, 0, 0, 0.4);text-align: left; padding: 4px;"> <a
                                    target="_blank" href="{{ $data['link_one'] }}">link</a> </td>
                            {{-- <td style="text-align: left;">{{ $data['is_posted'] }}</td> --}}
                            <td style="border: 0.4px solid rgba(0, 0, 0, 0.4);text-align: left; padding: 4px;"><a
                                    target="_blank" href={{ $data['image'] }}>
                                    {{-- <img style="width:20%;" src="{{ $data['image'] }}"
                                alt="Image"> --}}
                                    Picture
                                </a>
                            </td>
                        </tr>
                        @php
                            $currentPrNumber = $data['pr_number'];
                        @endphp
                    @endforeach

                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
