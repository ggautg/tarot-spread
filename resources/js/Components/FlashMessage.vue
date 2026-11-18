<script setup>
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

/**
 * Mensaje flash global.
 *
 * Lee `flash.success` / `flash.error` de las props compartidas por Inertia y
 * los muestra como una banda fija bajo la cabecera. Se cierra solo a los
 * cinco segundos o al pulsar la ✕.
 */
const page = usePage();

const visible = ref(false);
const message = ref('');
const tone = ref('success');
let timer = null;

const flash = computed(() => page.props.flash ?? {});

/** Muestra el mensaje y reinicia el temporizador de cierre. */
function show(text, type) {
    if (!text) {
        return;
    }

    message.value = text;
    tone.value = type;
    visible.value = true;

    clearTimeout(timer);
    timer = setTimeout(() => (visible.value = false), 5000);
}

function dismiss() {
    visible.value = false;
    clearTimeout(timer);
}

// Cada respuesta de Inertia trae un flash nuevo (o vacío).
watch(
    () => [flash.value.success, flash.value.error],
    ([success, error]) => {
        if (error) {
            show(error, 'error');
        } else if (success) {
            show(success, 'success');
        }
    },
    { immediate: true },
);

onBeforeUnmount(() => clearTimeout(timer));
</script>

<template>
    <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="-translate-y-2 opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="visible"
            class="flash"
            :class="tone === 'error' ? 'theme-alert theme-alert-error' : 'theme-alert'"
            role="status"
            aria-live="polite"
        >
            <span class="flash-glyph" aria-hidden="true">
                {{ tone === 'error' ? '✕' : '✦' }}
            </span>
            <p class="flex-1 text-sm leading-snug">{{ message }}</p>
            <button
                type="button"
                class="flash-close"
                aria-label="Cerrar el aviso"
                @click="dismiss"
            >
                ✕
            </button>
        </div>
    </Transition>
</template>

<style scoped>
.flash {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    border-radius: var(--t-radius);
    box-shadow: 0 12px 28px -18px rgb(0 0 0 / 0.45);
}

.flash-glyph {
    font-size: 0.8rem;
    line-height: 1;
}

.flash-close {
    flex-shrink: 0;
    padding: 0.15rem 0.35rem;
    border-radius: var(--t-radius);
    font-size: 0.7rem;
    opacity: 0.65;
    transition: opacity 0.2s ease;
}

.flash-close:hover {
    opacity: 1;
}
</style>
