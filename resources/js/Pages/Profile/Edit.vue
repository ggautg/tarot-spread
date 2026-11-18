<script setup>
import { Head } from '@inertiajs/vue3';
import OracleLayout from '@/Layouts/OracleLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import AppPanel from '@/Components/AppPanel.vue';
import ThemePreviewCard from '@/Components/ThemePreviewCard.vue';
import { useTheme } from '@/composables/useTheme';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';

/**
 * Página de cuenta.
 *
 * Reúne datos personales, corriente visual, contraseña y borrado de cuenta.
 * La selección de tema se guarda sola (con sesión iniciada viaja con la cuenta).
 */
defineProps({
    mustVerifyEmail: { type: Boolean, default: false },
    status: { type: String, default: '' },
});

const { themes, current, select } = useTheme();
</script>

<template>
    <Head title="Tu cuenta" />

    <OracleLayout>
        <div class="mx-auto w-full max-w-3xl px-4 py-8 sm:px-6 sm:py-12">
            <PageHeader
                eyebrow="Cuenta"
                title="Tu cuenta"
                subtitle="Tus datos, tu contraseña y la corriente visual que prefiere el oráculo."
                :back-href="route('home')"
                back-label="Volver al oráculo"
            />

            <div class="space-y-6">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                />

                <AppPanel
                    eyebrow="Apariencia"
                    title="Corriente visual"
                    subtitle="Cada corriente cambia la paleta y la tipografía de toda la aplicación. Se guarda en tu cuenta."
                >
                    <div class="grid gap-3 sm:grid-cols-2">
                        <ThemePreviewCard
                            v-for="theme in themes"
                            :key="theme.id"
                            :theme="theme"
                            :active="current.id === theme.id"
                            compact
                            @select="select"
                        />
                    </div>

                    <p class="theme-label theme-faint mt-4 text-[9px] leading-relaxed">
                        Corriente activa · {{ current.name }} — {{ current.epithet }}
                    </p>
                </AppPanel>

                <UpdatePasswordForm />
                <DeleteUserForm />
            </div>
        </div>
    </OracleLayout>
</template>
