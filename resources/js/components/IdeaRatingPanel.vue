<template>
    <div class="win95-border-inset bg-white p-2 mt-2">
        <div class="text-xs mb-2 text-gray-700">
            <strong>Group vibe:</strong>
            <span v-if="hasRatings">
                {{ averageLabel }} ({{ formattedAverage }}/3 | {{ ratingCount }} vote{{ ratingCount === 1 ? "" : "s" }})
            </span>
            <span v-else>No ratings yet</span>
        </div>

        <div class="text-xs mb-2 text-gray-700">
            <strong>Your rating:</strong>
            <span>{{ myRatingLabel }}</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-1">
            <button
                v-for="option in ratingOptions"
                :key="option.value"
                @click="setRating(option.value)"
                :class="[
                    'win95-button text-xs',
                    isSelected(option.value)
                        ? 'bg-blue-600 text-white font-bold'
                        : 'bg-forum-bg',
                ]"
                :disabled="saving"
            >
                {{ option.label }}
            </button>
        </div>

        <p v-if="error" class="text-xs text-red-600 mt-2">{{ error }}</p>

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
    { value: 0, label: "Don't love it" },
    { value: 1, label: "Cool!" },
    { value: 2, label: "LOVE it!" },
    { value: 3, label: "WEMUSTGOHERE!" },
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
        formattedAverage() {
            if (!this.hasRatings) return "-";
            return Number(this.idea.rating_average || 0).toFixed(1);
        },
        averageLabel() {
            if (!this.hasRatings) return "No ratings";
            const nearest = Math.round(Number(this.idea.rating_average || 0));
            return this.labelForRating(nearest);
        },
        myRatingLabel() {
            return this.idea.my_rating === null || this.idea.my_rating === undefined
                ? "Not rated"
                : this.labelForRating(Number(this.idea.my_rating));
        },
    },
    methods: {
        labelForRating(value) {
            const found = this.ratingOptions.find((opt) => opt.value === value);
            return found ? found.label : "Unknown";
        },
        isSelected(value) {
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
                    this.authError = payload.error || "Could not create session.";
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
