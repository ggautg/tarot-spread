<script setup>
/**
 * Lista ordenada de posiciones de una tirada.
 *
 * Sirve como vista previa antes de tirar las cartas: explica qué representa
 * cada posición sin revelar nada.
 */
defineProps({
    positions: { type: Array, default: () => [] },
});
</script>

<template>
    <ol class="grid gap-x-8 gap-y-4 sm:grid-cols-2">
        <li
            v-for="position in positions"
            :key="position.id"
            class="theme-rule flex gap-3 border-t pt-3"
        >
            <span class="theme-label theme-accent mt-0.5 shrink-0 text-[9px]" aria-hidden="true">
                {{ String(position.orden).padStart(2, '0') }}
            </span>

            <div class="min-w-0">
                <p class="theme-display text-lg leading-snug">
                    {{ position.nombre_posicion }}
                </p>
                <p v-if="position.descripcion" class="theme-body theme-faint mt-0.5 text-sm leading-snug">
                    {{ position.descripcion }}
                </p>
            </div>
        </li>
    </ol>
</template>
