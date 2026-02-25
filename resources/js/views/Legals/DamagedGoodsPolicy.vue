<template>
  <DefaultLayout>
    <div class="container bt5 py-3 px-4">
      <!-- Page Header with Breadcrumb -->
      <nav class="breadcrumb mb-4">
        <router-link to="/shop" class="breadcrumb-item">Back to Shop</router-link>
        <span class="breadcrumb-item active">Damaged Goods Policy</span>
      </nav>
      
      <div class="row">
        
        <!-- Content Area -->
        <main class="col-md-9">
          <h3 class="h4 mb-3 ngn-store-title-lg font-three ngn-active-color aos-init" data-aos="fade-right">Damaged Goods Policy</h3>
          
          <section class="mt-4">
            <p class="ngn-store-paragraph">
              Neguinho Motors is dedicated to ensuring that you are completely satisfied with your purchase. In the rare event that you receive a damaged or defective product, please follow our damaged goods policy outlined below.
            </p>
            <p class="ngn-store-paragraph">
              If you receive a product that is damaged or defective, you must report it to our customer service team within 7 days of receipt. Please provide your order reference number, details of the damage, and, if possible, photographic evidence.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">Reporting Damaged or Defective Goods</h4>
            <p class="ngn-store-paragraph">
              To report a damaged or defective product, please contact us via:
            </p>
            <ul class="ngn-store-list">
              <li>Phone: 0208 314 1498</li>
              <li>Email: <a href="mailto:enquiries@neguinhomotors.co.uk">enquiries@neguinhomotors.co.uk</a></li>
            </ul>
            <p class="ngn-store-paragraph">
              Please include your order reference number and a detailed description of the issue.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">Resolution Methods</h4>
            <p class="ngn-store-paragraph">
              Upon verification of the damage or defect, we will offer one of the following resolution methods:
            </p>
            <ul class="ngn-store-list">
              <li>Replacement of the faulty product</li>
              <li>Repair of the damaged item</li>
              <li>Full refund of the purchase price</li>
            </ul>
            <p class="ngn-store-paragraph">
              The choice of resolution method will depend on the nature of the damage or defect and the availability of replacement parts or products.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">Return Process</h4>
            <p class="ngn-store-paragraph">
              If a return is required, we will provide you with instructions on how to return the damaged or defective product to us. Please ensure that the product is securely packaged to prevent further damage during transit.
            </p>
            <p class="ngn-store-paragraph">
              Return Address:
              <br />
              Neguinho Motors LTD
              <br />
              9-13 Catford Hill
              <br />
              London, England, SE6 4NU
            </p>
            <p class="ngn-store-paragraph">
              Please note that you are responsible for the cost of returning the product unless the damage or defect was our fault.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">Timeframe for Resolutions</h4>
            <p class="ngn-store-paragraph">
              We aim to resolve all damaged or defective goods claims promptly. Once the returned product is received and inspected, you will be notified of the approval or rejection of your refund or replacement.
            </p>
            <p class="ngn-store-paragraph">
              - Refunds: Processed within 10 business days after approval.
              <br />
              - Replacements: Shipped within 7 business days after approval.
            </p>
          </section>
        </main>

        <!-- Aside Sidebar -->
        <LegalSidebar />
        
      </div>
    </div>
  </DefaultLayout>
</template>

<script setup>
import LegalSidebar from '@/components/legals/LegalSidebar.vue';
import DefaultLayout from '@/components/layout/DefaultLayout.vue';
</script>

<style scoped>
.bt5 {
  /* Bootstrap 5 specific styles if needed */
}
</style>