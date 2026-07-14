@extends(backpack_view('blank'))

@php
  $breadcrumbs = [
    'Admin' => backpack_url('dashboard'),
    'Recurring Payments' => false,
  ];
@endphp

@section('content')
    @include('livewire.agreements.migrated.admin.partials.judopay-body')
@endsection
