<script setup>
// Utilzied chatgpt to create this component to help display the ranking of the players

import { computed } from "vue";

const props = defineProps({
    value: { type: [Number, String], required: true },
    size: { type: Number, default: 40 },
    color: { type: String, default: "#facc15" },
    textColor: { type: String, default: "#111827" },
});

const fontSize = computed(() => {
    const len = String(props.value).length;
    const factor = len === 1 ? 0.48 : len === 2 ? 0.4 : 0.34;
    return `${Math.max(10, Math.floor(props.size * factor))}px`;
});
</script>

<template>
    <div
        class="relative inline-block align-middle"
        :style="{ width: size + 'px', height: size + 'px' }"
        aria-label="Rank icon"
    >
        <svg
            viewBox="0 0 24 24"
            class="w-full h-full drop-shadow-sm"
            :style="{ color }"
            aria-hidden="true"
        >
            <path
                fill="currentColor"
                d="M11.48 3.499c.27-.753 1.27-.753 1.54 0l1.68 4.7a1 1 0 0 0 .95.66h4.96c.79 0 1.12 1.01.48 1.47l-4.01 2.92a1 1 0 0 0-.36 1.12l1.68 4.7c.27.75-.62 1.37-1.26.91l-4.01-2.92a1 1 0 0 0-1.18 0l-4.01 2.92c-.64.46-1.53-.16-1.26-.91l1.68-4.7a1 1 0 0 0-.36-1.12L2.91 10.33c-.64-.46-.31-1.47.48-1.47h4.96a1 1 0 0 0 .95-.66l1.68-4.7Z"
            />
        </svg>
        <div
            class="absolute inset-0 grid place-items-center pointer-events-none"
        >
            <span
                class="font-semibold leading-none"
                :style="{ color: textColor, fontSize }"
            >
                {{ value }}
            </span>
        </div>
    </div>
</template>
