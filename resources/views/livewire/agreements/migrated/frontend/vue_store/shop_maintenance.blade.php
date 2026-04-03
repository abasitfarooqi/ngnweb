   {{-- resources/views/frontend/vue_store/app.blade.php --}}
   @extends('livewire.agreements.migrated.frontend.ngnstore.layouts.master')

   @section('title', 'Shop Maintenance')

   @section('content')
       <div class="container items-center justify-center">
           <div class="text-center my-5">
               <h3 class="display-6 mt-2">ONLINE SHOP IS COMING LIVE BY JANUARY 2025</h3>
               <h2 class="display-6 mt-2 bg-success text-white"
                   style="border-radius: 0px; width: 50%; margin: 0 auto;margin-top: 10px; padding: 10px;">CLICK & COLLECT IS
                   AVAILABLE BY JANUARY 2025</h2>
               <p class="lead mt-2 mt-4" style="font-weight: 600;">Meanwhile, you can visit any of our stores</p>
           </div>
           <style>
               .location-image {
                   height: 150px;
                   -o-object-fit: cover;
                   object-fit: cover;
                   border-radius: 0px;
                   margin: 0 auto;
               }
           </style>
           @include('livewire.agreements.migrated.frontend.locationsHomeSection')
       </div>
   @endsection
