<script setup>
import { computed } from 'vue';
import TarotCard from './TarotCard.vue';

/**
 * Tablero de la tirada.
 *
 * Coloca cada posición según `coord_x` / `coord_y` (porcentajes), aplica su
 * rotación y muestra la carta correspondiente. Mientras el sobre está sellado,
 * dibuja los dorsos y un número por posición.
 */
const props = defineProps({
    positions: { type: Array, default: () => [] },
    /** `ReadingCard[]`; vacío antes de revelar. */
    cards: { type: Array, default: () => [] },
    revealed: { type: Boolean, default: false },
});

const ordered = computed(() =>
    [...props.positions].sort((a, b) => a.orden - b.orden),
);

const cardsByPosition = computed(() => {
    const map = new Map();

    for (const readingCard of props.cards) {
        map.set(readingCard.spread_position_id, readingCard);
    }

    return map;
});
</script>

<template>
    <div
        class="relative mx-auto aspect-[4/3] w-full max-w-3xl"
        role="list"
        aria-label="Tablero de la tirada"
    >
        <div
            v-for="position in ordered"
            :key="position.id"
            class="absolute -translate-x-1/2 -translate-y-1/2"
            :style="{
                left: `${position.coord_x}%`,
                top: `${position.coord_y}%`,
                zIndex: position.orden,
            }"
            role="listitem"
        >
            <div :style="{ transform: `rotate(${position.rotacion}deg)` }">
                <TarotCard
                    :reading-card="cardsByPosition.get(position.id) ?? null"
                    :position="position"
                    :revealed="revealed"
                    :index="position.orden - 1"
                />
            </div>

            <span
                class="order-badge"
                :class="position.rotacion !== 0 ? '-bottom-1.5 -left-1.5' : '-right-1.5 -top-1.5'"
                aria-hidden="true"
            >
                {{ position.orden }}
            </span>
        </div>
    </div>
</template>

<style scoped>
.order-badge {
    position: absolute;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 1.05rem;
    height: 1.05rem;
    border-radius: 999px;
    background-color: var(--t-accent);
    color: var(--t-on-accent);
    font-family: var(--t-font-label);
    font-size: 0.55rem;
    line-height: 1;
    box-shadow: 0 1px 5px rgb(0 0 0 / 0.35);
}
</style>
