<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
  spreads: Array,
});
</script>

<template>

  <Head title="El Oráculo" />

  <div class="min-h-screen relative bg-[#150F26] text-[#EDE3D0] flex flex-col items-center justify-center px-6 py-16">
    <Link :href="route('readings.history')"
      class="absolute top-6 right-6 text-sm font-mono uppercase tracking-widest text-[#C9A227] border border-[#C9A227]/40 rounded px-4 py-2 hover:bg-[#C9A227]/10 transition">
      Mis lecturas
    </Link>

    <div class="absolute top-6 left-6 text-sm font-mono uppercase tracking-widest text-[#EDE3D0]/60">
      <template v-if="$page.props.auth.user">
        {{ $page.props.auth.user.name }} ·
        <Link :href="route('logout')" method="post" as="button" class="hover:text-[#C9A227] transition">Salir</Link>
      </template>
      <Link v-else :href="route('login')" class="hover:text-[#C9A227] transition">Iniciar sesión</Link>
    </div>

    <p class="font-mono text-xs tracking-[0.4em] text-[#C9A227] uppercase mb-3">
      ✦ ✦ ✦
    </p>
    <h1 class="text-5xl text-center mb-2" style="font-family: 'Cinzel', serif;">
      El Oráculo
    </h1>
    <p class="text-[#EDE3D0]/50 mb-14 text-center" style="font-family: 'Cormorant Garamond', serif; font-size: 1.2rem;">
      Elegí tu tirada
    </p>

    <div class="grid gap-6 w-full max-w-2xl" :class="spreads.length > 1 ? 'sm:grid-cols-2' : ''">
      <Link v-for="spread in spreads" :key="spread.id" :href="route('readings.create', spread.slug)"
        class="group block border border-[#C9A227]/25 rounded-lg p-8 text-center hover:border-[#C9A227] hover:bg-[#4A3B6B]/20 transition">
        <p class="font-mono text-[10px] tracking-widest text-[#C9A227]/70 mb-3">
          {{ spread.positions_count }} CARTAS
        </p>
        <h2 class="text-2xl mb-3 group-hover:text-[#C9A227] transition" style="font-family: 'Cinzel', serif;">
          {{ spread.nombre }}
        </h2>
        <p class="text-[#EDE3D0]/60 text-sm" style="font-family: 'Cormorant Garamond', serif; font-size: 1.05rem;">
          {{ spread.descripcion }}
        </p>
      </Link>
    </div>
  </div>
</template>