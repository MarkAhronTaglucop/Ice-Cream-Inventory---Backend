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
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Flavor
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Description
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Price
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Stock
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="icecream in icecreams" :key="icecream.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ icecream.flavor }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ icecream.description }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₱{{ icecream.price }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ icecream.stock }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button @click="editIceCream(icecream)" class="text-blue-600 hover:text-blue-900 mr-2">Edit</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <button @click="showAddIceCreamModal = true"
          class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
          Add New Ice Cream
        </button>
      </div>
    </div>

    <!-- Add Ice Cream Modal -->
    <div v-if="showAddIceCreamModal && !editingIceCream" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title"
      role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div
          class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
          <div>
            <div class="mt-3 text-center sm:mt-5">
              <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                Add Ice Cream
              </h3>
              <div class="mt-2">
                <input v-model="currentIceCream.flavor" type="text" placeholder="Flavor"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <textarea v-model="currentIceCream.description" placeholder="Description"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                <input v-model="currentIceCream.price" type="number" step="0.01" placeholder="Price"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <input v-model="currentIceCream.stock" type="number" placeholder="Stock"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <input v-model="currentIceCream.manufactured_date" type="text" placeholder="Manufactured Date"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <select v-model="currentIceCream.status"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                  <option value="Available">Available</option>
                  <option value="Out of Stock">Out of Stock</option>
                </select>
              </div>
            </div>
          </div>
          <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
            <button @click="saveIceCream" type="button"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">
              Add
            </button>
            <button @click="cancelEdit" type="button"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Ice Cream Modal -->
    <div v-if="showAddIceCreamModal && editingIceCream" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title"
      role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div
          class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
          <div>
            <div class="mt-3 text-center sm:mt-5">
              <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                Edit Ice Cream
              </h3>
              <div class="mt-2">
                <input v-model="currentIceCream.flavor" type="text" placeholder="Flavor"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <textarea v-model="currentIceCream.description" placeholder="Description"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                <input v-model="currentIceCream.price" type="number" step="0.01" placeholder="Price"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <input v-model="currentIceCream.stock" type="number" placeholder="Stock"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <input v-model="currentIceCream.manufactured_date" type="text" placeholder="Manufactured Date"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <select v-model="currentIceCream.status"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                  <option value="Available">Available</option>
                  <option value="Out of Stock">Out of Stock</option>
                </select>
              </div>
            </div>
          </div>
          <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
            <button @click="saveIceCream" type="button"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">
              Save
            </button>
            <button @click="cancelEdit" type="button"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head,router } from "@inertiajs/vue3";

const props = defineProps({
  icecreams: Array,
});

const showAddIceCreamModal = ref(false);
const editingIceCream = ref(null);
const currentIceCream = ref({
  flavor: '',
  description: '',
  price: 0,
  stock: 0,
  manufactured_date: '',
  status: 'Available',
});

const editIceCream = (iceCream) => {
  editingIceCream.value = iceCream;
  currentIceCream.value = { ...iceCream };
  showAddIceCreamModal.value = true;
};
 

const saveIceCream = async () => {
  try {
    await router.put(`/employee/update/${currentIceCream.value.id}`, {
      name: currentIceCream.value.flavor,
      description: currentIceCream.value.description,
      price: currentIceCream.value.price,
      stock: currentIceCream.value.stock,
      manufactured_date: currentIceCream.value.manufactured_date,
      status: currentIceCream.value.status,
    });
    alert("Ice cream updated successfully!");
  } catch (error) {
    console.error("Failed to update ice cream:", error.response?.data || error.message);
    alert("An error occurred while updating the ice cream. Please try again.");
  } finally {
    cancelEdit();
  }
};

const cancelEdit = () => {
  showAddIceCreamModal.value = false;
  editingIceCream.value = null;
  currentIceCream.value = {
    flavor: '',
    description: '',
    price: 0,
    stock: 0,
    manufactured_date: '',
    status: 'Available',
  };
};

</script>