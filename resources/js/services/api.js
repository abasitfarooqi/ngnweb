// File: resources/js/services/api.js
import axios from 'axios';
import initializeCsrf from '@/services/initializeCsrf';
import { isValidProduct, isValidBrand } from '@/utils/validators';

// Public Base axios instances
export const publicClient = axios.create({
  baseURL: '/api/v1',
  headers: {
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
  withCredentials: false,
});

const privateClient = axios.create({
  baseURL: '/api/v1',
  headers: {
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
  withCredentials: true,
});

// Base axios instances
export const getCsrfToken = axios.create({
  headers: {
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
  withCredentials: false,
});

// Auth API endpoints
export const authAPI = {
  /**
   * POST ('/api/v1/customer/forgot-password', Auth.forgotPassword)
   * Send a password reset email to the user
   * Send email or phone
   * @param {string} email - The email address of the user
   */
  async forgotPassword({ identifier, type }) {
    await this.getCsrfToken();
    return privateClient.post('/customer/forgot-password', { identifier, type });
  },

  async getCsrfToken() {
    await initializeCsrf();
  },

  async login(credentials) {
    await this.getCsrfToken();
    try {
      return await privateClient.post('/customer/login', credentials);
    } catch (error) {
      console.log('Login error response:', error.response?.data);

      if (error.response?.status === 422) {
        // Get the error message from the response
        const errorMessage = error.response.data.message ||
          error.response.data.errors?.email?.[0] ||
          'Invalid credentials';
        throw new Error(errorMessage);
      }
      throw error;
    }
  },

  async register(userData) {
    await this.getCsrfToken();
    try {

      const response = await privateClient.post('/customer/register', userData);

      return response.data;
    } catch (error) {

      if (error.response?.status === 422) {
        throw error; // Let component handle validation errors
      }
      throw new Error(error.response?.data?.message || 'Registration failed');
    }
  },

  async logout() {
    return privateClient.post('/customer/logout');
  },

  async getUser() {
    return privateClient.get('/customer/user');
  },

  async getUserById(id) {
    return privateClient.get(`/customer/user/${id}`);
  },

  async confirmResetPassword(resetData) {
    await this.getCsrfToken();
    return privateClient.post('/customer/confirm-reset-password', resetData);
  }
};


// Shop API endpoints
export const shopAPI = {

  /**
   * GET ('/api/v1/shop/products', ECommerceShop.getProducts)
   * Get products with filtering, sorting and pagination
   * @param {Object} options
   * @param {number} options.page - Page number (default: 1)
   * @param {number} options.per_page - Items per page (default: 12)
   * @param {string} options.sort - Sort order ('newest'|'price_low'|'price_high'|'name')
   * @param {Object} options.filters - Filter options
   * @param {string} [options.filters.search] - Search query for name/sku/description
   * @param {number} [options.filters.category] - Filter by category_id
   * @param {number} [options.filters.brand] - Filter by brand_id
   * @param {boolean} [options.filters.has_image] - Filter by has_image
   */
  async getProducts({ page = 1, per_page = 12, sort = 'newest', hasImage = true, filters = {} } = {}) {
    try {
      const response = await publicClient.get('/shop/products', {
        params: {
          page,
          per_page,
          has_image: filters.hasImage,
          sort,
          ...filters
        }
      });

      if (!response.data || !Array.isArray(response.data.data)) {
        throw new Error('Invalid response format from server');
      }

      return {
        data: response.data.data.map(product => ({
          ...product,
          id: product.id || 0,
          name: product.name || '',
          slug: product.slug || '',
          image_url: product.image_url || '',
          normal_price: parseFloat(product.normal_price) || 0,
          discount_price: product.discount_price ? parseFloat(product.discount_price) : null,
          discount_percentage: product.discount_percentage ? parseInt(product.discount_percentage) : null,
          points: product.points ? parseInt(product.points) : null,
          brand: product.brand || '',
          in_stock: product.global_stock > 0,
          rating: parseFloat(product.rating) || 0,
          review_count: parseInt(product.review_count) || 0,
          global_stock: parseInt(product.global_stock) || 0
        })),
        meta: {
          current_page: parseInt(response.data.current_page) || 1,
          last_page: parseInt(response.data.last_page) || 1,
          per_page: parseInt(response.data.per_page) || per_page,
          total: parseInt(response.data.total) || 0
        }
      };
    } catch (error) {
      console.error('Error fetching products:', error);
      throw new Error(error.response?.data?.message || 'Failed to fetch products');
    }
  },

  /**
   * // NOT USED / 
   * GET ('/api/v1/shop/product/{id}', ECommerceShop.getProductById)
   * Get product by ID
   * @param {number} id - The ID of the product
   * @returns {Promise<Object>} Product data
   */
  async getProductById(id) {
    const response = await publicClient.get(`/shop/p/${id}`);
    return response.data;
  },

  /**
 * GET ('/api/v1/shop/product/{slug}', ECommerceShop.getProductBySlug)
 * Get product by Slug
 * @param {string} slug - The Slug of the product
 * @returns {Promise<Object>} Product data
 */
  async getProductBySlug(slug) {
    try {
      const decodedSlug = decodeURIComponent(slug);

      const cleanedSlug = decodedSlug.replace(/[^a-zA-Z0-9\s/-]+/g, '').trim();

      const response = await publicClient.get(`/shop/product/${cleanedSlug}`);

      const data = response.data;

      data.products = data.products.map(product => ({
        ...product,
        slug: product.slug || product.name.replace(/[^a-zA-Z0-9]+/g, '-'),
      }));

      if (!data.redundantData.colour || data.redundantData.colour === 'Not specified') {
        data.redundantData.colour = 'Default Colour';
      }

      return data;
    } catch (error) {
      console.error('Error fetching product by slug:', error);
      throw error;
    }
  },

  /**
   * GET ('/api/v1/shop/brands', ECommerceShop.getBrands)
   * Get list of brands
   */
  async getBrands() {
    const response = await publicClient.get('/shop/brands');
    return response.data;
  },

  /**
   * GET ('/api/v1/shop/categories', ECommerceShop.getCategories)
   * Get list of categories
   */
  async getCategories() {
    const response = await publicClient.get('/shop/categories');
    return response.data;
  },

  /**
   * GET ('/api/v1/shop/cart')
   * Get the current user's cart
   * @returns {Promise<Object>} Cart data
   */
  // async getCart() {
  //   try {
  //     const response = await privateClient.get('/shop/cart');
  //     return response.data;
  //   } catch (error) {
  //     console.error('Error getting cart:', error);
  //     throw new Error(error.response?.data?.message || 'Failed to get cart');
  //   }
  // },

  /**
   * POST ('/api/v1/shop/cart/add')
   * Add product to cart
   * @param {number} productId
   */
  async addToCart(productId) {
    try {
      const response = await privateClient.post('/shop/cart/add', { product_id: productId });
      return response.data;
    } catch (error) {
      console.error('Error adding to cart:', error);
      throw new Error(error.response?.data?.message || 'Failed to add to cart');
    }
  },

  /**
   * POST ('/api/v1/shop/wishlist/add')
   * Add product to wishlist
   * @param {number} productId
   */
  async addToWishlist(productId) {
    try {
      const response = await privateClient.post('/shop/wishlist/add', { product_id: productId });
      return response.data;
    } catch (error) {
      console.error('Error adding to wishlist:', error);
      throw new Error(error.response?.data?.message || 'Failed to add to wishlist');
    }
  },


  /**
   * GET ('/api/v1/shop/branches', ECommerceShop.getBranches)
   * Get list of branches
   */
  async getBranches() {
    const response = await publicClient.get('/shop/branches');
    return response.data;
  },

  /**
   * POST ('/api/v1/shop/branches', ECommerceShop.createBranch)
   * Create a new branch
   * @param {Object} branchData - The data for the new branch
   */
  async createBranch(branchData) {
    const response = await privateClient.post('/shop/branches', branchData);
    return response.data;
  },

  async getBranchByName(name) {
    const response = await publicClient.get(`/shop/branches/${name}`);
    return response.data;
  },

  async updateBranch(name, branchData) {
    const response = await privateClient.put(`/shop/branches/${name}`, branchData);
    return response.data;
  },

  async deleteBranch(name) {
    const response = await privateClient.delete(`/shop/branches/${name}`);
    return response.data;
  },

  // Terms API endpoints
  async getTerms() {
    const response = await publicClient.get('/shop/terms');
    return response.data;
  },

  async createTerms(termData) {
    const response = await privateClient.post('/shop/terms', termData);
    return response.data;
  },

  async updateTerms(id, termData) {
    const response = await privateClient.put(`/shop/terms/${id}`, termData);
    return response.data;
  },

  async deleteTerms(id) {
    const response = await privateClient.delete(`/shop/terms/${id}`);
    return response.data;
  },

  // Payment Methods API endpoints
  async getPaymentMethods() {
    const response = await publicClient.get('/shop/payment-methods');
    return response.data;
  },

  async createPaymentMethod(paymentData) {
    const response = await privateClient.post('/shop/payment-methods', paymentData);
    return response.data;
  },

  async updatePaymentMethod(id, paymentData) {
    const response = await privateClient.put(`/shop/payment-methods/${id}`, paymentData);
    return response.data;
  },

  async deletePaymentMethod(id) {
    const response = await privateClient.delete(`/shop/payment-methods/${id}`);
    return response.data;
  },

  // Shipping Methods API endpoints
  async getShippingMethods() {
    const response = await publicClient.get('/shop/shipping-methods');
    return response.data;
  },

  async createShippingMethod(shippingData) {
    const response = await privateClient.post('/shop/shipping-methods', shippingData);
    return response.data;
  },

  async updateShippingMethod(id, shippingData) {
    const response = await privateClient.put(`/shop/shipping-methods/${id}`, shippingData);
    return response.data;
  },

  async deleteShippingMethod(id) {
    const response = await privateClient.delete(`/shop/shipping-methods/${id}`);
    return response.data;
  },

  /**
   * GET ('/api/v1/shop/orders', ECommerceShop.getOrders)
   * Get list of orders
   * @returns {Promise<Object>} Orders data
   */
  async getOrders() {
    const response = await privateClient.get('/shop/orders');
    return response.data;
  },

  /**
   * GET ('/api/v1/shop/customer-orders', ECommerceShop.getCustomerOrders)
   * Get list of orders for the current customer
   * @returns {Promise<Object>} Orders data
   */
  async getCustomerOrders() {
    const response = await privateClient.get('/shop/customer-orders');
    return response.data;
  },

  /**
   * POST ('/api/v1/shop/orders', ECommerceShop.createOrder)
   * Create a new order
   * @param {Object} orderData - The data for the new order
   */
  async createOrder(orderData) {
    const response = await privateClient.post('/shop/orders', orderData);
    return response.data;
  },

  async updateOrder(id, orderData) {
    const response = await privateClient.put(`/shop/orders/${id}`, orderData);
    return response.data;
  },

  async cancelOrder(id) {
    const response = await privateClient.put(`/shop/orders/${id}/cancel`);
    return response.data;
  },


  /**
   * GET ('/api/v1/shop/cart-pending-order')
   * Get the current pending order for logged-in user
   * @returns {Promise<Object>}
   */
  async getCartPendingOrder() {
    try {
      const response = await privateClient.get('/shop/cart-pending-order');
      return response.data;
    } catch (error) {
      // If no pending order found (404), create one from session data if available
      // if (error.response?.status === 404) {
      //   const cartData = JSON.parse(sessionStorage.getItem('cart') || '[]');
      //   if (cartData.length > 0) {
      //     const shippingDetails = JSON.parse(sessionStorage.getItem('shipping_details') || 'null');

      //     // Calculate totals
      //     const total_amount = cartData.reduce((sum, item) => sum + (item.product.normal_price * item.quantity), 0);
      //     const shipping_cost = parseFloat(sessionStorage.getItem('shipping_cost') || '0');
      //     const grand_total = total_amount + shipping_cost;

      //     // Create new order with current cart data
      //     const orderData = {
      //       items: cartData.map(item => ({
      //         product_id: item.product.id,
      //         quantity: item.quantity
      //       })),
      //       shipping_method_id: sessionStorage.getItem('shipping_method_id'),
      //       payment_method_id: sessionStorage.getItem('payment_method_id'),
      //       customer_address_id: sessionStorage.getItem('customer_address_id'),
      //       shipping_cost: shipping_cost,
      //       total_amount: total_amount,
      //       tax: 0,
      //       discount: 0,
      //       grand_total: grand_total,
      //       shipping_details: shippingDetails
      //     };

      //     // Create new order and return it
      //     await this.createOrder(orderData);
      //     const retryResponse = await privateClient.get('/shop/cart-pending-order');
      //     return retryResponse.data;
      //   }
      // }
      throw error;
    }
  },

  /**
   * POST ('/api/v1/shop/order-item/add')
   * Add item to pending order for logged-in user (if no pending order found, create one)
   * @param {Object} data
   * @param {number} data.product_id - The ID of the product to add
   * @param {number} data.quantity - The quantity to add
   * @returns {Promise<Object>}
   */
  async addOrderItem(data) {
    try {
      const response = await privateClient.post('/shop/order-item/add', data);
      return response.data;
    } catch (error) {
      throw error;
    }
  },

  /**
   * PUT ('/api/v1/shop/cart-item/{product_id}')
   * Update the quantity of a specific cart item in the pending order
   * @param {number} product_id - The product ID
   * @param {number} quantity - The new quantity
   * @returns {Promise<Object>}
   */
  async updateCartItemQuantity(product_id, quantity) {
    try {
      const response = await privateClient.put(`/shop/cart-item/${product_id}`, {
        quantity: quantity
      });
      return response.data;
    } catch (error) {
      // If order not found (404), try to create a new order with the current cart items
      // if (error.response?.status === 404) {
      //   const cartData = JSON.parse(sessionStorage.getItem('cart') || '[]');
      //   const shippingDetails = JSON.parse(sessionStorage.getItem('shipping_details') || 'null');

      //   // Calculate totals
      //   const total_amount = cartData.reduce((sum, item) => sum + (item.product.normal_price * item.quantity), 0);
      //   const shipping_cost = parseFloat(sessionStorage.getItem('shipping_cost') || '0');
      //   const grand_total = total_amount + shipping_cost;

      //   // Create new order with current cart data
      //   const orderData = {
      //     items: cartData.map(item => ({
      //       product_id: item.product.id,
      //       quantity: item.quantity
      //     })),
      //     shipping_method_id: sessionStorage.getItem('shipping_method_id'),
      //     payment_method_id: sessionStorage.getItem('payment_method_id'),
      //     customer_address_id: sessionStorage.getItem('customer_address_id'),
      //     shipping_cost: shipping_cost,
      //     total_amount: total_amount,
      //     tax: 0,
      //     discount: 0,
      //     grand_total: grand_total,
      //     shipping_details: shippingDetails
      //   };

      //   // Create new order and retry the update
      //   await this.createOrder(orderData);
      //   const retryResponse = await privateClient.put(`/shop/cart-item/${product_id}`, {
      //     quantity: quantity
      //   });
      //   return retryResponse.data;
      // }
      throw error;
    }
  },

  /**
   * PUT ('/api/v1/shop/cart-pending-order/delivery-method')
   * Change the delivery method of the pending order
   * @param {number|null} deliveryMethodId - The ID of the delivery method (null to reset)
   * @param {number|null} branchId - The ID of the branch (optional)
   * @returns {Promise<{success: boolean, message?: string}>}
   */
  async changeDeliveryMethod(deliveryMethodId, branchId = null) {
    const response = await privateClient.put(`/shop/cart-pending-order/delivery-method`, {
      delivery_method_id: deliveryMethodId,
      branch_id: branchId
    });
    return response.data;
  },

  /**
   * Remove a specific cart item
   * @param {number} productId
   * @returns {Promise<Object>}
   */
  removeCartItem: async (productId) => {
    try {
      const response = await privateClient.delete(`/shop/cart-items/${productId}`);
      return response.data;
    } catch (error) {
      console.error(`Error removing cart item ${productId}:`, error);
      throw error;
    }
  },


  async getOrderById(id) {
    const response = await privateClient.get(`/shop/orders/${id}`);
    return response.data;
  },

  async updateOrder(id, orderData) {
    const response = await privateClient.put(`/shop/orders/${id}`, orderData);
    return response.data;
  },

  async deleteOrder(id) {
    const response = await privateClient.delete(`/shop/orders/${id}`);
    return response.data;
  },

  // Order Shipping API endpoints
  async getOrderShipping(id) {
    const response = await privateClient.get(`/shop/orders/${id}/shipping`);
    return response.data;
  },

  async createOrderShipping(id, shippingData) {
    const response = await privateClient.post(`/shop/orders/${id}/shipping`, shippingData);
    return response.data;
  },

  async updateOrderShipping(id, shippingData) {
    const response = await privateClient.put(`/shop/orders/${id}/shipping`, shippingData);
    return response.data;
  },

  // Countries API endpoints
  async getCountries() {
    const response = await publicClient.get('/shop/countries');
    return response.data;
  },


  // Customer Addresses API endpoints
  async getCustomerAddresses() {
    try {
      const response = await privateClient.get('/shop/customer-addresses');
      return response.data;
    } catch (error) {
      console.error('Error fetching customer addresses:', error);
      throw new Error(error.response?.data?.error || 'Failed to fetch customer addresses');
    }
  },

  /**
   * POST ('/api/v1/shop/customer-addresses', ECommerceShop.createCustomerAddress)
   * Create a new customer address
   * @param {Object} addressData - The data for the new address
   */
  async createCustomerAddress(addressData) {
    try {
      const response = await privateClient.post('/shop/customer-addresses', addressData);
      return response.data;
    } catch (error) {
      console.error('Error creating customer address:', error);
      throw new Error(error.response?.data?.error || 'Failed to create address');
    }
  },

  /**
   * PUT ('/api/v1/shop/customer-addresses/{id}', ECommerceShop.updateCustomerAddress)
   * Update an existing customer address
   * @param {number} id - The ID of the address to update
   * @param {Object} addressData - The updated address data
   */
  async updateCustomerAddress(id, addressData) {
    try {
      const response = await privateClient.put(`/shop/customer-addresses/${id}`, addressData);
      return response.data;
    } catch (error) {
      console.error('Error updating customer address:', error);
      throw new Error(error.response?.data?.error || 'Failed to update address');
    }
  },

  /**
   * DELETE ('/api/v1/shop/customer-addresses/{id}', ECommerceShop.deleteCustomerAddress)
   * Delete a customer address
   * @param {number} id - The ID of the address to delete
   */
  async deleteCustomerAddress(id) {
    try {
      await privateClient.delete(`/shop/customer-addresses/${id}`);
      return;
    } catch (error) {
      console.error('Error deleting customer address:', error);
      throw new Error(error.response?.data?.error || 'Failed to delete address');
    }
  },

  // Blog API endpoints
  async getPosts() {
    const response = await publicClient.get('/shop/blog/posts');
    return response.data;
  },


  async getPostBySlug(slug) {
    const response = await publicClient.get(`/shop/blog/posts/${slug}`);
    return response.data;
  },

  /**
   * GET ('/api/v1/shop/order-summary')
   * Get the current order summary including shipping costs and payment status
   * @returns {Promise<Object>} Order summary data
   */
  async getOrderSummary() {
    try {
      const response = await privateClient.get('/shop/order-summary');
      return response.data;
    } catch (error) {
      console.error('Error fetching order summary:', error);
      throw new Error(error.response?.data?.message || 'Failed to fetch order summary');
    }
  },

  // Add new method to check pending order status
  async checkPendingOrderStatus() {
    try {
      const response = await privateClient.get('/shop/check-pending-order-status');
      return response.data;
    } catch (error) {
      console.error('Error checking pending order status:', error);
      throw new Error(error.response?.data?.message || 'Failed to check order status');
    }
  },

  // Service Enquiry Form API endpoints submit
  async submitEnquiry(data) {
    const response = await publicClient.post('shop/service-enquiry-form-vue', data);
    return response.data;
  },

};

// protected routes / order proceeds, login, logout, register etc...
privateClient.interceptors.request.use(
  async (config) => {
    await initializeCsrf();
    return config;
  },
  (error) => Promise.reject(error)
);

export async function getProductById(id) {
  const response = await publicClient.get(`/shop/p/${id}`);
  return response.data;
}
