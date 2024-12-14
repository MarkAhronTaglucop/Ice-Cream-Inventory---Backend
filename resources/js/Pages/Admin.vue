<template>
  <div class="min-h-screen bg-gray-100">
    <Head title="Dashboard" />

    <AuthenticatedLayout>
      <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-blue-800 mb-6">Admin Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <!-- Statistics -->
          <div class="bg-white p-6 rounded-lg shadow-md col-span-2">
            <h2 class="text-xl font-semibold text-blue-600 mb-4">Statistics</h2>
            <div class="grid grid-cols-2 gap-4">
              <div class="bg-blue-50 p-4 rounded-lg">
                <p class="text-sm text-blue-600 mb-1">Total Ice Creams</p>
                <p class="text-2xl font-bold">{{ totalIceCreams }}</p>
              </div>
              <div class="bg-green-50 p-4 rounded-lg">
                <p class="text-sm text-green-600 mb-1">Total Users</p>
                <p class="text-2xl font-bold">{{ users.length }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- User Management -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
          <h2 class="text-xl font-semibold text-blue-600 mb-4">User Management</h2>
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="bg-blue-100">
                  <th class="p-2 text-left">Name</th>
                  <th class="p-2 text-left">Email</th>
                  <th class="p-2 text-left">Role</th>
                  <th class="p-2 text-left">Actions</th>
                </tr>
              </thead>
             <tbody>
  <tr
    v-for="user in users"
    :key="user.id"
    class="border-b hover:bg-gray-50 transition duration-300"
  >
    <td class="p-2">{{ user.name }}</td>
    <td class="p-2">{{ user.email }}</td>
    <td class="p-2">
      <select
        v-model="user.role_id"
        class="border border-blue-300 bg-blue-50 text-blue-700 rounded-lg p-2 w-full focus:ring-2 focus:ring-blue-500 focus:outline-none"
        @change="saveUserRole(user)"
      >
        <option value="" disabled>Select Role</option>
        <option
          v-for="role in roles"
          :key="role.id"
          :value="role.id"
        >
          {{ role.user_type }}
        </option>
      </select>
    </td>
    <td class="p-2 flex gap-2">
      <button
        @click="editUser(user)"
        class="flex items-center bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-lg shadow transition duration-300 ease-in-out"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="2"
          stroke="currentColor"
          class="w-4 h-4 mr-1"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M16.862 3.487a2.25 2.25 0 10-3.194 3.194L19.5 12.512m0 0L12.487 19.5m7.013-7.013h-1.125a2.25 2.25 0 00-2.25 2.25v1.125m-7.016-7.01a2.25 2.25 0 10-3.192 3.192L12.488 19.5m0 0H11.25a2.25 2.25 0 01-2.25-2.25v-1.125"
          />
        </svg>
        Edit
      </button>
      <button
        @click="deleteUser(user.id)"
        class="flex items-center bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg shadow transition duration-300 ease-in-out"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="2"
          stroke="currentColor"
          class="w-4 h-4 mr-1"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M19.5 12h-15m0 0L7.5 8.25m-3 3.75l3 3.75"
          />
        </svg>
        Delete
      </button>
    </td>
  </tr>
</tbody>

            </table>
          </div>
        </div>

        <!-- Activity Logs -->
        <div class="bg-white p-6 rounded-lg shadow-md">
          <h2 class="text-xl font-semibold text-blue-600 mb-4">Activity Logs</h2>
          <div class="max-h-64 overflow-y-auto">
            <ul class="space-y-2">
              <li v-for="log in activityLogs" :key="log.id" class="border-b pb-2 hover:bg-gray-50">
                <span class="font-semibold text-blue-600">{{ log.user }}</span>
                <span class="text-gray-700">{{ log.action }}</span>
                <span class="text-gray-500 text-sm">at {{ formatDate(log.timestamp) }}</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { Head, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
  users: Array,
  roles: Array,
});

// Reactive state for users
const users = ref([...props.users]);

// Get role based on user id
const getRole = (roleId) => {
  const role = props.roles.find((role) => role.id === roleId);
  return role ? role.user_type : "Unknown";
};

const deleteUser = (userId) => {
  if (confirm("Are you sure you want to delete this user?")) {
    router.put(route("admin.deleteUser", { user: userId }), {
      onSuccess: () => {
        // Filter out the deleted user
        users.value = users.value.filter((user) => user.id !== userId);
        alert("User deleted successfully.");
      },
      onError: (error) => {
        alert(
          error.response?.data?.message ||
          "There was an error deleting the user."
        );
      },
    });
  }
};

const totalIceCreams = ref(25);

const activityLogs = ref([
  { id: 1, user: 'John Doe', action: 'added a new ice cream flavor', timestamp: '2023-05-10 14:30' },
  { id: 2, user: 'Jane Smith', action: 'updated inventory', timestamp: '2023-05-10 15:45' },
  { id: 3, user: 'Bob Johnson', action: 'placed an order', timestamp: '2023-05-10 16:20' },
]);

const editUser = (user) => {
  // Implement edit user logic
  console.log('Editing user:', user);
};

const saveUserRole = (user) => {
  // Here you would typically make an API call to update the user's role
  console.log(`Saving new role for user ${user.id}: ${user.role_id}`);
  // For demonstration, we'll just log the change and add it to activity logs
  const newRole = getRole(user.role_id);
  activityLogs.value.unshift({
    id: activityLogs.value.length + 1,
    user: user.name,
    action: `changed role to ${newRole}`,
    timestamp: new Date().toISOString()
  });
};

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

// Ensure all users have a role
users.value.forEach(user => {
  if (!user.role_id) {
    const defaultRole = props.roles.find(role => role.user_type.toLowerCase() === 'customer') || props.roles[0];
    user.role_id = defaultRole.id;
  }
});
</script>
