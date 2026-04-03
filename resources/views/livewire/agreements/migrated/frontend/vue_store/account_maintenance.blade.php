   {{-- resources/views/frontend/vue_store/app.blade.php --}}
   @extends('livewire.agreements.migrated.frontend.ngnstore.layouts.master')

   @section('title', 'Account Maintenance')

   @section('content')
       <div class="container">
           <div class="text-center my-5">
               <h1 class="display-4">ACCOUNT PAGE UNDER MAINTENANCE</h1>
               <p class="lead mb-5">Sorry for the inconvenience. Meanwhile, you can contact any of our stores.</p>
           </div>

           <div class="row">
               <div class="col-md-12">
                   <div class="title-section">
                       <h2 class="title">Our Locations</h2>
                       <p class="locations-text"><strong>Email:</strong> <a
                               href="mailto:enquiries@neguinhomotors.co.uk">enquiries@neguinhomotors.co.uk</a></p>
                   </div>
               </div>

               <div class="col-md-4 col-sm-12">
                   <div class="location-item">
                       <img loading="lazy" src="/img/catford.jpg" alt="Catford Location" class="location-image img-fluid mt-2">
                       <h3 class="location-subheading">Catford</h3>
                       <p class="locations-text">
                           <a href="https://www.google.com/maps?q=9-13+Unit+1179+Catford+Hill,+London+SE6+4NU"
                               target="_blank" rel="noopener noreferrer">
                               9-13 Unit 1179 Catford Hill, London SE6 4NU
                           </a>
                       </p>
                       <p class="locations-text"><strong>Phone:</strong> <a href="tel:02083141498">0208 314 1498</a>
                           (Catford)</p>
                   </div>
               </div>

               <div class="col-md-4 col-sm-12">
                   <div class="location-item">
                       <img loading="lazy" src="/img/tooting.jpg" alt="Tooting Location" class="location-image img-fluid mt-2">
                       <h3 class="location-subheading">Tooting</h3>
                       <p class="locations-text">
                           <a href="https://www.google.com/maps?q=4A+Penwortham+Road,+London+SW16+6RE" target="_blank"
                               rel="noopener noreferrer">
                               4A Penwortham Road, London SW16 6RE
                           </a>
                       </p>
                       <p class="locations-text"><strong>Phone:</strong> <a href="tel:02034095478">0203 409 5478</a>
                           (Tooting)</p>
                   </div>
               </div>

               <div class="col-md-4 col-sm-12">
                   <div class="location-item">
                       <img loading="lazy" src="/img/sutton.png" alt="Sutton Location" class="location-image img-fluid mt-2">
                       <h3 class="location-subheading">Sutton</h3>
                       <p class="locations-text">
                           <a href="https://www.google.com/maps?q=329+High+St,+Sutton+SM1+1LW" target="_blank"
                               rel="noopener noreferrer">
                               329 High St, Sutton SM1 1LW
                           </a>
                       </p>
                       <p class="locations-text"><strong>Phone:</strong> <a href="tel:02084129275">0208 412 9275</a>
                           (Sutton)</p>
                   </div>
               </div>
           </div>
       </div>
   @endsection
