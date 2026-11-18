<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppButton from './AppButton.vue';
import { formatDate } from '@/utils/formatDate';

/**
 * Fila del historial de lecturas.
 *
 * Enlaza al tablero y emite `delete` para que la página abra el diálogo de
 * confirmación (el hijo no borra nada por su cuenta).
 */
const props = defineProps({
    reading: { type: Object, required: true },
});

defineEmits(['delete']);

const date = computed(() => formatDate(props.reading.created_at));
const cardCount = computed(() => props.reading.cards_count ?? props.reading.cards?.length ?? 0);
</script>

<template>
    <article class="theme-panel flex flex-wrap items-center justify-between gap-4 px-5 py-4 sm:px-6">
        <Link
            :href="route('readings.show', reading.uuid)"
            class="min-w-0 flex-1"
        >
            <div class="flex flex-wrap items-center gap-2">
                <h3 class="theme-display text-xl">
                    {{ reading.spread.nombre }}
                </h3>
                <span class="theme-chip">{{ cardCount }} cartas</span>
                <span v-if="!reading.revealed_at" class="theme-chip theme-chip-danger">
                    Sin revelar
                </span>
            </div>

            <p v-if="reading.pregunta" class="theme-body theme-muted mt-1 text-base italic leading-snug">
                «{{ reading.pregunta }}»
            </p>

            <p v-if="reading.notas_personales" class="theme-accent-2 mt-1.5 line-clamp-2 text-sm leading-snug">
                ✎ {{ reading.notas_personales }}
            </p>

            <p class="theme-label theme-faint mt-2 text-[9px]">
                {{ date }}
            </p>
        </Link>

        <div class="flex shrink-0 items-center gap-2">
            <AppButton
                :href="route('readings.show', reading.uuid)"
                variant="ghost"
                size="sm"
            >
                Abrir
            </AppButton>

            <AppButton
                variant="danger"
                size="sm"
                :aria-label="`Borrar la lectura de ${reading.spread.nombre}`"
                @click="$emit('delete', reading)"
            >
                Borrar
            </AppButton>
        </div>
    </article>
</template>
