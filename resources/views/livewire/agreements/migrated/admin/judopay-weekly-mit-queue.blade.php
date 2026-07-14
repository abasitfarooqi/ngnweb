@extends(backpack_view('blank'))

@php
  $breadcrumbs = [
    'Admin' => backpack_url('dashboard'),
    'Recurring Payments' => route('page.judopay.index'),
    'MIT Dashboard' => route('page.judopay.mit-dashboard'),
    'Weekly Schedule' => false,
  ];
@endphp

@section('content')
    @include('livewire.agreements.migrated.admin.partials.judopay-weekly-mit-queue-body')
@endsection
