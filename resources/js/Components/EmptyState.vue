<script setup>
/**
 * Estado vacío.
 *
 * Se usa cuando una lista no tiene elementos (por ejemplo, el historial de un
 * visitante nuevo). Explica qué pasó y ofrece la acción siguiente.
 */
defineProps({
    glyph: { type: String, default: '✦' },
    title: { type: String, required: true },
    text: { type: String, default: '' },
});
</script>

<template>
    <div class="theme-panel px-6 py-12 text-center sm:px-10">
        <p class="theme-accent text-3xl leading-none" aria-hidden="true">
            {{ glyph }}
        </p>

        <h2 class="theme-display mt-4 text-2xl">
            {{ title }}
        </h2>

        <p v-if="text" class="theme-body theme-muted mx-auto mt-2 max-w-md text-base leading-relaxed">
            {{ text }}
        </p>

        <div v-if="$slots.default" class="mt-6 flex justify-center gap-3">
            <slot />
        </div>
    </div>
</template>
