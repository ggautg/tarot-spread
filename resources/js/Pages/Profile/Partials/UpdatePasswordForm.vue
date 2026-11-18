<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppButton from '@/Components/AppButton.vue';
import AppInput from '@/Components/AppInput.vue';
import AppPanel from '@/Components/AppPanel.vue';

/** Cambio de contraseña desde la cuenta. */
const currentPasswordInput = ref(null);
const passwordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

function updatePassword() {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value?.focus();
            }

            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value?.focus();
            }
        },
    });
}
</script>

<template>
    <AppPanel
        eyebrow="Seguridad"
        title="Contraseña"
        subtitle="Usa una contraseña larga y distinta a la de otros sitios."
    >
        <form class="space-y-4" @submit.prevent="updatePassword">
            <AppInput
                id="current_password"
                ref="currentPasswordInput"
                v-model="form.current_password"
                label="Contraseña actual"
                type="password"
                autocomplete="current-password"
                :error="form.errors.current_password"
            />

            <AppInput
                id="password"
                ref="passwordInput"
                v-model="form.password"
                label="Nueva contraseña"
                type="password"
                autocomplete="new-password"
                hint="Mínimo 8 caracteres."
                :error="form.errors.password"
            />

            <AppInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                label="Repite la nueva contraseña"
                type="password"
                autocomplete="new-password"
                :error="form.errors.password_confirmation"
            />

            <div class="flex flex-wrap items-center gap-3">
                <AppButton
                    type="submit"
                    size="sm"
                    :loading="form.processing"
                    loading-label="Guardando…"
                >
                    Cambiar contraseña
                </AppButton>

                <Transition
                    enter-active-class="transition duration-200"
                    enter-from-class="opacity-0"
                    leave-active-class="transition duration-150"
                    leave-to-class="opacity-0"
                >
                    <span
                        v-if="form.recentlySuccessful"
                        class="theme-label theme-accent text-[9px]"
                        role="status"
                    >
                        ✓ Guardado
                    </span>
                </Transition>
            </div>
        </form>
    </AppPanel>
</template>
