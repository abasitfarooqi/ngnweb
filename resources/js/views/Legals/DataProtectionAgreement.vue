<template>
  <DefaultLayout>
    <div class="container bt5 py-3 px-4">
      <!-- Page Header with Breadcrumb -->
      <nav class="breadcrumb mb-4">
        <router-link to="/shop" class="breadcrumb-item">Back to Shop</router-link>
        <span class="breadcrumb-item active">Data Protection Agreement</span>
      </nav>
      
      <div class="row">
        
        
        <!-- Content Area -->
        <main class="col-md-9">
          <h3 class="h4 mb-3 ngn-store-title-lg font-three ngn-active-color aos-init" data-aos="fade-right">Data Protection Agreement</h3>
          
          <section class="mt-4">
            <p class="ngn-store-paragraph">
              At Neguinho Motors LTD, we are committed to protecting your personal data and respecting your privacy. This Data Protection Agreement outlines how we collect, use, store, and protect your information in compliance with the General Data Protection Regulation (GDPR) and other relevant data protection laws.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">1. Information We Collect</h4>
            <p class="ngn-store-paragraph">
              We may collect and process the following types of personal data:
            </p>
            <ul class="ngn-store-list">
              <li>Personal Identification Information: Name, address, email address, phone number.</li>
              <li>Transactional Information: Details of products or services purchased, order history.</li>
              <li>Technical Data: IP address, browser type, device information.</li>
              <li>Communication Data: Records of your interactions with our customer service.</li>
            </ul>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">2. How We Use Your Information</h4>
            <p class="ngn-store-paragraph">
              We use your personal data for the following purposes:
            </p>
            <ul class="ngn-store-list">
              <li>Order Processing: To fulfill and manage your orders.</li>
              <li>Customer Support: To provide assistance and respond to your inquiries.</li>
              <li>Marketing Communications: To send you promotional materials and updates (you can opt-out at any time).</li>
              <li>Improving Services: To enhance our website and services based on your feedback and usage patterns.</li>
              <li>Compliance: To comply with legal obligations and protect our rights.</li>
            </ul>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">3. Sharing Your Information</h4>
            <p class="ngn-store-paragraph">
              We do not sell, trade, or otherwise transfer your personal data to outside parties except in the following circumstances:
            </p>
            <ul class="ngn-store-list">
              <li>Service Providers: Trusted third parties who assist us in operating our website, conducting our business, or servicing you, provided they agree to keep your information confidential.</li>
              <li>Legal Requirements: When required by law or to protect our rights.</li>
              <li>Business Transfers: In the event of a merger, acquisition, or sale of assets.</li>
            </ul>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">4. Data Security</h4>
            <p class="ngn-store-paragraph">
              We implement a variety of security measures to maintain the safety of your personal information. These measures include secure servers, encryption, and access controls to protect against unauthorized access, alteration, disclosure, or destruction of your data.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">5. Your Rights</h4>
            <p class="ngn-store-paragraph">
              You have the following rights regarding your personal data:
            </p>
            <ul class="ngn-store-list">
              <li>Access: Request access to your personal data.</li>
              <li>Rectification: Request correction of inaccurate or incomplete data.</li>
              <li>Erasure: Request deletion of your data under certain conditions.</li>
              <li>Restriction: Request restriction of processing your data.</li>
              <li>Data Portability: Receive your data in a structured, commonly used format.</li>
              <li>Objection: Object to the processing of your data for specific purposes.</li>
            </ul>
            <p class="ngn-store-paragraph">
              To exercise any of these rights, please contact us using the details provided below.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">6. Cookies and Tracking Technologies</h4>
            <p class="ngn-store-paragraph">
              Our website uses cookies and similar tracking technologies to enhance your browsing experience, analyze site traffic, and understand where our visitors are coming from. You can control the use of cookies through your browser settings.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">7. Changes to This Agreement</h4>
            <p class="ngn-store-paragraph">
              We may update this Data Protection Agreement from time to time. Any changes will be posted on this page, and we encourage you to review it periodically to stay informed about how we are protecting your information.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">8. Contact Us</h4>
            <p class="ngn-store-paragraph">
              If you have any questions or concerns about this Data Protection Agreement or our data practices, please contact us at:
            </p>
            <ul class="ngn-store-list">
              <li>Phone: 0208 314 1498</li>
              <li>Email: <a href="mailto:enquiries@neguinhomotors.co.uk">enquiries@neguinhomotors.co.uk</a></li>
              <li>Address: Neguinho Motors LTD, 9-13 Catford Hill, London, England, SE6 4NU</li>
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
</style>