<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthCard from '@/Components/AuthCard.vue';
import AppButton from '@/Components/AppButton.vue';
import AppInput from '@/Components/AppInput.vue';

/** Registro de una cuenta nueva. */
const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <Head title="Crear cuenta" />

    <AuthCard
        title="Crear cuenta"
        subtitle="Guarda tus lecturas y lleva tu corriente visual a cualquier dispositivo."
    >
        <form class="space-y-4" @submit.prevent="submit">
            <AppInput
                id="name"
                v-model="form.name"
                label="Nombre"
                autocomplete="name"
                placeholder="Cómo quieres que te llamemos"
                required
                autofocus
                :error="form.errors.name"
            />

            <AppInput
                id="email"
                v-model="form.email"
                label="Correo electrónico"
                type="email"
                autocomplete="username"
                placeholder="tu@correo.com"
                required
                :error="form.errors.email"
            />

            <AppInput
                id="password"
                v-model="form.password"
                label="Contraseña"
                type="password"
                autocomplete="new-password"
                hint="Mínimo 8 caracteres."
                required
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
                loading-label="Creando cuenta…"
            >
                Crear cuenta
            </AppButton>
        </form>

        <template #footer>
            ¿Ya tienes cuenta?
            <Link :href="route('login')" class="theme-link">Entrar</Link>
        </template>
    </AuthCard>
</template>
