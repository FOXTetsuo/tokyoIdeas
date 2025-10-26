<template>
    <div
        v-if="visible"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        @click.self="$emit('cancel')"
    >
        <div class="win95-border bg-white p-4 max-w-sm w-full mx-4">
            <p class="mb-4 text-center">{{ message }}</p>
            <div class="flex gap-2 justify-center">
                <button
                    @click="$emit('confirm')"
                    class="win95-button bg-red-500 text-white hover:bg-red-600"
                >
                    Confirm
                </button>
                <button @click="$emit('cancel')" class="win95-button">
                    Cancel
                </button>
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
        message: {
            type: String,
            required: true,
        },
    },
    emits: ["confirm", "cancel"],
};
</script>
