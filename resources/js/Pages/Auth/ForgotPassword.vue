<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthCard from '@/Components/AuthCard.vue';
import AppButton from '@/Components/AppButton.vue';
import AppInput from '@/Components/AppInput.vue';

/** Solicitud del enlace para restablecer la contraseña. */
defineProps({
    status: { type: String, default: '' },
});

const form = useForm({
    email: '',
});

function submit() {
    form.post(route('password.email'));
}
</script>

<template>
    <Head title="Recuperar contraseña" />

    <AuthCard
        title="Recuperar contraseña"
        subtitle="Escribe tu correo y te enviaremos un enlace para elegir una nueva."
    >
        <p v-if="status" class="theme-alert mb-4 text-sm" role="status">
            {{ status }}
        </p>

        <form class="space-y-4" @submit.prevent="submit">
            <AppInput
                id="email"
                v-model="form.email"
                label="Correo electrónico"
                type="email"
                autocomplete="username"
                placeholder="tu@correo.com"
                required
                autofocus
                :error="form.errors.email"
            />

            <AppButton
                type="submit"
                block
                :loading="form.processing"
                loading-label="Enviando…"
            >
                Enviar enlace
            </AppButton>
        </form>

        <template #footer>
            <Link :href="route('login')" class="theme-link">Volver a entrar</Link>
        </template>
    </AuthCard>
</template>
