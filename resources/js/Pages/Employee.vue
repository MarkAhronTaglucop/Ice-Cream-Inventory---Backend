<template>

<Head title="Dashboard" />

<AuthenticatedLayout>
<div class="bg-white shadow rounded-lg">
  <div class="px-4 py-5 sm:p-6">
    <h3 class="text-lg font-semibold text-blue-800 mb-4">Ice Cream Inventory</h3>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Flavor</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="iceCream in iceCreams" :key="iceCream.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ iceCream.flavor }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ iceCream.description }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₱{{ iceCream.price.toFixed(2) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ iceCream.stock }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button @click="editIceCream(iceCream)" class="text-blue-600 hover:text-blue-900 mr-2">Edit</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <button @click="showAddIceCreamModal = true" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
      <PlusIcon class="h-5 w-5 mr-2" />
      Add New Ice Cream
    </button>
  </div>
</div>

<!-- Add/Edit Ice Cream Modal -->
<div v-if="showAddIceCreamModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
  <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
    <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
      <div>
        <div class="mt-3 text-center sm:mt-5">
          <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
            {{ editingIceCream ? 'Edit' : 'Add' }} Ice Cream
          </h3>
          <div class="mt-2">
            <input v-model="currentIceCream.flavor" type="text" placeholder="Flavor" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            <textarea v-model="currentIceCream.description" placeholder="Description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
            <input v-model="currentIceCream.price" type="number" step="0.01" placeholder="Price" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            <input v-model="currentIceCream.stock" type="number" placeholder="Stock" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
          </div>
        </div>
      </div>
      <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
        <button @click="saveIceCream" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">
          {{ editingIceCream ? 'Save' : 'Add' }}
        </button>
        <button @click="cancelEdit" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">
          Cancel
        </button>
      </div>
    </div>
  </div>
</div>
  </AuthenticatedLayout>
  </template>
  
  <script setup>
  import { ref, computed } from "vue";
  import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
  import { Head, router } from "@inertiajs/vue3";
  
  const iceCreams = ref([
  { id: 1, flavor: 'Vanilla', description: 'Classic vanilla flavor', price: 50, stock: 100 },
  { id: 2, flavor: 'Chocolate', description: 'Rich chocolate flavor', price: 55, stock: 80 },
  { id: 3, flavor: 'Strawberry', description: 'Fresh strawberry flavor', price: 52, stock: 75 },
]);

const showAddIceCreamModal = ref(false);
const editingIceCream = ref(null);
const currentIceCream = ref({ flavor: '', description: '', price: 0, stock: 0 });

const editIceCream = (iceCream) => {
  editingIceCream.value = iceCream;
  currentIceCream.value = { ...iceCream };
  showAddIceCreamModal.value = true;
};

const saveIceCream = () => {
  if (editingIceCream.value) {
    const index = iceCreams.value.findIndex(ic => ic.id === editingIceCream.value.id);
    iceCreams.value[index] = { ...currentIceCream.value };
  } else {
    iceCreams.value.push({
      id: iceCreams.value.length + 1,
      ...currentIceCream.value
    });
  }
  cancelEdit();
};

const cancelEdit = () => {
  showAddIceCreamModal.value = false;
  editingIceCream.value = null;
  currentIceCream.value = { flavor: '', description: '', price: 0, stock: 0 };
};
  </script>