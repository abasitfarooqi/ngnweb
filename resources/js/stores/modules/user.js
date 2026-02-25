// File: resources/js/stores/modules/user.js
import { defineStore } from 'pinia';
import { authAPI } from '@/services/api';
import { guestDataTransferService } from '@/services/GuestDataTransferService';
import { useCartStore } from '@/stores/modules/cart';

export const useUserStore = defineStore('user', {
  state: () => ({
    isAuthenticated: false,
    loading: false,
    error: null,
    initialized: false,
    user: null,
    sessionValidationInProgress: false,
    guestDataTransferred: false,
    lastValidationTime: null
  }),

  actions: {
    async login(credentials) {
      if (this.loading) return false;
      this.loading = true;
      this.error = null;

      try {
        // First attempt login
        await authAPI.login(credentials);

        // Then validate session and get user data
        const isValid = await this.validateSession();
        if (!isValid) {
          throw new Error('Session validation failed after login');
        }

        // After successful login and validation, transfer guest data
        if (!this.guestDataTransferred && this.user?.id) {
          try {
            await guestDataTransferService.transferGuestData(this.user.id);
            this.guestDataTransferred = true;
          } catch (transferError) {
            console.error('Guest data transfer failed:', transferError);
          }
        }

        // Initialize cart after successful login
        try {
          const cartStore = useCartStore();
          if (cartStore?.initializeCart) {
            await cartStore.initializeCart();
          }
        } catch (cartError) {
          console.error('Failed to initialize cart:', cartError);
        }

        return true;
      } catch (error) {
        // Set the error message directly from the caught error
        this.error = error.message || 'Login failed';
        console.log('Login error:', this.error); // For debugging

        return false;
      } finally {
        this.loading = false;
      }
    },

    async register(userData) {
      if (this.loading) return false;
      this.loading = true;
      this.error = null;

      try {
        // Register the user and let validation errors propagate
        await authAPI.register(userData);

        // If registration successful, proceed with login
        await authAPI.login({
          email: userData.email,
          password: userData.password,
          sessionId: window.sessioncid
        });

        // Validate session and get user data
        const isValid = await this.validateSession();
        if (!isValid) {
          throw new Error('Session validation failed after registration');
        }

        // Handle guest data transfer
        if (!this.guestDataTransferred && this.user?.id) {
          try {
            await guestDataTransferService.transferGuestData(this.user.id);
            this.guestDataTransferred = true;

            // Initialize cart
            const cartStore = useCartStore();
            if (cartStore?.initializeCart) {
              await cartStore.initializeCart();
            }
          } catch (error) {
            console.error('Post-registration data transfer failed:', error);
            // Don't fail the registration process for data transfer errors
          }
        }

        return true;
      } catch (error) {
        // Always propagate 422 validation errors to the component
        if (error.response?.status === 422) {
          throw error;
        }

        // Handle other errors in the store
        this.error = error.response?.data?.message || error.message || 'Registration failed';
        this.resetState();
        return false;
      } finally {
        this.loading = false;
      }
    },

    async logout() {
      if (this.loading) return false;
      this.loading = true;

      try {
        await authAPI.logout();
        const cartStore = useCartStore();
        if (cartStore?.clearCart) {
          cartStore.clearCart();
        }
        this.resetState();
        return true;
      } catch (error) {
        console.error('Logout error:', error);
        this.resetState();
        return false;
      } finally {
        this.loading = false;
      }
    },

    async validateSession() {
      // Don't check lastValidationTime for initial validation
      if (this.initialized && this.lastValidationTime) {
        const now = Date.now();
        if ((now - this.lastValidationTime) < 5000) {
          return this.isAuthenticated;
        }
      }

      // Prevent multiple simultaneous validation calls
      if (this.sessionValidationInProgress) {
        return this.isAuthenticated;
      }

      this.sessionValidationInProgress = true;
      console.log('Validating session...');

      try {
        const response = await authAPI.getUser();

        // Update user data and authentication state
        this.user = {
          id: response.data.customer_id,
          name: response.data.full_name,
          branch_id: response.data.branch_id
        };

        this.isAuthenticated = true;
        this.initialized = true;
        this.lastValidationTime = Date.now();
        console.log('Session validated successfully');

        return true;
      } catch (error) {
        console.log('Session validation error:', error.response?.status);
        this.resetState();
        return false;
      } finally {
        this.sessionValidationInProgress = false;
        this.loading = false;
      }
    },

    clearError() {
      this.error = null;
    },

    resetState() {
      this.isAuthenticated = false;
      this.loading = false;
      this.error = null;
      this.initialized = true;
      this.user = null;
      this.sessionValidationInProgress = false;
      this.guestDataTransferred = false;
      this.lastValidationTime = null;
    }
  },

  persist: {
    enabled: true,
    strategies: [
      {
        key: 'user',
        storage: localStorage,
        paths: ['isAuthenticated', 'user', 'initialized', 'lastValidationTime']
      }
    ]
  }
});