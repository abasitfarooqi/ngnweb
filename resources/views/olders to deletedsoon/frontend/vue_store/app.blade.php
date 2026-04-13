   {{-- resources/views/frontend/vue_store/app.blade.php --}}
   @extends('olders.frontend.ngnstore.layouts.master')

   @section('title', 'Ecommerce Application')

   @section('content')
       <div id="app"></div>
   @endsection

   @push('vueapp')
       @vite(['resources/js/app.js'])
   @endpush
