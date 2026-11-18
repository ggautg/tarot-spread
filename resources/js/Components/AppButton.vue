<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

/**
 * Botón (o enlace con apariencia de botón) único para toda la app.
 *
 * Centraliza variantes, tamaños, estado de carga y accesibilidad, de modo que
 * ninguna página tenga que repetir clases de color o de foco.
 *
 * Variantes:
 *  - primary      acción principal (fondo acento)
 *  - ghost        acción secundaria (borde acento)
 *  - danger       acción destructiva secundaria (borde rojo)
 *  - danger-solid acción destructiva principal (fondo rojo)
 *  - quiet        enlace discreto sin caja
 */
const props = defineProps({
    /** Si se indica, se renderiza un enlace de Inertia en lugar de <button>. */
    href: { type: String, default: null },
    /** Método HTTP para el enlace de Inertia. */
    method: { type: String, default: 'get' },
    /** Etiqueta del enlace de Inertia (a, button, div…). */
    as: { type: String, default: 'a' },
    variant: {
        type: String,
        default: 'primary',
        validator: (value) =>
            ['primary', 'ghost', 'danger', 'danger-solid', 'quiet'].includes(value),
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value),
    },
    type: { type: String, default: 'button' },
    loading: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    block: { type: Boolean, default: false },
    /** Texto mostrado mientras `loading` está activo. */
    loadingLabel: { type: String, default: '' },
});

const baseClass = computed(() => {
    if (props.variant === 'quiet') {
        return 'theme-btn-quiet';
    }

    if (props.variant === 'danger-solid') {
        return 'theme-danger-solid';
    }

    if (props.variant === 'danger') {
        return 'theme-danger-btn';
    }

    return props.variant === 'ghost' ? 'theme-btn-ghost' : 'theme-btn';
});

const sizeClass = computed(() => {
    if (props.variant === 'quiet') {
        return 'px-1 py-1';
    }

    return {
        sm: 'min-h-9 px-3 py-1.5 text-[0.64rem]',
        md: '',
        lg: 'min-h-12 px-6 py-3 text-[0.78rem]',
    }[props.size];
});

const classes = computed(() => [
    baseClass.value,
    sizeClass.value,
    props.block ? 'w-full' : '',
    props.loading || props.disabled ? 'pointer-events-none' : '',
]);

const isDisabled = computed(() => props.disabled || props.loading);
</script>

<template>
    <Link
        v-if="href"
        :href="href"
        :method="method"
        :as="as"
        :class="classes"
        :aria-disabled="isDisabled ? 'true' : undefined"
        :aria-busy="loading ? 'true' : undefined"
        :tabindex="isDisabled ? -1 : undefined"
    >
        <svg
            v-if="loading"
            class="h-3.5 w-3.5 shrink-0 animate-spin"
            viewBox="0 0 24 24"
            fill="none"
            aria-hidden="true"
        >
            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3" opacity="0.25" />
            <path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
        </svg>

        <span v-if="loading && loadingLabel">{{ loadingLabel }}</span>
        <slot v-else />
    </Link>

    <button
        v-else
        :type="type"
        :class="classes"
        :disabled="isDisabled"
        :aria-busy="loading ? 'true' : undefined"
    >
        <svg
            v-if="loading"
            class="h-3.5 w-3.5 shrink-0 animate-spin"
            viewBox="0 0 24 24"
            fill="none"
            aria-hidden="true"
        >
            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3" opacity="0.25" />
            <path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
        </svg>

        <span v-if="loading && loadingLabel">{{ loadingLabel }}</span>
        <slot v-else />
    </button>
</template>
