
<template>
  <div class="service-enquiry-form">
    <h1>Service Enquiry Form</h1>
    <form @submit.prevent="submitEnquiry" class="space-y-6 bg-white p-6 rounded-lg shadow-md">
      <div class="flex flex-col">
        <label for="service_type" class="font-semibold mb-2">Select Service Type:</label>
        <select v-model="form.service_type" required @change="toggleBookingFields" class="border border-gray-300 rounded-md p-2">
          <option value="">Select Service Type</option>
          <option value="Accident Management Services Enquiry">Accident Management Services Enquiry</option>
          <option value="MOT Booking Enquiry">MOT Booking Enquiry</option>
          <option value="Motorcycle Repairs">Motorcycle Repairs</option>
          <option value="Motorcycle Full Service">Motorcycle Full Service</option>
          <option value="Motorcycle Basic Service">Motorcycle Basic Service</option>
          <option value="Other">Other</option>
        </select>
      </div>
      <div class="flex flex-col">
        <label for="email" class="font-semibold mb-2">Email:</label>
        <input type="email" v-model="form.email" required class="border border-gray-300 rounded-md p-2" />
      </div>
      <div class="flex flex-col">
        <label for="fullname" class="font-semibold mb-2">Full Name:</label>
        <input type="text" v-model="form.fullname" required class="border border-gray-300 rounded-md p-2" />
      </div>
      <div class="flex flex-col">
        <label for="phone" class="font-semibold mb-2">Phone:</label>
        <input type="tel" v-model="form.phone" required class="border border-gray-300 rounded-md p-2" />
      </div>
      <div class="flex flex-col">
        <label for="reg_no" class="font-semibold mb-2">Your Number Plate / VRM:</label>
        <input type="text" v-model="form.reg_no" required class="border border-gray-300 rounded-md p-2" />
      </div>
      <div class="flex flex-col">
        <label for="description" class="font-semibold mb-2">Additional Information:</label>
        <textarea v-model="form.description" placeholder="Please provide any details or comments regarding your enquiry." class="border border-gray-300 rounded-md p-2"></textarea>
      </div>
      <div v-if="requiresSchedule" class="space-y-4">
        <div class="flex flex-col">
          <label for="booking_date" class="font-semibold mb-2">Booking Date:</label>
          <input type="date" v-model="form.booking_date" :min="minDate" class="border border-gray-300 rounded-md p-2" />
        </div>
        <div class="flex flex-col">
          <label for="booking_time" class="font-semibold mb-2">Booking Time:</label>
          <div class="flex space-x-4">
            <span v-for="time in availableTimes" :key="time" class="flex items-center space-x-2">
              <input type="radio" :id="`time_${time}`" v-model="form.booking_time" :value="time" required class="form-radio text-blue-600">
              <label :for="`time_${time}`" class="font-medium">{{ time }}</label>
            </span>
          </div>
        </div>
      </div>
      <div class="flex items-center space-x-2">
        <input type="checkbox" class="rounded text-blue-600 focus:ring-2 focus:ring-blue-500" v-model="form.cookie_policy">
        <span class="text-sm">I have read and agree to the <a class="active-color" href="#" style="color: #c31924;" target="_blank">Cookie and Privacy Policy</a></span>
      </div>
      <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700">Enquire Now</button>
    </form>
    <div v-if="successMessage">{{ successMessage }}</div>
    <div v-if="errorMessage">{{ errorMessage }}</div>
  </div>
</template>

<script>
import { ref } from 'vue';
import { shopAPI } from '@/services/api';

export default {
  setup() {
    const form = ref({
      service_type: '',
      fullname: '',
      phone: '',
      reg_no: '',
      email: '',
      booking_date: '',
      booking_time: '',
      description: '',
      cookie_policy: false,
    });
    const successMessage = ref('');
    const errorMessage = ref('');
    const requiresSchedule = ref(false);
    const minDate = new Date().toISOString().split('T')[0];
    const availableTimes = ref(['10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00']);

    const toggleBookingFields = () => {
      requiresSchedule.value = form.value.service_type === 'MOT Booking Enquiry' || form.value.service_type === 'Accident Management Services Enquiry';
    };

    const submitEnquiry = async () => {
      try {
        await shopAPI.submitEnquiry(form.value);
        successMessage.value = 'Your enquiry has been submitted successfully.';
        errorMessage.value = '';
        // Reset form
        form.value = {
          service_type: '',
          fullname: '',
          phone: '',
          reg_no: '',
          email: '',
          booking_date: '',
          booking_time: '',
          description: '',
          cookie_policy: false,
        };
      } catch (error) {
        errorMessage.value = 'There was an error submitting your enquiry. Please try again.';
        successMessage.value = '';
      }
    };

    return {
      form,
      submitEnquiry,
      successMessage,
      errorMessage,
      requiresSchedule,
      minDate,
      availableTimes,
      toggleBookingFields,
    };
  },
};
</script>

<style scoped>
.service-enquiry-form {
  max-width: 600px;
  margin: auto;
}
.service-enquiry-form div {
  margin-bottom: 1em;
}
</style>
