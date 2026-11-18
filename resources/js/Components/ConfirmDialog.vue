<script setup>
import { nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';

/**
 * Diálogo de confirmación para acciones destructivas.
 *
 * Mantiene el foco dentro del diálogo, cierra con Escape o con el fondo y
 * bloquea el scroll de la página mientras está abierto.
 *
 * Uso:
 *   <ConfirmDialog v-model:show="open" title="…" @confirm="doIt" />
 */
const props = defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, required: true },
    message: { type: String, default: '' },
    confirmLabel: { type: String, default: 'Confirmar' },
    cancelLabel: { type: String, default: 'Cancelar' },
    busy: { type: Boolean, default: false },
    danger: { type: Boolean, default: true },
    /** Texto del botón mientras `busy` está activo. */
    busyLabel: { type: String, default: 'Procesando…' },
});

const emit = defineEmits(['update:show', 'confirm']);

const cancelButton = ref(null);
const dialog = ref(null);
let previousOverflow = '';
let previouslyFocused = null;

function close() {
    if (!props.busy) {
        emit('update:show', false);
    }
}

function confirm() {
    if (!props.busy) {
        emit('confirm');
    }
}

function onKeydown(event) {
    if (!props.show) {
        return;
    }

    if (event.key === 'Escape') {
        event.preventDefault();
        close();
        return;
    }

    // El foco no debe escapar del diálogo mientras está abierto.
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

watch(
    () => props.show,
    async (open) => {
        if (typeof document === 'undefined') {
            return;
        }

        if (open) {
            previouslyFocused = document.activeElement;
            previousOverflow = document.body.style.overflow;
            document.body.style.overflow = 'hidden';
            await nextTick();
            cancelButton.value?.focus();
        } else {
            document.body.style.overflow = previousOverflow;
            previouslyFocused?.focus?.();
            previouslyFocused = null;
        }
    },
);

onMounted(() => window.addEventListener('keydown', onKeydown));

onBeforeUnmount(() => {
    window.removeEventListener('keydown', onKeydown);

    if (typeof document !== 'undefined') {
        document.body.style.overflow = previousOverflow;
    }
});
</script>

<template>
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
                v-if="show"
                class="theme-overlay fixed inset-0 z-50 flex items-center justify-center px-4 py-8"
                @click.self="close"
            >
                <Transition
                    appear
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="translate-y-2 scale-[0.98] opacity-0"
                    enter-to-class="translate-y-0 scale-100 opacity-100"
                >
                    <div
                        ref="dialog"
                        class="theme-panel-solid w-full max-w-md p-6 text-center sm:p-8"
                        role="alertdialog"
                        aria-modal="true"
                        aria-labelledby="confirm-title"
                        aria-describedby="confirm-message"
                    >
                        <p class="theme-accent text-2xl leading-none" aria-hidden="true">
                            {{ danger ? '⚠' : '✦' }}
                        </p>

                        <h2 id="confirm-title" class="theme-display mt-3 text-2xl">
                            {{ title }}
                        </h2>

                        <div id="confirm-message" class="theme-body theme-muted mt-2 text-base leading-relaxed">
                            <slot>
                                <p>{{ message }}</p>
                            </slot>
                        </div>

                        <div class="mt-7 flex flex-col-reverse justify-center gap-3 sm:flex-row">
                            <button
                                ref="cancelButton"
                                type="button"
                                class="theme-btn-ghost"
                                :disabled="busy"
                                @click="close"
                            >
                                {{ cancelLabel }}
                            </button>

                            <button
                                type="button"
                                :class="danger ? 'theme-danger-solid' : 'theme-btn'"
                                :disabled="busy"
                                :aria-busy="busy ? 'true' : undefined"
                                @click="confirm"
                            >
                                {{ busy ? busyLabel : confirmLabel }}
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
