<template>
    <div class="relative" ref="dropdown">
        <button
            type="button"
            @click="toggleDropdown"
            class="win95-button text-sm w-full sm:w-auto flex items-center justify-between gap-2 min-w-[150px]"
        >
            <span>{{ selectedLabel }}</span>
            <span class="text-xs">{{ isOpen ? '▲' : '▼' }}</span>
        </button>

        <div
            v-if="isOpen"
            class="absolute top-full left-0 mt-1 win95-border bg-white shadow-lg z-50 min-w-[150px]"
        >
            <div
                v-for="option in options"
                :key="option.value"
                @click="selectOption(option)"
                :class="[
                    'px-3 py-2 cursor-pointer text-sm hover:bg-blue-500 hover:text-white',
                    modelValue === option.value ? 'bg-blue-100' : ''
                ]"
            >
                {{ option.label }}
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'CategoryDropdown',
    props: {
        modelValue: {
            type: String,
            default: ''
        },
        options: {
            type: Array,
            required: true
        }
    },
    emits: ['update:modelValue'],
    data() {
        return {
            isOpen: false
        };
    },
    computed: {
        selectedLabel() {
            const selected = this.options.find(opt => opt.value === this.modelValue);
            return selected ? selected.label : this.options[0]?.label || 'Select';
        }
    },
    mounted() {
        document.addEventListener('click', this.handleClickOutside);
    },
    beforeUnmount() {
        document.removeEventListener('click', this.handleClickOutside);
    },
    methods: {
        toggleDropdown() {
            this.isOpen = !this.isOpen;
        },
        selectOption(option) {
            this.$emit('update:modelValue', option.value);
            this.isOpen = false;
        },
        handleClickOutside(event) {
            if (this.$refs.dropdown && !this.$refs.dropdown.contains(event.target)) {
                this.isOpen = false;
            }
        }
    }
};
</script>
