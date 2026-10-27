<script setup>
import { Head, Link } from '@inertiajs/vue3';
import OracleLayout from '@/Layouts/OracleLayout.vue';
import { useTheme } from '@/composables/useTheme';

defineProps({
    spreads: Array,
});

const { current } = useTheme();
</script>

<template>
    <Head title="El Oráculo" />

    <OracleLayout>
        <div class="flex min-h-screen flex-col items-center justify-center px-6 py-16">
            <Link
                :href="route('readings.history')"
                class="theme-btn-ghost absolute right-6 top-6 rounded px-4 py-2 text-xs"
            >
                Mis lecturas
            </Link>

            <div class="theme-label theme-faint absolute left-6 top-6 text-[10px]">
                <template v-if="$page.props.auth.user">
                    {{ $page.props.auth.user.name }} ·
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="theme-link"
                    >
                        Salir
                    </Link>
                </template>
                <Link v-else :href="route('login')" class="theme-link">Iniciar sesión</Link>
            </div>

            <p class="theme-accent mb-4 text-3xl leading-none">{{ current.glyph }}</p>

            <p class="theme-label theme-accent mb-3 text-[10px]">✦ ✦ ✦</p>

            <h1 class="theme-display mb-2 text-center text-5xl sm:text-6xl">
                El Oráculo
            </h1>

            <p class="theme-body theme-muted mb-2 text-center text-xl">
                Elegí tu tirada
            </p>

            <p class="theme-label theme-faint mb-14 text-center text-[9px]">
                Corriente actual · {{ current.name }} — {{ current.epithet }}
            </p>

            <div
                class="grid w-full max-w-2xl gap-6"
                :class="spreads.length > 1 ? 'sm:grid-cols-2' : ''"
            >
                <Link
                    v-for="spread in spreads"
                    :key="spread.id"
                    :href="route('readings.create', spread.slug)"
                    class="spread-card theme-panel group block p-8 text-center"
                >
                    <p class="theme-label theme-accent mb-3 text-[9px]">
                        {{ spread.positions_count }} cartas
                    </p>
                    <h2 class="theme-display mb-3 text-2xl">
                        {{ spread.nombre }}
                    </h2>
                    <p class="theme-body theme-muted text-base leading-relaxed">
                        {{ spread.descripcion }}
                    </p>
                </Link>
            </div>

            <p class="theme-faint theme-label mt-16 text-[9px]">
                El sobre se compromete antes de abrirse · la tirada se puede verificar
            </p>
        </div>
    </OracleLayout>
</template>

<style scoped>
.spread-card {
    transition:
        border-color 0.25s ease,
        background-color 0.25s ease,
        transform 0.25s ease;
}

.spread-card:hover {
    border-color: var(--t-accent);
    transform: translateY(-3px);
    background-color: color-mix(in srgb, var(--t-surface) 72%, transparent);
}

@media (prefers-reduced-motion: reduce) {
    .spread-card {
        transition: none;
    }

    .spread-card:hover {
        transform: none;
    }
}
</style>
