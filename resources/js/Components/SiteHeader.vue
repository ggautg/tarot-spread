<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import OracleMark from './OracleMark.vue';
import ThemePicker from './ThemePicker.vue';

/**
 * Cabecera global del oráculo.
 *
 * Reúne la marca, la navegación principal, el selector de corriente y el
 * acceso a la cuenta. Es la única cabecera de la aplicación: las páginas no
 * repiten enlaces sueltos.
 */
const page = usePage();

const user = computed(() => page.props.auth?.user ?? null);

/** Marca el enlace de la sección activa. */
function navClass(active) {
    return ['nav-link', active ? 'is-active' : ''];
}
</script>

<template>
    <header class="site-header">
        <div class="mx-auto flex max-w-5xl flex-wrap items-center gap-x-3 gap-y-2 px-4 py-3 sm:px-6">
            <Link :href="route('home')" class="flex shrink-0 items-center gap-2.5">
                <span class="theme-accent"><OracleMark :size="26" /></span>
                <span class="theme-display text-xl leading-none">El Oráculo</span>
            </Link>

            <nav
                aria-label="Navegación principal"
                class="order-3 flex w-full items-center gap-5 sm:order-none sm:ml-6 sm:w-auto"
            >
                <Link
                    :href="route('home')"
                    :class="navClass(route().current('home'))"
                    :aria-current="route().current('home') ? 'page' : undefined"
                >
                    Tiradas
                </Link>
                <Link
                    :href="route('readings.history')"
                    :class="navClass(route().current('readings.history'))"
                    :aria-current="route().current('readings.history') ? 'page' : undefined"
                >
                    Mis lecturas
                </Link>
            </nav>

            <div class="ml-auto flex items-center gap-2 sm:gap-3">
                <ThemePicker />

                <template v-if="user">
                    <Link
                        :href="route('profile.edit')"
                        :class="['nav-link hidden sm:inline-flex', route().current('profile.*') ? 'is-active' : '']"
                    >
                        {{ user.name }}
                    </Link>
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="theme-btn-quiet"
                    >
                        Salir
                    </Link>
                </template>

                <template v-else>
                    <Link
                        :href="route('register')"
                        class="theme-btn-ghost hidden px-3 py-1.5 sm:inline-flex"
                    >
                        Crear cuenta
                    </Link>
                    <Link :href="route('login')" class="theme-btn px-3 py-1.5">
                        Entrar
                    </Link>
                </template>
            </div>
        </div>
    </header>
</template>

<style scoped>
.site-header {
    position: sticky;
    top: 0;
    z-index: 30;
    border-bottom: 1px solid color-mix(in srgb, var(--t-accent) 20%, transparent);
    background-color: color-mix(in srgb, var(--t-bg) 84%, transparent);
    backdrop-filter: blur(12px);
}

.nav-link {
    color: color-mix(in srgb, var(--t-ink) 70%, transparent);
    font-family: var(--t-font-label);
    font-size: 0.6rem;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    transition: color 0.2s ease;
}

.nav-link:hover,
.nav-link.is-active {
    color: var(--t-accent);
}
</style>
