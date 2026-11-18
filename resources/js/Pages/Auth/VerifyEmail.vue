<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthCard from '@/Components/AuthCard.vue';
import AppButton from '@/Components/AppButton.vue';

/**
 * Aviso de verificación de correo.
 *
 * Permite reenviar el enlace o cerrar la sesión si el correo no es correcto.
 */
const props = defineProps({
    status: { type: String, default: '' },
});

const form = useForm({});

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);

function resend() {
    form.post(route('verification.send'));
}
</script>

<template>
    <Head title="Verifica tu correo" />

    <AuthCard
        title="Verifica tu correo"
        subtitle="Te enviamos un enlace para confirmar tu dirección. Ábrelo para activar todas las funciones de tu cuenta."
    >
        <p
            v-if="verificationLinkSent"
            class="theme-alert mb-4 text-sm"
            role="status"
        >
            Enviamos un enlace nuevo a tu correo. Revisa también la carpeta de spam.
        </p>

        <form @submit.prevent="resend">
            <AppButton
                type="submit"
                block
                :loading="form.processing"
                loading-label="Enviando…"
            >
                Reenviar el enlace
            </AppButton>
        </form>

        <template #footer>
            ¿Escribiste mal tu correo?
            <Link :href="route('logout')" method="post" as="button" class="theme-link">
                Cerrar sesión
            </Link>
        </template>
    </AuthCard>
</template>
