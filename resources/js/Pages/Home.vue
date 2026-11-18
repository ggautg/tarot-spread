<script setup>
import { Head } from '@inertiajs/vue3';
import OracleLayout from '@/Layouts/OracleLayout.vue';
import SpreadCard from '@/Components/SpreadCard.vue';
import EmptyState from '@/Components/EmptyState.vue';
import AppButton from '@/Components/AppButton.vue';
import { useTheme } from '@/composables/useTheme';

/** Portada: presenta el oráculo y deja elegir una tirada. */
defineProps({
    spreads: { type: Array, default: () => [] },
});

const { current } = useTheme();

const steps = [
    {
        number: '01',
        title: 'Elige',
        text: 'Escoge la tirada y, si quieres, escribe la pregunta que te trae aquí.',
    },
    {
        number: '02',
        title: 'Sella',
        text: 'El servidor guarda un secreto y publica su huella. La tirada ya no puede cambiar.',
    },
    {
        number: '03',
        title: 'Abre y verifica',
        text: 'Al abrir el sobre se reparten las cartas. Cualquiera puede comprobar el resultado.',
    },
];
</script>

<template>
    <Head title="El Oráculo" />

    <OracleLayout>
        <div class="mx-auto w-full max-w-5xl px-4 sm:px-6">
            <section class="py-14 text-center sm:py-20">
                <p class="theme-accent text-4xl leading-none" aria-hidden="true">
                    {{ current.glyph }}
                </p>

                <p class="theme-label theme-accent mt-5 text-[10px]">
                    ✦ ✦ ✦
                </p>

                <h1 class="theme-display mt-2 text-5xl leading-none sm:text-7xl">
                    El Oráculo
                </h1>

                <p class="theme-body theme-muted mx-auto mt-5 max-w-2xl text-xl leading-relaxed">
                    Tarot honesto: elige una tirada, sella el sobre y ábrelo
                    cuando estés listo. El resultado queda comprometido antes de
                    revelarse.
                </p>

                <p class="theme-label theme-faint mt-6 text-[9px]">
                    Corriente actual · {{ current.name }} — {{ current.epithet }}
                </p>
            </section>

            <section aria-labelledby="spreads-title">
                <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <p class="theme-label theme-accent text-[10px]">Tiradas</p>
                        <h2 id="spreads-title" class="theme-display mt-1 text-3xl">
                            Elige tu tirada
                        </h2>
                    </div>

                    <AppButton
                        v-if="$page.props.auth.user"
                        :href="route('readings.history')"
                        variant="quiet"
                    >
                        Ver mi historial →
                    </AppButton>
                </div>

                <div
                    v-if="spreads.length"
                    class="grid gap-5 sm:grid-cols-2"
                >
                    <SpreadCard
                        v-for="spread in spreads"
                        :key="spread.id"
                        :spread="spread"
                    />
                </div>

                <EmptyState
                    v-else
                    glyph="☾"
                    title="Todavía no hay tiradas"
                    text="El oráculo está preparando el mazo. Vuelve en un momento."
                />
            </section>

            <section class="mt-16 sm:mt-20" aria-labelledby="how-title">
                <p class="theme-label theme-accent text-[10px]">Cómo funciona</p>
                <h2 id="how-title" class="theme-display mt-1 text-3xl">
                    Sin trampas, sin sorpresas
                </h2>

                <div class="mt-6 grid gap-4 sm:grid-cols-3">
                    <article
                        v-for="step in steps"
                        :key="step.number"
                        class="theme-panel p-5"
                    >
                        <p class="theme-label theme-accent text-[9px]">
                            {{ step.number }}
                        </p>
                        <h3 class="theme-display mt-2 text-xl">
                            {{ step.title }}
                        </h3>
                        <p class="theme-body theme-muted mt-1.5 text-base leading-relaxed">
                            {{ step.text }}
                        </p>
                    </article>
                </div>
            </section>
        </div>
    </OracleLayout>
</template>
