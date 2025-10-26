import { createRouter, createWebHistory } from "vue-router";
import ListView from "../views/ListView.vue";
import MapView from "../views/MapView.vue";
import CalenderView from "../views/CalenderView.vue";

const routes = [
    {
        path: "/",
        name: "ListView",
        component: ListView,
    },
    {
        path: "/calendar",
        name: "CalendarView",
        component: CalenderView,
    },
    {
        path: "/map",
        name: "MapView",
        component: MapView,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
