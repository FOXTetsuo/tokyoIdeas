<template>
    <div class="win95-border bg-forum-bg p-4">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-forum-blue">
                <span class="animate-pulse">►</span> ALL THREADS
            </h2>
            <button @click="showModal = true" class="win95-button text-base">
                ➕ NEW THREAD
            </button>
        </div>

        <div class="win95-border-inset bg-white p-4 mb-4">
            <table class="forum-table">
                <thead>
                    <tr>
                        <th class="text-left">THREAD TITLE</th>
                        <th class="w-32">DATE</th>
                        <th class="w-32">LOCATION</th>
                        <th class="w-48">ACTIONS</th>
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
                                <div>
                                    <div class="font-bold text-forum-blue">
                                        {{ idea.title }}
                                    </div>
                                    <div
                                        v-if="idea.description"
                                        class="text-sm text-gray-600 mt-1"
                                    >
                                        {{ idea.description }}
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
                        <td colspan="4" class="text-center text-gray-500 py-8">
                            <div class="text-4xl mb-2">🌸</div>
                            No threads yet! Start your Tokyo adventure!
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="text-center text-sm text-forum-dark">
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

        <!-- Modal -->
        <div
            v-if="showModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
        >
            <div class="win95-border bg-forum-bg p-1 max-w-2xl w-full">
                <div
                    class="bg-gradient-to-r from-blue-800 to-blue-600 px-3 py-1 flex justify-between items-center mb-1"
                >
                    <span class="text-white font-bold text-sm">
                        {{ editingIdea ? "✏️ EDIT THREAD" : "➕ NEW THREAD" }}
                    </span>
                    <button
                        @click="closeModal"
                        class="text-white hover:bg-blue-700 px-2 text-xl leading-none"
                    >
                        ×
                    </button>
                </div>

                <div class="win95-border-inset bg-white p-4">
                    <form @submit.prevent="saveIdea" class="space-y-3">
                        <div>
                            <label class="block text-sm font-bold mb-1"
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
                            <label class="block text-sm font-bold mb-1"
                                >MESSAGE</label
                            >
                            <textarea
                                v-model="form.description"
                                placeholder="Share your Tokyo trip idea..."
                                rows="4"
                                class="w-full win95-border-inset px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                            ></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-bold mb-1"
                                    >📅 DATE (optional)</label
                                >
                                <input
                                    v-model="form.date"
                                    type="date"
                                    class="w-full win95-border-inset px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-bold mb-1"
                                    >📍 LOCATION NAME (optional)</label
                                >
                                <input
                                    v-model="form.location_name"
                                    placeholder="e.g., Shibuya Crossing"
                                    class="w-full win95-border-inset px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-bold mb-1"
                                    >🗺️ LATITUDE (optional)</label
                                >
                                <input
                                    v-model="form.latitude"
                                    type="number"
                                    step="0.00000001"
                                    placeholder="35.6762"
                                    class="w-full win95-border-inset px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-bold mb-1"
                                    >🗺️ LONGITUDE (optional)</label
                                >
                                <input
                                    v-model="form.longitude"
                                    type="number"
                                    step="0.00000001"
                                    placeholder="139.6503"
                                    class="w-full win95-border-inset px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>
                        </div>

                        <div class="flex gap-2 justify-end pt-2">
                            <button
                                type="button"
                                @click="closeModal"
                                class="win95-button"
                            >
                                ❌ CANCEL
                            </button>
                            <button type="submit" class="win95-button">
                                💾 {{ editingIdea ? "UPDATE" : "POST" }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            ideas: [],
            showModal: false,
            editingIdea: null,
            form: {
                title: "",
                description: "",
                date: "",
                location_name: "",
                latitude: "",
                longitude: "",
            },
        };
    },
    mounted() {
        this.fetchIdeas();
    },
    methods: {
        async fetchIdeas() {
            const response = await fetch("/api/trip-ideas");
            this.ideas = await response.json();
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
            this.form = { ...idea };
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
            this.form = {
                title: "",
                description: "",
                date: "",
                location_name: "",
                latitude: "",
                longitude: "",
            };
        },
        formatDate(date) {
            return new Date(date).toLocaleDateString("en-US", {
                month: "short",
                day: "numeric",
                year: "numeric",
            });
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
