<template>
  <div class="branch-selector">
    <div v-if="loading" class="text-gray-600 p-4">
      Loading available branches...
    </div>
    <div v-else-if="error" class="text-red-600 p-4">
      {{ error }}
    </div>
    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Left Column: Store List and Details -->
      <div class="space-y-6">
        <div v-for="branch in branches" :key="branch.id"
          class="border p-4 cursor-pointer hover:border-primary-500 transition-colors"
          :class="{ 'border-primary-500 bg-primary-50': selectedBranchId === branch.id }" @click="selectBranch(branch)">
          <div class="flex items-start gap-3">
            <input type="radio" :value="branch.id" :checked="selectedBranchId === branch.id"
              class="form-radio h-5 w-5 mt-1 text-primary-500" />
            <div class="space-y-4 flex-1">
              <!-- Store Name and Address -->
              <div>
                <h3 class="font-medium text-lg">{{ branch.name }}</h3>
                <p class="text-gray-800">{{ branch.address }}</p>
              </div>

              <!-- Store Opening Times -->
              <div>
                <h4 class="font-medium mb-2">Store Opening Times</h4>
                <div class="grid grid-cols-2 gap-x-4 text-sm">
                  <div>
                    <p>Mon-Sat: 09:00 - 18:00</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Collection Requirements -->
      <div class="bg-gray-50 p-6 ">
        <h3 class="text-lg font-semibold mb-4">Collection Requirements</h3>

        <div class="space-y-1">
          <div>
            <p class="font-medium mb-2">You must bring with you:</p>
            <ul class="list-disc list-inside text-gray-600 space-y-2">
              <li class="text-sm">Your order invoice number (provided in confirmation email)</li>
              <li class="text-sm">Your order confirmation email</li>
              <li class="text-sm">Your Surname & matched with with your ID</li>
            </ul>
          </div>

          <div>
            <p class="font-medium mb-2">PLUS any one of the following as proof of identity:</p>
            <ul class="list-disc list-inside text-gray-600 space-y-1">
              <li class="text-sm">Cheque Guarantee/Credit/Debit Card</li>
              <li class="text-sm">Bank/Building Society Book</li>
              <li class="text-sm">Valid Passport</li>
              <li class="text-sm">Cheque Book</li>
              <li class="text-sm">Driving Licence</li>
            </ul>
          </div>

          <div>
            <p class="font-medium mb-2">For Under 18s:</p>
            <ul class="list-disc list-inside text-gray-600 space-y-1">
              <li class="text-sm">National Insurance Card</li>
              <li class="text-sm">Medical Card</li>
              <li class="text-sm">Savings Book</li>
            </ul>
          </div>

          <div class="mt-6 text-sm text-gray-500">
            <p class="font-medium">Please Note:</p>
            <p>If someone else is collecting on your behalf, they will need either:</p>
            <ul class="list-disc list-inside mt-2">
              <li class="text-sm">Proof of your identity, or</li>
              <li class="text-sm">Identification with the same surname as yours</li>
            </ul>
          </div>

          <div class="mt-4 p-3 bg-blue-50 text-blue-700 rounded-md">
            <p class="text-sm">Collection In Store is only available to stores in London, UK</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { shopAPI } from '@/services/api';

export default {
  name: 'BranchSelector',
  props: {
    modelValue: {
      type: Number,
      default: null
    }
  },
  emits: ['update:modelValue'],
  setup(props, { emit }) {
    const branches = ref([]);
    const loading = ref(false);
    const error = ref(null);
    const selectedBranchId = ref(props.modelValue);

    const fetchBranches = async () => {
      loading.value = true;
      error.value = null;
      try {
        const response = await shopAPI.getBranches();
        branches.value = response;
      } catch (err) {
        error.value = 'Failed to load branches. Please try again.';
        console.error('Error fetching branches:', err);
      } finally {
        loading.value = false;
      }
    };

    const selectBranch = (branch) => {
      selectedBranchId.value = branch.id;
      emit('update:modelValue', branch.id);
    };

    onMounted(fetchBranches);

    return {
      branches,
      loading,
      error,
      selectedBranchId,
      selectBranch
    };
  }
};
</script>