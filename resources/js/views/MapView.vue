<template>
    <div class="win95-border bg-forum-bg p-2 sm:p-4">
        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-4"
        >
            <h2 class="text-xl sm:text-2xl font-bold text-forum-blue">
                <span class="animate-pulse">►</span> LOCATION MAP
            </h2>
            <button
                @click="addingMode = !addingMode"
                :class="{ 'win95-border-inset': addingMode }"
                class="win95-button text-sm sm:text-base w-full sm:w-auto"
            >
                {{ addingMode ? "❌ CANCEL" : "➕ ADD LOCATION" }}
            </button>
        </div>

        <div
            v-if="addingMode"
            class="win95-border bg-yellow-100 p-2 sm:p-3 mb-2 sm:mb-4 text-xs sm:text-sm"
        >
            <strong>🗺️ ADD MODE:</strong> Click anywhere on the map to add a new
            trip idea at that location!
        </div>

        <div class="win95-border-inset">
            <div id="map" class="h-64 sm:h-96 md:h-[600px]"></div>
        </div>

        <div
            class="mt-2 sm:mt-4 text-center text-xs sm:text-sm text-forum-dark"
        >
            <img
                src="data:image/gif;base64,R0lGODlhFAAUAKIAAP///wAAAP8AAP//AAAAAAAAAAAAAAAAACwAAAAAFAAUAAADMzi63P4wyklrC0IJnj8t4bkYpFmVJIqubKu27gvH8kzX9o3n+s73/g8MCofEovGITCoBADs="
                alt="map"
                class="inline pixelated"
            />
            Showing {{ markers.length }} location(s) in Tokyo
        </div>

        <!-- Modal -->
        <div
            v-if="showModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[10000] p-2 sm:p-4 overflow-y-auto"
        >
            <div class="win95-border bg-forum-bg p-1 max-w-2xl w-full my-4">
                <div
                    class="bg-gradient-to-r from-blue-800 to-blue-600 px-3 py-1 flex justify-between items-center mb-1"
                >
                    <span class="text-white font-bold text-xs sm:text-sm">
                        ➕ NEW THREAD
                    </span>
                    <button
                        @click="closeModal"
                        class="text-white hover:bg-blue-700 px-2 text-xl leading-none"
                    >
                        ×
                    </button>
                </div>

                <div
                    class="win95-border-inset bg-white p-3 sm:p-4 max-h-[80vh] overflow-y-auto"
                >
                    <form @submit.prevent="saveIdea" class="space-y-3">
                        <div>
                            <label
                                class="block text-xs sm:text-sm font-bold mb-1"
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
                            <label
                                class="block text-xs sm:text-sm font-bold mb-1"
                                >MESSAGE</label
                            >
                            <textarea
                                v-model="form.description"
                                placeholder="Share your Tokyo trip idea..."
                                rows="4"
                                class="w-full win95-border-inset px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                            ></textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label
                                    class="block text-xs sm:text-sm font-bold mb-1"
                                    >📅 DATE (optional)</label
                                >
                                <input
                                    v-model="form.date"
                                    type="date"
                                    class="w-full win95-border-inset px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>

                            <div>
                                <label
                                    class="block text-xs sm:text-sm font-bold mb-1"
                                    >💴 PRICE ¥ (optional)</label
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
                            <label
                                class="block text-xs sm:text-sm font-bold mb-1"
                                >🔗 URL (optional)</label
                            >
                            <input
                                v-model="form.url"
                                type="url"
                                placeholder="https://example.com"
                                class="w-full win95-border-inset px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>

                        <div>
                            <label
                                class="block text-xs sm:text-sm font-bold mb-1"
                                >📍 LOCATION NAME *</label
                            >
                            <input
                                v-model="form.location_name"
                                placeholder="e.g., Shibuya Crossing"
                                required
                                class="w-full win95-border-inset px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label
                                    class="block text-xs sm:text-sm font-bold mb-1"
                                    >🗺️ LATITUDE *</label
                                >
                                <input
                                    v-model="form.latitude"
                                    type="number"
                                    step="0.00000001"
                                    placeholder="35.6762"
                                    required
                                    class="w-full win95-border-inset px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>

                            <div>
                                <label
                                    class="block text-xs sm:text-sm font-bold mb-1"
                                    >🗺️ LONGITUDE *</label
                                >
                                <input
                                    v-model="form.longitude"
                                    type="number"
                                    step="0.00000001"
                                    placeholder="139.6503"
                                    required
                                    class="w-full win95-border-inset px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>
                        </div>

                        <div class="flex gap-2 justify-end pt-2">
                            <button
                                type="button"
                                @click="closeModal"
                                class="win95-button text-xs sm:text-sm"
                            >
                                ❌ CANCEL
                            </button>
                            <button
                                type="submit"
                                class="win95-button text-xs sm:text-sm"
                            >
                                💾 POST
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import L from "leaflet";
import "leaflet/dist/leaflet.css";

