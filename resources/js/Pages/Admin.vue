<template>
  <div class="min-h-screen bg-gray-100">
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
      <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-3xl font-bold text-blue-800">Admin Dashboard</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <!-- Statistics -->
          <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-blue-600 mb-4">Statistics</h2>
            <div class="grid grid-cols-2 gap-4">
              <div class="bg-blue-50 p-4 rounded-lg">
                <p class="text-sm text-blue-600 mb-1">Total Ice Creams</p>
                <p class="text-2xl font-bold">{{ totalIcecreams }}</p>
              </div>
              <div class="bg-green-50 p-4 rounded-lg">
                <p class="text-sm text-green-600 mb-1">Total Users</p>
                <p class="text-2xl font-bold">{{ users.length }}</p>
              </div>
            </div>
          </div>

          <!-- Activity Logs -->
          <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-blue-600 mb-4">Activity Logs</h2>
            <div class="max-h-48 overflow-y-auto">
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
                <tr v-for="user in users" :key="user.id" class="border-b hover:bg-gray-50">
                  <td class="p-2">{{ user.name }}</td>
                  <td class="p-2">{{ user.email }}</td>
                  <td class="p-2">{{ getRoleName(user.role_id) }}</td>
                  <td class="p-2 flex gap-2">
                    <!-- Show buttons only if the user is not an admin -->
                    <div v-if="user.role_id !== 1">
                      <button
                        @click="editUserRole(user)"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded mr-2 transition duration-300 ease-in-out"
                      >
                        Edit Role
                      </button>
                      <button
                        @click="deleteUser(user.id)"
                        class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded transition duration-300 ease-in-out"
                      >
                        Delete
                      </button>
                    </div>
                    <!-- If admin, show no actions -->
                    <div v-else class="text-gray-400 text-sm">No actions available</div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { Head, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
  users: Array,
  roles: Array,

});

const users = ref([...props.users]);

const currentUser = ref({ role_id: 1 }); // Assuming the current user is an admin for this example

const roleMap = {
  1: 'Admin',
  2: 'Employee',
  3: 'Customer'
};

const getRoleName = (roleId) => roleMap[roleId] || 'Unknown';

const deleteUser = (userId) => {
  const user = users.value.find((u) => u.id === userId);
  if (user && user.role_id === 1) {
    alert("You cannot delete another admin.");
    return;
  }

  if (confirm("Are you sure you want to delete this user?")) {
    router.put(route("admin.deleteUser", { user: userId }), {
      onSuccess: () => {
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

const totalIcecreams = ref(0);

const activityLogs = ref([
  { id: 1, user: 'John Doe', action: 'added a new ice cream flavor', timestamp: '2023-05-10 14:30' },
  { id: 2, user: 'Jane Smith', action: 'updated inventory', timestamp: '2023-05-10 15:45' },
  { id: 3, user: 'Bob Johnson', action: 'placed an order', timestamp: '2023-05-10 16:20' },
]);

const editUserRole = (user) => {
  if (user.role_id === 1) {
    alert("You cannot edit the role of another admin.");
    return;
  }

  const newRole = prompt(`Enter new role for ${user.name} (1: Admin, 2: Employee, 3: Customer):`, user.role_id);
  if (newRole && [1, 2, 3].includes(Number(newRole))) {
    user.role_id = Number(newRole);
    saveUserRole(user);
  } else {
    alert('Invalid role. Please enter 1, 2, or 3.');
  }
};

const saveUserRole = (user) => {
  if (currentUser.value.role_id !== 1) {
    alert("Only admins can change user roles.");
    return;
  }

  router.put(route('admin.updateUserRole', { userId: user.id }), { role_id: user.role_id }, {
    onSuccess: () => {
      const newRole = getRoleName(user.role_id);
      alert(`Role updated successfully to ${newRole}`);
      activityLogs.value.unshift({
        id: activityLogs.value.length + 1,
        user: user.name,
        action: `changed role to ${newRole}`,
        timestamp: new Date().toISOString(),
      });
    },
    onError: (error) => {
      alert(
        error.response?.data?.message ||
        "There was an error updating the user's role."
      );
    },
  });
};

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};
</script>
