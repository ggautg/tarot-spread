<script setup>
import { Link } from '@inertiajs/vue3';

/**
 * Ficha de una tirada disponible en la portada.
 *
 * Resume el nombre, la cantidad de cartas y la descripción, y enlaza al
 * formulario de la tirada.
 */
const props = defineProps({
    spread: { type: Object, required: true },
});

const cardCount = props.spread.positions_count ?? props.spread.positions?.length ?? props.spread.cantidad_cartas;
</script>

<template>
    <Link
        :href="route('readings.create', spread.slug)"
        class="theme-panel theme-interactive group flex h-full flex-col p-7 text-left sm:p-8"
    >
        <div class="flex items-center justify-between gap-3">
            <span class="theme-chip">{{ cardCount }} cartas</span>
            <span class="theme-accent text-lg leading-none transition-transform duration-200 group-hover:translate-x-1" aria-hidden="true">
                →
            </span>
        </div>

        <h2 class="theme-display mt-5 text-2xl">
            {{ spread.nombre }}
        </h2>

        <p class="theme-body theme-muted mt-2 flex-1 text-base leading-relaxed">
            {{ spread.descripcion }}
        </p>

        <span class="theme-label theme-accent mt-6 text-[9px]">
            Comenzar tirada
        </span>
    </Link>
</template>
