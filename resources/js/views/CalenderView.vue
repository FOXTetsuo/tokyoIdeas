<template>
    <div class="win95-border bg-forum-bg p-2 sm:p-4">
        <h2 class="text-xl sm:text-2xl font-bold text-forum-blue mb-4">
            <span class="animate-pulse">►</span> TRIP CALENDAR
        </h2>

        <div class="win95-border-inset bg-white p-2 sm:p-4">
            <FullCalendar :options="calendarOptions" />
        </div>

        <div class="mt-4 text-center text-xs sm:text-sm text-forum-dark">
            <img
                src="data:image/gif;base64,R0lGODlhFAAUAKIAAP///wAAAP8AAP//AAAAAAAAAAAAAAAAACwAAAAAFAAUAAADMzi63P4wyklrC0IJnj8t4bkYpFmVJIqubKu27gvH8kzX9o3n+s73/g8MCofEovGITCoBADs="
                alt="calendar"
                class="inline pixelated"
            />
            Click on events to see details! Click on a date to add new idea!
        </div>

        <!-- Modal -->
        <div
            v-if="showModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-2 sm:p-4 overflow-y-auto"
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
                                    >📅 DATE *</label
                                >
                                <input
                                    v-model="form.date"
                                    type="date"
                                    required
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
                                >📍 LOCATION NAME (optional)</label
                            >
                            <input
                                v-model="form.location_name"
                                placeholder="e.g., Shibuya Crossing"
                                class="w-full win95-border-inset px-2 py-1 text-sm focus:outline-none focus:ring-blue-500"
                            />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label
                                    class="block text-xs sm:text-sm font-bold mb-1"
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
                                <label
                                    class="block text-xs sm:text-sm font-bold mb-1"
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
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";

export default {
    components: {
        FullCalendar,
    },
    data() {
        return {
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
            calendarOptions: {
                plugins: [dayGridPlugin, interactionPlugin],
                initialView: "dayGridMonth",
                events: [],
                eventClick: this.handleEventClick,
                dateClick: this.handleDateClick,
                headerToolbar: {
                    left: "prev,next today",
                    center: "title",
                    right: "dayGridMonth,dayGridWeek",
                },
                eventColor: "#0000ff",
                eventTextColor: "#ffffff",
                height: "auto",
                contentHeight: "auto",
            },
        };
    },
    async mounted() {
        await this.fetchIdeas();
    },
    methods: {
        async fetchIdeas() {
            const response = await fetch("/api/trip-ideas");
            const ideas = await response.json();

            this.calendarOptions.events = ideas
                .filter((idea) => idea.date)
                .map((idea) => ({
                    id: idea.id,
                    title: `🎌 ${idea.title}`,
                    start: idea.date,
                    extendedProps: idea,
                }));
        },
        handleEventClick(info) {
            const idea = info.event.extendedProps;
            const details = [
                `📅 ${info.event.title}`,
                "",
                idea.description || "No description",
                "",
                idea.location_name ? `📍 ${idea.location_name}` : "",
                idea.price
                    ? `💴 ¥${Number(idea.price).toLocaleString("ja-JP")}`
                    : "",
                idea.url ? `🔗 ${idea.url}` : "",
            ]
                .filter((line) => line !== "")
                .join("\n");

            alert(details);
        },
        handleDateClick(info) {
            this.form.date = info.dateStr;
            this.showModal = true;
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
/* FullCalendar 90s styling */
:deep(.fc) {
    font-family: "MS Sans Serif", Arial, sans-serif;
}

:deep(.fc-button) {
    border-style: solid !important;
    border-width: 2px !important;
    border-top-color: #ffffff !important;
    border-left-color: #ffffff !important;
    border-right-color: #000000 !important;
    border-bottom-color: #000000 !important;
    background: #c0c0c0 !important;
    color: #000000 !important;
    box-shadow: none !important;
    font-weight: bold !important;
    text-transform: uppercase !important;
    font-size: 10px !important;
    padding: 4px 8px !important;
}

:deep(.fc-button:active) {
    border-top-color: #000000 !important;
    border-left-color: #000000 !important;
    border-right-color: #ffffff !important;
    border-bottom-color: #ffffff !important;
}

:deep(.fc-button-primary:disabled) {
    opacity: 0.5;
}

:deep(.fc-toolbar-title) {
    color: #0000ff !important;
    font-weight: bold !important;
    font-size: 1.2rem !important;
}

@media (min-width: 640px) {
    :deep(.fc-toolbar-title) {
        font-size: 1.5rem !important;
    }

    :deep(.fc-button) {
        font-size: 11px !important;
    }
}

:deep(.fc-daygrid-day) {
    border-color: #000000 !important;
}

:deep(.fc-col-header-cell) {
    background: #0000ff !important;
    color: white !important;
    font-weight: bold !important;
    border-color: #000000 !important;
    font-size: 11px !important;
    padding: 4px 2px !important;
}

:deep(.fc-event) {
    border-style: solid !important;
    border-width: 2px !important;
    border-top-color: #ffffff !important;
    border-left-color: #ffffff !important;
    border-right-color: #000000 !important;
    border-bottom-color: #000000 !important;
    cursor: pointer !important;
    font-size: 11px !important;
    padding: 1px 2px !important;
}

:deep(.fc-daygrid-day.fc-day-today) {
    background-color: #ffff00 !important;
}

:deep(.fc-daygrid-day-number) {
    font-size: 12px !important;
    padding: 2px !important;
}

:deep(.fc-scrollgrid) {
    border-color: #000000 !important;
}

:deep(.fc-daygrid-day:hover) {
    background-color: #e0e0e0 !important;
    cursor: pointer !important;
}
</style>
