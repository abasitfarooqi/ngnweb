<!-- File: resources/js/components/ui/Toast.vue -->

<template>
  <Transition name="toast-fade">
    <div v-if="show" class="toast-container">
      <div class="toast-backdrop"></div>
      <div :class="`font-three fw-600 ls-1 uppercase toast toast-${type}`">
        <!-- Centered content wrapper with full width -->
        <div class="w-full flex flex-col items-center gap-2">
          <!-- Icon centered -->
          <div class="w-full flex justify-center">
            <span class="icon">
              <svg v-if="type === 'success'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <svg v-else-if="type === 'error'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
              <svg v-else-if="type === 'warning'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <svg v-else-if="type === 'info'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
              </svg>
            </span>
          </div>
          <!-- Message centered -->
          <span class="text-center w-full">{{ message }}</span>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { computed, watch } from 'vue';
import { useToastStore } from '@/stores/modules/toast';

const toastStore = useToastStore();

const show = computed(() => {
  return toastStore.show;
});

const message = computed(() => toastStore.message);
const type = computed(() => toastStore.type);
const duration = computed(() => toastStore.duration);

// Automatically hide the toast after the specified duration
watch(show, (newVal) => {
  if (newVal) {
    setTimeout(() => {
      toastStore.show = false;
    }, duration.value);
  }
});
</script>

<style scoped>
.toast-container {
  @apply fixed inset-0 flex items-center justify-center z-[9999];
}

.toast-backdrop {
  @apply fixed inset-0 bg-black/10 backdrop-blur-sm;
}

.toast {
  @apply relative flex items-center justify-center bg-gray-800 text-white px-6 py-4 shadow-lg;
  min-width: 200px;
  /* Ensure minimum width for better appearance */
}

/* Toast Animation */
.toast-fade-enter-active,
.toast-fade-leave-active {
  transition: all 0.3s ease;
}

.toast-fade-enter-from,
.toast-fade-leave-to {
  opacity: 0.2;
}

.toast-fade-enter-from .toast {
  transform: scale(0.9);
}

.toast-fade-leave-to .toast {
  transform: scale(0.9);
}

.toast-success {
  background-color: #19b261;
  /* Green */
}

.toast-error {
  background-color: #e53e3e;
  /* Red */
}

.toast-warning {
  background-color: #dd6b20;
  /* Orange */
}

.toast-info {
  background-color: #3182ce;
  /* Blue */
}

.icon {
  @apply flex justify-center items-center;
}
</style>