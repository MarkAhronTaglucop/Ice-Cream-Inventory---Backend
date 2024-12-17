<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <div class="bg-white shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-semibold text-blue-800 mb-4">
          Ice Cream Inventory
        </h3>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Icecream Name
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Description
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  manufactured Date
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Price
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Stock
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="icecream in icecreams" :key="icecream.icecream_id">
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                >
                  {{ icecream.icecream_name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ icecream.description }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ icecream.manufactured_date }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  ₱{{ icecream.price }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ icecream.stock }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button
                    @click="editIceCream(icecream)"
                    class="text-blue-600 hover:text-blue-900 mr-2"
                  >
                    Edit
                  </button>

                  <button
                    v-if="$page.props.auth.user.role_id === 1"
                    @click="editstocks(icecream)"
                    class="text-green-600 hover:text-green-900 mr-2"
                  >
                    admin-edit
                  </button>

                  <button
                    v-if="$page.props.auth.user.role_id === 1"
                    @click="deleteIceCream(icecream.icecream_id)"
                    class="text-red-600 hover:text-red-900 mr-2"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <button
          @click="showAddIceCreamModal = true"
          class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          Add New Ice Cream
        </button>
      </div>
    </div>

    <!-- Add/Edit Ice Cream Modal -->
    <div
      v-if="showAddIceCreamModal"
      class="fixed z-10 inset-0 overflow-y-auto"
      aria-labelledby="modal-title"
      role="dialog"
      aria-modal="true"
    >
      <div
        class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
      >
        <div
          class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
          aria-hidden="true"
        ></div>
        <span
          class="hidden sm:inline-block sm:align-middle sm:h-screen"
          aria-hidden="true"
          >&#8203;</span
        >
        <div
          class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
        >
          <div>
            <div class="mt-3 text-center sm:mt-5">
              <h3
                class="text-lg leading-6 font-medium text-gray-900"
                id="modal-title"
              >
                {{ editingIceCream ? "Edit Ice Cream" : "Add Ice Cream" }}
              </h3>
              <div class="mt-2">
                <input
                  v-model="currentIceCream.icecream_name"
                  type="text"
                  placeholder="Flavor"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                />
                <textarea
                  v-model="currentIceCream.description"
                  placeholder="Description"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                ></textarea>
                <input
                  v-model="currentIceCream.price"
                  type="number"
                  step="0.01"
                  placeholder="Price"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                />

                <input
                  v-model="currentIceCream.manufactured_date"
                  type="date"
                  placeholder="Manufactured Date"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                />
                <div class="mt-4">
                  <label
                    for="image"
                    class="block text-sm font-medium text-gray-700"
                    >Upload Image</label
                  >
                  <input
                    type="file"
                    id="image"
                    @change="handleFileUpload"
                    accept="image/*"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  />
                  <p
                    v-if="currentIceCream.image_url"
                    class="mt-2 text-sm text-gray-500"
                  >
                    current file: {{ currentIceCream.image_url }}
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div
            class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense"
          >
            <button
              @click="editingIceCream ? saveIceCream() : addIceCream()"
              type="button"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm"
            >
              {{ editingIceCream ? "Save" : "Add" }}
            </button>
            <button
              @click="cancelEdit"
              type="button"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- AdminEdit Ice Cream Modal -->
    <div
      v-if="showAdminModal"
      class="fixed z-10 inset-0 overflow-y-auto"
      aria-labelledby="modal-title"
      role="dialog"
      aria-modal="true"
    >
      <div
        class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
      >
        <div
          class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
          aria-hidden="true"
        ></div>
        <span
          class="hidden sm:inline-block sm:align-middle sm:h-screen"
          aria-hidden="true"
          >&#8203;</span
        >
        <div
          class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
        >
          <div>
            <div class="mt-3 text-center sm:mt-5">
              <h3
                class="text-lg leading-6 font-medium text-gray-900 font-bold"
                id="modal-title"
              >
                Edit Stocks
              </h3>
              <div class="mt-2">
                <p class="text-md leading-6 font-medium text-gray-900">
                  {{ currentIceCream.icecream_name }}
                </p>
                <input
                  v-model="currentIceCream.stock"
                  type="number"
                  step="0.01"
                  placeholder="stocks"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                />
              </div>
            </div>
          </div>
          <div
            class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense"
          >
            <button
              @click="updatestocks()"
              type="button"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm"
            >
              update
            </button>
            <button
              @click="showAdminModal = false"
              type="button"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm"
            >
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
import { Head, router } from "@inertiajs/vue3";

