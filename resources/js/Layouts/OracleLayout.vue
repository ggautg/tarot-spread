<script setup>
import FlashMessage from '@/Components/FlashMessage.vue';
import SiteHeader from '@/Components/SiteHeader.vue';

/**
 * Layout único de la aplicación.
 *
 * Fondo temático + cabecera + avisos flash + contenido + pie. Las páginas de
 * acceso usan `AuthCard` porque no necesitan la navegación completa.
 */
</script>

<template>
    <div class="theme-page flex min-h-screen flex-col">
        <SiteHeader />

        <div class="mx-auto w-full max-w-5xl px-4 pt-4 sm:px-6">
            <FlashMessage />
        </div>

        <main class="flex-1">
            <slot />
        </main>

        <footer class="mx-auto w-full max-w-5xl px-4 pb-8 pt-14 sm:px-6">
            <div class="theme-rule flex flex-wrap items-center justify-between gap-2 border-t pt-4">
                <p class="theme-label theme-faint text-[8px] leading-relaxed">
                    El Oráculo · tiradas selladas y verificables
                </p>
                <p class="theme-label theme-faint text-[8px] leading-relaxed">
                    Cada resultado se compromete antes de abrirse
                </p>
            </div>
        </footer>
    </div>
</template>
