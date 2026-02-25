@extends('layouts.admin')

@section('content')
    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-fluid">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered" style="font-size: 11px;">
                                <thead>
                                    <tr>
                                        <th>Customer Name</th>
                                        <th>Booking ID</th>
                                        <th>Passcode</th>
                                        <th>Expires At</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Link</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($access as $item)
                                        <tr>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->booking_id }}</td>
                                            <td>{{ $item->passcode }}</td>
                                            <td>{{ $item->expires_at }}</td>
                                            <td>{{ $item->created_at }}</td>
                                            <td>{{ $item->updated_at }}</td>
                                            <td><a href="{{ $item->link }}" target="_blank">Customer Link</a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->
            </div> <!-- container-fluid -->
    </div @endsection
