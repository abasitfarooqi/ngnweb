<template>
  <DefaultLayout>
    <div class="container bt5 py-3 px-4">
      <!-- Page Header with Breadcrumb -->
      <nav class="breadcrumb mb-4">
        <router-link to="/shop" class="breadcrumb-item">Back to Shop</router-link>
        <span class="breadcrumb-item active">Accessibility Statement</span>
      </nav>
      
      <div class="row">
        
        
        <!-- Content Area -->
        <main class="col-md-9">
          <h3 class="h4 mb-3 ngn-store-title-lg font-three ngn-active-color aos-init" data-aos="fade-right">Accessibility Statement</h3>
          
          <section class="mt-4">
            <p class="ngn-store-paragraph">
              Neguinho Motors is committed to ensuring digital accessibility for people with disabilities. We strive to comply with the Web Content Accessibility Guidelines (WCAG) 2.1 Level AA. Where required, we provide alternative text for images, sufficient contrast for text, and ensure keyboard navigability throughout our website.
            </p>
            <p class="ngn-store-paragraph">
              If you experience any difficulties accessing our website or have suggestions on how we can improve the accessibility of our services, please contact our customer support team. We are dedicated to making our website as accessible as possible for all users.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">Accessibility Features</h4>
            <ul class="ngn-store-list">
              <li>Keyboard navigable interface</li>
              <li>Screen reader compatibility</li>
              <li>Adjustable text sizes</li>
              <li>Alternative text for all images</li>
              <li>High contrast mode available</li>
              <li>Accessible forms and interactive elements</li>
            </ul>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">Feedback and Contact Information</h4>
            <p class="ngn-store-paragraph">
              We welcome your feedback on the accessibility of Neguinho Motors' website. Please let us know if you encounter accessibility barriers:
            </p>
            <ul class="ngn-store-list">
              <li>Email: <a href="mailto:enquiries@neguinhomotors.co.uk">enquiries@neguinhomotors.co.uk</a></li>
              <li>Phone: <a href="tel:0208-314-1498">0208 314 1498</a></li>
            </ul>
            <p class="ngn-store-paragraph">
              We try to respond to feedback within 2 business days.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">Technical Specifications</h4>
            <p class="ngn-store-paragraph">
              Accessibility of Neguinho Motors' website relies on the following technologies to work with the particular combination of web browser and any assistive technologies or plugins installed on your computer:
            </p>
            <ul class="ngn-store-list">
              <li>HTML</li>
              <li>CSS</li>
              <li>JavaScript</li>
              <li>Web Browser (e.g., Chrome, Firefox, Safari, Edge)</li>
            </ul>
            <p class="ngn-store-paragraph">
              These technologies are relied upon for conformance with the accessibility standards used.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">Compliance Status</h4>
            <p class="ngn-store-paragraph">
              The Web Content Accessibility Guidelines define requirements for designers and developers to improve accessibility for people with disabilities. It defines three levels of conformance: Level A, Level AA, and Level AAA. Neguinho Motors is partially conformant with WCAG 2.1 level AA. Partially conformant means that some parts of the content do not fully conform to the accessibility standard.
            </p>
            <p class="ngn-store-paragraph">
              While Neguinho Motors strives to ensure all content is accessible, some content may not fully comply with WCAG 2.1 accessibility standards.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">Enforcement Procedure</h4>
            <p class="ngn-store-paragraph">
              Compliant organisations are required to follow the accessibility standards set out in the Web Content Accessibility Guidelines (WCAG). However, no entity is formally responsible for enforcing the accessibility requirements. If you are dissatisfied with Neguinho Motors' response to your feedback, you can contact the Information Commissioner’s Office.
            </p>
            <p class="ngn-store-paragraph">
              More information:
              <a href="https://www.ico.org.uk/concerns">https://www.ico.org.uk/concerns</a>
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">Known Accessibility Issues</h4>
            <p class="ngn-store-paragraph">
              As we are actively improving the accessibility of our website, some content may not fully conform to WCAG 2.1 level AA at this time. We are continually working to address these issues and enhance the overall accessibility of our website.
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