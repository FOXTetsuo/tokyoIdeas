<template>
    <div class="win95-border bg-forum-bg p-2 sm:p-4">
        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-4"
        >
            <h2 class="text-xl sm:text-2xl font-bold text-forum-blue">
                <span class="animate-pulse">►</span> Location map
            </h2>
            <button
                @click="addingMode = !addingMode"
                :class="{ 'win95-border-inset': addingMode }"
                class="win95-button text-sm sm:text-base w-full sm:w-auto"
            >
                {{ addingMode ? "Cancel" : "Add a new idea!" }}
            </button>
        </div>

        <div
            v-if="addingMode"
            class="win95-border bg-yellow-100 p-2 sm:p-3 mb-2 sm:mb-4 text-xs sm:text-sm"
        >
            <strong>🗺️ ADD MODE:</strong> Click anywhere on the map to add a new
            trip idea at that location!
        </div>

        <!-- Modal -->
        <IdeaModal
            v-model:visible="showModal"
            :editing="!!editingIdea"
            :initial-data="editingIdea || form"
            @submit="saveIdea"
            :date-required="false"
            :location-required="true"
            :lat-lng-required="true"
            :show-location-search="false"
            :force-show-manual-coords="true"
            :modal-z-index="'z-[10000]'"
        />

        <IdeaViewModal
            v-model:visible="showViewModal"
            :idea="viewingIdea"
            @close="showViewModal = false"
            @rating-updated="applyRatingUpdate"
            @edit="
                editIdea(viewingIdea);
                showViewModal = false;
            "
            @delete="
                showConfirmDelete = true;
                deleteId = viewingIdea.id;
            "
        />

        <ConfirmationModal
            v-model:visible="showConfirmDelete"
            message="🗑️ Delete this idea? This cannot be undone!"
            @confirm="confirmDelete($event)"
            @cancel="showConfirmDelete = false"
        />

        <div class="win95-border-inset">
            <div id="map" class="h-64 sm:h-96 md:h-[600px]"></div>
        </div>

        <div
            class="mt-2 sm:mt-4 text-center text-xs sm:text-sm text-forum-dark"
        >
            Showing {{ markers.length }} location(s) in Tokyo
        </div>
    </div>
</template>

<script>
import L from "leaflet";
import "leaflet/dist/leaflet.css";
import IdeaModal from "../components/IdeaModal.vue";
import IdeaViewModal from "../components/IdeaViewModal.vue";
import ConfirmationModal from "../components/ConfirmationModal.vue";

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
    components: {
        IdeaModal,
        IdeaViewModal,
        ConfirmationModal,
    },
    data() {
        return {
            map: null,
            markers: [],
            ideas: [],
            addingMode: false,
            showModal: false,
            editingIdea: null,
            showViewModal: false,
            viewingIdea: null,
            showConfirmDelete: false,
            deleteId: null,
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
        window.viewIdea = (id) => this.viewIdeaById(id);
    },
    methods: {
        initMap() {
            this.map = L.map("map").setView([35.6762, 139.6503], 11);

            L.tileLayer(
                "https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png",
                {
                    attribution:
                        '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                    maxZoom: 19,
                },
            ).addTo(this.map);

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
            this.ideas = await response.json();

            // Clear existing markers
            this.markers.forEach((marker) => marker.remove());
            this.markers = [];

            this.ideas
                .filter((idea) => idea.latitude && idea.longitude)
                .forEach((idea) => {
                    const truncatedDesc =
                        idea.description && idea.description.length > 100
                            ? idea.description.substring(0, 100) + "..."
                            : idea.description || "";
                    const popupContent = `
            <div class="font-bold text-forum-blue text-sm">🎌 ${idea.title}</div>
            ${idea.location_name ? `<div class="text-xs mt-1">📍 ${idea.location_name}</div>` : ""}
            ${truncatedDesc ? `<div class="text-xs mt-1">${truncatedDesc}</div>` : ""}
            ${idea.date ? `<div class="text-xs mt-1">📅 ${new Date(idea.date).toLocaleDateString()}</div>` : ""}
            ${idea.price ? `<div class="text-xs mt-1">💴 ¥${Number(idea.price).toLocaleString("ja-JP")}</div>` : ""}
            ${idea.url ? `<div class="text-xs mt-1 truncate max-w-[200px]">🔗 <a href="${idea.url}" target="_blank" class="text-forum-blue underline">${idea.url}</a></div>` : ""}
            <div class="mt-2">
                <button class="win95-button text-xs bg-blue-500 text-white" onclick="window.viewIdea(${idea.id})">View</button>
            </div>
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
        async saveIdea(formData) {
            const url = this.editingIdea
                ? `/api/trip-ideas/${this.editingIdea.id}`
                : "/api/trip-ideas";
            const method = this.editingIdea ? "PUT" : "POST";

            const response = await fetch(url, {
                method,
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: JSON.stringify(formData),
            });

            if (!response.ok) {
                const error = await response.json();
                console.error("Error saving idea:", error);
                alert("Error saving idea. Check console for details.");
                return;
            }

            this.showModal = false;
            this.resetForm();
            await this.fetchIdeas();
        },
        resetForm() {
            this.editingIdea = null;
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
        viewIdeaById(id) {
            const idea = this.ideas.find((i) => i.id == id);
            if (idea) this.viewIdea(idea);
        },
        viewIdea(idea) {
            this.viewingIdea = idea;
            this.showViewModal = true;
        },
        editIdea(idea) {
            this.editingIdea = idea;
            this.showModal = true;
        },
        confirmDelete(password) {
            fetch(`/api/trip-ideas/${this.deleteId}`, {
                method: "DELETE",
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-Delete-Password": password,
                },
            }).then(() => {
                this.showConfirmDelete = false;
                this.showViewModal = false;
                this.fetchIdeas();
            });
        },
        applyRatingUpdate(payload) {
            const match = this.ideas.find(
                (idea) => Number(idea.id) === Number(payload.trip_idea_id),
            );

            if (match) {
                match.my_rating = payload.my_rating;
                match.rating_average = payload.rating_average;
                match.rating_count = payload.rating_count;
                match.wemust_count = payload.wemust_count;
                match.latest_votes = payload.latest_votes;
            }

            if (
                this.viewingIdea &&
                Number(this.viewingIdea.id) === Number(payload.trip_idea_id)
            ) {
                this.viewingIdea.my_rating = payload.my_rating;
                this.viewingIdea.rating_average = payload.rating_average;
                this.viewingIdea.rating_count = payload.rating_count;
                this.viewingIdea.wemust_count = payload.wemust_count;
                this.viewingIdea.latest_votes = payload.latest_votes;
            }
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
