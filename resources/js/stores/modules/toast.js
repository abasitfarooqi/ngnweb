import { defineStore } from 'pinia';
import { ref, nextTick } from 'vue';

// Toast Store for managing toast notifications
export const useToastStore = defineStore('toast', () => {
  const show = ref(false);
  const message = ref('');
  const type = ref('success');
  const duration = ref(3000);

  const triggerToast = (msg, toastType = 'success', toastDuration = 3000) => {
    // Reset any existing toast first
    show.value = false;
    
    // Use nextTick to ensure the DOM updates
    nextTick(() => {
      message.value = msg;
      type.value = toastType;
      duration.value = toastDuration;
      show.value = true;
      
      // Auto-hide the toast
      setTimeout(() => {
        show.value = false;
      }, toastDuration);
    });
  };

  return { show, message, type, duration, triggerToast };
}); 