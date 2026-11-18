<script setup>
import { nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { useTheme } from '@/composables/useTheme';
import ThemePreviewCard from './ThemePreviewCard.vue';

/**
 * Selector de corriente visual (tema).
 *
 * Botón + diálogo modal con una ficha por tema. Aplica el tema al instante
 * (sin recargar) y, si hay sesión, lo guarda en la cuenta.
 *
 * Variantes del disparador:
 *  - `inline`   dentro de la cabecera (por defecto).
 *  - `floating` botón fijo en la esquina inferior derecha (páginas de acceso).
 */
const props = defineProps({
    variant: {
        type: String,
        default: 'inline',
        validator: (value) => ['inline', 'floating'].includes(value),
    },
});

const { themes, current, select } = useTheme();

const isOpen = ref(false);
const closeButton = ref(null);
const trigger = ref(null);
const dialog = ref(null);
let previousOverflow = '';

function open() {
    isOpen.value = true;
}

function close() {
    isOpen.value = false;
    nextTick(() => trigger.value?.focus());
}

function onKeydown(event) {
    if (!isOpen.value) {
        return;
    }

    if (event.key === 'Escape') {
        event.preventDefault();
        close();
        return;
    }

    // Mantiene el foco dentro del diálogo mientras está abierto.
    if (event.key !== 'Tab' || !dialog.value) {
        return;
    }

    const focusable = [
        ...dialog.value.querySelectorAll(
            'button:not([disabled]), a[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])',
        ),
    ];

    if (focusable.length === 0) {
        return;
    }

    const first = focusable[0];
    const last = focusable[focusable.length - 1];

    if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
    }
}

watch(isOpen, async (open) => {
    if (typeof document === 'undefined') {
        return;
    }

    if (open) {
        previousOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';
        await nextTick();
        closeButton.value?.focus();
    } else {
        document.body.style.overflow = previousOverflow;
    }
});

onMounted(() => window.addEventListener('keydown', onKeydown));

onBeforeUnmount(() => {
    window.removeEventListener('keydown', onKeydown);

    if (typeof document !== 'undefined') {
        document.body.style.overflow = previousOverflow;
    }
});
</script>

<template>
    <div :class="variant === 'floating' ? 'fixed bottom-5 right-5 z-40' : 'relative'">
        <button
            ref="trigger"
            type="button"
            class="trigger"
            :class="variant === 'floating' ? 'theme-panel-solid shadow-lg' : ''"
            :aria-expanded="isOpen"
            aria-haspopup="dialog"
            title="Cambiar la corriente visual"
            @click="open"
        >
            <span class="theme-accent text-xl leading-none" aria-hidden="true">
                {{ current.glyph }}
            </span>
            <span class="text-left leading-tight">
                <span class="theme-label theme-faint hidden text-[8px] sm:block">
                    Corriente
                </span>
                <span class="theme-display block text-sm">
                    {{ current.name }}
                </span>
            </span>
        </button>

        <Teleport to="body">
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="isOpen"
                    class="theme-overlay fixed inset-0 z-50 flex items-center justify-center px-4 py-8"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="theme-picker-title"
                    @click.self="close"
                >
                    <div ref="dialog" class="theme-panel-solid max-h-[88vh] w-full max-w-4xl overflow-y-auto p-6 sm:p-8">
                        <div class="flex items-start justify-between gap-6">
                            <div>
                                <p class="theme-label theme-accent text-[10px]">
                                    Círculo de iniciados
                                </p>
                                <h2 id="theme-picker-title" class="theme-display mt-1 text-3xl sm:text-4xl">
                                    Elige tu corriente
                                </h2>
                                <p class="theme-body theme-muted mt-2 max-w-xl text-base leading-relaxed">
                                    Cada tema toma la paleta y la voz de un mago u ocultista.
                                    El oráculo cambia de piel; las cartas y las tiradas son las mismas.
                                </p>
                            </div>

                            <button
                                ref="closeButton"
                                type="button"
                                class="theme-btn-ghost shrink-0 px-3 py-1.5 text-sm leading-none"
                                aria-label="Cerrar el selector de temas"
                                @click="close"
                            >
                                ✕
                            </button>
                        </div>

                        <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            <ThemePreviewCard
                                v-for="theme in themes"
                                :key="theme.id"
                                :theme="theme"
                                :active="current.id === theme.id"
                                @select="select"
                            />
                        </div>

                        <div class="theme-rule mt-6 flex flex-wrap items-center justify-between gap-3 border-t pt-4">
                            <p class="theme-label theme-faint text-[9px] leading-relaxed">
                                Se guarda en este dispositivo · con sesión iniciada, viaja con tu cuenta
                            </p>
                            <button type="button" class="theme-btn px-5 py-2" @click="close">
                                Listo
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<style scoped>
.trigger {
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    padding: 0.4rem 0.65rem;
    border: 1px solid transparent;
    border-radius: var(--t-radius);
    color: var(--t-ink);
    cursor: pointer;
    transition: border-color 0.2s ease, background-color 0.2s ease;
}

.trigger:hover {
    border-color: color-mix(in srgb, var(--t-accent) 45%, transparent);
    background-color: color-mix(in srgb, var(--t-accent) 8%, transparent);
}
</style>
