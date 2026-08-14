<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  reading: Object,
  verified: {
    type: Boolean,
    default: null,
  },
});

const notesForm = useForm({
  notas_personales: props.reading.notas_personales || '',
});

const notesSaved = ref(false);

function saveNotes() {
  notesForm.patch(route('readings.notes', props.reading.uuid), {
    preserveScroll: true,
    onSuccess: () => {
      notesSaved.value = true;
      setTimeout(() => (notesSaved.value = false), 2000);
    },
  });
}

const revealed = ref(new Set(props.reading.cards.map(rc => rc.id)));
const copied = ref(false);

function copyLink() {
  navigator.clipboard.writeText(window.location.href).then(() => {
    copied.value = true;
    setTimeout(() => (copied.value = false), 2000);
  });
}

function toggleReveal(cardId) {
  if (revealed.value.has(cardId)) return;
  revealed.value.add(cardId);
  revealed.value = new Set(revealed.value); // forzar reactividad
}

function isRevealed(cardId) {
  return revealed.value.has(cardId);
}
</script>

<template>
  <Head :title="reading.spread.nombre">
    <meta property="og:title" :content="`Tirada de ${reading.spread.nombre}`" />
    <meta
      property="og:description"
      :content="reading.pregunta ? `«${reading.pregunta}»` : `Una lectura de ${reading.spread.nombre} revelada en El Oráculo.`"
    />
    <meta property="og:type" content="website" />
    <meta property="og:image" :content="`${$page.props.appUrl}/og-tarot.png`" />
    <meta name="twitter:card" content="summary_large_image" />
  </Head>

  <div class="min-h-screen relative bg-[#150F26] text-[#EDE3D0] px-6 py-10">
    <Link
      :href="route('home')"
      class="absolute top-6 left-6 text-sm font-mono uppercase tracking-widest text-[#C9A227] border border-[#C9A227]/40 rounded px-4 py-2 hover:bg-[#C9A227]/10 transition"
    >
      ← Inicio
    </Link>

    <div class="max-w-3xl mx-auto text-center mb-4 pt-14">
      <p class="font-mono text-xs tracking-[0.3em] text-[#C9A227] uppercase">
        {{ reading.spread.nombre }}
      </p>
      <p v-if="reading.pregunta" class="mt-2 text-[#EDE3D0]/60 italic" style="font-family: 'Cormorant Garamond', serif;">
        "{{ reading.pregunta }}"
      </p>
      <p class="mt-3 text-[10px] font-mono text-[#EDE3D0]/25 break-all">
        hash del compromiso: {{ reading.server_seed_hash }}
      </p>

      <div v-if="reading.revealed_at" class="mt-2 flex items-center justify-center gap-2 text-[10px] font-mono">
        <span v-if="verified" class="text-[#C9A227]">✓ Tirada verificada — el sobre no fue tocado</span>
        <span v-else class="text-[#7A2638]">✕ No se pudo verificar esta tirada</span>
      </div>

      <button
        @click="copyLink"
        class="mt-4 inline-flex items-center gap-2 text-xs font-mono uppercase tracking-widest border border-[#C9A227]/40 rounded px-4 py-2 text-[#C9A227] hover:bg-[#C9A227]/10 transition"
      >
        {{ copied ? '✓ Copiado' : 'Copiar link' }}
      </button>
    </div>

    <!-- Pantalla de compromiso: todavía no se tiraron las cartas -->
    <div v-if="!reading.revealed_at" class="max-w-md mx-auto text-center mt-16">
      <p class="text-[#EDE3D0]/70 mb-8 leading-relaxed" style="font-family: 'Cormorant Garamond', serif; font-size: 1.15rem;">
        El sobre está cerrado. El servidor ya comprometió su seed secreto —
        el hash de arriba es la prueba. Al abrir el sobre, se combina con tu
        seed público para determinar la tirada, y el servidor revela su seed
        para que cualquiera pueda verificar que no cambió de opinión.
      </p>
      <Link
        :href="route('readings.reveal', reading.uuid)"
        method="post"
        as="button"
        class="bg-[#C9A227] text-[#150F26] font-semibold py-3 px-8 rounded hover:bg-[#dbb53a] transition"
      >
        Abrir el sobre
      </Link>
    </div>

    <!-- Layout relativo, posicionado con coord_x / coord_y de spread_positions -->
    <div class="relative max-w-3xl mx-auto aspect-[4/3]">
      <div
        v-for="rc in reading.cards"
        :key="rc.id"
        class="absolute -translate-x-1/2 -translate-y-1/2 cursor-pointer"
        :style="{
          left: rc.position.coord_x + '%',
          top: rc.position.coord_y + '%',
          transform: `translate(-50%, -50%) rotate(${rc.position.rotacion}deg)`,
          zIndex: rc.position.orden,
        }"
        @click="toggleReveal(rc.id)"
      >
        <div class="flip-card" :class="{ flipped: isRevealed(rc.id) }">
          <span
            class="absolute -top-2 -right-2 z-10 w-5 h-5 rounded-full bg-[#C9A227] text-[#150F26] text-[10px] font-mono font-bold flex items-center justify-center shadow-md"
          >
            {{ rc.position.orden }}
          </span>
          <div class="flip-inner">
            <!-- Dorso -->
            <div class="flip-face flip-back">
              <div class="w-full h-full rounded border-2 border-[#C9A227]/60 bg-[#4A3B6B] flex items-center justify-center">
                <span class="text-2xl text-[#C9A227]">✦</span>
              </div>
            </div>
            <!-- Frente -->
            <div class="flip-face flip-front">
              <div
                class="w-full h-full rounded border-2 p-2 flex flex-col justify-between text-center"
                :class="rc.invertida ? 'border-[#7A2638] bg-[#7A2638]/10' : 'border-[#C9A227] bg-[#EDE3D0]/5'"
                :style="{ transform: rc.invertida ? 'rotate(180deg)' : 'none' }"
              >
                <p class="text-[9px] leading-tight" style="font-family: 'Cinzel', serif;">
                  {{ rc.card.name }}
                </p>
              </div>
            </div>
          </div>
        </div>
        <p class="mt-1 text-[9px] uppercase tracking-wider text-[#EDE3D0]/40 text-center w-24 -ml-4">
          {{ rc.position.nombre_posicion }}
        </p>
      </div>
    </div>

    <!-- Detalle de cartas reveladas -->
    <div class="max-w-2xl mx-auto mt-16 space-y-6">
      <div
        v-for="rc in reading.cards.filter(c => isRevealed(c.id))"
        :key="'detail-' + rc.id"
        class="border-t border-[#C9A227]/20 pt-4"
      >
        <p class="text-xs uppercase tracking-widest text-[#C9A227] mb-1">
          {{ rc.position.nombre_posicion }}
        </p>
        <h3 class="text-xl mb-1" style="font-family: 'Cinzel', serif;">
          {{ rc.card.name }} <span v-if="rc.invertida" class="text-sm text-[#7A2638]">(invertida)</span>
        </h3>
        <p style="font-family: 'Cormorant Garamond', serif; font-size: 1.1rem;" class="text-[#EDE3D0]/80 leading-relaxed">
          {{ rc.invertida ? rc.card.meaning_rev : rc.card.meaning_up }}
        </p>
      </div>
    </div>

    <!-- Notas personales -->
    <div v-if="reading.revealed_at" class="max-w-2xl mx-auto mt-12 border-t border-[#C9A227]/20 pt-8">
      <p class="text-xs uppercase tracking-widest text-[#C9A227] mb-3">
        Tus notas
      </p>
      <textarea
        v-model="notesForm.notas_personales"
        rows="4"
        placeholder="¿Cómo te sentiste con esta lectura? ¿Se cumplió algo, te dolió, te hizo pensar?"
        class="w-full bg-[#4A3B6B]/20 border border-[#C9A227]/25 rounded px-4 py-3 text-[#EDE3D0] placeholder-[#EDE3D0]/30 focus:outline-none focus:border-[#C9A227] transition resize-none"
        style="font-family: 'Cormorant Garamond', serif; font-size: 1.05rem;"
      />
      <div class="flex items-center gap-3 mt-3">
        <button
          @click="saveNotes"
          :disabled="notesForm.processing"
          class="bg-[#C9A227] text-[#150F26] font-semibold text-sm py-2 px-5 rounded hover:bg-[#dbb53a] transition disabled:opacity-50"
        >
          Guardar notas
        </button>
        <span v-if="notesSaved" class="text-xs font-mono text-[#C9A227]">✓ Guardado</span>
      </div>
    </div>
  </div>
</template>

<style scoped>
.flip-card {
  position: relative;
  width: 64px;
  height: 96px;
  perspective: 800px;
}
.flip-inner {
  position: relative;
  width: 100%;
  height: 100%;
  transition: transform 0.6s;
  transform-style: preserve-3d;
}
.flip-card.flipped .flip-inner {
  transform: rotateY(180deg);
}
.flip-face {
  position: absolute;
  width: 100%;
  height: 100%;
  backface-visibility: hidden;
}
.flip-back {
  transform: rotateY(0deg);
}
.flip-front {
  transform: rotateY(180deg);
}
</style>