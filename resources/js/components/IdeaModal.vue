tokyoIdeas/resources/js/components/IdeaModal.vue
<template>
    <div
        v-if="visible"
        :class="[
            'fixed inset-0  flex items-center justify-center p-2 sm:p-4 overflow-y-auto',
            modalZIndex,
        ]"
    >
        <div class="win95-border bg-forum-bg p-1 max-w-2xl w-full my-4">
            <div
                class="bg-gradient-to-r from-blue-800 to-blue-600 px-3 py-1 flex justify-between items-center mb-1"
            >
                <span class="text-white font-bold text-xs sm:text-sm">
                    {{ editing ? "EDIT THREAD" : "NEW THREAD" }}
                </span>
                <button
                    @click="close"
                    class="text-white hover:bg-blue-700 px-2 text-xl leading-none"
                >
                    ×
                </button>
            </div>

            <div
                class="win95-border-inset bg-white p-3 sm:p-4 overflow-visible"
                style="min-height: 500px"
            >
                <form @submit.prevent="submit" class="space-y-3">
                    <div>
                        <label class="block text-xs sm:text-sm font-bold mb-1"
                            >THREAD TITLE *</label
                        >
                        <input
                            v-model="form.title"
                            placeholder="Enter thread title..."
                            required
                            class="w-full win95-border-inset px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-bold mb-1"
                            >MESSAGE</label
                        >
                        <textarea
                            v-model="form.description"
                            placeholder="Share your Tokyo trip idea..."
                            rows="4"
                            class="w-full win95-border-inset px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-bold mb-1"
                            >CATEGORY (optional)</label
                        >
                        <select
                            v-model="form.category"
                            class="w-full win95-border-inset px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">-- Select Category --</option>
                            <option value="Activity">Activity</option>
                            <option value="Drinks">Drinks</option>
                            <option value="Food">Food</option>
                            <option value="Museum">Museum</option>
                            <option value="Nightlife">Nightlife</option>
                            <option value="Sexy">Sexy</option>
                            <option value="Shop">Shop</option>
                            <option value="Sight">Sight</option>
                            <option value="Trip">Trip</option>
                            <option value="Weird">Weird</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label
                                class="block text-xs sm:text-sm font-bold mb-1"
                                >📅 DATE
                                {{ dateRequired ? "*" : "(optional)" }}</label
                            >
                            <input
                                v-model="form.date"
                                type="date"
                                :required="dateRequired"
                                class="w-full win95-border-inset px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>

                        <div>
                            <label
                                class="block text-xs sm:text-sm font-bold mb-1"
                                >PRICE ¥ (optional)</label
                            >
                            <input
                                v-model="form.price"
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="5000"
                                class="w-full win95-border-inset px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-bold mb-1"
                            >🔗 URL (optional)</label
                        >
                        <input
                            v-model="form.url"
                            type="url"
                            placeholder="https://example.com"
                            class="w-full win95-border-inset px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>

                    <!-- Location Search Component -->
                    <LocationSearch
                        v-if="showLocationSearch"
                        v-model="locationData"
                        @update:modelValue="handleLocationUpdate"
                    />

                    <!-- Manual coordinate entry (always shown if forced or collapsible) -->
                    <div v-if="!showLocationSearch || showManualCoords">
                        <button
                            v-if="showLocationSearch"
                            type="button"
                            @click="showManualCoords = !showManualCoords"
                            class="text-xs text-forum-blue underline mb-2"
                        >
                            {{ showManualCoords ? "▼" : "►" }} Or enter
                            coordinates manually
                        </button>

                        <div
                            :class="{ 'mt-2': !showLocationSearch }"
                            class="space-y-3"
                        >
                            <div>
                                <label
                                    class="block text-xs sm:text-sm font-bold mb-1"
                                    >LOCATION NAME
                                    {{ locationRequired ? "*" : "" }}</label
                                >
                                <input
                                    v-model="form.location_name"
                                    placeholder="e.g., Shibuya Crossing"
                                    :required="locationRequired"
                                    class="w-full win95-border-inset px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label
                                        class="block text-xs sm:text-sm font-bold mb-1"
                                        >🗺️ LATITUDE
                                        {{ latLngRequired ? "*" : "" }}</label
                                    >
                                    <input
                                        v-model="form.latitude"
                                        type="number"
                                        step="0.00000001"
                                        placeholder="35.6762"
                                        :required="latLngRequired"
                                        class="w-full win95-border-inset px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    />
                                </div>

                                <div>
                                    <label
                                        class="block text-xs sm:text-sm font-bold mb-1"
                                        >🗺️ LONGITUDE
                                        {{ latLngRequired ? "*" : "" }}</label
                                    >
                                    <input
                                        v-model="form.longitude"
                                        type="number"
                                        step="0.00000001"
                                        placeholder="139.6503"
                                        :required="latLngRequired"
                                        class="w-full win95-border-inset px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-2 justify-end pt-2">
                        <button
                            type="button"
                            @click="close"
                            class="win95-button text-xs sm:text-sm"
                        >
                            CANCEL
                        </button>
                        <button
                            type="submit"
                            class="win95-button text-xs sm:text-sm"
                        >
                            {{ editing ? "UPDATE" : "POST" }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import LocationSearch from "./LocationSearch.vue";

export default {
    name: "IdeaModal",
    components: {
        LocationSearch,
    },
    props: {
        visible: {
            type: Boolean,
            default: false,
        },
        editing: {
            type: Boolean,
            default: false,
        },
        initialData: {
            type: Object,
            default: () => ({
                title: "",
                description: "",
                date: "",
                location_name: "",
                latitude: "",
                longitude: "",
                url: "",
                price: "",
                category: "",
            }),
        },
        dateRequired: {
            type: Boolean,
            default: false,
        },
        locationRequired: {
            type: Boolean,
            default: false,
        },
        latLngRequired: {
            type: Boolean,
            default: false,
        },
        showLocationSearch: {
            type: Boolean,
            default: true,
        },
        forceShowManualCoords: {
            type: Boolean,
            default: false,
        },
        modalZIndex: {
            type: String,
            default: "z-50",
        },
    },
    emits: ["update:visible", "submit"],
    data() {
        return {
            form: { ...this.initialData },
            locationData: null,
            showManualCoords: this.forceShowManualCoords,
        };
    },
    watch: {
        initialData: {
            handler(newData) {
                this.form = { ...newData };
                if (newData.location_name) {
                    this.locationData = {
                        name: newData.location_name,
                        display_name: newData.location_name,
                        lat: newData.latitude,
                        lon: newData.longitude,
                    };
                }
            },
            deep: true,
            immediate: true,
        },
        visible(newVisible) {
            if (newVisible) {
                this.form = { ...this.initialData };
                this.locationData = null;
                this.showManualCoords = this.forceShowManualCoords;
                if (this.initialData.location_name) {
                    this.locationData = {
                        name: this.initialData.location_name,
                        display_name: this.initialData.location_name,
                        lat: this.initialData.latitude,
                        lon: this.initialData.longitude,
                    };
                }
            }
        },
    },
    methods: {
        handleLocationUpdate(data) {
            if (data) {
                this.form.location_name = data.location_name;
                this.form.latitude = data.latitude
                    ? parseFloat(data.latitude)
                    : "";
                this.form.longitude = data.longitude
                    ? parseFloat(data.longitude)
                    : "";
            }
        },
        submit() {
            this.$emit("submit", this.form);
        },
        close() {
            this.$emit("update:visible", false);
        },
    },
};
</script>
