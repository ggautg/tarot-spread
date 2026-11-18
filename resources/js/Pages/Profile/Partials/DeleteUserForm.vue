<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppButton from '@/Components/AppButton.vue';
import AppInput from '@/Components/AppInput.vue';
import AppPanel from '@/Components/AppPanel.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';

/**
 * Borrado definitivo de la cuenta.
 *
 * Pide la contraseña actual antes de eliminar. El diálogo se mantiene abierto
 * si la contraseña falla, para no perder el contexto del error.
 */
const confirming = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

function openDialog() {
    confirming.value = true;
}

function closeDialog() {
    if (form.processing) {
        return;
    }

    confirming.value = false;
    form.clearErrors();
    form.reset();
}

function deleteUser() {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            confirming.value = false;
        },
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <AppPanel
        eyebrow="Zona sensible"
        title="Borrar cuenta"
        subtitle="Al borrarla se eliminan también tus lecturas, notas y preferencias. No se puede deshacer."
    >
        <AppButton variant="danger" @click="openDialog">
            Borrar mi cuenta
        </AppButton>

        <ConfirmDialog
            :show="confirming"
            title="¿Borrar tu cuenta?"
            confirm-label="Borrar cuenta"
            busy-label="Borrando…"
            :busy="form.processing"
            @update:show="closeDialog"
            @confirm="deleteUser"
        >
            <p>
                Esta acción elimina tu cuenta y todo su contenido de forma permanente.
                Escribe tu contraseña para confirmar.
            </p>

            <div class="mt-5 text-left">
                <AppInput
                    id="delete_password"
                    ref="passwordInput"
                    v-model="form.password"
                    label="Contraseña"
                    type="password"
                    autocomplete="current-password"
                    placeholder="Tu contraseña actual"
                    autofocus
                    :error="form.errors.password"
                    @keyup.enter="deleteUser"
                />
            </div>
        </ConfirmDialog>
    </AppPanel>
</template>
