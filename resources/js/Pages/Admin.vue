<template>
  <Head title="Dashboard" />

<AuthenticatedLayout>
  <div class="space-y-6">
    <h2 class="text-3xl font-bold text-blue-800">Admin Dashboard</h2>
    
    <!-- Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div v-for="stat in stats" :key="stat.name" class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <component :is="stat.icon" class="h-6 w-6 text-blue-600" />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">{{ stat.name }}</dt>
                <dd class="text-lg font-bold text-gray-900">{{ stat.value }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- User Management -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-semibold text-blue-800 mb-4">User Management</h3>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="user in users" :key="user.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ user.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.email }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  <select v-model="user.role" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="admin">Admin</option>
                    <option value="employee">Employee</option>
                    <option value="customer">Customer</option>
                  </select>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button @click="editUser(user)" class="text-blue-600 hover:text-blue-900 mr-2">Edit</button>
                  <button @click="deleteUser(user.id)" class="text-red-600 hover:text-red-900">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <button @click="showAddUserModal = true" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
          <PlusIcon class="h-5 w-5 mr-2" />
          Add User
        </button>
      </div>
    </div>
    
    <!-- Activity Logs -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-semibold text-blue-800 mb-4">Activity Logs</h3>
        <ul class="divide-y divide-gray-200">
          <li v-for="log in activityLogs" :key="log.id" class="py-4">
            <div class="flex space-x-3">
              <div class="flex-1 space-y-1">
                <div class="flex items-center justify-between">
                  <h3 class="text-sm font-medium">{{ log.user }}</h3>
                  <p class="text-sm text-gray-500">{{ log.timestamp }}</p>
                </div>
                <p class="text-sm text-gray-500">{{ log.action }}</p>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <!-- Add User Modal -->
    <div v-if="showAddUserModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
          <div>
            <div class="mt-3 text-center sm:mt-5">
              <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                Add New User
              </h3>
              <div class="mt-2">
                <input v-model="newUser.name" type="text" placeholder="Name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <input v-model="newUser.email" type="email" placeholder="Email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <select v-model="newUser.role" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                  <option value="admin">Admin</option>
                  <option value="employee">Employee</option>
                  <option value="customer">Customer</option>
                </select>
              </div>
            </div>
          </div>
          <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
            <button @click="addUser" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">
              Add
            </button>
            <button @click="showAddUserModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">
              Cancel
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
  import { Head, router } from "@inertiajs/vue3";
  import { PlusIcon, PencilIcon, TrashIcon } from 'lucide-vue-next';
  import { IceCream, Users, ShoppingCart, Activity } from 'lucide-vue-next';
  
  const stats = ref([
  { name: 'Total Ice Creams', value: '24', icon: IceCream },
  { name: 'Total Users', value: '1,234', icon: Users },
  { name: 'Pending Orders', value: '13', icon: ShoppingCart },
  { name: 'Daily Active Users', value: '573', icon: Activity },
]);

const users = ref([
  { id: 1, name: 'John Doe', email: 'john@example.com', role: 'admin' },
  { id: 2, name: 'Jane Smith', email: 'jane@example.com', role: 'employee' },
  { id: 3, name: 'Bob Johnson', email: 'bob@example.com', role: 'customer' },
]);

const activityLogs = ref([
  { id: 1, user: 'John Doe', action: 'Added a new ice cream flavor', timestamp: '2023-05-10 14:30' },
  { id: 2, user: 'Jane Smith', action: 'Updated inventory', timestamp: '2023-05-10 15:45' },
  { id: 3, user: 'Bob Johnson', action: 'Placed an order', timestamp: '2023-05-10 16:20' },
]);

const showAddUserModal = ref(false);
const newUser = ref({ name: '', email: '', role: 'customer' });

const editUser = (user) => {
  // Implement edit user logic
  console.log('Editing user:', user);
};

const deleteUser = (userId) => {
  users.value = users.value.filter(user => user.id !== userId);
};

const addUser = () => {
  users.value.push({
    id: users.value.length + 1,
    ...newUser.value
  });
  showAddUserModal.value = false;
  newUser.value = { name: '', email: '', role: 'customer' };
};
  </script>