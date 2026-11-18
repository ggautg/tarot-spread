<script setup>
import { onBeforeUnmount, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppButton from './AppButton.vue';
import AppTextarea from './AppTextarea.vue';

/**
 * Notas privadas de una lectura.
 *
 * Solo se renderiza para el dueño de la lectura. Guarda con PATCH, conserva el
 * scroll y confirma brevemente sin recargar la página.
 */
const props = defineProps({
    reading: { type: Object, required: true },
});

const form = useForm({
    notas_personales: props.reading.notas_personales ?? '',
});

const saved = ref(false);
let timer = null;

function save() {
    form.patch(route('readings.notes', props.reading.uuid), {
        preserveScroll: true,
        onSuccess: () => {
            saved.value = true;
            clearTimeout(timer);
            timer = setTimeout(() => (saved.value = false), 2500);
        },
    });
}

onBeforeUnmount(() => clearTimeout(timer));
</script>

<template>
    <form @submit.prevent="save">
        <AppTextarea
            id="notas_personales"
            v-model="form.notas_personales"
            label="Tus notas"
            hint="Privadas: solo tú puedes verlas. Máximo 2000 caracteres."
            placeholder="¿Cómo te sentiste con esta lectura? ¿Se cumplió algo? ¿Qué te hizo pensar?"
            :rows="4"
            :maxlength="2000"
            resize
            :error="form.errors.notas_personales"
        />

        <div class="mt-4 flex flex-wrap items-center gap-3">
            <AppButton
                type="submit"
                size="sm"
                :loading="form.processing"
                loading-label="Guardando…"
            >
                Guardar notas
            </AppButton>

            <Transition
                enter-active-class="transition duration-200"
                enter-from-class="opacity-0"
                leave-active-class="transition duration-150"
                leave-to-class="opacity-0"
            >
                <span v-if="saved" class="theme-label theme-accent text-[9px]" role="status">
                    ✓ Guardado
                </span>
            </Transition>
        </div>
    </form>
</template>
