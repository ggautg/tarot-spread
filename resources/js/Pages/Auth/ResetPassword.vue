<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthCard from '@/Components/AuthCard.vue';
import AppButton from '@/Components/AppButton.vue';
import AppInput from '@/Components/AppInput.vue';

/** Formulario de nueva contraseña, abierto desde el enlace del correo. */
const props = defineProps({
    email: { type: String, required: true },
    token: { type: String, required: true },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <Head title="Nueva contraseña" />

    <AuthCard
        title="Nueva contraseña"
        subtitle="Elige una contraseña que no uses en ningún otro sitio."
    >
        <form class="space-y-4" @submit.prevent="submit">
            <AppInput
                id="email"
                v-model="form.email"
                label="Correo electrónico"
                type="email"
                autocomplete="username"
                required
                :error="form.errors.email"
            />

            <AppInput
                id="password"
                v-model="form.password"
                label="Nueva contraseña"
                type="password"
                autocomplete="new-password"
                hint="Mínimo 8 caracteres."
                required
                autofocus
                :error="form.errors.password"
            />

            <AppInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                label="Repite la contraseña"
                type="password"
                autocomplete="new-password"
                required
                :error="form.errors.password_confirmation"
            />

            <AppButton
                type="submit"
                block
                :loading="form.processing"
                loading-label="Guardando…"
            >
                Guardar contraseña
            </AppButton>
        </form>
    </AuthCard>
</template>
