   <!-- File: resources/js/components/ui/Button.vue -->
   <template>
    <button
      :class="[buttonClasses, 'bg-ngn-primary hover:bg-ngn-primary-1000 transition-colors', additionalClasses]"
      :disabled="disabled"
      @click="handleClick"
      v-bind="$attrs"
    >
      <slot></slot>
    </button>
  </template>

  <script setup lang="ts">
  import { computed } from 'vue';

  const props = defineProps({
    variant: {
      type: String,
      default: 'primary',
      validator: (value: string) => ['primary', 'secondary', 'icon', 'success', 'danger', 'warning', 'ngn-invert'].includes(value),
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    additionalClasses: {
      type: String,
      default: '',
    },
  });

  const emit = defineEmits(['click']);

  const handleClick = () => {
    emit('click');
  };

  const baseClasses = 'items-center px-2 py-2 bg-ngn-primary-500 hover:bg-ngn-primary-600 text-white btn-ngn-effect';
  
  const variantClasses = {
    primary: 'bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500',
    secondary: 'bg-secondary-600 text-white hover:bg-secondary-700 focus:ring-secondary-500',
  
  };

  const buttonClasses = computed(() => {
    return `${baseClasses} ${variantClasses[props.variant]}`;
  });
  </script>

  <style scoped>
  /* Scoped styles if necessary */
  </style>