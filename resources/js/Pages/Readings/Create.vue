<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';

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

  <div class="min-h-screen relative bg-[#150F26] text-[#EDE3D0] flex items-center justify-center px-6">
    <Link
      :href="route('home')"
      class="absolute top-6 left-6 text-sm font-mono uppercase tracking-widest text-[#C9A227] border border-[#C9A227]/40 rounded px-4 py-2 hover:bg-[#C9A227]/10 transition"
    >
      ← Inicio
    </Link>

    <div class="max-w-md w-full">
      <p class="font-mono text-xs tracking-[0.3em] text-[#C9A227] uppercase mb-3">
        {{ spread.cantidad_cartas }} cartas
      </p>
      <h1 class="text-4xl mb-4" style="font-family: 'Cinzel', serif;">
        {{ spread.nombre }}
      </h1>
      <p class="text-[#EDE3D0]/70 mb-8 leading-relaxed" style="font-family: 'Cormorant Garamond', serif; font-size: 1.15rem;">
        {{ spread.descripcion }}
      </p>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-xs uppercase tracking-widest text-[#C9A227]/80 mb-2">
            Tu pregunta (opcional)
          </label>
          <textarea
            v-model="form.pregunta"
            rows="3"
            placeholder="¿Qué querés consultar?"
            class="w-full bg-[#4A3B6B]/30 border border-[#C9A227]/30 rounded px-4 py-3 text-[#EDE3D0] placeholder-[#EDE3D0]/30 focus:outline-none focus:border-[#C9A227] transition"
          />
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          class="w-full bg-[#C9A227] text-[#150F26] font-semibold py-3 rounded hover:bg-[#dbb53a] transition disabled:opacity-50"
        >
          Barajar y tirar
        </button>
      </form>
    </div>
  </div>
</template>