<script setup>
import { nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { useTheme } from '@/composables/useTheme';

const { themes, current, select } = useTheme();

const isOpen = ref(false);
const panel = ref(null);

function open() {
    isOpen.value = true;

    nextTick(async () => {
        if (
            typeof window === 'undefined' ||
            window.matchMedia('(prefers-reduced-motion: reduce)').matches
        ) {
            return;
        }

        const { gsap } = await import('gsap');

        gsap.from(panel.value, {
            opacity: 0,
            y: 28,
            scale: 0.98,
            duration: 0.35,
            ease: 'power3.out',
        });

        gsap.from(panel.value.querySelectorAll('[data-theme-card]'), {
            opacity: 0,
            y: 16,
            duration: 0.35,
            stagger: 0.045,
            delay: 0.08,
            ease: 'power2.out',
        });
    });
}

function close() {
    isOpen.value = false;
}

function onKeydown(event) {
    if (event.key === 'Escape' && isOpen.value) {
        close();
    }
}

watch(isOpen, (open) => {
    if (typeof document === 'undefined') {
        return;
    }

    document.body.style.overflow = open ? 'hidden' : '';
});

onMounted(() => window.addEventListener('keydown', onKeydown));

onBeforeUnmount(() => {
    window.removeEventListener('keydown', onKeydown);

    if (typeof document !== 'undefined') {
        document.body.style.overflow = '';
    }
});
</script>

<template>
    <div>
        <!-- Disparador flotante -->
        <button
            type="button"
            class="theme-panel-solid fixed bottom-5 right-5 z-40 flex items-center gap-3 px-4 py-3 shadow-lg backdrop-blur-sm transition hover:-translate-y-0.5"
            :aria-expanded="isOpen"
            aria-haspopup="dialog"
            title="Cambiar la corriente del oráculo"
            @click="open"
        >
            <span class="theme-accent text-2xl leading-none">{{ current.glyph }}</span>
            <span class="text-left">
                <span class="theme-label theme-faint block text-[9px]">Corriente</span>
                <span class="theme-display block text-sm leading-tight">{{ current.name }}</span>
            </span>
        </button>

        <!-- Círculo de iniciados -->
        <Teleport to="body">
            <div
                v-if="isOpen"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/75 px-4 py-8 backdrop-blur-sm"
                role="dialog"
                aria-modal="true"
                aria-labelledby="theme-picker-title"
                @click.self="close"
            >
                <div
                    ref="panel"
                    class="theme-panel-solid max-h-[88vh] w-full max-w-4xl overflow-y-auto p-6 sm:p-8"
                >
                    <div class="flex items-start justify-between gap-6">
                        <div>
                            <p class="theme-label theme-accent text-[10px]">Círculo de iniciados</p>
                            <h2 id="theme-picker-title" class="theme-display mt-1 text-3xl sm:text-4xl">
                                Elegí tu corriente
                            </h2>
                            <p class="theme-body theme-muted mt-2 max-w-xl text-[1.05rem] leading-relaxed">
                                Cada tema toma la paleta y la voz de un mago u ocultista. El oráculo
                                cambia de piel, pero las cartas y las tiradas son las mismas.
                            </p>
                        </div>

                        <button
                            type="button"
                            class="theme-btn-ghost rounded px-3 py-1.5 text-sm leading-none"
                            aria-label="Cerrar el selector de temas"
                            @click="close"
                        >
                            ✕
                        </button>
                    </div>

                    <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <button
                            v-for="theme in themes"
                            :key="theme.id"
                            type="button"
                            data-theme-card
                            :data-theme="theme.id"
                            :class="['theme-preview theme-card', { 'is-active': current.id === theme.id }]"
                            :aria-pressed="current.id === theme.id"
                            @click="select(theme.id)"
                        >
                            <span class="flex items-start justify-between">
                                <span class="theme-accent text-3xl leading-none">{{ theme.glyph }}</span>
                                <span
                                    v-if="current.id === theme.id"
                                    class="theme-label theme-accent text-[8px]"
                                >
                                    ✦ En uso
                                </span>
                                <span v-else class="theme-label theme-faint text-[8px]">
                                    {{ theme.era }}
                                </span>
                            </span>

                            <span class="theme-display mt-3 block text-left text-xl">
                                {{ theme.name }}
                            </span>
                            <span class="theme-label theme-muted mt-1 block text-left text-[9px]">
                                {{ theme.epithet }}
                            </span>

                            <span class="theme-body theme-muted mt-2 block text-left text-sm leading-snug">
                                {{ theme.description }}
                            </span>

                            <span class="mt-4 flex items-center gap-1.5">
                                <span class="theme-swatch h-2.5 w-6 rounded-full" />
                                <span class="theme-swatch-2 h-2.5 w-6 rounded-full" />
                                <span class="theme-swatch-surface h-2.5 w-6 rounded-full ring-1 ring-black/10" />
                            </span>
                        </button>
                    </div>

                    <div class="theme-rule mt-6 flex flex-wrap items-center justify-between gap-3 border-t pt-4">
                        <p class="theme-label theme-faint text-[9px] leading-relaxed">
                            Se guarda en este dispositivo · con sesión iniciada, viaja con tu cuenta
                        </p>
                        <button type="button" class="theme-btn rounded px-5 py-2 text-xs" @click="close">
                            Listo
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<style scoped>
.theme-card {
    border: 1px solid color-mix(in srgb, var(--t-accent) 24%, transparent);
    border-radius: var(--t-radius);
    padding: 1.15rem;
    cursor: pointer;
    transition:
        border-color 0.25s ease,
        box-shadow 0.25s ease,
        transform 0.25s ease;
}

.theme-card:hover {
    border-color: var(--t-accent);
    transform: translateY(-2px);
}

.theme-card.is-active {
    border-color: var(--t-accent);
    box-shadow:
        0 0 0 1px var(--t-accent),
        0 18px 40px -24px color-mix(in srgb, var(--t-accent) 65%, transparent);
}

@media (prefers-reduced-motion: reduce) {
    .theme-card {
        transition: none;
    }

    .theme-card:hover {
        transform: none;
    }
}
</style>
