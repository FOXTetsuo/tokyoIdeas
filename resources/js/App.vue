<template>
    <div
        class="min-h-screen bg-linear-to-br from-teal-400 via-purple-500 to-pink-500"
    >
        <!-- Animated background pattern -->
        <div
            class="fixed inset-0 opacity-10 pointer-events-none"
            :style="{ backgroundImage }"
        ></div>

        <div class="relative z-10">
            <!-- Header -->
            <div class="win95-border bg-forum-bg m-4 p-1">
                <div
                    class="bg-linear-to-r from-blue-800 to-blue-600 px-3 py-2 flex items-center justify-between"
                >
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">🗾</span>
                        <h1 class="text-white font-bold text-xl tracking-wider">
                            TOKYO TRIP IDEAS - THE ULTIMATE FORUM
                        </h1>
                    </div>
                    <div class="flex gap-1">
                        <div class="w-4 h-4 win95-border bg-forum-bg"></div>
                        <div class="w-4 h-4 win95-border bg-forum-bg"></div>
                        <div class="w-4 h-4 win95-border bg-forum-bg"></div>
                    </div>
                </div>

                <!-- Navigation Bar -->
                <div class="bg-forum-bg p-2 flex gap-2">
                    <router-link to="/" custom v-slot="{ navigate, isActive }">
                        <button
                            @click="navigate"
                            :class="{ 'win95-border-inset': isActive }"
                            class="win95-button"
                        >
                            📋 MESSAGE BOARD
                        </button>
                    </router-link>
                    <router-link
                        to="/calendar"
                        custom
                        v-slot="{ navigate, isActive }"
                    >
                        <button
                            @click="navigate"
                            :class="{ 'win95-border-inset': isActive }"
                            class="win95-button"
                        >
                            📅 CALENDAR
                        </button>
                    </router-link>
                    <router-link
                        to="/map"
                        custom
                        v-slot="{ navigate, isActive }"
                    >
                        <button
                            @click="navigate"
                            :class="{ 'win95-border-inset': isActive }"
                            class="win95-button"
                        >
                            🗺️ MAP
                        </button>
                    </router-link>
                </div>
            </div>

            <!-- Main Content -->
            <div class="mx-4 mb-4">
                <router-view />
            </div>

            <!-- Footer -->
            <div class="win95-border bg-forum-bg mx-4 mb-4 p-4">
                <div class="flex items-center justify-center gap-4 text-sm">
                    <img
                        src="data:image/gif;base64,R0lGODlhEAAQAMQAAAAAAP///wAAAP8AAP//AP///wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAEACwAAAAAEAAQAAAFPSAgjmRpnmiqrmzrvnAsz3Rt33iu73zv/8CgcEgsGo/IpHLJbDqf0Kh0Sq1ar9isdsvter/gsHhMLpvPCAA7"
                        alt="construction"
                        class="pixelated"
                    />
                    <marquee
                        behavior="scroll"
                        class="text-forum-blue font-bold flex-1"
                    >
                        ✨ Welcome to Tokyo Trip Ideas! Est. 2025 ✨ Best viewed
                        in Netscape Navigator ✨
                    </marquee>
                    <img
                        src="data:image/gif;base64,R0lGODlhEAAQAMQAAAAAAP///wAAAP8AAP//AP///wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAEACwAAAAAEAAQAAAFPSAgjmRpnmiqrmzrvnAsz3Rt33iu73zv/8CgcEgsGo/IpHLJbDqf0Kh0Sq1ar9isdsvter/gsHhMLpvPCAA7"
                        alt="construction"
                        class="pixelated"
                    />
                </div>
                <div class="text-center mt-2 text-xs text-forum-dark">
                    Visitor Counter:
                    <span class="bg-black text-lime-400 px-2 py-1 font-mono">{{
                        visitorCount
                    }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            visitorCount: "000042",
        };
    },
    computed: {
        backgroundImage() {
            return 'url(\'data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="1"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\')';
        },
    },
    mounted() {
        // Simulate visitor counter incrementing
        setInterval(() => {
            const current = parseInt(this.visitorCount);
            this.visitorCount = String(current + 1).padStart(6, "0");
        }, 5000);
    },
};
</script>

<style>
.pixelated {
    image-rendering: pixelated;
    image-rendering: -moz-crisp-edges;
    image-rendering: crisp-edges;
}

marquee {
    animation: none !important;
}
</style>
