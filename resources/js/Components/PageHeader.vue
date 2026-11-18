<script setup>
import { Link } from '@inertiajs/vue3';

/**
 * Cabecera estándar de página.
 *
 * Unifica el orden visual (volver → etiqueta → título → descripción → acciones)
 * para que todas las pantallas se lean igual.
 */
defineProps({
    eyebrow: { type: String, default: '' },
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
    /** Ruta de retorno; si se omite, no se muestra el enlace. */
    backHref: { type: String, default: '' },
    backLabel: { type: String, default: 'Volver' },
});
</script>

<template>
    <header class="mb-8 sm:mb-10">
        <Link
            v-if="backHref"
            :href="backHref"
            class="theme-btn-quiet mb-5 inline-flex"
        >
            <span aria-hidden="true">←</span>
            {{ backLabel }}
        </Link>

        <div class="flex flex-wrap items-end justify-between gap-4">
            <div class="min-w-0">
                <p v-if="eyebrow" class="theme-label theme-accent text-[10px]">
                    {{ eyebrow }}
                </p>
                <h1 class="theme-display mt-1 text-3xl leading-tight sm:text-4xl">
                    {{ title }}
                </h1>
                <p v-if="subtitle" class="theme-body theme-muted mt-3 max-w-2xl text-lg leading-relaxed">
                    {{ subtitle }}
                </p>
            </div>

            <div v-if="$slots.actions" class="flex flex-wrap items-center gap-3">
                <slot name="actions" />
            </div>
        </div>

        <slot />
    </header>
</template>
