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
                    :title="option.label"
                    @click="setRating(option.value)"
                    :class="[
                        'win95-button text-[10px] leading-none px-0 py-0.5 flex items-center justify-center w-full',
                        Number(idea.my_rating) === option.value
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
                        'font-bold text-forum-blue':
                            Number(idea.my_rating) === option.value,
                    }"
                >
                    {{ option.label }}
                </span>
            </div>
        </div>

        <div class="text-[10px] text-gray-600 mt-1">
            You: {{ myRatingLabel }}
            <span v-if="hasRatings">
                | {{ ratingCount }} vote{{ ratingCount === 1 ? "" : "s" }}</span
            >
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
                    "linear-gradient(90deg, #2563eb 0%, #16a34a 25%, #86efac 50%, #ef4444 75%, #ef4444 100%)",
                clipPath: `inset(0 ${100 - percent}% 0 0)`,
            };
        },
        myRatingLabel() {
            return this.idea.my_rating === null ||
                this.idea.my_rating === undefined
                ? "Not rated"
                : this.labelForRating(Number(this.idea.my_rating));
        },
    },
    methods: {
        labelForRating(value) {
            const found = this.ratingOptions.find((opt) => opt.value === value);
            return found ? found.label : "Unknown";
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
