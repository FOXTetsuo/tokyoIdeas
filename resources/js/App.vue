<template>
    <div
        class="min-h-screen py-4 px-2 sm:py-8 sm:px-4"
        style="
            background-image: url(&quot;/images/blobz.gif&quot;);
            background-repeat: repeat;
        "
    >
        <!-- Animated background pattern -->
        <!-- 4:3 Container -->
        <div class="relative z-10 mx-auto max-w-7xl bg-white">
            <div class="win95-border bg-forum-bg p-1">
                <div
                    class="bg-gradient-to-r from-blue-800 to-blue-600 px-2 sm:px-3 py-2 flex items-center justify-between"
                >
                    <div class="flex items-center gap-1 sm:gap-2">
                        <img
                            src="/images/Japan.gif"
                            alt="Japan"
                            class="w-6 h-6 sm:w-8 sm:h-8"
                        />
                        <h1
                            class="text-white font-bold text-xs sm:text-xl tracking-wider"
                        >
                            TOKYO TRIP IDEAS
                        </h1>
                    </div>
                    <div class="hidden sm:flex gap-1">
                        <div class="w-4 h-4 win95-border bg-forum-bg"></div>
                        <div class="w-4 h-4 win95-border bg-forum-bg"></div>
                        <div class="w-4 h-4 win95-border bg-forum-bg"></div>
                    </div>
                </div>

                <!-- Navigation Bar -->
                <div
                    class="bg-forum-bg p-1 sm:p-2 flex flex-wrap gap-1 sm:gap-2"
                >
                    <router-link to="/" custom v-slot="{ navigate, isActive }">
                        <button
                            @click="navigate"
                            :class="{ 'win95-border-inset': isActive }"
                            class="win95-button text-xs sm:text-sm"
                        >
                            📋
                            <span class="hidden sm:inline">Forum</span
                            ><span class="sm:hidden">Forum</span>
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
                            class="win95-button text-xs sm:text-sm"
                        >
                            📅 Calendar
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
                            class="win95-button text-xs sm:text-sm"
                        >
                            🗺️ Map
                        </button>
                    </router-link>
                </div>
            </div>

            <!-- Main Content -->
            <div class="mt-0">
                <router-view />
            </div>

            <!-- Footer -->
            <div class="win95-border bg-forum-bg mt-0 p-2 sm:p-4">
                <div
                    class="flex items-center justify-center gap-2 sm:gap-4 text-xs sm:text-sm"
                >
                    <marquee
                        behavior="scroll"
                        class="text-forum-blue font-bold flex-1"
                    >
                        <transition name="fade" mode="out-in">
                            <span :key="currentMessageIndex">
                                {{ currentMarqueeMessage }}
                            </span>
                        </transition>
                    </marquee>
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
            marqueeMessages: [
                "*** Welcome to Tokyo Trip Ideas! ***",
                "*** Go do some weird shit! ***",
                "*** Explore Tokyo like never before! ***",
            ],
            currentMessageIndex: 0,
        };
    },
    computed: {
        currentMarqueeMessage() {
            return this.marqueeMessages[this.currentMessageIndex];
        },
    },
    mounted() {
        setInterval(() => {
            const current = parseInt(this.visitorCount);
            this.visitorCount = String(current + 1).padStart(6, "0");
        }, 5000);

        // Change marquee message periodically (every 10 seconds)
        setInterval(() => {
            this.changeMarqueeMessage();
        }, 10000);
    },
    methods: {
        changeMarqueeMessage() {
            // Get a random index different from the current one
            let newIndex;
            do {
                newIndex = Math.floor(
                    Math.random() * this.marqueeMessages.length,
                );
            } while (
                newIndex === this.currentMessageIndex &&
                this.marqueeMessages.length > 1
            );

            this.currentMessageIndex = newIndex;
        },
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

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.8s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.fade-enter-to,
.fade-leave-from {
    opacity: 1;
}
</style>
