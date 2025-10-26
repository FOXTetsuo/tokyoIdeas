<template>
    <div
        v-if="visible"
        class="fixed inset-0 flex items-center justify-center z-[10001]"
        @click.self="$emit('cancel')"
    >
        <div class="win95-border bg-white p-4 max-w-sm w-full mx-4">
            <p class="mb-4 text-center">{{ message }}</p>
            <input
                v-model="password"
                type="password"
                placeholder="Enter secret password"
                class="win95-button w-full mb-4"
                @keyup.enter="handleConfirm()"
            />
            <div class="flex gap-2 justify-center">
                <button
                    @click="handleConfirm"
                    class="win95-button bg-red-500 text-white hover:bg-red-600"
                >
                    Confirm
                </button>
                <button @click="handleCancel" class="win95-button">
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
    data() {
        return {
            password: "",
        };
    },
    methods: {
        handleConfirm() {
            const pwd = this.password;
            this.password = "";
            this.$emit("confirm", pwd);
        },
        handleCancel() {
            this.password = "";
            this.$emit("cancel");
        },
    },
};
</script>
