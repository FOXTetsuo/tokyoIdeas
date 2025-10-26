<template>
    <div
        v-if="visible"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[10000]"
        @click.self="$emit('close')"
    >
        <div
            class="win95-border bg-white p-4 max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto"
        >
            <h3 class="text-lg font-bold mb-4 text-forum-blue">
                {{ idea.title }}
            </h3>
            <div class="space-y-2 mb-4 text-sm">
                <p v-if="idea.description" class="break-words">
                    <strong>Description:</strong> {{ idea.description }}
                </p>
                <p v-if="idea.date">
                    <strong>Date:</strong> {{ formatDate(idea.date) }}
                </p>
                <p v-if="idea.location_name">
                    <strong>Location:</strong>
                    <MapPin class="w-4 h-4 inline mr-1 text-blue-500" />
                    {{ idea.location_name }}
                </p>
                <p v-if="idea.price">
                    <strong>Price:</strong>
                    <CreditCard class="w-4 h-4 inline mr-1 text-yellow-500" />
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
            </div>
            <div class="flex gap-2 justify-end">
                <button
                    @click="$emit('edit')"
                    class="win95-button bg-green-500 text-white hover:bg-green-600"
                >
                    <Edit class="w-4 h-4 inline mr-1" /> Edit
                </button>
                <button
                    @click="confirmDelete = true"
                    class="win95-button bg-red-500 text-white hover:bg-red-600"
                >
                    <Trash2 class="w-4 h-4 inline mr-1" /> Delete
                </button>
                <button @click="$emit('close')" class="win95-button">
                    Close
                </button>
            </div>
        </div>

        <!-- Confirmation Modal -->
        <div
            v-if="confirmDelete"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[10001]"
            @click.self="confirmDelete = false"
        >
            <div class="win95-border bg-white p-4 max-w-sm w-full mx-4">
                <p class="mb-4 text-center">
                    🗑️ Delete this idea? This cannot be undone!
                </p>
                <div class="flex gap-2 justify-center">
                    <button
                        @click="handleConfirmDelete"
                        class="win95-button bg-red-500 text-white hover:bg-red-600"
                    >
                        Yes, Delete
                    </button>
                    <button @click="confirmDelete = false" class="win95-button">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Edit, Trash2, MapPin, CreditCard } from "lucide-vue-next";

export default {
    components: {
        Edit,
        Trash2,
        MapPin,
        CreditCard,
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
    emits: ["close", "edit", "delete"],
    data() {
        return {
            confirmDelete: false,
        };
    },
    methods: {
        handleConfirmDelete() {
            this.confirmDelete = false;
            this.$emit("delete");
        },
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
    },
};
</script>
