@extends('livewire.agreements.migrated.frontend.ngnstore.layouts.master')

@section('title', 'Review Your Details')

@section('content')
<h1>Review Your Details</h1>
<form action="{{ route('motorcycle.delivery.store') }}" method="POST">
    @csrf
    <div>
        <h2>Collection and Delivery Details</h2>
        <p>Collection Name: {{ $data['collection_name'] }}</p>
        <p>Collection Contact Number: {{ $data['collection_contact_number'] }}</p>
        <p>Collection Address: {{ $data['collection_address_line_1'] }}</p>
        <p>Collection Postcode: {{ $data['collection_postcode'] }}</p>
        <p>Delivery Name: {{ $data['delivery_name'] }}</p>
        <p>Delivery Contact Number: {{ $data['delivery_contact_number'] }}</p>
        <p>Delivery Address: {{ $data['delivery_address_line_1'] }}</p>
        <p>Delivery Postcode: {{ $data['delivery_postcode'] }}</p>
        <p>Bike Registration Number: {{ $data['bike_registration_number'] }}</p>
        <p>Make: {{ $data['make'] }}</p>
        <p>Model: {{ $data['model'] }}</p>
        <p>Year: {{ $data['year'] }}</p>
        <p>Mileage: {{ $data['mileage'] }}</p>
        <p>Colour: {{ $data['colour'] }}</p>
        <p>Does the bike roll? {{ $data['does_bike_roll'] ? 'Yes' : 'No' }}</p>
        <p>Are all documents present? {{ $data['are_documents_present'] ? 'Yes' : 'No' }}</p>
        <p>Does the bike have spare parts? {{ $data['does_bike_have_spare_parts'] ? 'Yes' : 'No' }}</p>
        <p>Are all keys present? {{ $data['are_keys_present'] ? 'Yes' : 'No' }}</p>
    </div>

    <div>
        <h2>Delivery Options</h2>
        <p>Specific Date: {{ $data['specific_date'] }}</p>
        <p>Delivery Speed: {{ $data['delivery_speed'] }}</p>
    </div>

    <div>
        <h2>Personal Information</h2>
        <p>Email: {{ $data['email'] }}</p>
        <p>Contact Number: {{ $data['contact_number'] }}</p>
        <p>First Name: {{ $data['first_name'] }}</p>
        <p>Last Name: {{ $data['last_name'] }}</p>
    </div>

    <button type="submit">Confirm and Submit</button>
</form>
@endsection
