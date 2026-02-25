// File: resources/js/stores/modules/cart.js

import { defineStore } from 'pinia';

export const useCartStore = defineStore('cart', {
    state: () => ({
        items: [], // Array of objects: { id, quantity, product }
        loading: false,
        error: null,
        lastAddedItem: null,
        lastAction: null,
    }),
    getters: {
        cartItemCount: (state) =>
            state.items.reduce((total, item) => total + (parseInt(item.quantity) || 0), 0),
        cartItems: (state) => state.items,
        subtotal: (state) => {
            const total = state.items
                .reduce((total, item) => {
                    const price = item.product.discount_price || item.product.normal_price;
                    const quantity = parseInt(item.quantity) || 0;
                    return total + (parseFloat(price) || 0) * quantity;
                }, 0);
            return Number.isFinite(total) ? total.toFixed(2) : '0.00';
        },
        points: (state) => {
            const totalPoints = state.items.reduce((total, item) => {
                const price = item.product.discount_price || item.product.normal_price;
                const quantity = parseInt(item.quantity) || 0;
                return total + Math.floor(((parseFloat(price) || 0) * quantity) / 10);
            }, 0);
            return Number.isFinite(totalPoints) ? totalPoints : 0;
        },
    },
    actions: {
        // Initialize cart from session storage
        initializeCart() {
            try {
                const sessionCart = sessionStorage.getItem('cart');
                if (sessionCart) {
                    const parsedCart = JSON.parse(sessionCart);
                    if (Array.isArray(parsedCart)) {
                        this.items = parsedCart.map((item) => ({
                            id: item.id,
                            quantity: parseInt(item.quantity) || 1,
                            product: item.product || {
                                id: item.id,
                                name: '',
                                sku: '',
                                image_url: '',
                                normal_price: 0,
                                discount_price: null,
                                global_stock: 0
                            }
                        }));
                    } else {
                        this.items = [];
                    }
                } else {
                    this.items = [];
                }
            } catch (error) {
                console.error('Error initializing cart:', error);
                this.items = [];
            }
        },

        // Sync store with session storage
        syncWithSession() {
            try {
                sessionStorage.setItem('cart', JSON.stringify(this.items));
            } catch (error) {
                console.error('Error syncing cart with session:', error);
            }
        },

        // Add item to cart
        async addToCart(product) {
            try {
                this.loading = true;
                this.error = null;

                const existingItem = this.items.find(
                    (item) => item.id === product.id && item.variation === product.variation && item.price === product.normal_price
                );

                if (existingItem) {
                    existingItem.quantity += 1;
                    this.lastAction = 'update';
                } else {
                    this.items.push({
                        id: product.id,
                        variation: product.variation,
                        price: product.normal_price,
                        quantity: 1,
                        product: {
                            id: product.id,
                            name: product.name,
                            sku: product.sku,
                            image_url: product.image_url,
                            normal_price: product.normal_price,
                            discount_price: product.discount_price,
                            global_stock: product.global_stock
                        }
                    });
                    this.lastAction = 'add';
                }

                this.lastAddedItem = product.id;
                this.syncWithSession();

                // Reset last added item after animation
                setTimeout(() => {
                    this.lastAddedItem = null;
                    this.lastAction = null;
                }, 2000);

                return true;
            } catch (error) {
                console.error('Error adding to cart:', error);
                this.error = 'Failed to add item to cart';
                return false;
            } finally {
                this.loading = false;
            }
        },

        // Update item quantity
        async updateQuantity(productId, newQuantity) {
            try {
                this.loading = true;
                this.error = null;

                const id = parseInt(productId);
                const quantity = parseInt(newQuantity);

                if (quantity < 1) {
                    return this.removeFromCart(id);
                }

                const item = this.items.find((item) => item.id === id);
                if (item) {
                    item.quantity = quantity;
                    this.lastAction = 'update';
                    this.syncWithSession();

                    // Reset last action after animation
                    setTimeout(() => {
                        this.lastAction = null;
                    }, 2000);

                    return true;
                }
                return false;
            } catch (error) {
                console.error('Error updating quantity:', error);
                this.error = 'Failed to update quantity';
                return false;
            } finally {
                this.loading = false;
            }
        },

        // Remove item from cart
        async removeFromCart(productId) {
            try {
                this.loading = true;
                this.error = null;

                const id = parseInt(productId);

                this.items = this.items.filter((item) => item.id !== id);
                this.lastAction = 'remove';
                this.syncWithSession();

                // Reset last action after animation
                setTimeout(() => {
                    this.lastAction = null;
                }, 2000);

                return true;
            } catch (error) {
                console.error('Error removing from cart:', error);
                this.error = 'Failed to remove item from cart';
                return false;
            } finally {
                this.loading = false;
            }
        },

        // Clear cart
        clearCart() {
            this.items = [];
            this.lastAction = 'clear';
            this.syncWithSession();

            setTimeout(() => {
                this.lastAction = null;
            }, 2000);
        },
    },
});
