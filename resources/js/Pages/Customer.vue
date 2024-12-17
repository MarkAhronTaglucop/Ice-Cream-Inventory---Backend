<template>
  <Head title="Customer Dashboard" />

  <AuthenticatedLayout>
    <div class="space-y-6">
      <h2 class="text-3xl font-bold text-blue-800">
        Welcome to Frosty Delights!
      </h2>

      <!-- Search and Filter -->
      <div class="bg-white shadow rounded-lg p-4 flex items-center">
        <input
          v-model="searchQuery"
          @keyup.esc="clearSearch"
          placeholder="Search ice creams..."
          class="w-full px-4 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        <button
          @click="clearSearch"
          class="ml-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500"
        >
          Clear
        </button>
      </div>
      <div
        v-if="filteredIcecreams.length === 0"
        class="text-gray-500 text-center"
      >
        No ice creams match your search.
      </div>

      <!-- Ice Cream List -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="icecream in filteredIcecreams"
          :key="icecream.icecream_id"
          class="bg-white rounded-lg shadow-md overflow-hidden"
        >
          <img
            :src="icecream.image_url"
            :alt="icecream.flavor"
            class="w-full h-48 object-cover"
          />
          <div class="p-4">
            <h3 class="text-xl font-semibold text-blue-600">
              {{ icecream.icecream_name || icecream.name }}
            </h3>
            <p class="text-gray-600 mt-2">{{ icecream.description }}</p>
            <div class="mt-4 flex justify-between items-center">
              <button
                @click="viewDetails(icecream)"
                class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-300"
              >
                View Details
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Ice Cream Details Modal -->
      <div
        v-if="selectedIceCream"
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
                  {{ selectedIceCream.icecream_name }}
                </h3>
                <div class="mt-2">
                  <img
                    :src="selectedIceCream.image_url"
                    :alt="selectedIceCream.flavor"
                    class="w-full h-48 object-cover rounded-md"
                  />
                  <p class="text-sm text-gray-500 mt-4">
                    Description: {{ selectedIceCream.description }}
                  </p>
                  <p class="text-sm text-gray-500">
                    Manufactured date: {{ selectedIceCream.manufactured_date }}
                  </p>
                  <p class="text-sm text-gray-500">
                    Price: {{ selectedIceCream.price }}
                  </p>
                  <p class="text-sm text-gray-500">
                    Available stocks: {{ selectedIceCream.stock }}
                  </p>
                </div>
              </div>
            </div>
            <div class="mt-5 sm:mt-6">
              <button
                @click="closeDetails"
                type="button"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm"
              >
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
import { ref, computed, watch } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { debounce } from "lodash";
import { Head, router } from "@inertiajs/vue3";

const props = defineProps({
  icecreams: Array,
  icecreamsresult: Array,
});

const searchQuery = ref("");
const selectedIceCream = ref(null);
const filteredIcecreams = ref(Array.isArray(props.icecreams) ? [...props.icecreams] : []);

const performSearch = debounce((query) => {
  if (!query.trim()) {
    resetSearch();
    return;
  }

  router.get(
    "/customers/search",
    { name: query },
    {
      preserveState: true,
      preserveScroll: true,
      only: ['icecreamsresult'],
      onSuccess: (page) => {
        filteredIcecreams.value = page.props.icecreamsresult || [];
      },
      onError: () => {
        console.error("Search failed");
        filteredIcecreams.value = [];
      },
    }
  );
}, 300);

const clearSearch = () => {
  searchQuery.value = "";
  filteredIcecreams.value = Array.isArray(props.icecreams) ? [...props.icecreams] : [];
  router.get("/customers", {}, { preserveState: true });
};

watch(searchQuery, (newQuery) => {
  if (!props.icecreams || !Array.isArray(props.icecreams)) {
    console.error("Invalid icecreams prop. Expected an array.");
    filteredIcecreams.value = [];
    return;
  }

  if (newQuery.trim() === "") {
    clearSearch();
  } else {
    performSearch(newQuery);
  }
});

const viewDetails = (iceCream) => {
  selectedIceCream.value = iceCream;
};

const closeDetails = () => {
  selectedIceCream.value = null;
};
</script>

