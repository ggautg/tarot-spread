<script setup>
/**
 * Ficha visual de una corriente (tema).
 *
 * Se pinta con las variables del propio tema (`data-theme`), así el usuario ve
 * la paleta real antes de elegirla. La usan el selector y la página de perfil.
 */
defineProps({
    theme: { type: Object, required: true },
    active: { type: Boolean, default: false },
    /** En modo compacto se oculta la descripción larga. */
    compact: { type: Boolean, default: false },
});

defineEmits(['select']);
</script>

<template>
    <button
        type="button"
        :data-theme="theme.id"
        class="theme-preview theme-card"
        :class="{ 'is-active': active }"
        :aria-pressed="active"
        @click="$emit('select', theme.id)"
    >
        <span class="flex items-start justify-between gap-2">
            <span class="theme-accent text-3xl leading-none" aria-hidden="true">
                {{ theme.glyph }}
            </span>

            <span v-if="active" class="theme-label theme-accent text-[8px]">
                ✦ En uso
            </span>
            <span v-else class="theme-label theme-faint text-[8px]">
                {{ theme.era }}
            </span>
        </span>

        <span class="theme-display mt-3 block text-left text-xl">
            {{ theme.name }}
        </span>

        <span class="theme-label theme-muted mt-1 block text-left text-[9px]">
            {{ theme.epithet }}
        </span>

        <span
            v-if="!compact"
            class="theme-body theme-muted mt-2 block text-left text-sm leading-snug"
        >
            {{ theme.description }}
        </span>

        <span class="mt-4 flex items-center gap-1.5" aria-hidden="true">
            <span class="theme-swatch h-2.5 w-6 rounded-full" />
            <span class="theme-swatch-2 h-2.5 w-6 rounded-full" />
            <span class="theme-swatch-surface h-2.5 w-6 rounded-full ring-1 ring-black/10" />
        </span>
    </button>
</template>

<style scoped>
.theme-card {
    border: 1px solid color-mix(in srgb, var(--t-accent) 24%, transparent);
    border-radius: var(--t-radius);
    padding: 1.15rem;
    cursor: pointer;
    transition:
        border-color 0.25s ease,
        box-shadow 0.25s ease,
        transform 0.25s ease;
}

.theme-card:hover {
    border-color: var(--t-accent);
    transform: translateY(-2px);
}

.theme-card.is-active {
    border-color: var(--t-accent);
    box-shadow:
        0 0 0 1px var(--t-accent),
        0 18px 40px -24px color-mix(in srgb, var(--t-accent) 65%, transparent);
}

@media (prefers-reduced-motion: reduce) {
    .theme-card,
    .theme-card:hover {
        transition: none;
        transform: none;
    }
}
</style>
