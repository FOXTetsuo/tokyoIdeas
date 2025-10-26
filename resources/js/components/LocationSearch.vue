<template>
    <div>
        <label class="block text-xs sm:text-sm font-bold mb-1"
            >📍 LOCATION SEARCH</label
        >
        <div class="relative">
            <input
                v-model="searchQuery"
                @input="debouncedSearch"
                @focus="handleFocus"
                placeholder="Search for a location in Tokyo..."
                class="w-full win95-border-inset px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            />

            <!-- Loading indicator -->
            <div
                v-if="loading"
                class="absolute right-2 top-1/2 -translate-y-1/2"
            >
                <span class="text-xs animate-pulse">🔍</span>
            </div>

            <!-- Results dropdown - Fixed height to show ~5 items -->
            <div
                v-if="showResults && results.length > 0"
                class="absolute z-50 w-full mt-1 win95-border bg-white shadow-lg overflow-y-auto"
                style="max-height: 250px"
            >
                <div
                    v-for="(result, index) in results"
                    :key="index"
                    @click="selectLocation(result)"
                    class="p-3 hover:bg-yellow-100 cursor-pointer border-b border-forum-dark last:border-b-0"
                    style="min-height: 50px"
                >
                    <div class="font-bold text-sm">{{ result.name }}</div>
                    <div class="text-xs text-gray-600 mt-1">
                        {{ result.display_name }}
                    </div>
                </div>
            </div>

            <!-- No results -->
            <div
                v-if="
                    showResults &&
                    !loading &&
                    searchQuery.length > 2 &&
                    results.length === 0
                "
                class="absolute z-50 w-full mt-1 win95-border bg-white p-3 shadow-lg"
            >
                <div class="text-sm text-gray-600">
                    No results found. Try a different search term.
                </div>
            </div>
        </div>

        <!-- Selected location display -->
        <div
            v-if="selectedLocation"
            class="mt-2 win95-border bg-yellow-50 p-2 text-xs"
        >
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="font-bold">
                        ✅ Selected: {{ selectedLocation.name }}
                    </div>
                    <div class="text-gray-600 mt-1">
                        📍 {{ selectedLocation.display_name }}
                    </div>
                    <div class="text-gray-600 mt-1">
                        🗺️ {{ Number(selectedLocation.lat).toFixed(6) }},
                        {{ Number(selectedLocation.lon).toFixed(6) }}
                    </div>
                </div>
                <button
                    @click="clearSelection"
                    type="button"
                    class="text-red-600 hover:text-red-800 font-bold ml-2"
                >
                    ×
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        modelValue: {
            type: Object,
            default: null,
        },
    },
    emits: ["update:modelValue"],
    data() {
        return {
            searchQuery: "",
            results: [],
            loading: false,
            showResults: false,
            selectedLocation: this.modelValue,
            debounceTimer: null,
        };
    },
    watch: {
        modelValue(newVal) {
            this.selectedLocation = newVal;
            if (newVal && newVal.name) {
                this.searchQuery = newVal.name;
            }
        },
    },
    mounted() {
        document.addEventListener("click", this.handleClickOutside);
    },
    beforeUnmount() {
        document.removeEventListener("click", this.handleClickOutside);
    },
    methods: {
        handleFocus() {
            if (this.results.length > 0) {
                this.showResults = true;
            }
        },
        debouncedSearch() {
            clearTimeout(this.debounceTimer);

            if (this.searchQuery.length < 2) {
                this.results = [];
                this.showResults = false;
                return;
            }

            this.debounceTimer = setTimeout(() => {
                this.searchLocation();
            }, 500);
        },
        async searchLocation() {
            this.loading = true;

            try {
                // Photon API - Free and unlimited, better than Nominatim
                const response = await fetch(
                    `https://photon.komoot.io/api/?` +
                        `q=${encodeURIComponent(this.searchQuery)}` +
                        `&lon=139.6503&lat=35.6762` + // Tokyo center for bias
                        `&limit=10` +
                        `&lang=en`,
                    {
                        headers: {
                            Accept: "application/json",
                        },
                    },
                );

                const data = await response.json();

                this.results = data.features.map((feature) => {
                    const props = feature.properties;
                    const coords = feature.geometry.coordinates;

                    // Build a nice display name
                    const nameParts = [
                        props.name,
                        props.street,
                        props.city || props.county,
                        props.state,
                    ].filter(Boolean);

                    return {
                        name: props.name || props.street || "Unknown Location",
                        display_name: nameParts.join(", "),
                        lat: coords[1], // GeoJSON is [lon, lat], so index 1 is latitude
                        lon: coords[0], // GeoJSON is [lon, lat], so index 0 is longitude
                        type: props.type,
                    };
                });

                this.showResults = true;
            } catch (error) {
                console.error("Geocoding error:", error);
                this.results = [];
                this.showResults = true;
            } finally {
                this.loading = false;
            }
        },
        selectLocation(result) {
            this.selectedLocation = {
                name: result.name,
                display_name: result.display_name,
                lat: result.lat,
                lon: result.lon,
            };
            this.searchQuery = result.name;
            this.showResults = false;

            // Emit the selected location to parent
            this.$emit("update:modelValue", {
                location_name: result.name,
                latitude: parseFloat(result.lat),
                longitude: parseFloat(result.lon),
            });
        },
        clearSelection() {
            this.searchQuery = "";
            this.results = [];
            this.selectedLocation = null;
            this.showResults = false;
            this.$emit("update:modelValue", null);
        },
        handleClickOutside(event) {
            if (!this.$el.contains(event.target)) {
                this.showResults = false;
            }
        },
    },
};
</script>

<style scoped>
/* Custom scrollbar for results */
div::-webkit-scrollbar {
    width: 8px;
}

div::-webkit-scrollbar-track {
    background: #f1f1f1;
}

div::-webkit-scrollbar-thumb {
    background: #888;
    border: 1px solid #f1f1f1;
}

div::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
