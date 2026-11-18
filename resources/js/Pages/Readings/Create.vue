<script setup>
import { computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import OracleLayout from '@/Layouts/OracleLayout.vue';
import AppButton from '@/Components/AppButton.vue';
import AppPanel from '@/Components/AppPanel.vue';
import AppTextarea from '@/Components/AppTextarea.vue';
import PageHeader from '@/Components/PageHeader.vue';
import PositionList from '@/Components/PositionList.vue';

/**
 * Pantalla previa a la tirada: explica la tirada, recoge la pregunta y sella
 * el sobre (paso COMMIT). Las cartas todavía no se reparten.
 */
const props = defineProps({
    spread: { type: Object, required: true },
});

const form = useForm({
    pregunta: '',
});

const positions = computed(() => props.spread.positions ?? []);

function submit() {
    form.post(route('readings.store', props.spread.slug));
}
</script>

<template>
    <Head :title="`Tirada · ${spread.nombre}`" />

    <OracleLayout>
        <div class="mx-auto w-full max-w-5xl px-4 py-8 sm:px-6 sm:py-12">
            <PageHeader
                eyebrow="Preparar la tirada"
                :title="spread.nombre"
                :subtitle="spread.descripcion"
                :back-href="route('home')"
                back-label="Volver a las tiradas"
            />

            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]">
                <AppPanel
                    as="form"
                    eyebrow="Tu consulta"
                    title="¿Qué quieres preguntar?"
                    subtitle="La pregunta es opcional. Si la dejas en blanco, el oráculo responderá igual."
                    @submit.prevent="submit"
                >
                    <AppTextarea
                        id="pregunta"
                        v-model="form.pregunta"
                        label="Pregunta"
                        placeholder="¿Qué necesito entender sobre…?"
                        hint="Máximo 255 caracteres. Escríbela en presente y con claridad."
                        :rows="4"
                        :maxlength="255"
                        :error="form.errors.pregunta"
                        resize
                    />

                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        <AppButton
                            type="submit"
                            :loading="form.processing"
                            loading-label="Sellando…"
                        >
                            Sellar la tirada
                        </AppButton>

                        <AppButton :href="route('home')" variant="quiet">
                            Cancelar
                        </AppButton>
                    </div>

                    <p class="theme-body theme-faint mt-5 text-sm leading-relaxed">
                        Al sellar, el servidor genera un secreto y publica su huella.
                        Nadie —tampoco el servidor— puede cambiar el resultado sin que
                        la verificación lo delate.
                    </p>
                </AppPanel>

                <AppPanel
                    eyebrow="El tablero"
                    title="Posiciones"
                    subtitle="Cada carta ocupará uno de estos lugares."
                >
                    <PositionList :positions="positions" />
                </AppPanel>
            </div>
        </div>
    </OracleLayout>
</template>
