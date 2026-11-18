<script setup>
import { Link } from '@inertiajs/vue3';
import FlashMessage from './FlashMessage.vue';
import OracleMark from './OracleMark.vue';
import ThemePicker from './ThemePicker.vue';

/**
 * Marco común de las páginas de acceso (entrar, registro, contraseñas).
 *
 * Mantiene el fondo temático, la marca y el selector de corriente, y centra el
 * formulario en un panel legible.
 */
defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
});
</script>

<template>
    <div class="theme-page flex min-h-screen flex-col">
        <div class="mx-auto flex w-full max-w-5xl items-center justify-between px-4 py-4 sm:px-6">
            <Link :href="route('home')" class="theme-btn-quiet">
                <span aria-hidden="true">←</span>
                Volver al oráculo
            </Link>

            <ThemePicker />
        </div>

        <main class="flex flex-1 items-center justify-center px-4 py-10 sm:px-6">
            <div class="w-full max-w-md">
                <div class="mb-4">
                    <FlashMessage />
                </div>

                <div class="theme-panel-solid p-6 sm:p-9">
                    <div class="mb-6 text-center">
                        <span class="theme-accent inline-block">
                            <OracleMark :size="40" />
                        </span>

                        <p class="theme-label theme-accent mt-3 text-[9px]">
                            El Oráculo
                        </p>

                        <h1 class="theme-display mt-1 text-3xl">
                            {{ title }}
                        </h1>

                        <p v-if="subtitle" class="theme-body theme-muted mt-2 text-base leading-relaxed">
                            {{ subtitle }}
                        </p>
                    </div>

                    <slot />
                </div>

                <p v-if="$slots.footer" class="theme-label theme-faint mt-6 text-center text-[9px] leading-relaxed">
                    <slot name="footer" />
                </p>
            </div>
        </main>
    </div>
</template>
