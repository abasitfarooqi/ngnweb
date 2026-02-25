<template>
  <DefaultLayout>
    <div class="container bt5 py-3 px-4">
      <!-- Page Header with Breadcrumb -->
      <nav class="breadcrumb mb-4">
        <router-link to="/shop" class="breadcrumb-item">Back to Shop</router-link>
        <span class="breadcrumb-item active">Payment Policy</span>
      </nav>
      
      <div class="row">
        
        <!-- Content Area -->
        <main class="col-md-9">
          <h3 class="h4 mb-3 ngn-store-title-lg font-three ngn-active-color aos-init" data-aos="fade-right">Payment Policy</h3>
          
          <section class="mt-4">
            <p class="ngn-store-paragraph">
              At Neguinho Motors LTD, we strive to provide a secure and seamless payment experience for our customers. This Payment Policy outlines the accepted payment methods, security measures, and terms related to payments and billing.
            </p>
            <p class="ngn-store-paragraph">
              By placing an order with us, you agree to the terms outlined in this policy. Please read it carefully to understand our practices regarding your payment information.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">1. Accepted Payment Methods</h4>
            <p class="ngn-store-paragraph">
              We offer a variety of payment options to cater to your preferences:
            </p>
            <ul class="ngn-store-list">
              <li><strong>Credit/Debit Cards:</strong> We accept major credit and debit cards, including Visa, MasterCard, American Express, and Discover.</li>
              <!-- <li><strong>PayPal:</strong> Secure payments through your PayPal account.</li> -->
              <li><strong>Bank Transfers:</strong> Direct bank transfers for larger transactions.</li>
              <li><strong>Klarna:</strong>  We accept through Klarna Pay.</li>
              <!-- <li><strong>Mobile Payments:</strong> Compatible with Apple Pay, Google Pay, and other mobile payment solutions.</li> -->
              <li><strong>Financing Options:</strong> Flexible payment plans available through our partnered financial institutions.</li>
            </ul>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">2. Payment Security</h4>
            <p class="ngn-store-paragraph">
              Your security is our top priority. We implement robust security measures to protect your payment information:
            </p>
            <ul class="ngn-store-list">
              <li><strong>Encryption:</strong> All payment transactions are encrypted using industry-standard SSL technology.</li>
              <li><strong>PCI DSS Compliance:</strong> We adhere to the Payment Card Industry Data Security Standard (PCI DSS) to ensure the secure handling of card information.</li>
              <li><strong>Fraud Prevention:</strong> Advanced fraud detection systems monitor transactions to prevent unauthorized activities.</li>
              <li><strong>Secure Storage:</strong> Sensitive payment data is stored securely, and access is restricted to authorized personnel only.</li>
            </ul>
            <p class="ngn-store-paragraph">
              Additionally, we do not store your payment information on our servers. All transactions are processed through secure, third-party payment gateways.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">3. Billing Information</h4>
            <p class="ngn-store-paragraph">
              Accurate billing information is essential for processing your orders efficiently. Please ensure that the billing details provided are correct:
            </p>
            <ul class="ngn-store-list">
              <li><strong>Billing Address:</strong> The address associated with your payment method must match the address provided during checkout.</li>
              <li><strong>Contact Information:</strong> Provide a valid email address and phone number for billing notifications and receipts.</li>
              <li><strong>Cardholder Authorization:</strong> By providing your payment information, you authorize Neguinho Motors LTD to charge the specified amount for your purchase.</li>
            </ul>
            <p class="ngn-store-paragraph">
              If your billing information is incorrect or incomplete, it may result in delays or cancellation of your order.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">4. Payment Terms</h4>
            <p class="ngn-store-paragraph">
              Please review the following terms related to payments and billing:
            </p>
            <ul class="ngn-store-list">
              <li><strong>Payment Processing:</strong> Payments are processed immediately upon order confirmation.</li>
              <li><strong>Full Payment Requirement:</strong> Orders must be paid in full before dispatch. Partial payments are not accepted unless arranged through our financing options.</li>
              <li><strong>Currency:</strong> All transactions are conducted in British Pounds (GBP).</li>
              <li><strong>Recurring Payments:</strong> For subscription-based services, recurring payments will be processed as per the agreed schedule unless canceled.</li>
            </ul>
            <p class="ngn-store-paragraph">
              We reserve the right to refuse or cancel any order placed through the website for reasons including but not limited to: product or service availability, errors in the description or price of the product or service, error in your order, or other reasons.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">5. Refunds and Chargebacks</h4>
            <p class="ngn-store-paragraph">
              In the event of a refund or chargeback, the following policies apply:
            </p>
            <ul class="ngn-store-list">
              <li><strong>Refunds:</strong> Refunds will be processed to the original payment method within 10 business days after approval.</li>
              <li><strong>Chargebacks:</strong> If you initiate a chargeback, we will cooperate with your financial institution to resolve the issue. Unauthorized chargebacks may result in account suspension.</li>
              <li><strong>Disputed Transactions:</strong> Please contact our customer service team immediately if you dispute a transaction. We aim to resolve disputes promptly and amicably.</li>
            </ul>
            <p class="ngn-store-paragraph">
              For refunds related to returns or cancellations, please refer to our <router-link to="/legals/refund-policy">Refund Policy</router-link>.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">6. Failed Transactions</h4>
            <p class="ngn-store-paragraph">
              Occasionally, payment transactions may fail due to insufficient funds, expired cards, or other issues. In such cases:
            </p>
            <ul class="ngn-store-list">
              <li><strong>Notification:</strong> You will receive an email notification if a transaction fails.</li>
              <li><strong>Retry Attempts:</strong> We may attempt to process the payment again after a short period.</li>
              <li><strong>Order Cancellation:</strong> If the payment remains unresolved, your order may be canceled. You will be notified accordingly.</li>
            </ul>
            <p class="ngn-store-paragraph">
              To prevent failed transactions, please ensure that your payment information is up-to-date and that sufficient funds are available.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">7. Changes to Payment Policy</h4>
            <p class="ngn-store-paragraph">
              We reserve the right to update this Payment Policy from time to time. Any changes will be posted on this page, and we encourage you to review it periodically to stay informed about our payment practices.
            </p>
            <p class="ngn-store-paragraph">
              Continued use of our services after any changes indicates your acceptance of the updated Payment Policy.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">8. Contact Us</h4>
            <p class="ngn-store-paragraph">
              If you have any questions or concerns about our Payment Policy, please contact us at:
            </p>
            <ul class="ngn-store-list">
              <li><strong>Phone:</strong> 0208 314 1498</li>
              <li><strong>Email:</strong> <a href="mailto:enquiries@neguinhomotors.co.uk">enquiries@neguinhomotors.co.uk</a></li>
              <li><strong>Address:</strong> Neguinho Motors LTD, 9-13 Catford Hill, London, England, SE6 4NU</li>
            </ul>
            <p class="ngn-store-paragraph">
              We will respond to your inquiry as soon as possible.
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

/* Additional styles for better presentation */
.breadcrumb-item + .breadcrumb-item::before {
  content: ">";
}
</style>