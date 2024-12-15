<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { IceCream, ClipboardList, Users, BarChart } from 'lucide-vue-next';
</script>

<template>
    <Head title="Ice Cream Inventory" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Welcome to Ice Cream Inventory
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="text-center mb-8">
                            <IceCream class="w-20 h-20 text-blue-500 mx-auto mb-4" />
                            <h1 class="text-3xl font-bold text-blue-800 mb-2">Ice Cream Inventory Management</h1>
                            <p class="text-xl text-gray-600">Streamline your ice cream business operations</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                            <div class="text-center">
                                <ClipboardList class="w-12 h-12 text-green-500 mx-auto mb-2" />
                                <h3 class="text-lg font-semibold mb-2">Manage Inventory</h3>
                                <p class="text-gray-600">Keep track of flavors, stock levels, and ingredients</p>
                            </div>
                            <div class="text-center">
                                <Users class="w-12 h-12 text-purple-500 mx-auto mb-2" />
                                <h3 class="text-lg font-semibold mb-2">User Management</h3>
                                <p class="text-gray-600">Control access and roles for your team members</p>
                            </div>
                            <div class="text-center">
                                <BarChart class="w-12 h-12 text-blue-500 mx-auto mb-2" />
                                <h3 class="text-lg font-semibold mb-2">Sales Analytics</h3>
                                <p class="text-gray-600">Gain insights into your best-selling flavors and peak times</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
