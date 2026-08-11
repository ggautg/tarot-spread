<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
  readings: Array,
});

function formatDate(dateStr) {
  return new Date(dateStr).toLocaleDateString('es-PY', {
    day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit',
  });
}
</script>

<template>
  <Head title="Mis lecturas" />

  <div class="min-h-screen relative bg-[#150F26] text-[#EDE3D0] px-6 py-16">
    <Link
      :href="route('home')"
      class="absolute top-6 left-6 text-sm font-mono uppercase tracking-widest text-[#C9A227] border border-[#C9A227]/40 rounded px-4 py-2 hover:bg-[#C9A227]/10 transition"
    >
      ← Inicio
    </Link>

    <div class="max-w-2xl mx-auto pt-10">
      <p class="font-mono text-xs tracking-[0.3em] text-[#C9A227] uppercase mb-2">Historial</p>
      <h1 class="text-4xl mb-10" style="font-family: 'Cinzel', serif;">Mis lecturas</h1>

      <div v-if="readings.length === 0" class="text-[#EDE3D0]/50 italic" style="font-family: 'Cormorant Garamond', serif; font-size: 1.2rem;">
        Todavía no hiciste ninguna tirada en esta sesión.
      </div>

      <div v-else class="space-y-3">
        <Link
          v-for="r in readings"
          :key="r.id"
          :href="route('readings.show', r.uuid)"
          class="block border border-[#C9A227]/20 rounded-lg px-6 py-4 hover:border-[#C9A227]/60 hover:bg-[#4A3B6B]/15 transition flex justify-between items-center"
        >
          <div>
            <h3 class="text-lg" style="font-family: 'Cinzel', serif;">{{ r.spread.nombre }}</h3>
            <p v-if="r.pregunta" class="text-sm text-[#EDE3D0]/50 italic mt-0.5" style="font-family: 'Cormorant Garamond', serif;">
              "{{ r.pregunta }}"
            </p>
          </div>
          <span class="text-xs font-mono text-[#EDE3D0]/30 whitespace-nowrap ml-4">
            {{ formatDate(r.created_at) }}
          </span>
        </Link>
      </div>
    </div>
  </div>
</template>