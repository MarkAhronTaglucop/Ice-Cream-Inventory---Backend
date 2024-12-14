<template>
  <Head title="Dashboard" />
  
  <AuthenticatedLayout>
    <div class="space-y-6">
    <h2 class="text-3xl font-bold text-blue-800">Welcome to Frosty Delights!</h2>
    
    <!-- Search and Filter -->
    <div class="bg-white shadow rounded-lg p-4">
      <input v-model="searchQuery" placeholder="Search ice creams..." class="w-full px-4 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    
    <!-- Ice Cream List -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="iceCream in filteredIceCreams" :key="iceCream.id" class="bg-white rounded-lg shadow-md overflow-hidden">
        <img :src="iceCream.image" :alt="iceCream.flavor" class="w-full h-48 object-cover">
        <div class="p-4">
          <h3 class="text-xl font-semibold text-blue-600">{{ iceCream.flavor }}</h3>
          <p class="text-gray-600 mt-2">{{ iceCream.description }}</p>
          <div class="mt-4 flex justify-between items-center">
            <button @click="viewDetails(iceCream)" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-300">View Details</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Ice Cream Details Modal -->
    <div v-if="selectedIceCream" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
          <div>
            <div class="mt-3 text-center sm:mt-5">
              <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                {{ selectedIceCream.flavor }}
              </h3>
              <div class="mt-2">
                <img :src="selectedIceCream.image" :alt="selectedIceCream.flavor" class="w-full h-48 object-cover rounded-md">
                <p class="text-sm text-gray-500 mt-4">{{ selectedIceCream.description }}</p>
              </div>
            </div>
          </div>
          <div class="mt-5 sm:mt-6">
            <button @click="closeDetails" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm">
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";

// Assuming this is the ice creams data you get from the backend
const iceCreams = ref([
  { id: 1, flavor: 'Vanilla', description: 'Classic vanilla flavor', image: 'https://via.placeholder.com/300x200.png?text=Vanilla' },
  { id: 2, flavor: 'Chocolate', description: 'Rich chocolate flavor', image: 'https://via.placeholder.com/300x200.png?text=Chocolate' },
  { id: 3, flavor: 'Strawberry', description: 'Fresh strawberry flavor', image: 'https://via.placeholder.com/300x200.png?text=Strawberry' },
  { id: 4, flavor: 'Mint Chocolate Chip', description: 'Cool mint with chocolate chips', image: 'https://via.placeholder.com/300x200.png?text=Mint+Chocolate+Chip' },
  { id: 5, flavor: 'Cookie Dough', description: 'Vanilla ice cream with cookie dough pieces', image: 'https://via.placeholder.com/300x200.png?text=Cookie+Dough' },
]);

const searchQuery = ref('');
const selectedIceCream = ref(null);

const filteredIceCreams = computed(() => {
  return iceCreams.value.filter(iceCream => 
    iceCream.flavor.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    iceCream.description.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

const viewDetails = (iceCream) => {
  selectedIceCream.value = iceCream;
};

const closeDetails = () => {
  selectedIceCream.value = null;
};
</script>
