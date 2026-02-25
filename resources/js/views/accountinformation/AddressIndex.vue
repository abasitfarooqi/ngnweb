<template>
    
<div class="account-information-container">
        <div class="container account-information-container" style="margin-top: -32px;padding-top: 62px;">
            <div class="flex flex-col lg:flex-row gap-3" >
                <div class="w-full lg:w-1/6 mb-6 lg:mb-0">
                    <Sidebar />
                </div>
                <div class="w-full lg:w-3/4 mt-0 m-0 ngn-box-style-container">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-semibold">Customer Addresses</h2>
                        <button @click="openCreateForm" class="ngn-btn-sm">
                            Add New Address
                        </button>
                    </div>

                    <!-- Create Address Form -->
                    <div v-if="showCreateForm" class="mb-4 p-4 border rounded bg-gray-50">
                        <h3 class="text-xl font-semibold mb-4">Add New Address</h3>
                        <form @submit.prevent="createAddress">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="type" class="block mb-2 font-medium">Type</label>
                                    <select v-model="form.type" id="type" required class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500">
                                        <option disabled value="">Please select one</option>
                                        <option value="billing">Billing</option>
                                        <option value="shipping">Shipping</option>
                                        <option value="office">Office</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="first_name" class="block mb-2 font-medium">First Name</label>
                                    <input v-model="form.first_name" type="text" id="first_name" required class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500" />
                                </div>

                                <div>
                                    <label for="last_name" class="block mb-2 font-medium">Last Name</label>
                                    <input v-model="form.last_name" type="text" id="last_name" required class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500" />
                                </div>

                                <div>
                                    <label for="company_name" class="block mb-2 font-medium">Company Name</label>
                                    <input v-model="form.company_name" type="text" id="company_name" class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500" />
                                </div>

                                <div>
                                    <label for="street_address" class="block mb-2 font-medium">Street Address</label>
                                    <input v-model="form.street_address" type="text" id="street_address" required class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500" />
                                </div>

                                <div>
                                    <label for="street_address_plus" class="block mb-2 font-medium">Apartment/Suite</label>
                                    <input v-model="form.street_address_plus" type="text" id="street_address_plus" class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500" />
                                </div>

                                <div>
                                    <label for="city" class="block mb-2 font-medium">City</label>
                                    <input v-model="form.city" type="text" id="city" required class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500" />
                                </div>

                                <div>
                                    <label for="postcode" class="block mb-2 font-medium">Postal Code</label>
                                    <input v-model="form.postcode" type="text" id="postcode" required class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500" />
                                </div>

                                <div>
                                    <label for="phone_number" class="block mb-2 font-medium">Phone Number</label>
                                    <input v-model="form.phone_number" type="text" id="phone_number" class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500" />
                                </div>

                                <div>
                                    <label for="country" class="block mb-2 font-medium">Country</label>
                                    <select v-model="form.country_id" id="country" required class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500">
                                        <option disabled value="">Please select a country</option>
                                        <option v-for="country in countries" :key="country.id" :value="country.id">
                                            {{ country.name }}
                                        </option>
                                    </select>
                                </div>

                                <div class="col-span-2">
                                    <label class="flex items-center space-x-2">
                                        <input v-model="form.is_default" type="checkbox" id="is_default" class="rounded text-blue-600 focus:ring-2 focus:ring-blue-500">
                                        <span>Set as Default</span>
                                    </label>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-2 mt-4">
                                <button type="submit" class="ngn-btn-sm">
                                    Create Address
                                </button>
                                <button type="button" @click="closeCreateForm" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Edit Address Form -->
                    <div v-if="showEditForm" class="mb-4 p-4 border rounded bg-gray-50">
                        <h3 class="text-xl font-semibold mb-4">Edit Address</h3>
                        <form @submit.prevent="updateAddress">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="type" class="block mb-2 font-medium">Type</label>
                                    <select v-model="form.type" id="type" required class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500">
                                        <option disabled value="">Please select one</option>
                                        <option value="billing">Billing</option>
                                        <option value="shipping">Shipping</option>
                                        <option value="office">Office</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="first_name" class="block mb-2 font-medium">First Name</label>
                                    <input v-model="form.first_name" type="text" id="first_name" required class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500" />
                                </div>

                                <div>
                                    <label for="last_name" class="block mb-2 font-medium">Last Name</label>
                                    <input v-model="form.last_name" type="text" id="last_name" required class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500" />
                                </div>

                                <div>
                                    <label for="company_name" class="block mb-2 font-medium">Company Name</label>
                                    <input v-model="form.company_name" type="text" id="company_name" class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500" />
                                </div>

                                <div>
                                    <label for="street_address" class="block mb-2 font-medium">Street Address</label>
                                    <input v-model="form.street_address" type="text" id="street_address" required class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500" />
                                </div>

                                <div>
                                    <label for="street_address_plus" class="block mb-2 font-medium">Apartment/Suite</label>
                                    <input v-model="form.street_address_plus" type="text" id="street_address_plus" class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500" />
                                </div>

                                <div>
                                    <label for="city" class="block mb-2 font-medium">City</label>
                                    <input v-model="form.city" type="text" id="city" required class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500" />
                                </div>

                                <div>
                                    <label for="postcode" class="block mb-2 font-medium">Postal Code</label>
                                    <input v-model="form.postcode" type="text" id="postcode" required class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500" />
                                </div>

                                <div>
                                    <label for="phone_number" class="block mb-2 font-medium">Phone Number</label>
                                    <input v-model="form.phone_number" type="text" id="phone_number" class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500" />
                                </div>

                                <div>
                                    <label for="country" class="block mb-2 font-medium">Country</label>
                                    <select v-model="form.country_id" id="country" required class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500">
                                        <option disabled value="">Please select a country</option>
                                        <option v-for="country in countries" :key="country.id" :value="country.id">
                                            {{ country.name }}
                                        </option>
                                    </select>
                                </div>

                                <div class="col-span-2">
                                    <label class="flex items-center space-x-2">
                                        <input v-model="form.is_default" type="checkbox" id="is_default" class="rounded text-blue-600 focus:ring-2 focus:ring-blue-500">
                                        <span>Set as Default</span>
                                    </label>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-2 mt-4">
                                <button type="submit" class="ngn-btn-sm ">
                                    Update Address
                                </button>
                                <button type="button" @click="closeEditForm" class="ngn-btn-sm ngn-bg">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <template v-if="addresses.length === 0">
                            <div class="col-span-2 text-center text-gray-500 p-4">
                                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                <p>No addresses found.</p>
                                <p class="text-sm">Add a new address to get started.</p>
                            </div>
                        </template>
                        <template v-else>
                            <div v-for="address in addresses" :key="address.id" 
                                class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
                                <div class="mb-3">
                                    <div class="flex items-center justify-between">
                                        <div class="font-medium text-lg flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            {{ address.first_name }} {{ address.last_name }}
                                        </div>
                                        <span v-if="address.is_default" class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Default</span>
                                    </div>
                                    <div v-if="address.company_name" class="text-sm text-gray-500 flex items-center mt-1">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        {{ address.company_name }}
                                    </div>
                                </div>
                                <div class="text-gray-600 mb-4 pl-1">
                                    <div class="flex items-start mb-1">
                                        <svg class="w-4 h-4 mr-2 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <div>
                                            {{ address.street_address }}
                                            <span v-if="address.street_address_plus">, {{ address.street_address_plus }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center mb-1 pl-6">
                                        {{ address.city }}, {{ address.postcode }}
                                    </div>
                                    <div class="flex items-center pl-6">
                                        {{ getCountryName(address.country_id) }}
                                    </div>
                                </div>
                                <div class="flex space-x-2 border-t pt-4">
                                    <button @click="openEditForm(address)" class="ngn-btn-sm flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </button>
                                    <button @click="confirmDelete(address.id)" class="ngn-btn-sm ngn-bg flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Delete Confirmation Section -->
                    <div v-if="showDeleteConfirmation" class="mt-4 p-4 border rounded bg-red-50">
                        <h3 class="text-xl font-semibold mb-4">Confirm Delete</h3>
                        <p class="mb-4">Are you sure you want to delete this address?</p>
                        <div class="flex justify-end space-x-2">
                            <button @click="deleteAddress" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                                Yes, Delete
                            </button>
                            <button @click="closeDeleteConfirmation" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>

</template>

<script setup>
import { ref, onMounted } from 'vue';
import Sidebar from '@/components/accountinformation/Sidebar.vue';
import { shopAPI } from '@/services/api';

// Reactive state
const addresses = ref([]);
const countries = ref([]);
const showCreateForm = ref(false);
const showEditForm = ref(false);
const showDeleteConfirmation = ref(false);
const form = ref({
  id: null,
  type: '',
  first_name: '',
  last_name: '',
  company_name: '',
  street_address: '',
  street_address_plus: '',
  city: '',
  postcode: '',
  phone_number: '',
  is_default: false,
  country_id: '',
  customer_id: null,
  created_at: null,
  updated_at: null
});
const addressToDelete = ref(null);

// Fetch addresses and countries when component is mounted
onMounted(async () => {
  await Promise.all([
    fetchAddresses(),
    fetchCountries()
  ]);
});

// Fetch customer addresses from API
async function fetchAddresses() {
  try {
    const data = await shopAPI.getCustomerAddresses();
    addresses.value = data;
  } catch (error) {
    console.error('Error fetching addresses:', error);
  }
}

// Fetch countries from API
async function fetchCountries() {
  try {
    const data = await shopAPI.getCountries();
    countries.value = data;
  } catch (error) {
    console.error('Error fetching countries:', error);
  }
}

// Get country name by ID
function getCountryName(countryId) {
  const country = countries.value.find(c => c.id === countryId);
  return country ? country.name : '';
}

// Open form to create a new address
function openCreateForm() {
  showCreateForm.value = true;
  showEditForm.value = false;
  showDeleteConfirmation.value = false;
  form.value = {
    type: '',
    first_name: '',
    last_name: '',
    company_name: '',
    street_address: '',
    street_address_plus: '',
    city: '',
    postcode: '',
    phone_number: '',
    is_default: false,
    country_id: '',
  };
}

// Close the create form
function closeCreateForm() {
  showCreateForm.value = false;
}

// Open form to edit an existing address
function openEditForm(address) {
  showEditForm.value = true;
  showCreateForm.value = false;
  showDeleteConfirmation.value = false;
  form.value = { ...address };
}

// Close the edit form
function closeEditForm() {
  showEditForm.value = false;
}

// Create a new address
async function createAddress() {
  try {
    await shopAPI.createCustomerAddress(form.value);
    await fetchAddresses();
    closeCreateForm();
  } catch (error) {
    console.error('Error creating address:', error);
  }
}

// Update an existing address
async function updateAddress() {
  try {
    await shopAPI.updateCustomerAddress(form.value.id, form.value);
    await fetchAddresses();
    closeEditForm();
  } catch (error) {
    console.error('Error updating address:', error);
  }
}

// Confirm deletion of an address
function confirmDelete(id) {
  addressToDelete.value = id;
  showDeleteConfirmation.value = true;
  showCreateForm.value = false;
  showEditForm.value = false;
}

// Close the delete confirmation
function closeDeleteConfirmation() {
  showDeleteConfirmation.value = false;
  addressToDelete.value = null;
}

// Delete an address
async function deleteAddress() {
  try {
    await shopAPI.deleteCustomerAddress(addressToDelete.value);
    await fetchAddresses();
    closeDeleteConfirmation();
  } catch (error) {
    console.error('Error deleting address:', error);
  }
}
</script>

<style scoped>
/* You can add additional custom styles here if needed */
</style>