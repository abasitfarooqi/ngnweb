// File: resources/js/stores/modules/shipping.js
import { defineStore } from 'pinia';

export const useShippingStore = defineStore('shipping', {
  state: () => ({
    method: '',
    homeDelivery: {
      addressLine1: '',
      addressLine2: '',
      city: '',
      postalCode: '',
    },
    storePickup: null,
  }),
  actions: {
    setShippingMethod(method) {
      this.method = method;
    },
    async setHomeDeliveryAddress(address) {
      this.homeDelivery = address;
      // Example API call to save address
      await api.post('/shipping/home-delivery', address);
    },
    setStorePickup(store) {
      this.storePickup = store;
      // Example API call to set store pickup
      api.post('/shipping/collect-in-store', { storeId: store.id });
    },
  },
});