<template>
  <Head title="Dashboard" />

<AuthenticatedLayout>
  <div class="space-y-6 p-6">
      <h2 class="text-3xl font-bold text-blue-800">Admin Dashboard</h2>

      <!-- Navigation Tabs -->
      <nav class="flex space-x-4 border-b border-gray-200">
        <button
          v-for="tab in tabs"
          :key="tab.name"
          @click="currentTab = tab.name"
          class="px-3 py-2 text-sm font-medium rounded-t-lg transition-colors duration-200"
          :class="currentTab === tab.name ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700'"
        >
          {{ tab.label }}
        </button>
      </nav>

      <!-- Overview -->
      <div v-if="currentTab === 'overview'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div
          v-for="stat in stats"
          :key="stat.name"
          class="bg-white overflow-hidden shadow rounded-lg cursor-pointer hover:shadow-md transition-shadow duration-200"
          @click="showDetailedReport(stat.name)"
        >
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
      <div v-if="currentTab === 'users'" class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-blue-800">User Management</h3>
            <button @click="showAddUserModal = true" class="btn-primary">
              <PlusIcon class="h-5 w-5 mr-2" />
              Add User
            </button>
          </div>

          <!-- Search Bar -->
          <div class="mb-4">
            <input
              v-model="userSearchQuery"
              type="text"
              placeholder="Search users..."
              class="input-primary"
            />
          </div>

          <div class="overflow-x-auto">
            <table class="table-auto min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th v-for="header in ['Name', 'Email', 'Role', 'Actions']" :key="header" class="th">
                    {{ header }}
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="user in paginatedUsers" :key="user.id">
                  <td class="td">{{ user.name }}</td>
                  <td class="td">{{ user.email }}</td>
                  <td class="td">
                    <select v-model="user.role" @change="confirmRoleChange(user)" class="select-primary">
                      <option value="admin">Admin</option>
                      <option value="employee">Employee</option>
                      <option value="customer">Customer</option>
                    </select>
                  </td>
                  <td class="td">
                    <div class="flex space-x-2">
                      <button @click="editUser(user)" class="btn-icon text-blue-600">
                        <PencilIcon class="h-5 w-5" />
                      </button>
                      <button @click="confirmDeleteUser(user)" class="btn-icon text-red-600">
                        <TrashIcon class="h-5 w-5" />
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="mt-4 flex justify-between items-center">
            <button @click="prevPage" :disabled="currentPage === 1" class="btn-secondary">
              Previous
            </button>
            <span class="text-sm text-gray-700">Page {{ currentPage }} of {{ totalPages }}</span>
            <button @click="nextPage" :disabled="currentPage === totalPages" class="btn-secondary">
              Next
            </button>
          </div>
        </div>
      </div>

      <!-- Activity Logs -->
      <div v-if="currentTab === 'logs'" class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-blue-800">Activity Logs</h3>
            <button @click="toggleLogs" class="btn-link">
              {{ showAllLogs ? 'Collapse' : 'Expand' }}
            </button>
          </div>
          <ul class="divide-y divide-gray-200">
            <li v-for="log in displayedLogs" :key="log.id" class="py-4">
              <div class="flex space-x-3">
                <component :is="getLogIcon(log.action)" class="h-6 w-6 text-gray-400" />
                <div>
                  <p class="text-sm font-medium">{{ log.user }}</p>
                  <p class="text-sm text-gray-500">{{ log.action }} - {{ log.timestamp }}</p>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>

      <!-- Add User Modal -->
      <div v-if="showAddUserModal" class="modal">
        <div class="modal-content">
          <h3 class="text-lg leading-6 font-medium">Add New User</h3>
          <input v-model="newUser.name" type="text" placeholder="Name" class="input-primary" />
          <input v-model="newUser.email" type="email" placeholder="Email" class="input-primary" />
          <select v-model="newUser.role" class="select-primary">
            <option value="admin">Admin</option>
            <option value="employee">Employee</option>
            <option value="customer">Customer</option>
          </select>
          <div class="modal-actions">
            <button @click="addUser" class="btn-primary">Add</button>
            <button @click="showAddUserModal = false" class="btn-secondary">Cancel</button>
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
  import { PlusIcon, PencilIcon, TrashIcon, IceCream, Users, ShoppingCart, Activity } from 'lucide-vue-next';
  
  // Tabs and State
const tabs = [
  { name: "overview", label: "Overview" },
  { name: "users", label: "Users" },
  { name: "logs", label: "Activity Logs" },
];
const currentTab = ref("overview");

// Statistics
const stats = [
  { name: "Total Ice Creams", value: "24", icon: IceCream },
  { name: "Total Users", value: "1,234", icon: Users },
  { name: "Daily Active Users", value: "573", icon: Activity },
];

// Users and Pagination
const users = ref([
  { id: 1, name: "John Doe", email: "john@example.com", role: "admin" },
  { id: 2, name: "Jane Smith", email: "jane@example.com", role: "employee" },
  { id: 3, name: "Bob Johnson", email: "bob@example.com", role: "customer" },
]);
const userSearchQuery = ref("");
const currentPage = ref(1);
const usersPerPage = 5;
const filteredUsers = computed(() =>
  users.value.filter(
    (user) =>
      user.name.toLowerCase().includes(userSearchQuery.value.toLowerCase()) ||
      user.email.toLowerCase().includes(userSearchQuery.value.toLowerCase())
  )
);
const paginatedUsers = computed(() => {
  const start = (currentPage.value - 1) * usersPerPage;
  const end = start + usersPerPage;
  return filteredUsers.value.slice(start, end);
});
const totalPages = computed(() =>
  Math.ceil(filteredUsers.value.length / usersPerPage)
);

// Activity Logs
const activityLogs = ref([
  { id: 1, user: "John Doe", action: "Added a new ice cream flavor", timestamp: "2024-05-10 14:30" },
]);
const showAllLogs = ref(false);
const displayedLogs = computed(() =>
  showAllLogs.value ? activityLogs.value : activityLogs.value.slice(0, 5)
);

// Modal State
const showAddUserModal = ref(false);
const newUser = ref({ name: "", email: "", role: "customer" });

// Methods
const addUser = () => {
  users.value.push({ id: users.value.length + 1, ...newUser.value });
  newUser.value = { name: "", email: "", role: "customer" };
  showAddUserModal.value = false;
};
const prevPage = () => currentPage.value > 1 && currentPage.value--;
const nextPage = () => currentPage.value < totalPages.value && currentPage.value++;
const toggleLogs = () => (showAllLogs.value = !showAllLogs.value);
const getLogIcon = (action) => (action.toLowerCase().includes("add") ? PlusIcon : Activity);

  </script>

  