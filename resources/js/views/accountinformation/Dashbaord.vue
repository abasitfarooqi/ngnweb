<template>
    <div class="account-information-container">
        <div class="container" style="margin-top: -64px;padding-top: 62px;">
            <div class="flex flex-col lg:flex-row gap-3" style="margin-top: 32px;">
                <div class="w-full lg:w-1/6 mb-6 lg:mb-0 ">
                    <Sidebar />
                </div>
                <div class="w-full lg:w-3/4 mt-0 m-0 ">
                    <div class="grid grid-cols-3 gap-3  p-0">
                        <DashboardMenuItems />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Sidebar from '@/components/accountinformation/Sidebar.vue';
import DashboardMenuItems from '@/components/accountinformation/DashboardMenuItems.vue';
</script>

<style scoped>
/* You can add additional custom styles here if needed */
</style>