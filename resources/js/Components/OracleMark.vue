<script setup>
/**
 * Marca de El Oráculo.
 *
 * SVG decorativo (sol, luna y estrella) que sustituye el logo de Laravel.
 * Usa `currentColor` para heredar el color del tema activo.
 */
defineProps({
    size: {
        type: [Number, String],
        default: 28,
    },
});
</script>

<template>
    <svg
        :width="size"
        :height="size"
        viewBox="0 0 48 48"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        aria-hidden="true"
        focusable="false"
    >
        <circle
            cx="24"
            cy="24"
            r="21"
            stroke="currentColor"
            stroke-width="1.2"
            opacity="0.55"
        />
        <path
            d="M30.8 12.6a12.2 12.2 0 1 0 4.6 21.4 13.6 13.6 0 1 1-4.6-21.4Z"
            fill="currentColor"
            opacity="0.9"
        />
        <path
            d="M34.5 14.2l1.15 3.05L38.7 18.4l-3.05 1.15L34.5 22.6l-1.15-3.05L30.3 18.4l3.05-1.15L34.5 14.2Z"
            fill="currentColor"
        />
        <circle cx="17" cy="17" r="1.15" fill="currentColor" opacity="0.9" />
        <circle cx="20" cy="31.5" r="0.9" fill="currentColor" opacity="0.7" />
    </svg>
</template>
