<template>
    <div class="win95-border bg-forum-bg p-2 sm:p-4">
        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-4"
        >
            <h2 class="text-xl sm:text-2xl font-bold text-forum-blue">
                <span class="animate-pulse">►</span> All Threads
            </h2>
            <button
                @click="openNewModal"
                class="win95-button text-sm sm:text-base w-full sm:w-auto"
            >
                ➕ NEW THREAD
            </button>
        </div>

        <!-- Mobile Card View -->
        <div class="sm:hidden space-y-2">
            <div
                v-for="idea in ideas"
                :key="idea.id"
                class="win95-border bg-white p-3"
            >
                <div class="flex items-start gap-2 mb-2">
                    <span class="text-2xl">{{ getEmoji(idea) }}</span>
                    <div class="flex-1 min-w-0">
                        <div class="font-bold text-forum-blue break-words">
                            {{ idea.title }}
                        </div>
                        <div
                            v-if="idea.description"
                            class="text-sm text-gray-600 mt-1 break-words"
                        >
                            {{ idea.description }}
                        </div>
                    </div>
                </div>

                <div class="text-xs text-gray-600 space-y-1 mb-2">
                    <div v-if="idea.date">📅 {{ formatDate(idea.date) }}</div>
                    <div v-if="idea.location_name">
                        📍 {{ idea.location_name }}
                    </div>
                    <div v-if="idea.price">
                        💴 ¥{{ formatPrice(idea.price) }}
                    </div>
                    <div v-if="idea.url" class="truncate">
                        🔗
                        <a
                            :href="idea.url"
                            target="_blank"
                            class="text-forum-blue underline"
                            >{{ idea.url }}</a
                        >
                    </div>
                </div>

                <div class="flex gap-1">
                    <button
                        @click="editIdea(idea)"
                        class="win95-button text-xs flex-1"
                    >
                        ✏️ EDIT
                    </button>
                    <button
                        @click="deleteIdea(idea.id)"
                        class="win95-button text-xs flex-1"
                    >
                        🗑️ DELETE
                    </button>
                </div>
            </div>

            <div
                v-if="ideas.length === 0"
                class="win95-border bg-white p-8 text-center text-gray-500"
            >
                <div class="text-4xl mb-2">🌸</div>
                No threads yet! Start your Tokyo adventure!
            </div>
        </div>

        <!-- Desktop Table View -->
        <div
            class="hidden sm:block win95-border-inset bg-white p-4 mb-4 overflow-x-auto"
        >
            <table class="forum-table">
                <thead>
                    <tr>
                        <th class="text-left">THREAD TITLE</th>
                        <th class="w-24">DATE</th>
                        <th class="w-32">LOCATION</th>
                        <th class="w-24">PRICE</th>
                        <th class="w-40">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="idea in ideas"
                        :key="idea.id"
                        class="hover:bg-yellow-50"
                    >
                        <td>
                            <div class="flex items-start gap-2">
                                <span class="text-2xl">{{
                                    getEmoji(idea)
                                }}</span>
                                <div class="min-w-0">
                                    <div class="font-bold text-forum-blue">
                                        {{ idea.title }}
                                    </div>
                                    <div
                                        v-if="idea.description"
                                        class="text-sm text-gray-600 mt-1"
                                    >
                                        {{ idea.description }}
                                    </div>
                                    <div
                                        v-if="idea.url"
                                        class="text-xs mt-1 truncate"
                                    >
                                        🔗
                                        <a
                                            :href="idea.url"
                                            target="_blank"
                                            class="text-forum-blue underline"
                                            >{{ idea.url }}</a
                                        >
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span v-if="idea.date" class="text-sm">
                                {{ formatDate(idea.date) }}
                            </span>
                            <span v-else class="text-gray-400">-</span>
                        </td>
                        <td class="text-center">
                            <span v-if="idea.location_name" class="text-sm">
                                📍 {{ idea.location_name }}
                            </span>
                            <span v-else class="text-gray-400">-</span>
                        </td>
                        <td class="text-center">
                            <span v-if="idea.price" class="text-sm">
                                💴 ¥{{ formatPrice(idea.price) }}
                            </span>
                            <span v-else class="text-gray-400">-</span>
                        </td>
                        <td>
                            <div class="flex gap-1 justify-center">
                                <button
                                    @click="editIdea(idea)"
                                    class="win95-button text-xs"
                                >
                                    ✏️ EDIT
                                </button>
                                <button
                                    @click="deleteIdea(idea.id)"
                                    class="win95-button text-xs"
                                >
                                    🗑️ DELETE
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="ideas.length === 0">
                        <td colspan="5" class="text-center text-gray-500 py-8">
                            <div class="text-4xl mb-2">🌸</div>
                            No threads yet! Start your Tokyo adventure!
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="text-center text-xs sm:text-sm text-forum-dark">
            <img
                src="data:image/gif;base64,R0lGODlhFAAUAKIAAP///wAAAP8AAP//AAAAAAAAAAAAAAAAACwAAAAAFAAUAAADMzi63P4wyklrC0IJnj8t4bkYpFmVJIqubKu27gvH8kzX9o3n+s73/g8MCofEovGITCoBADs="
                alt="new"
                class="inline pixelated"
            />
            Total Threads: {{ ideas.length }} | Members Online:
            <span class="text-lime-600 font-bold">{{
                Math.floor(Math.random() * 20) + 5
            }}</span>
        </div>

        <IdeaModal
            v-model:visible="showModal"
            :editing="!!editingIdea"
            :initial-data="editingIdea || emptyForm"
            @submit="handleSubmit"
            :date-required="false"
            :location-required="false"
            :lat-lng-required="false"
        />
    </div>
</template>

<script>
import LocationSearch from "../components/LocationSearch.vue";
import IdeaModal from "../components/IdeaModal.vue";

export default {
    components: {
        LocationSearch,
        IdeaModal,
    },
    data() {
        return {
            ideas: [],
            showModal: false,
            editingIdea: null,
            locationData: null,
            emptyForm: {
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
    mounted() {
        this.fetchIdeas();
    },
    watch: {
        showModal(newVal) {
            if (!newVal) {
                this.closeModal();
            }
        },
    },
    methods: {
        async fetchIdeas() {
            const response = await fetch("/api/trip-ideas");
            this.ideas = await response.json();
        },
        openNewModal() {
            this.editingIdea = null;
            this.showModal = true;
        },
        handleSubmit(formData) {
            this.form = formData;
            this.saveIdea();
        },
        async saveIdea() {
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
                body: JSON.stringify(this.form),
            });

            if (!response.ok) {
                const error = await response.json();
                console.error("Error saving idea:", error);
                alert("Error saving idea. Check console for details.");
                return;
            }

            this.closeModal();
            this.fetchIdeas();
        },
        editIdea(idea) {
            this.editingIdea = idea;
            this.showModal = true;
        },
        async deleteIdea(id) {
            if (
                confirm("🗑️ Delete this thread? This action cannot be undone!")
            ) {
                await fetch(`/api/trip-ideas/${id}`, {
                    method: "DELETE",
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                });
                this.fetchIdeas();
            }
        },
        closeModal() {
            this.showModal = false;
            this.editingIdea = null;
            this.locationData = null;
            this.form = { ...this.emptyForm };
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
        getEmoji(idea) {
            if (idea.location_name && idea.date) return "🎌";
            if (idea.date) return "📅";
            if (idea.location_name) return "📍";
            return "💡";
        },
    },
};
</script>