// Fix for default marker icons in Leaflet
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl:
        "https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png",
    iconUrl:
        "https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png",
    shadowUrl:
        "https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png",
});

export default {
    data() {
        return {
            map: null,
            markers: [],
            addingMode: false,
            showModal: false,
            form: {
                title: "",
                description: "",
                date: "",
                location_name: "",
                latitude: "",
                longitude: "",
                url: "",
                price: "",
            },
        };
    },
    async mounted() {
        this.initMap();
        await this.fetchIdeas();
    },
    methods: {
        initMap() {
            this.map = L.map("map").setView([35.6762, 139.6503], 11);

            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                attribution: "© OpenStreetMap contributors",
                maxZoom: 19,
            }).addTo(this.map);

            // Add click handler for adding new locations
            this.map.on("click", (e) => {
                if (this.addingMode) {
                    this.form.latitude = e.latlng.lat.toFixed(8);
                    this.form.longitude = e.latlng.lng.toFixed(8);
                    this.showModal = true;
                    this.addingMode = false;
                }
            });

            // Fix map size on window resize
            setTimeout(() => {
                this.map.invalidateSize();
            }, 100);
        },
        async fetchIdeas() {
            const response = await fetch("/api/trip-ideas");
            const ideas = await response.json();

            // Clear existing markers
            this.markers.forEach((marker) => marker.remove());
            this.markers = [];

            ideas
                .filter((idea) => idea.latitude && idea.longitude)
                .forEach((idea) => {
                    const popupContent = `
            <div class="font-bold text-forum-blue text-sm">🎌 ${idea.title}</div>
            ${idea.location_name ? `<div class="text-xs mt-1">📍 ${idea.location_name}</div>` : ""}
            ${idea.description ? `<div class="text-xs mt-1">${idea.description}</div>` : ""}
            ${idea.date ? `<div class="text-xs mt-1">📅 ${new Date(idea.date).toLocaleDateString()}</div>` : ""}
            ${idea.price ? `<div class="text-xs mt-1">💴 ¥${Number(idea.price).toLocaleString("ja-JP")}</div>` : ""}
            ${idea.url ? `<div class="text-xs mt-1 truncate max-w-[200px]">🔗 <a href="${idea.url}" target="_blank" class="text-forum-blue underline">${idea.url}</a></div>` : ""}
          `;

                    const marker = L.marker([idea.latitude, idea.longitude])
                        .addTo(this.map)
                        .bindPopup(popupContent);

                    this.markers.push(marker);
                });

            if (this.markers.length > 0) {
                const group = L.featureGroup(this.markers);
                this.map.fitBounds(group.getBounds().pad(0.1));
            }
        },
        async saveIdea() {
            const response = await fetch("/api/trip-ideas", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: JSON.stringify(this.form),
            });

            if (!response.ok) {
                const error = await response.json();
                console.error("Error saving idea:", error);
                alert("Error saving idea. Check console for details.");
                return;
            }

            this.closeModal();
            await this.fetchIdeas();
        },
        closeModal() {
            this.showModal = false;
            this.form = {
                title: "",
                description: "",
                date: "",
                location_name: "",
                latitude: "",
                longitude: "",
                url: "",
                price: "",
            };
        },
    },
};
</script>

<style>
:deep(.leaflet-popup-content-wrapper) {
    border-style: solid;
    border-width: 2px;
    border-top-color: #ffffff;
    border-left-color: #ffffff;
    border-right-color: #000000;
    border-bottom-color: #000000;
    background: #c0c0c0;
    font-family: "MS Sans Serif", Arial, sans-serif;
}

:deep(.leaflet-popup-tip) {
    background: #c0c0c0;
}

:deep(.leaflet-container) {
    cursor: default;
}

:deep(.leaflet-container.adding-mode) {
    cursor: crosshair !important;
}
</style>
