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
                New Thread
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
                        @click="viewIdea(idea)"
                        class="win95-button text-xs flex-1 bg-blue-500 hover:bg-blue-600 text-white"
                    >
                        View
                    </button>
                    <button
                        @click="editIdea(idea)"
                        class="win95-button text-xs flex-1"
                    >
                        Edit
                    </button>
                    <button
                        @click="deleteIdea(idea.id)"
                        class="win95-button text-xs flex-1"
                    >
                        DELETE
                    </button>
                </div>
            </div>

            <div
                v-if="ideas.length === 0"
                class="win95-border bg-white p-8 text-center text-gray-500"
            >
                <div class="text-4xl mb-2">
                    <Sprout class="w-16 h-16 text-pink-500" />
                </div>
                No threads yet! Start your Tokyo adventure!
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden sm:block win95-border-inset bg-white p-4 mb-4">
            <table
                class="w-full table-fixed border-separate border-spacing-y-1"
            >
                <thead>
                    <tr>
                        <th class="text-left w-[50%]">THREAD TITLE</th>
                        <th class="text-center w-[15%]">DATE</th>
                        <th class="text-center w-[15%]">LOCATION</th>
                        <th class="text-center w-[10%]">PRICE</th>
                        <th class="text-center w-[10%]">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(idea, index) in ideas"
                        :key="idea.id"
                        :class="[
                            'hover:bg-yellow-50',
                            index % 2 === 0 ? 'bg-pink-50' : 'bg-purple-50',
                        ]"
                    >
                        <td class="break-all align-top">
                            <div class="flex items-start gap-2">
                                <div class="min-w-0">
                                    <div
                                        class="font-bold text-forum-blue break-all"
                                    >
                                        {{ idea.title }}
                                    </div>
                                    <div
                                        v-if="idea.description"
                                        class="text-sm text-gray-600 mt-1 break-all"
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
                        <td class="text-center align-top">
                            <span v-if="idea.date" class="text-sm">
                                {{ formatDate(idea.date) }}
                            </span>
                            <span v-else class="text-gray-400">-</span>
                        </td>
                        <td class="text-center align-top">
                            <span v-if="idea.location_name" class="text-sm">
                                {{ idea.location_name }}
                            </span>
                            <span v-else class="text-gray-400">-</span>
                        </td>
                        <td class="text-center align-top">
                            <span v-if="idea.price" class="text-sm">
                                ¥{{ formatPrice(idea.price) }}
                            </span>
                            <span v-else class="text-gray-400">-</span>
                        </td>
                        <td class="align-top">
                            <div class="flex gap-1 justify-center">
                                <button
                                    @click="viewIdea(idea)"
                                    class="win95-button text-xs flex-1 bg-blue-500 hover:bg-blue-600 text-white"
                                >
                                    VIEW
                                </button>
                                <button
                                    @click="editIdea(idea)"
                                    class="win95-button text-xs flex-1 bg-green-500 hover:bg-green-600 text-white"
                                >
                                    EDIT
                                </button>
                                <button
                                    @click="deleteIdea(idea.id)"
                                    class="win95-button text-xs flex-1 bg-red-500 hover:bg-red-600 text-white"
                                >
                                    DELETE
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="ideas.length === 0">
                        <td colspan="5" class="text-center text-gray-500 py-8">
                            <div class="text-4xl mb-2">
                                <Sprout class="w-16 h-16 text-pink-500" />
                            </div>
                            No threads yet! Start your Tokyo adventure!
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="text-center text-xs sm:text-sm text-forum-dark">
            Total cool ideas: {{ ideas.length }}
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

        <IdeaViewModal
            v-model:visible="showViewModal"
            :idea="viewingIdea"
            @close="showViewModal = false"
            @edit="
                editIdea(viewingIdea);
                showViewModal = false;
            "
            @delete="doDelete(viewingIdea.id)"
        />

        <ConfirmationModal
            v-model:visible="showConfirmDelete"
            message="🗑️ Delete this idea? This cannot be undone!"
            @confirm="confirmDelete"
            @cancel="showConfirmDelete = false"
        />
    </div>
</template>

<script>
import LocationSearch from "../components/LocationSearch.vue";
import IdeaModal from "../components/IdeaModal.vue";
import IdeaViewModal from "../components/IdeaViewModal.vue";
import ConfirmationModal from "../components/ConfirmationModal.vue";
import { Sprout } from "lucide-vue-next";

export default {
    components: {
        LocationSearch,
        IdeaModal,
        IdeaViewModal,
        ConfirmationModal,
        Sprout,
    },
    data() {
        return {
            ideas: [],
            showModal: false,
            editingIdea: null,
            locationData: null,
            showViewModal: false,
            viewingIdea: null,
            showConfirmDelete: false,
            deleteId: null,
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
        viewIdea(idea) {
            this.viewingIdea = idea;
            this.showViewModal = true;
        },
        editIdea(idea) {
            this.editingIdea = idea;
            this.showModal = true;
        },
        deleteIdea(id) {
            this.showConfirmDelete = true;
            this.deleteId = id;
        },
        async confirmDelete() {
            await fetch(`/api/trip-ideas/${this.deleteId}`, {
                method: "DELETE",
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });
            this.showConfirmDelete = false;
            this.fetchIdeas();
        },
        async doDelete(id) {
            await fetch(`/api/trip-ideas/${id}`, {
                method: "DELETE",
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });
            this.showViewModal = false;
            this.fetchIdeas();
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
    },
};
</script>
