<template>
    <div
        v-if="visible"
        class="fixed inset-0 flex items-center justify-center z-[10000]"
        @click.self="$emit('close')"
    >
        <div
            class="win95-border bg-white p-4 max-w-md md:max-w-xl w-full mx-4 max-h-[90vh] overflow-y-auto"
        >
            <h3 class="text-lg font-bold mb-4 text-forum-blue">
                {{ idea.title }}
            </h3>
            <div class="space-y-2 mb-4 text-sm">
                <p v-if="idea.description" class="break-words">
                    <strong>Description:</strong> {{ idea.description }}
                </p>
                <p v-if="idea.category">
                    <strong>Category:</strong>
                    {{ idea.category }}
                </p>
                <p v-if="idea.date">
                    <strong>Date:</strong> {{ formatDate(idea.date) }}
                </p>
                <p v-if="idea.location_name">
                    <strong>Location:</strong>
                    {{ idea.location_name }}
                </p>
                <div v-if="idea.latitude && idea.longitude" class="mt-2">
                    <div
                        id="small-map"
                        class="h-48 w-full win95-border-inset"
                    ></div>
                </div>
                <p v-if="idea.price">
                    <strong>Price:</strong>
                    ¥{{ formatPrice(idea.price) }}
                </p>
                <p v-if="idea.url">
                    <strong>URL:</strong>
                    <a
                        :href="idea.url"
                        target="_blank"
                        class="text-forum-blue underline"
                    >
                        {{ idea.url }}
                    </a>
                </p>

                <IdeaRatingPanel :idea="idea" @updated="handleRatingUpdated" />
            </div>
            <div class="flex gap-2 justify-end">
                <button
                    @click="$emit('edit')"
                    class="win95-button bg-green-500 text-white hover:bg-green-600"
                >
                    Edit
                </button>
                <button
                    @click="$emit('delete')"
                    class="win95-button bg-red-500 text-white hover:bg-red-600"
                >
                    Delete
                </button>
                <button @click="$emit('close')" class="win95-button">
                    Close
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import L from "leaflet";
import "leaflet/dist/leaflet.css";
import IdeaRatingPanel from "./IdeaRatingPanel.vue";

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
        IdeaRatingPanel,
    },
    props: {
        idea: {
            type: Object,
            required: true,
        },
        visible: {
            type: Boolean,
            default: false,
        },
    },
    emits: ["close", "edit", "delete", "rating-updated"],
    data() {
        return {
            smallMap: null,
        };
    },
    watch: {
        visible(val) {
            if (val && this.idea.latitude && this.idea.longitude) {
                this.$nextTick(() => this.initSmallMap());
            }
        },
    },
    methods: {
        formatDate(date) {
            return new Date(date).toLocaleDateString("en-US", {
                month: "short",
                day: "numeric",
                year: "numeric",
            });
        },
        formatPrice(price) {
            return Number(price).toLocaleString("ja-JP");
        },

        initSmallMap() {
            if (this.smallMap) {
                this.smallMap.remove();
            }
            this.smallMap = L.map("small-map").setView(
                [this.idea.latitude, this.idea.longitude],
                15,
            );
            L.tileLayer(
                "https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png",
                {
                    attribution:
                        '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                    maxZoom: 19,
                },
            ).addTo(this.smallMap);
            L.marker([this.idea.latitude, this.idea.longitude]).addTo(
                this.smallMap,
            );
            setTimeout(() => {
                this.smallMap.invalidateSize();
            }, 100);
        },
        handleRatingUpdated(payload) {
            this.$emit("rating-updated", payload);
        },
    },
};
</script>
