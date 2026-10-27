<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import OracleLayout from '@/Layouts/OracleLayout.vue';

const props = defineProps({
  spread: Object,
});

const form = useForm({
  pregunta: '',
});

const submit = () => {
  form.post(route('readings.store', props.spread.slug));
};
</script>

<template>
  <Head :title="spread.nombre" />

  <OracleLayout>
    <div class="flex min-h-screen items-center justify-center px-6 py-16">
      <Link
        :href="route('home')"
        class="theme-btn-ghost absolute left-6 top-6 rounded px-4 py-2 text-xs"
      >
        ← Inicio
      </Link>

      <div class="w-full max-w-md">
        <p class="theme-label theme-accent mb-3 text-[10px]">
          {{ spread.cantidad_cartas }} cartas
        </p>
        <h1 class="theme-display mb-4 text-4xl">
          {{ spread.nombre }}
        </h1>
        <p class="theme-body theme-muted mb-8 text-lg leading-relaxed">
          {{ spread.descripcion }}
        </p>

        <form class="space-y-4" @submit.prevent="submit">
          <div>
            <label class="theme-label theme-accent mb-2 block text-[9px]">
              Tu pregunta (opcional)
            </label>
            <textarea
              v-model="form.pregunta"
              rows="3"
              placeholder="¿Qué querés consultar?"
              class="theme-input w-full px-4 py-3"
            />
          </div>

          <button
            type="submit"
            :disabled="form.processing"
            class="theme-btn w-full rounded py-3 text-sm font-semibold"
          >
            Barajar y tirar
          </button>
        </form>

        <p class="theme-label theme-faint mt-6 text-[9px] leading-relaxed">
          La tirada queda comprometida con un hash antes de revelarse.
        </p>
      </div>
    </div>
  </OracleLayout>
</template>
