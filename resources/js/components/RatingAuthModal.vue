<template>
    <div
        v-if="visible"
        class="fixed inset-0 flex items-center justify-center z-[10001]"
        @click.self="$emit('cancel')"
    >
        <div class="win95-border bg-white p-4 max-w-sm w-full mx-4">
            <p class="mb-3 text-center font-bold text-forum-blue">
                First rating setup
            </p>
            <p class="mb-4 text-xs text-center text-gray-600">
                Enter your name and delete password once. We keep you signed in for
                1 week.
            </p>

            <input
                v-model="localName"
                type="text"
                maxlength="50"
                placeholder="Your name"
                class="win95-button w-full mb-2"
                @keyup.enter="handleConfirm"
            />
            <input
                v-model="localPassword"
                type="password"
                placeholder="Delete password"
                class="win95-button w-full mb-3"
                @keyup.enter="handleConfirm"
            />

            <p v-if="error" class="text-xs text-red-600 mb-3">{{ error }}</p>

            <div class="flex gap-2 justify-center">
                <button
                    @click="handleConfirm"
                    :disabled="loading"
                    class="win95-button bg-blue-500 text-white hover:bg-blue-600 disabled:opacity-50"
                >
                    {{ loading ? "Saving..." : "Save" }}
                </button>
                <button @click="handleCancel" class="win95-button">Cancel</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        visible: {
            type: Boolean,
            default: false,
        },
        loading: {
            type: Boolean,
            default: false,
        },
        error: {
            type: String,
            default: "",
        },
    },
    emits: ["confirm", "cancel"],
    data() {
        return {
            localName: "",
            localPassword: "",
        };
    },
    methods: {
        handleConfirm() {
            this.$emit("confirm", {
                name: this.localName.trim(),
                password: this.localPassword,
            });
        },
        handleCancel() {
            this.localName = "";
            this.localPassword = "";
            this.$emit("cancel");
        },
    },
    watch: {
        visible(next) {
            if (!next) {
                this.localPassword = "";
            }
        },
    },
};
</script>
