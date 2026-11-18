<script setup>
import { computed, onBeforeUnmount, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import OracleLayout from '@/Layouts/OracleLayout.vue';
import AppButton from '@/Components/AppButton.vue';
import AppPanel from '@/Components/AppPanel.vue';
import PageHeader from '@/Components/PageHeader.vue';
import PositionList from '@/Components/PositionList.vue';
import ReadingBoard from '@/Components/ReadingBoard.vue';
import ReadingCardDetail from '@/Components/ReadingCardDetail.vue';
import ReadingNotesForm from '@/Components/ReadingNotesForm.vue';
import { formatDate } from '@/utils/formatDate';

/**
 * Tablero de una lectura.
 *
 * Antes de revelar muestra los dorsos y el compromiso (hash + seed público).
 * Después de revelar, las cartas se voltean en cascada y aparece el detalle de
 * cada posición, la verificación criptográfica y las notas privadas del dueño.
 */
const props = defineProps({
    reading: { type: Object, required: true },
    canEdit: { type: Boolean, default: false },
    /** null = sin revelar; true/false = resultado de la verificación. */
    verified: { type: Boolean, default: null },
});

const revealed = computed(() => Boolean(props.reading.revealed_at));

const sortedCards = computed(() =>
    [...props.reading.cards].sort(
        (a, b) => (a.position?.orden ?? 0) - (b.position?.orden ?? 0),
    ),
);

const copyState = ref('idle');
let copyTimer = null;

const copyLabel = computed(() => {
    if (copyState.value === 'copied') {
        return '✓ Enlace copiado';
    }

    if (copyState.value === 'error') {
        return 'No se pudo copiar';
    }

    return 'Copiar enlace';
});

/** Copia la URL pública de la lectura, con aviso temporal. */
async function copyLink() {
    try {
        await navigator.clipboard.writeText(window.location.href);
        copyState.value = 'copied';
    } catch {
        copyState.value = 'error';
    }

    clearTimeout(copyTimer);
    copyTimer = setTimeout(() => (copyState.value = 'idle'), 2500);
}

onBeforeUnmount(() => clearTimeout(copyTimer));
</script>

<template>
    <Head :title="`Lectura · ${reading.spread.nombre}`">
        <meta property="og:title" :content="`Tirada de ${reading.spread.nombre}`" />
        <meta
            property="og:description"
            :content="reading.pregunta
                ? `«${reading.pregunta}»`
                : `Una lectura de ${reading.spread.nombre} revelada en El Oráculo.`"
        />
        <meta property="og:type" content="website" />
        <meta property="og:image" :content="`${$page.props.appUrl}/og-tarot.png`" />
        <meta name="twitter:card" content="summary_large_image" />
    </Head>

    <OracleLayout>
        <div class="mx-auto w-full max-w-5xl px-4 py-8 sm:px-6 sm:py-12">
            <PageHeader
                eyebrow="Lectura"
                :title="reading.spread.nombre"
                :back-href="route('home')"
                back-label="Volver a las tiradas"
            >
                <template #actions>
                    <AppButton variant="ghost" size="sm" @click="copyLink">
                        {{ copyLabel }}
                    </AppButton>
                    <AppButton :href="route('readings.history')" variant="quiet">
                        Historial
                    </AppButton>
                </template>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <span class="theme-chip">{{ reading.cards.length }} cartas</span>
                    <span class="theme-chip">{{ formatDate(reading.created_at) }}</span>

                    <span v-if="!revealed" class="theme-chip theme-chip-danger">
                        Sobre sellado
                    </span>
                    <span v-else-if="verified" class="theme-chip">
                        ✓ Verificada
                    </span>
                    <span v-else-if="verified === false" class="theme-chip theme-chip-danger">
                        ✕ Verificación fallida
                    </span>
                </div>

                <blockquote
                    v-if="reading.pregunta"
                    class="theme-body theme-muted mt-5 max-w-2xl border-l-2 pl-4 text-xl italic leading-relaxed"
                    style="border-color: color-mix(in srgb, var(--t-accent) 45%, transparent)"
                >
                    «{{ reading.pregunta }}»
                </blockquote>
            </PageHeader>

            <section class="theme-panel p-3 sm:p-6" aria-label="Tablero de cartas">
                <ReadingBoard
                    :positions="reading.spread.positions"
                    :cards="reading.cards"
                    :revealed="revealed"
                />
            </section>

            <!-- Paso pendiente: abrir el sobre. -->
            <AppPanel
                v-if="!revealed"
                class="mt-6"
                eyebrow="Paso 2"
                title="El sobre está sellado"
                subtitle="El servidor ya eligió un secreto y publicó su huella. Nada puede cambiar el resultado que saldrá de aquí."
            >
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                    <p class="theme-body theme-muted max-w-xl text-base leading-relaxed">
                        Al abrirlo, el secreto del servidor se combina con la semilla
                        pública de esta lectura. Recién entonces se reparten las cartas.
                    </p>

                    <AppButton
                        :href="route('readings.reveal', reading.uuid)"
                        method="post"
                        as="button"
                        size="lg"
                    >
                        Abrir el sobre
                    </AppButton>
                </div>

                <div class="mt-6">
                    <p class="theme-label theme-accent mb-2 text-[9px]">Posiciones</p>
                    <PositionList :positions="reading.spread.positions" />
                </div>
            </AppPanel>

            <!-- Resultado revelado. -->
            <section
                v-else
                class="mt-8"
                aria-labelledby="cards-detail-title"
            >
                <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <p class="theme-label theme-accent text-[10px]">Interpretación</p>
                        <h2 id="cards-detail-title" class="theme-display mt-1 text-3xl">
                            Las cartas
                        </h2>
                    </div>
                    <p class="theme-label theme-faint text-[8px]">
                        {{ reading.spread.positions.length }} posiciones · significado según orientación
                    </p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <ReadingCardDetail
                        v-for="(readingCard, index) in sortedCards"
                        :key="readingCard.id"
                        :reading-card="readingCard"
                        :index="index"
                    />
                </div>
            </section>

            <!-- Compromiso y verificación. -->
            <details class="theme-panel mt-6 p-5 sm:p-6">
                <summary class="cursor-pointer list-none">
                    <span class="theme-label theme-accent text-[9px]">
                        Verificación del compromiso
                    </span>
                    <span class="theme-body theme-muted ml-2 text-sm">
                        {{ revealed ? 'Huella, semillas y resultado' : 'Huella y semilla pública' }}
                    </span>
                </summary>

                <div class="mt-4 space-y-4">
                    <p class="theme-body theme-muted text-sm leading-relaxed">
                        El hash publicado se calculó con SHA-256 sobre el secreto del
                        servidor. Al revelar, ese secreto se hace público: si coincide
                        con el hash y las cartas coinciden con el reparto, la lectura
                        no fue alterada.
                    </p>

                    <dl class="grid gap-3 sm:grid-cols-2">
                        <div class="min-w-0">
                            <dt class="theme-label theme-faint text-[8px]">Hash del compromiso</dt>
                            <dd class="mt-1 break-all font-mono text-[11px] leading-relaxed">
                                {{ reading.server_seed_hash }}
                            </dd>
                        </div>

                        <div class="min-w-0">
                            <dt class="theme-label theme-faint text-[8px]">Semilla pública</dt>
                            <dd class="mt-1 break-all font-mono text-[11px] leading-relaxed">
                                {{ reading.client_seed }}
                            </dd>
                        </div>

                        <div v-if="revealed" class="min-w-0">
                            <dt class="theme-label theme-faint text-[8px]">Secreto del servidor</dt>
                            <dd class="mt-1 break-all font-mono text-[11px] leading-relaxed">
                                {{ reading.server_seed }}
                            </dd>
                        </div>

                        <div v-if="revealed" class="min-w-0">
                            <dt class="theme-label theme-faint text-[8px]">Fecha de apertura</dt>
                            <dd class="mt-1 text-sm">
                                {{ formatDate(reading.revealed_at) }}
                            </dd>
                        </div>
                    </dl>

                    <p
                        v-if="revealed"
                        class="text-sm leading-relaxed"
                        :class="verified ? 'theme-accent' : 'theme-danger-text'"
                    >
                        <template v-if="verified">
                            ✓ Verificación correcta: el compromiso y las cartas coinciden.
                        </template>
                        <template v-else>
                            ✕ No se pudo verificar esta lectura. El resultado no debería
                            considerarse confiable.
                        </template>
                    </p>
                </div>
            </details>

            <!-- Notas: solo el dueño de la lectura. -->
            <AppPanel
                v-if="revealed && canEdit"
                class="mt-6"
                eyebrow="Diario"
                title="Tus notas"
            >
                <ReadingNotesForm :reading="reading" />
            </AppPanel>
        </div>
    </OracleLayout>
</template>
