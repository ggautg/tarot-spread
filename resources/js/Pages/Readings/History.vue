<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
  readings: Array,
});

const readingToDelete = ref(null);

function formatDate(dateStr) {
  return new Date(dateStr).toLocaleDateString('es-PY', {
    day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit',
  });
}

function askDelete(reading) {
  readingToDelete.value = reading;
}

function cancelDelete() {
  readingToDelete.value = null;
}

function confirmDelete() {
  router.delete(route('readings.destroy', readingToDelete.value.uuid), {
    preserveScroll: true,
    onFinish: () => (readingToDelete.value = null),
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
          class="block border border-[#C9A227]/20 rounded-lg px-6 py-4 hover:border-[#C9A227]/60 hover:bg-[#4A3B6B]/15 transition flex justify-between items-center group"
        >
          <div>
            <h3 class="text-lg" style="font-family: 'Cinzel', serif;">{{ r.spread.nombre }}</h3>
            <p v-if="r.pregunta" class="text-sm text-[#EDE3D0]/50 italic mt-0.5" style="font-family: 'Cormorant Garamond', serif;">
              "{{ r.pregunta }}"
            </p>
            <p v-if="r.notas_personales" class="text-sm text-[#C9A227]/70 mt-1.5 line-clamp-2" style="font-family: 'Cormorant Garamond', serif;">
              ✎ {{ r.notas_personales }}
            </p>
          </div>
          <div class="flex items-center gap-3 ml-4">
            <span class="text-xs font-mono text-[#EDE3D0]/30 whitespace-nowrap">
              {{ formatDate(r.created_at) }}
            </span>
            <button
              @click.stop.prevent="askDelete(r)"
              class="text-[#7A2638] border border-[#7A2638]/40 hover:bg-[#7A2638]/15 hover:border-[#7A2638] rounded px-3 py-1.5 text-xs font-mono uppercase tracking-widest transition"
            >
              Borrar
            </button>
          </div>
        </Link>
      </div>
    </div>

    <!-- Modal de confirmación -->
    <div
      v-if="readingToDelete"
      class="fixed inset-0 bg-black/70 flex items-center justify-center px-6 z-50"
      @click.self="cancelDelete"
    >
      <div class="bg-[#150F26] border border-[#C9A227]/40 rounded-lg p-8 max-w-sm w-full text-center">
        <p class="text-[#C9A227] text-2xl mb-3">✦</p>
        <h3 class="text-xl mb-2" style="font-family: 'Cinzel', serif;">¿Borrar esta lectura?</h3>
        <p class="text-[#EDE3D0]/60 mb-6" style="font-family: 'Cormorant Garamond', serif; font-size: 1.1rem;">
          La tirada de <strong class="text-[#EDE3D0]">{{ readingToDelete.spread.nombre }}</strong>
          se va a borrar para siempre. No se puede deshacer.
        </p>
        <div class="flex gap-3 justify-center">
          <button
            @click="cancelDelete"
            class="border border-[#C9A227]/40 text-[#C9A227] rounded px-5 py-2 text-sm font-mono uppercase tracking-widest hover:bg-[#C9A227]/10 transition"
          >
            Cancelar
          </button>
          <button
            @click="confirmDelete"
            class="bg-[#7A2638] text-[#EDE3D0] rounded px-5 py-2 text-sm font-mono uppercase tracking-widest hover:bg-[#8f2d43] transition"
          >
            Borrar definitivamente
          </button>
        </div>
      </div>
    </div>
  </div>
</template>