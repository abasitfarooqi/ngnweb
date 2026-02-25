<template>
  <DefaultLayout>
    <div class="container bt5 py-3 px-4">
      <!-- Page Header with Breadcrumb -->
      <nav class="breadcrumb mb-4">
        <router-link to="/shop" class="breadcrumb-item">Back to Shop</router-link>
        <span class="breadcrumb-item active">Terms and Conditions</span>
      </nav>
      
      <div class="row">
        
        <!-- Content Area -->
        <main class="col-md-9">
          <h3 class="h4 mb-3 ngn-store-title-lg font-three ngn-active-color aos-init" data-aos="fade-right">Terms and Conditions</h3>
          
          <section class="mt-4">
            <p class="ngn-store-paragraph">
              Welcome to Neguinho Motors! By placing an order with us, you agree to the following terms and conditions. Please take a few minutes to read through our policies to ensure a smooth and enjoyable shopping experience.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">1. Our Contract After You Have Ordered</h4>
            <p class="ngn-store-paragraph">
              If you make any mistakes during your order, you can correct them until you confirm payment. Once you’ve submitted your order, you’ll receive an Order Reference Number and details of the products or services you’ve ordered. An email titled ‘Order Acknowledgement’ will confirm receipt of your order, followed by an ‘Order Confirmation’ email outlining delivery details. If we need to cancel any part of your order, we will notify you via email.
            </p>
            <p class="ngn-store-paragraph">
              For phone orders without an email address, your Order Reference Number will be provided over the phone, and a copy of your receipt will be mailed to you.
            </p>
            <p class="ngn-store-paragraph">
              We strive to ensure all product details, descriptions, and prices are accurate at the time of entry into our system. In the event of an unintentional error, we may cancel your order and issue a full refund.
            </p>
            <p class="ngn-store-paragraph">
              If you choose our Reserve and Collect service, you will receive a reference number and order details. Payment will only be required upon collection.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">2. Prices and Payments</h4>
            <p class="ngn-store-paragraph">
              All prices are listed in UK pounds (£) and include VAT unless stated otherwise. The total cost of your order includes the price of the products, delivery charges (if applicable), and any additional services selected, such as premium delivery or installation. These costs will be visible in your Shopping Basket before you submit your order. Payment is processed once the order is submitted.
            </p>
            <p class="ngn-store-paragraph">
              We may use information from our accredited identity verification partners to protect against fraudulent activities. If a transaction is flagged as potentially fraudulent, we may cancel your order and notify you accordingly.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">3. Restrictions - Age Requirements and Order Limits</h4>
            <p class="ngn-store-paragraph">
              Certain products have minimum age requirements (e.g., motorcycle accessories). By ordering, you confirm that you meet these requirements. Additionally, some products may have purchase limits per customer within a 90-day period. These restrictions will be indicated on the product display pages.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">4. Delivery and Installation Services</h4>
            <p class="ngn-store-paragraph">
              Detailed information about our delivery options, installation services, and recycling can be found in our Help & Services section. We ensure safe disconnection of old appliances, especially gas and dual fuel cookers, by qualified professionals.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">5. Returns, Cancellations, and Exchanges</h4>
            <p class="ngn-store-paragraph">
              You may return items within 30 days of purchase if they are unused and in their original packaging, accompanied by proof of purchase. Returns can be made in-store or through our website. For online-only purchases, specific terms apply as outlined below.
            </p>
            <p class="ngn-store-paragraph">
              Cancellation Period: You have 14 calendar days from the day after delivery to cancel your order. Refunds will be processed once the returned goods are received and inspected.
            </p>
            <p class="ngn-store-paragraph">
              Non-Refundable Items: Certain items, such as software activation keys and worn accessories, are non-refundable.
            </p>
            <p class="ngn-store-paragraph">
              Faulty Goods: If an item is faulty, contact us within 7 days of receipt. We will arrange for repair, replacement, or refund as appropriate.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">6. Limitation of Liability</h4>
            <p class="ngn-store-paragraph">
              Neguinho Motors is not liable for any indirect, incidental, or consequential damages arising from the use of our products or services. Our liability is limited to the purchase price of the products.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">7. Governing Law</h4>
            <p class="ngn-store-paragraph">
              These terms and conditions are governed by English law. Any disputes arising from these terms will be subject to the exclusive jurisdiction of the English courts.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">8. Handling Complaints and Sending Notices</h4>
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