<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthCard from '@/Components/AuthCard.vue';
import AppButton from '@/Components/AppButton.vue';
import AppCheckbox from '@/Components/AppCheckbox.vue';
import AppInput from '@/Components/AppInput.vue';

/**
 * Inicio de sesión.
 *
 * `canResetPassword` lo calcula el controlador según las rutas disponibles;
 * `status` trae mensajes del flujo de recuperación.
 */
defineProps({
    canResetPassword: { type: Boolean, default: false },
    status: { type: String, default: '' },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <Head title="Entrar" />

    <AuthCard
        title="Entrar"
        subtitle="Vuelve a tus lecturas y conserva tu corriente visual."
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

            <AppInput
                id="password"
                v-model="form.password"
                label="Contraseña"
                type="password"
                autocomplete="current-password"
                placeholder="••••••••"
                required
                :error="form.errors.password"
            />

            <div class="flex flex-wrap items-center justify-between gap-3">
                <AppCheckbox
                    id="remember"
                    v-model="form.remember"
                    label="Recordarme"
                />

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="theme-link text-sm"
                >
                    ¿Olvidaste tu contraseña?
                </Link>
            </div>

            <AppButton
                type="submit"
                block
                :loading="form.processing"
                loading-label="Entrando…"
            >
                Entrar
            </AppButton>
        </form>

        <template #footer>
            ¿Aún no tienes cuenta?
            <Link :href="route('register')" class="theme-link">Crear una cuenta</Link>
        </template>
    </AuthCard>
</template>
