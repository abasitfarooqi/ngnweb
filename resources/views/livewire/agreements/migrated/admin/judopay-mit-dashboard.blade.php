@extends(backpack_view('blank'))

@php
  $breadcrumbs = [
    'Admin' => backpack_url('dashboard'),
    'Recurring Payments' => route('page.judopay.index'),
    'MIT Dashboard' => false,
  ];
@endphp

@section('content')
    @include('livewire.agreements.migrated.admin.partials.judopay-mit-dashboard-body')
@endsection
