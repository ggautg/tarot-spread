<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import OracleMark from './OracleMark.vue';

/**
 * Carta del tarot con dorso y frente.
 *
 * Se voltea sola cuando `revealed` pasa a `true`, tanto al montar la página
 * (lectura ya abierta) como al abrir el sobre sobre la misma página (Inertia
 * actualiza las props sin recrear el componente). El retardo según `index`
 * produce la cascada de revelado. La animación es CSS puro y respeta
 * `prefers-reduced-motion` (la duración se anula desde `app.css`).
 *
 * Si la carta salió invertida, su contenido gira 180° dentro del frente: es la
 * orientación tradicional. El significado legible aparece en el detalle.
 */
const props = defineProps({
    /** `ReadingCard` con `card` y `invertida`; null si el sobre sigue sellado. */
    readingCard: { type: Object, default: null },
    /** Posición del tablero, usada para la etiqueta accesible. */
    position: { type: Object, default: null },
    revealed: { type: Boolean, default: false },
    index: { type: Number, default: 0 },
});

const flipped = ref(false);

const invertida = computed(() => Boolean(props.readingCard?.invertida));
const cardName = computed(() => props.readingCard?.card?.name ?? '');
const suit = computed(() => props.readingCard?.card?.suit ?? '');

const ariaLabel = computed(() => {
    const place = props.position?.nombre_posicion ?? 'Carta';

    if (!props.revealed || !props.readingCard) {
        return `${place}: sobre sellado`;
    }

    return `${place}: ${cardName.value}${invertida.value ? ', invertida' : ''}`;
});

/** Voltea en el siguiente pintado, cuando el dorso ya es visible. */
let flipTimer = null;

function scheduleFlip() {
    clearTimeout(flipTimer);
    flipTimer = setTimeout(() => {
        flipped.value = true;
    }, 40);
}

onMounted(() => {
    if (props.revealed) {
        scheduleFlip();
    }
});

onBeforeUnmount(() => clearTimeout(flipTimer));

// Al abrir el sobre, Inertia actualiza `revealed` sin remontar la carta. El
// dorso ya estaba pintado, así que se voltea de inmediato para que la
// transición arranque sin esperas.
watch(
    () => props.revealed,
    (value) => {
        if (value) {
            flipped.value = true;
        }
    },
);
</script>

<template>
    <div
        class="tarot"
        :class="{ 'is-flipped': flipped }"
        role="img"
        :aria-label="ariaLabel"
    >
        <div
            class="tarot-inner"
            :style="{ transitionDelay: flipped ? `${index * 110}ms` : '0ms' }"
        >
            <div class="tarot-face tarot-back">
                <span class="theme-accent opacity-90">
                    <OracleMark :size="26" />
                </span>
            </div>

            <div class="tarot-face tarot-front">
                <div class="front-content" :class="{ 'is-inverted': invertida }">
                    <span v-if="suit" class="front-suit">{{ suit }}</span>
                    <span class="front-name">{{ cardName }}</span>
                    <span class="front-mark" aria-hidden="true">✦</span>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.tarot {
    width: clamp(46px, 9vw, 72px);
    aspect-ratio: 2 / 3;
    perspective: 700px;
}

.tarot-inner {
    position: relative;
    width: 100%;
    height: 100%;
    transform-style: preserve-3d;
    transition: transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
}

.tarot.is-flipped .tarot-inner {
    transform: rotateY(180deg);
}

.tarot-face {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: calc(var(--t-radius) * 0.7);
    border: 1px solid color-mix(in srgb, var(--t-accent) 55%, transparent);
    backface-visibility: hidden;
    overflow: hidden;
}

.tarot-back {
    background-color: var(--t-card-back);
    background-image:
        repeating-linear-gradient(45deg, rgb(255 255 255 / 0.045) 0 1px, transparent 1px 5px),
        repeating-linear-gradient(-45deg, rgb(0 0 0 / 0.12) 0 1px, transparent 1px 5px);
    box-shadow: inset 0 0 0 3px color-mix(in srgb, var(--t-accent) 16%, transparent);
}

.tarot-front {
    transform: rotateY(180deg);
    background-color: color-mix(in srgb, var(--t-surface) 78%, var(--t-bg));
    box-shadow: inset 0 0 0 3px color-mix(in srgb, var(--t-accent) 10%, transparent);
}

.front-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    height: 100%;
    padding: 0.35rem 0.2rem;
    text-align: center;
    transition: transform 0.4s ease;
}

.front-content.is-inverted {
    transform: rotate(180deg);
}

.front-suit {
    font-family: var(--t-font-label);
    font-size: clamp(0.26rem, 0.9vw, 0.36rem);
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: color-mix(in srgb, var(--t-ink) 55%, transparent);
}

.front-name {
    font-family: var(--t-font-body);
    font-size: clamp(0.38rem, 1.25vw, 0.56rem);
    line-height: 1.15;
    overflow-wrap: anywhere;
    color: var(--t-ink);
}

.front-mark {
    font-size: clamp(0.4rem, 1.4vw, 0.6rem);
    line-height: 1;
    color: var(--t-accent);
}
</style>
