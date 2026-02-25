// File: resources/js/services/GuestDataTransferService.js
import axios from 'axios';
import { shopAPI } from './api';
import { useCartStore } from '@/stores/modules/cart';

class GuestDataTransferService {

    /**
     * Transfer all guest session data to authenticated user
     * @param {number} userId - The authenticated user ID
     * @returns {Promise<Object>}
     */
    async transferGuestData(userId) {
        if (!userId) {
            return { success: false, message: 'No user ID provided' };
        }

        try {
            // Check if there's any data to transfer
            const hasCartData = this.hasGuestCartData();

            if (!hasCartData) {
                return { success: true, message: 'No guest data to transfer' };
            }

            const results = await Promise.all([
                this.transferCart(userId)
            ]);

            // Only clear session data if transfer was successful
            if (results[0].success) {
                this.clearSessionData();
                console.log('Guest data transfer completed successfully');
            }

            return { success: true, results };
        } catch (error) {
            console.error('Failed to transfer guest data:', error);
            return {
                success: false,
                error: error.message,
                message: 'Failed to transfer guest data'
            };
        }
    }

    /**
     * Check if there's any guest cart data to transfer
     * @returns {boolean}
     */
    hasGuestCartData() {
        const cartData = sessionStorage.getItem('cart');
        return cartData && cartData !== '[]';
    }

    /**
     * Transfer specific guest cart items to user's cart
     * @param {number} userId - The authenticated user ID
     * @returns {Promise<Object>}
     */
    async transferCart(userId) {
        console.log('transferCart', userId);
        try {
            // Get cart data from session storage
            const cartData = JSON.parse(sessionStorage.getItem('cart') || '[]');
            const shippingDetails = JSON.parse(sessionStorage.getItem('shipping_details') || 'null');

            if (cartData.length === 0) {
                return { success: true, message: 'No cart data to transfer' };
            }

            // Calculate total amount
            const total_amount = cartData.reduce((sum, item) => sum + (item.product.normal_price * item.quantity), 0);
            const shipping_cost = parseFloat(sessionStorage.getItem('shipping_cost') || '0');
            const grand_total = total_amount + shipping_cost;

            // Format cart items for order creation
            const orderData = {
                items: cartData.map(item => ({
                    product_id: item.product.id,
                    quantity: item.quantity
                })),
                shipping_method_id: sessionStorage.getItem('shipping_method_id'),
                payment_method_id: sessionStorage.getItem('payment_method_id'),
                customer_address_id: sessionStorage.getItem('customer_address_id'),
                shipping_cost: shipping_cost,
                total_amount: total_amount,
                tax: 0,
                discount: 0,
                grand_total: grand_total,
                shipping_details: shippingDetails,
                customer_id: userId
            };

            // Create order using shopAPI
            const response = await shopAPI.createOrder(orderData);

            return {
                success: true,
                message: 'Cart transferred successfully',
                order: response.data
            };
        } catch (error) {
            console.error('Cart transfer failed:', error);
            return {
                success: false,
                error: error.message,
                message: 'Failed to transfer cart data'
            };
        }
    }

    /**
     * Clear session data after successful transfer
     */
    clearSessionData() {
        const keysToRemove = [
            'cart',
            'shipping_details',
            'shipping_method_id',
            'payment_method_id',
            'customer_address_id',
            'previous_product'
        ];

        keysToRemove.forEach(key => {
            try {
                sessionStorage.removeItem(key);
            } catch (error) {
                console.warn(`Failed to remove ${key} from sessionStorage:`, error);
            }
        });
    }
}

export const guestDataTransferService = new GuestDataTransferService();
