<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import OracleLayout from '@/Layouts/OracleLayout.vue';

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

  <OracleLayout>
    <div class="min-h-screen px-6 py-16">
      <Link
        :href="route('home')"
        class="theme-btn-ghost absolute left-6 top-6 rounded px-4 py-2 text-xs"
      >
        ← Inicio
      </Link>

      <div class="mx-auto max-w-2xl pt-10">
        <p class="theme-label theme-accent mb-2 text-[10px]">Historial</p>
        <h1 class="theme-display mb-10 text-4xl">Mis lecturas</h1>

        <div v-if="readings.length === 0" class="theme-body theme-muted text-lg italic">
          Todavía no hiciste ninguna tirada en esta sesión.
        </div>

        <div v-else class="space-y-3">
          <Link
            v-for="r in readings"
            :key="r.id"
            :href="route('readings.show', r.uuid)"
            class="reading-row theme-panel group flex items-center justify-between px-6 py-4"
          >
            <div>
              <h3 class="theme-display text-lg">{{ r.spread.nombre }}</h3>
              <p v-if="r.pregunta" class="theme-body theme-faint mt-0.5 text-sm italic">
                "{{ r.pregunta }}"
              </p>
              <p v-if="r.notas_personales" class="theme-accent-2 mt-1.5 line-clamp-2 text-sm" style="font-family: var(--t-font-body);">
                ✎ {{ r.notas_personales }}
              </p>
            </div>
            <div class="ml-4 flex items-center gap-3">
              <span class="theme-label theme-faint whitespace-nowrap text-[9px]">
                {{ formatDate(r.created_at) }}
              </span>
              <button
                class="theme-danger-btn rounded px-3 py-1.5 text-[9px] uppercase tracking-widest"
                @click.stop.prevent="askDelete(r)"
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
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 px-6"
        @click.self="cancelDelete"
      >
        <div class="theme-panel-solid w-full max-w-sm p-8 text-center">
          <p class="theme-accent mb-3 text-2xl">✦</p>
          <h3 class="theme-display mb-2 text-xl">¿Borrar esta lectura?</h3>
          <p class="theme-body theme-muted mb-6 text-base">
            La tirada de <strong class="theme-accent">{{ readingToDelete.spread.nombre }}</strong>
            se va a borrar para siempre. No se puede deshacer.
          </p>
          <div class="flex justify-center gap-3">
            <button
              class="theme-btn-ghost rounded px-5 py-2 text-[9px]"
              @click="cancelDelete"
            >
              Cancelar
            </button>
            <button
              class="rounded px-5 py-2 text-[9px] font-semibold uppercase tracking-widest"
              style="background-color: var(--t-danger); color: var(--t-bg);"
              @click="confirmDelete"
            >
              Borrar definitivamente
            </button>
          </div>
        </div>
      </div>
    </div>
  </OracleLayout>
</template>

<style scoped>
.reading-row {
  transition:
    border-color 0.25s ease,
    background-color 0.25s ease;
}

.reading-row:hover {
  border-color: var(--t-accent);
  background-color: color-mix(in srgb, var(--t-surface) 72%, transparent);
}
</style>
