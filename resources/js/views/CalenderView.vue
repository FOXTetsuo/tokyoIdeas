<template>
    <div class="win95-border bg-forum-bg p-4">
        <h2 class="text-2xl font-bold text-forum-blue mb-4">
            <span class="animate-pulse">►</span> TRIP CALENDAR
        </h2>

        <div class="win95-border-inset bg-white p-4">
            <FullCalendar :options="calendarOptions" />
        </div>

        <div class="mt-4 text-center text-sm text-forum-dark">
            <img
                src="data:image/gif;base64,R0lGODlhFAAUAKIAAP///wAAAP8AAP//AAAAAAAAAAAAAAAAACwAAAAAFAAUAAADMzi63P4wyklrC0IJnj8t4bkYpFmVJIqubKu27gvH8kzX9o3n+s73/g8MCofEovGITCoBADs="
                alt="calendar"
                class="inline pixelated"
            />
            Click on events to see details!
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
            calendarOptions: {
                plugins: [dayGridPlugin, interactionPlugin],
                initialView: "dayGridMonth",
                events: [],
                eventClick: this.handleEventClick,
                headerToolbar: {
                    left: "prev,next today",
                    center: "title",
                    right: "dayGridMonth,dayGridWeek",
                },
                eventColor: "#0000ff",
                eventTextColor: "#ffffff",
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
            alert(
                `📅 ${info.event.title}\n\n${idea.description || "No description"}\n\n${idea.location_name ? "📍 " + idea.location_name : ""}`,
            );
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
    font-size: 11px !important;
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
    font-size: 1.5rem !important;
}

:deep(.fc-daygrid-day) {
    border-color: #000000 !important;
}

:deep(.fc-col-header-cell) {
    background: #0000ff !important;
    color: white !important;
    font-weight: bold !important;
    border-color: #000000 !important;
}

:deep(.fc-event) {
    border-style: solid !important;
    border-width: 2px !important;
    border-top-color: #ffffff !important;
    border-left-color: #ffffff !important;
    border-right-color: #000000 !important;
    border-bottom-color: #000000 !important;
    cursor: pointer !important;
}

:deep(.fc-daygrid-day.fc-day-today) {
    background-color: #ffff00 !important;
}
</style>
