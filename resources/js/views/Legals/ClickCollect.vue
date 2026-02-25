<template>
  <DefaultLayout>
    <div class="container bt5 py-3 px-4">
      <!-- Page Header with Breadcrumb -->
      <nav class="breadcrumb mb-4">
        <router-link to="/shop" class="breadcrumb-item">Back to Shop</router-link>
        <span class="breadcrumb-item active">Click & Collect</span>
      </nav>
      
      <div class="row">
        
        <!-- Content Area -->
        <main class="col-md-9">
          <h3 class="h4 mb-3 ngn-store-title-lg font-three ngn-active-color aos-init" data-aos="fade-right">Click & Collect</h3>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">How Does Click & Collect Work?</h4>
            <p class="ngn-store-paragraph">
              When you choose our Click & Collect option at checkout, you'll have the opportunity to select your local Neguinho Motors store to collect your order. Collection times may vary depending on item availability in-store. Items marked as available for collection today are in stock at your chosen store. Those indicating availability within 2-3 working days or 3-5 working days will need to be ordered in. You can also verify in-store stock availability for a product before adding it to your basket and proceeding to checkout.
            </p>
            <p class="ngn-store-paragraph">
              After placing your order using the Click & Collect option, you will receive an email confirmation detailing your selected collection store, the items you've ordered, and their respective collection dates. Please wait to visit your local store until you receive a notification confirming that your item(s) are ready for collection.
            </p>
            <p class="ngn-store-paragraph">
              Your selected store will process your order accordingly. Items available for same-day collection will be reserved for you, and you'll receive another confirmation once they're ready. Items that need to be ordered in will also be processed, and you'll receive a separate notification informing you when they're ready for collection. You may choose to collect all items once they're available or pick them up individually as they become ready. We will hold your items for 14 days.
            </p>
            <p class="ngn-store-paragraph">
              When collecting your order from the store, please bring a form of identification and the card used for online payment. Acceptable forms of ID include your driving licence or passport. We will not release goods without proper identification.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">How Do I Return My Click & Collect Order?</h4>
            <p class="ngn-store-paragraph">
              Similar to our online returns process, you can return your goods to any Neguinho Motors store or via our website. If you encounter any issues with your item(s) or find them unsuitable, we are committed to resolving the matter for you. Ensure you have your proof of purchase when returning goods to any Neguinho Motors store.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">Returning via Online</h4>
            <p class="ngn-store-paragraph">
              Learn more about how to return items online on our <router-link to="/refund-policy">Delivery & Returns</router-link> page.
            </p>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">Terms & Conditions for Click & Collect Returns</h4>
            
            <h5>Unwanted Items:</h5>
            <p class="ngn-store-paragraph">
              Unused goods that are either unsuitable or have been purchased from neguinhomotors.co.uk in error may be returned for an exchange or refund within 90 days of receipt.
            </p>
            <ol>
              <li>Goods must be returned to us in a new, unused, and re-saleable condition (including all packaging, tags, manuals, and keys). If the goods are not in this condition, we reserve the right to refuse a refund or deduct up to 50% of the purchase price from the refund amount.</li>
              <li>Any goods returned that have not been organised through our online team are done so at your own expense and risk. We recommend using a recorded delivery service, as we will not accept responsibility for goods “lost in transit”.</li>
              <li>If a refund is required, we will refund the price of the goods only. We will not refund any delivery charges you may have paid.</li>
              <li>PLEASE NOTE: Refunds will not be issued by the store but by Neguinho Motors Online. We will issue a refund once we have received and assessed the condition of the returned goods.</li>
              <li>If an exchange is required, we will dispatch the new item(s) once we have received the returned item(s). Any difference in price between the new and old product will be borne by you or refunded by us before your new product is dispatched.</li>
              <li>The return is free of charge for UK residents only (excluding Highlands and Islands). For any orders outside of the UK, return postage is your responsibility.</li>
              <li>For your confidence and safety, we will not accept refunds or exchanges for any returned helmet if the item(s) have been worn on a motorcycle or if the visor sticker has been removed.</li>
            </ol>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">Faulty or Wrongly Supplied Goods</h4>
            <p class="ngn-store-paragraph">
              Goods supplied incorrectly or faulty must be reported within 7 days of receipt by phone at <a href="tel:0208-314-1498">0208 314 1498</a> or by email at <a href="mailto:enquiries@neguinhomotors.co.uk">enquiries@neguinhomotors.co.uk</a>.
            </p>
            <ol>
              <li>If you need to return goods and have not organised a free return through our online team, please use a recorded delivery service. We will not accept responsibility for goods “lost in transit”.</li>
              <li>For heavy, bulky, or large goods, we can arrange collection for you. Please call our online team to organise a free return.</li>
              <li>Goods can also be returned to any Neguinho Motors store with proof of purchase.</li>
            </ol>
          </section>
          
          <section class="mt-4">
            <h4 class="ngn-store-title-md font-two ngn-active-color">Warranty Returns</h4>
            <p class="ngn-store-paragraph">
              If an item you purchased from neguinhomotors.co.uk develops a fault/problem within the warranty period, please call <a href="tel:0208-314-1498">0208 314 1498</a> or email <a href="mailto:enquiries@neguinhomotors.co.uk">enquiries@neguinhomotors.co.uk</a> to discuss the best way to rectify the issue.
            </p>
            <ol>
              <li>If an item has an obvious fault, we will do our best to repair, replace, or refund it within 7 days of receiving the item.</li>
              <li>If the fault is not obvious, we will need to send the item to the supplier or manufacturer for testing. Please note that this process can sometimes take up to 4 weeks, depending on the item and the nature of the fault.</li>
              <li>If the item is found to be faulty, it will be repaired, replaced, or refunded. Please arrange a free return with us online. The return is free of charge for UK residents only (excluding Highlands and Islands). For warranties outside of the UK, return postage is your responsibility.</li>
              <li>If the fault is due to misuse, damage sustained after purchase, or normal wear and tear, we will contact you to determine whether you would like the item returned or repaired if a repair is possible. In such cases, the cost of repair and return postage will be your responsibility.</li>
            </ol>
            <p class="ngn-store-paragraph">
              Please see all <router-link to="/warranty-periods">warranty periods here</router-link>.
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