<template>
    <div class="mt-1">
        <div class="flex items-center gap-2 mb-1">
            <span class="text-[11px] text-gray-700">Rating:</span>
            <div
                class="flex-1 h-4 win95-border-inset bg-forum-bg relative overflow-hidden"
            >
                <div
                    v-if="hasRatings"
                    class="absolute top-0 bottom-0 left-0"
                    :style="fillStyle"
                ></div>
                <span
                    v-if="showWow"
                    class="absolute right-1 top-1/2 -translate-y-1/2 text-[9px] font-bold text-white tracking-wide"
                >
                    WOW!
                </span>
                <div
                    v-if="hasRatings"
                    class="absolute top-0 bottom-0 w-[2px] bg-black/60"
                    :style="{ left: markerLeft }"
                ></div>
            </div>
        </div>

        <div class="grid grid-cols-4 gap-1 mt-1">
            <div
                v-for="option in ratingOptions"
                :key="option.value"
                class="flex flex-col items-center gap-0.5"
            >
                <button
                    type="button"
                    :title="option.label"
                    @click.prevent="setRating(option.value)"
                    :class="[
                        'win95-button text-[10px] leading-none px-0 py-0.5 flex items-center justify-center w-full',
                        isSelected(option.value)
                            ? 'win95-border-inset bg-blue-100'
                            : '',
                    ]"
                    :disabled="saving"
                >
                    ▲
                </button>
                <span
                    class="text-[9px] leading-tight text-center text-gray-700"
                    :class="{
                        'font-bold text-forum-blue': isSelected(option.value),
                    }"
                >
                    {{ option.label }}
                </span>
            </div>
        </div>

        <div v-if="votesSummary" class="text-[10px] text-gray-600 mt-1">
            {{ votesSummary }}
        </div>

        <p v-if="error" class="text-[10px] text-red-600 mt-1">{{ error }}</p>

        <RatingAuthModal
            :visible="showAuthModal"
            :loading="authLoading"
            :error="authError"
            @confirm="createSession"
            @cancel="cancelAuth"
        />
    </div>
</template>

<script>
import RatingAuthModal from "./RatingAuthModal.vue";

const ratingOptions = [
    { value: 0, label: "Don't love it..." },
    { value: 1, label: "Cool!" },
    { value: 2, label: "LOVE it!" },
    { value: 3, label: "NEED!" },
];

export default {
    components: {
        RatingAuthModal,
    },
    props: {
        idea: {
            type: Object,
            required: true,
        },
    },
    emits: ["updated"],
    data() {
        return {
            ratingOptions,
            saving: false,
            error: "",
            authLoading: false,
            authError: "",
            showAuthModal: false,
            pendingRating: null,
        };
    },
    computed: {
        ratingCount() {
            return Number(this.idea.rating_count || 0);
        },
        hasRatings() {
            return this.ratingCount > 0;
        },
        wemustCount() {
            return Number(this.idea.wemust_count || 0);
        },
        showWow() {
            return this.wemustCount > 1;
        },
        markerLeft() {
            const avg = Number(this.idea.rating_average || 0);
            const percent = Math.max(0, Math.min(100, (avg / 3) * 100));
            return `calc(${percent}% - 1px)`;
        },
        fillStyle() {
            const avg = Number(this.idea.rating_average || 0);
            const percent = Math.max(0, Math.min(100, (avg / 3) * 100));

            return {
                width: "100%",
                background:
                    "linear-gradient(90deg, #355bcc 0%, #169a90 25%, #9fe024 50%, #ef874e 75%, #dd551c 100%)",
                clipPath: `inset(0 ${100 - percent}% 0 0)`,
            };
        },
        latestVotes() {
            if (!Array.isArray(this.idea.latest_votes)) {
                return [];
            }

            return this.idea.latest_votes
                .map((vote) => {
                    const name = typeof vote?.name === "string" ? vote.name.trim() : "";
                    const rating = Number(vote?.rating);

                    return {
                        name,
                        rating,
                    };
                })
                .filter(
                    (vote) =>
                        vote.name.length > 0 &&
                        Number.isInteger(vote.rating) &&
                        vote.rating >= 0 &&
                        vote.rating <= 3,
                )
                .slice(0, 3);
        },
        votesSummary() {
            if (this.latestVotes.length === 0) {
                return "";
            }

            return this.latestVotes
                .map((vote) => `${vote.name}: ${this.shortLabelForRating(vote.rating)}`)
                .join(" | ");
        },
    },
    methods: {
        labelForRating(value) {
            const found = this.ratingOptions.find((opt) => opt.value === value);
            return found ? found.label : "Unknown";
        },
        shortLabelForRating(value) {
            const labels = {
                0: "DON'T LOVE IT",
                1: "COOL",
                2: "LOVE",
                3: "NEED",
            };

            return labels[value] || "UNKNOWN";
        },
        isSelected(value) {
            if (this.idea.my_rating === null || this.idea.my_rating === undefined) {
                return false;
            }

            return Number(this.idea.my_rating) === value;
        },
        async setRating(rating) {
            this.error = "";
            this.pendingRating = rating;
            await this.submitRating();
        },
        async submitRating() {
            if (this.pendingRating === null) {
                return;
            }

            this.saving = true;

            try {
                const response = await fetch(
                    `/api/trip-ideas/${this.idea.id}/rating`,
                    {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            Accept: "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                        },
                        body: JSON.stringify({ rating: this.pendingRating }),
                    },
                );

                if (response.status === 401) {
                    this.showAuthModal = true;
                    return;
                }

                if (!response.ok) {
                    const payload = await response.json();
                    this.error = payload.error || "Could not save rating.";
                    return;
                }

                const payload = await response.json();
                this.$emit("updated", payload);
                this.pendingRating = null;
            } catch {
                this.error = "Network error while saving rating.";
            } finally {
                this.saving = false;
            }
        },
        async createSession(authPayload) {
            this.authError = "";

            if (!authPayload.name || !authPayload.password) {
                this.authError = "Name and password are required.";
                return;
            }

            this.authLoading = true;

            try {
                const response = await fetch("/api/rater-session", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    body: JSON.stringify(authPayload),
                });

                if (!response.ok) {
                    const payload = await response.json();
                    this.authError =
                        payload.error || "Could not create session.";
                    return;
                }

                this.showAuthModal = false;
                await this.submitRating();
            } catch {
                this.authError = "Network error while creating session.";
            } finally {
                this.authLoading = false;
            }
        },
        cancelAuth() {
            this.showAuthModal = false;
            this.authError = "";
            this.pendingRating = null;
        },
    },
};
</script>
