<template>
    <div class="win95-border bg-forum-bg p-4">
        <h2 class="text-2xl font-bold text-forum-blue mb-4">
            <span class="animate-pulse">►</span> LOCATION MAP
        </h2>

        <div class="win95-border-inset">
            <div id="map" style="height: 600px"></div>
        </div>

        <div class="mt-4 text-center text-sm text-forum-dark">
            <img
                src="data:image/gif;base64,R0lGODlhFAAUAKIAAP///wAAAP8AAP//AAAAAAAAAAAAAAAAACwAAAAAFAAUAAADMzi63P4wyklrC0IJnj8t4bkYpFmVJIqubKu27gvH8kzX9o3n+s73/g8MCofEovGITCoBADs="
                alt="map"
                class="inline pixelated"
            />
            Showing {{ markers.length }} location(s) in Tokyo
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
        },
        async fetchIdeas() {
            const response = await fetch("/api/trip-ideas");
            const ideas = await response.json();

            ideas
                .filter((idea) => idea.latitude && idea.longitude)
                .forEach((idea) => {
                    const marker = L.marker([
                        idea.latitude,
                        idea.longitude,
                    ]).addTo(this.map).bindPopup(`
              <div class="font-bold text-forum-blue">🎌 ${idea.title}</div>
              ${idea.location_name ? `<div class="text-sm">📍 ${idea.location_name}</div>` : ""}
              ${idea.description ? `<div class="text-sm mt-1">${idea.description}</div>` : ""}
              ${idea.date ? `<div class="text-sm mt-1">📅 ${new Date(idea.date).toLocaleDateString()}</div>` : ""}
            `);

                    this.markers.push(marker);
                });

            if (this.markers.length > 0) {
                const group = L.featureGroup(this.markers);
                this.map.fitBounds(group.getBounds().pad(0.1));
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
</style>
