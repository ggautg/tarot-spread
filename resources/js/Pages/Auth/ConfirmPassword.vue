<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthCard from '@/Components/AuthCard.vue';
import AppButton from '@/Components/AppButton.vue';
import AppInput from '@/Components/AppInput.vue';

/** Confirmación de contraseña para acceder a un área protegida. */
const form = useForm({
    password: '',
});

function submit() {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
}
</script>

<template>
    <Head title="Confirmar contraseña" />

    <AuthCard
        title="Confirma tu identidad"
        subtitle="Esta es un área protegida. Escribe tu contraseña para continuar."
    >
        <form class="space-y-4" @submit.prevent="submit">
            <AppInput
                id="password"
                v-model="form.password"
                label="Contraseña"
                type="password"
                autocomplete="current-password"
                required
                autofocus
                :error="form.errors.password"
            />

            <AppButton
                type="submit"
                block
                :loading="form.processing"
                loading-label="Confirmando…"
            >
                Confirmar
            </AppButton>
        </form>
    </AuthCard>
</template>
