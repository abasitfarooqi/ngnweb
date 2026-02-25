<template>
    <!-- Orders Box -->
    <router-link to="/accountinformation/orders"
        class="p-6 ngn-box-style text-center hover:shadow-lg transition-shadow">
        <i class="fa fa-shopping-bag text-3xl  mb-1"></i>
        <h3 class="text-xl font-semibold mb-2 font-three fs">Orders</h3>
        <p class="text-gray-600 text-sm">View and track your orders</p>
    </router-link>

    <!-- Addresses Box -->
    <router-link to="/accountinformation/addresses"
        class="p-6 ngn-box-style text-center hover:shadow-lg transition-shadow">
        <i class="fa fa-map-marker text-3xl  mb-1"></i>

        <h3 class="text-xl font-semibold mb-2 font-three">Addresses</h3>
        <p class="text-gray-600 text-sm">Manage your shipping addresses</p>
    </router-link>

    <!-- Payment Methods Box -->
    <!-- <router-link to="/accountinformation/payment-methods" class="p-6 ngn-box-style text-center hover:shadow-lg transition-shadow">
                            <i class="fa fa-credit-card text-3xl  mb-1"></i>
                            <h3 class="text-xl font-semibold mb-2 font-three fs">Payment Methods</h3>
                            <p class="text-gray-600 text-sm">Manage your payment options</p>
                        </router-link> -->
    <!-- Profile Box -->
    <router-link to="/accountinformation/" class="p-6 ngn-box-style text-center hover:shadow-lg transition-shadow">
        <i class="fa fa-user text-3xl  mb-1"></i>
        <h3 class="text-xl font-semibold mb-2 font-three fs">Profile</h3>
        <p class="text-gray-600 text-sm">Update your personal information</p>
    </router-link>

    <!-- Change Password Box -->
    <router-link to="/accountinformation/change-password"
        class="p-6 ngn-box-style text-center hover:shadow-lg transition-shadow">
        <i class="fa fa-lock text-3xl  mb-1"></i>
        <h3 class="text-xl font-semibold mb-2 font-three fs">Change Password</h3>
        <p class="text-gray-600 text-sm">Update your password</p>
    </router-link>
</template>

<script>
export default {
    name: 'DashboardMenuItems'
}
</script>
