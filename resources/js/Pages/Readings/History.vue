<script setup>
import { computed, ref } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import OracleLayout from '@/Layouts/OracleLayout.vue';
import AppButton from '@/Components/AppButton.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import EmptyState from '@/Components/EmptyState.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ReadingHistoryItem from '@/Components/ReadingHistoryItem.vue';

/**
 * Historial de lecturas del visitante.
 *
 * Un usuario ve las de su cuenta; un invitado, solo las de su sesión. El
 * borrado pide confirmación y conserva la posición del scroll.
 */
defineProps({
    readings: { type: Array, default: () => [] },
});

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);

const readingToDelete = ref(null);
const deleting = ref(false);

function askDelete(reading) {
    readingToDelete.value = reading;
}

function closeDialog() {
    if (!deleting.value) {
        readingToDelete.value = null;
    }
}

function confirmDelete() {
    if (!readingToDelete.value) {
        return;
    }

    deleting.value = true;

    router.delete(route('readings.destroy', readingToDelete.value.uuid), {
        preserveScroll: true,
        onFinish: () => {
            deleting.value = false;
            readingToDelete.value = null;
        },
    });
}
</script>

<template>
    <Head title="Mis lecturas" />

    <OracleLayout>
        <div class="mx-auto w-full max-w-4xl px-4 py-8 sm:px-6 sm:py-12">
            <PageHeader
                eyebrow="Historial"
                title="Mis lecturas"
                :subtitle="readings.length
                    ? 'Cada tirada conserva su compromiso y su resultado. Ábrela para revisarla o compartirla.'
                    : 'Aquí se guardan las tiradas que vayas haciendo.'"
            >
                <template #actions>
                    <AppButton :href="route('home')" variant="ghost" size="sm">
                        Nueva tirada
                    </AppButton>
                </template>
            </PageHeader>

            <div
                v-if="readings.length === 0"
                class="space-y-4"
            >
                <EmptyState
                    glyph="☾"
                    title="Todavía no hay lecturas"
                    text="Elige una tirada y el oráculo guardará aquí su resultado."
                >
                    <AppButton :href="route('home')">
                        Elegir una tirada
                    </AppButton>
                </EmptyState>

                <p v-if="!user" class="theme-body theme-faint text-center text-sm leading-relaxed">
                    Estás como invitado: estas lecturas viven en esta sesión.
                    <a :href="route('register')" class="theme-link">Crea una cuenta</a>
                    para conservarlas cuando cierres el navegador.
                </p>
            </div>

            <div v-else class="space-y-3">
                <ReadingHistoryItem
                    v-for="reading in readings"
                    :key="reading.id"
                    :reading="reading"
                    @delete="askDelete"
                />

                <p v-if="!user" class="theme-body theme-faint pt-2 text-center text-sm leading-relaxed">
                    Como invitado, el historial pertenece a esta sesión.
                    <a :href="route('register')" class="theme-link">Crea una cuenta</a>
                    para no perderlo.
                </p>
            </div>
        </div>

        <ConfirmDialog
            :show="Boolean(readingToDelete)"
            title="¿Borrar esta lectura?"
            confirm-label="Borrar definitivamente"
            busy-label="Borrando…"
            :busy="deleting"
            @update:show="closeDialog"
            @confirm="confirmDelete"
        >
            <p>
                La tirada
                <strong class="theme-accent">{{ readingToDelete?.spread.nombre }}</strong>
                se borrará para siempre, junto con sus cartas y notas.
                No se puede deshacer.
            </p>
        </ConfirmDialog>
    </OracleLayout>
</template>