const props = defineProps({
  icecreams: Array,
});

const showAddIceCreamModal = ref(false);
const showAdminModal = ref(false);
const editingIceCream = ref(null);
const currentIceCream = ref({
  icecream_name: "",
  description: "",
  price: 0,
  stock: 0,
  manufactured_date: "",
  image_url: null,
});
const currentstocks = ref({
  stocks: 0,
});

const editIceCream = (iceCream) => {
  editingIceCream.value = iceCream;
  currentIceCream.value = { ...iceCream };
  showAddIceCreamModal.value = true;
};

const editstocks = (iceCream) => {
  editingIceCream.value = iceCream;
  currentIceCream.value = { ...iceCream };
  showAdminModal.value = true;
};

const handleFileUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    currentIceCream.value.image_url = file; // Save file object
    console.log("File selected:", file);
  }
};

const saveIceCream = async () => {
  if (!currentIceCream.value.image_url) {
    alert("Please upload an image before saving.");
    return;
  }

  if (confirm("Are you sure you want to update this ice cream?")) {
    // Create FormData to handle file uploads
    const formData = new FormData();
    formData.append("name", currentIceCream.value.icecream_name);
    formData.append("description", currentIceCream.value.description);
    formData.append("price", currentIceCream.value.price);
    formData.append(
      "manufactured_date",
      currentIceCream.value.manufactured_date
    );
    formData.append("image_url", currentIceCream.value.image_url); // Append file

    console.log("FormData entries:");
    for (const pair of formData.entries()) {
      console.log(pair[0] + ": ", pair[1]);
    }

    try {
      await router.post(
        `/employee/update/${currentIceCream.value.icecream_id}`,
        formData,
        {
          headers: {
            "Content-Type": "multipart/form-data", // Important for file upload
          },
        }
      );
      alert("Ice cream updated successfully!");
    } catch (error) {
      console.error(
        "Failed to update ice cream:",
        error.response?.data || error.message
      );
      alert(
        "An error occurred while updating the ice cream. Please try again."
      );
    } finally {
      cancelEdit();
    }
  }
};

const addIceCream = async () => {
  if (confirm("Are you sure you want to add this ice cream?")) {
    const formData = new FormData();
    formData.append("name", currentIceCream.value.icecream_name);
    formData.append("description", currentIceCream.value.description);
    formData.append("price", currentIceCream.value.price);
    formData.append(
      "manufactured_date",
      currentIceCream.value.manufactured_date
    );

    // Append the image file only if it exists
      formData.append("image_url", currentIceCream.value.image_url);
    

    console.log("FormData entries:");
    for (const pair of formData.entries()) {
      console.log(pair[0] + ": ", pair[1]);
    }

    try {
      // Send the data to the backend using FormData
      await router.post(route("employee.add"), formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });

      alert("Ice cream added successfully!");
    } catch (error) {
      console.error(
        "Failed to add ice cream:",
        error.response?.data || error.message
      );
      alert("An error occurred while adding the ice cream. Please try again.");
    } finally {
      cancelEdit();
    }
  }
};


const updatestocks = async () => {
  if (confirm("Are you sure you want to update this icecream stock?")) {
    console.log({
      stocks: currentIceCream.value.stock,
    });
    try {
      await router.post(`/admin/update/${currentIceCream.value.icecream_id}`, {
        new_stock: currentIceCream.value.stock,
      });
      alert("Stocks updated successfully!");
    } catch (error) {
      console.error(
        "Failed to update stocks:",
        error.response?.data || error.message
      );
      alert("An error occurred while updating the stocks. Please try again.");
    } finally {
      cancelEdit();
    }
  }
};

const deleteIceCream = async (id) => {
  if (confirm("Are you sure you want to delete this icecream?")) {
    try {
      console.log({
        id: id,
      });
      await router.post(route("employee.delete", { id }));
      alert("Deleted successfully!");
    } catch (error) {
      console.error("Failed to delete:", error.response?.data || error.message);
      alert("An error occurred while deleting. Please try again.");
    }
  }
};

const cancelEdit = () => {
  editingIceCream.value = null;
  currentIceCream.value = {
    icecream_name: "",
    description: "",
    price: 0,
    manufactured_date: "",
    imageFileName: "",
  };
  showAddIceCreamModal.value = false;
  showAdminModal.value = false;
};
</script>
