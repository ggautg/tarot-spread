<script setup>
import { computed } from 'vue';
import OracleMark from './OracleMark.vue';

/**
 * Detalle legible de una carta revelada.
 *
 * Muestra la posición que ocupa, el nombre de la carta, su orientación y el
 * significado que corresponde. La entrada se escalona con `index` para
 * acompañar la cascada del tablero.
 */
const props = defineProps({
    readingCard: { type: Object, required: true },
    index: { type: Number, default: 0 },
});

const position = computed(() => props.readingCard.position);
const card = computed(() => props.readingCard.card);
const meaning = computed(() =>
    props.readingCard.invertida ? card.value?.meaning_rev : card.value?.meaning_up,
);
</script>

<template>
    <article
        class="detail-in theme-panel p-5 sm:p-6"
        :style="{ animationDelay: `${index * 90}ms` }"
    >
        <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
            <span class="theme-chip">
                {{ String(position.orden).padStart(2, '0') }} · {{ position.nombre_posicion }}
            </span>

            <span v-if="readingCard.invertida" class="theme-chip theme-chip-danger">
                Invertida
            </span>
        </div>

        <h3 class="theme-display mt-3 text-2xl">
            {{ card.name }}
        </h3>

        <p class="theme-body mt-2 text-lg leading-relaxed text-[color-mix(in_srgb,var(--t-ink)_86%,transparent)]">
            {{ meaning }}
        </p>

        <div class="theme-rule mt-4 flex items-start gap-2.5 border-t pt-3">
            <span class="theme-accent mt-0.5 shrink-0 opacity-80">
                <OracleMark :size="16" />
            </span>
            <p class="theme-body theme-faint text-sm leading-snug">
                <span class="theme-label text-[8px]">{{ position.nombre_posicion }}</span>
                — {{ position.descripcion }}
            </p>
        </div>
    </article>
</template>

<style scoped>
.detail-in {
    animation: detail-in 0.45s cubic-bezier(0.22, 1, 0.36, 1) both;
}

@keyframes detail-in {
    from {
        opacity: 0;
        transform: translateY(8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (prefers-reduced-motion: reduce) {
    .detail-in {
        animation: none;
    }
}
</style>
