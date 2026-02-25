<template>
  <DefaultLayout>
    <div class="container bt5 py-3 px-4">
      <!-- Page Header with Breadcrumb -->
      <nav class="breadcrumb mb-4">
        <router-link to="/shop" class="breadcrumb-item">Back to Shop</router-link>
        <span class="breadcrumb-item active">Cancellation Policy</span>
      </nav>
      
      <div class="row">
        
        
        <!-- Content Area -->
        <main class="col-md-9">
          <h3 class="h4 mb-3 ngn-store-title-lg font-three ngn-active-color aos-init" data-aos="fade-right">Cancellation Policy</h3>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">1. Rights and Obligations</h4>
            <p class="ngn-store-paragraph">
              By purchasing or ordering goods and/or services from Neguinho Motors, you agree to be bound by the following terms and conditions. Please read them carefully before placing an order.
            </p>
            <ul class="ngn-store-list">
              <li>1.1 You undertake to pay any amounts due in a timely manner.</li>
              <li>1.2 All personal information provided must be accurate and up-to-date.</li>
              <li>1.3 You agree not to impersonate any other person or entity.</li>
              <li>1.4 Neguinho Motors reserves the right to modify prices, content, or withdraw any goods or services.</li>
              <!-- ...(about 10 lines omitted)... -->
            </ul>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">2. Orders</h4>
            <p class="ngn-store-paragraph">
              Orders are treated as offers to purchase goods and/or services. The contract is completed when we dispatch the goods or commence the provision of services.
            </p>
            <ul class="ngn-store-list">
              <li>2.1 Goods and services are available to eligible individuals residing in the UK.</li>
              <li>2.2 You must provide accurate personal information as requested.</li>
              <li>2.3 Each order is an offer that is accepted upon dispatch or commencement of services.</li>
              <!-- ...(about 5 lines omitted)... -->
            </ul>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">3. Price and Payment</h4>
            <p class="ngn-store-paragraph">
              All prices are listed in UK pounds (£) and include VAT unless stated otherwise. The total cost includes the price of products, delivery charges, and any additional services selected.
            </p>
            <ul class="ngn-store-list">
              <li>3.1 Payment is processed once the order is submitted.</li>
              <li>3.2 We may use identity verification partners to prevent fraudulent activities.</li>
              <li>3.3 If a transaction is flagged as potentially fraudulent, the order may be canceled.</li>
              <!-- ...(about 2 lines omitted)... -->
            </ul>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">4. Cancellation, Returns, and Exchanges</h4>
            <p class="ngn-store-paragraph">
              You may return items within 30 days of purchase if they are unused and in their original packaging, accompanied by proof of purchase. Returns can be made in-store or through our website.
            </p>
            <p class="ngn-store-paragraph">
              <strong>Cancellation Period:</strong> You have 14 calendar days from the day after delivery to cancel your order. Refunds will be processed once the returned goods are received and inspected.
            </p>
            <p class="ngn-store-paragraph">
              <strong>Non-Refundable Items:</strong> Certain items, such as software activation keys and worn accessories, are non-refundable.
            </p>
            <p class="ngn-store-paragraph">
              <strong>Faulty Goods:</strong> If an item is faulty, contact us within 7 days of receipt. We will arrange for repair, replacement, or refund as appropriate.
            </p>
            <!-- ...(about 5 lines omitted)... -->
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">5. Your Personal Information</h4>
            <p class="ngn-store-paragraph">
              Your data protection rights are outlined in our Privacy Policy, which details how we collect and use your personal information.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">6. Terms Applicable Only to Promotional Items</h4>
            <p class="ngn-store-paragraph">
              Promotional items received for free or at a discount when entering into a network contract or subscription services are subject to specific terms.
            </p>
            <!-- ...(about 5 lines omitted)... -->
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">7. Terms Applicable to Cash-back Deals</h4>
            <p class="ngn-store-paragraph">
              Cash-back deals offering financial incentives or rebates are subject to additional conditions.
            </p>
            <!-- ...(about 5 lines omitted)... -->
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">8. Limitation of Liability</h4>
            <p class="ngn-store-paragraph">
              Neguinho Motors is not liable for any indirect, incidental, or consequential damages arising from the use of our products or services. Our liability is limited to the purchase price of the products.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">9. General</h4>
            <p class="ngn-store-paragraph">
              These terms and conditions are governed by English law. Any disputes arising from these terms will be subject to the exclusive jurisdiction of the English courts.
            </p>
            <!-- ...(about 5 lines omitted)... -->
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">10. Handling Complaints and Sending Notices</h4>
            <p class="ngn-store-paragraph">
              If you wish to make a complaint, please contact us at:
            </p>
            <ul class="ngn-store-list">
              <li>Phone: 0208 314 1498</li>
              <li>Email: <a href="mailto:enquiries@neguinhomotors.co.uk">enquiries@neguinhomotors.co.uk</a></li>
              <li>Address: Neguinho Motors LTD, 9-13 Catford Hill, London, England, SE6 4NU</li>
            </ul>
            <p class="ngn-store-paragraph">
              Notices must be sent to the above address. Proof of sending does not guarantee receipt; ensure you receive an acknowledgment from us.
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