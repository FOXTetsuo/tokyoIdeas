<template>
    <div class="win95-border bg-forum-bg p-2 sm:p-4">
        <h2 class="text-xl sm:text-2xl font-bold text-forum-blue mb-4">
            <span class="animate-pulse">►</span> Cool events on specific dates!
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

        <IdeaModal
            v-model:visible="showModal"
            :editing="!!editingIdea"
            :initial-data="editingIdea || form"
            @submit="handleSubmit"
            :date-required="true"
            :location-required="false"
            :lat-lng-required="false"
        />

        <IdeaViewModal
            v-model:visible="showViewModal"
            :idea="viewingIdea"
            @close="showViewModal = false"
            @edit="editIdea(viewingIdea)"
            @delete="deleteIdea(viewingIdea.id)"
        />
    </div>
</template>

<script>
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import IdeaModal from "../components/IdeaModal.vue";
import IdeaViewModal from "../components/IdeaViewModal.vue";

export default {
    components: {
        FullCalendar,
        IdeaModal,
        IdeaViewModal,
    },
    data() {
        return {
            showModal: false,
            editingIdea: null,
            viewingIdea: null,
            showViewModal: false,
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
            this.viewingIdea = info.event.extendedProps;
            this.showViewModal = true;
        },
        handleDateClick(info) {
            this.form.date = info.dateStr;
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
            await this.fetchIdeas();
        },
        editIdea(idea) {
            this.editingIdea = idea;
            this.showModal = true;
            this.showViewModal = false;
        },
        async deleteIdea(id) {
            await fetch(`/api/trip-ideas/${id}`, {
                method: "DELETE",
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });
            this.showViewModal = false;
            await this.fetchIdeas();
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
