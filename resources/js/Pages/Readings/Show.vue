<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import OracleLayout from '@/Layouts/OracleLayout.vue';

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

// Al revelar, Inertia actualiza las props sin volver a montar el componente.
// Sincronizamos el set para que las cartas ya reveladas se den vuelta solas.
watch(
  () => props.reading.cards,
  (cards) => {
    revealed.value = new Set(cards.map(rc => rc.id));
  },
);

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

  <OracleLayout>
    <div class="min-h-screen px-6 py-10 pb-28">
      <Link
        :href="route('home')"
        class="theme-btn-ghost absolute left-6 top-6 rounded px-4 py-2 text-xs"
      >
        ← Inicio
      </Link>

      <div class="mx-auto mb-4 max-w-3xl pt-14 text-center">
        <p class="theme-label theme-accent text-[10px]">
          {{ reading.spread.nombre }}
        </p>
        <p v-if="reading.pregunta" class="theme-body theme-muted mt-2 text-lg italic">
          "{{ reading.pregunta }}"
        </p>
        <p class="theme-faint mt-3 break-all font-mono text-[10px]">
          hash del compromiso: {{ reading.server_seed_hash }}
        </p>

        <div v-if="reading.revealed_at" class="mt-2 flex items-center justify-center gap-2 font-mono text-[10px]">
          <span v-if="verified" class="theme-accent">✓ Tirada verificada — el sobre no fue tocado</span>
          <span v-else class="theme-danger-text">✕ No se pudo verificar esta tirada</span>
        </div>

        <button
          class="theme-btn-ghost mt-4 inline-flex items-center gap-2 rounded px-4 py-2 text-[9px]"
          @click="copyLink"
        >
          {{ copied ? '✓ Copiado' : 'Copiar link' }}
        </button>
      </div>

      <!-- Pantalla de compromiso: todavía no se tiraron las cartas -->
      <div v-if="!reading.revealed_at" class="mx-auto mt-16 max-w-md text-center">
        <p class="theme-body theme-muted mb-8 text-lg leading-relaxed">
          El sobre está cerrado. El servidor ya comprometió su seed secreto —
          el hash de arriba es la prueba. Al abrir el sobre, se combina con tu
          seed público para determinar la tirada, y el servidor revela su seed
          para que cualquiera pueda verificar que no cambió de opinión.
        </p>
        <Link
          :href="route('readings.reveal', reading.uuid)"
          method="post"
          as="button"
          class="theme-btn rounded px-8 py-3 text-sm font-semibold"
        >
          Abrir el sobre
        </Link>
      </div>

      <!-- Layout relativo, posicionado con coord_x / coord_y de spread_positions -->
      <div class="relative mx-auto aspect-[4/3] max-w-3xl">
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
              class="theme-accent-badge absolute -right-2 -top-2 z-10 flex h-5 w-5 items-center justify-center rounded-full font-mono text-[10px] font-bold"
            >
              {{ rc.position.orden }}
            </span>
            <div class="flip-inner">
              <!-- Dorso -->
              <div class="flip-face flip-back">
                <div class="card-back flex h-full w-full items-center justify-center rounded border-2">
                  <span class="theme-accent text-2xl">✦</span>
                </div>
              </div>
              <!-- Frente -->
              <div class="flip-face flip-front">
                <div
                  class="flex h-full w-full flex-col justify-between rounded border-2 p-2 text-center"
                  :class="rc.invertida ? 'card-face-down' : 'card-face-up'"
                  :style="{ transform: rc.invertida ? 'rotate(180deg)' : 'none' }"
                >
                  <p class="theme-body text-[9px] leading-tight">
                    {{ rc.card.name }}
                  </p>
                </div>
              </div>
            </div>
          </div>
          <p class="theme-label theme-faint -ml-4 mt-1 w-24 text-center text-[8px]">
            {{ rc.position.nombre_posicion }}
          </p>
        </div>
      </div>

      <!-- Detalle de cartas reveladas -->
      <div class="mx-auto mt-16 max-w-2xl space-y-6">
        <div
          v-for="rc in reading.cards.filter(c => isRevealed(c.id))"
          :key="'detail-' + rc.id"
          class="theme-rule border-t pt-4"
        >
          <p class="theme-label theme-accent mb-1 text-[9px]">
            {{ rc.position.nombre_posicion }}
          </p>
          <h3 class="theme-display mb-1 text-xl">
            {{ rc.card.name }} <span v-if="rc.invertida" class="theme-danger-text text-sm">(invertida)</span>
          </h3>
          <p class="theme-body text-lg leading-relaxed opacity-85">
            {{ rc.invertida ? rc.card.meaning_rev : rc.card.meaning_up }}
          </p>
        </div>
      </div>

      <!-- Notas personales -->
      <div v-if="reading.revealed_at" class="theme-rule mx-auto mt-12 max-w-2xl border-t pt-8">
        <p class="theme-label theme-accent mb-3 text-[9px]">
          Tus notas
        </p>
        <textarea
          v-model="notesForm.notas_personales"
          rows="4"
          placeholder="¿Cómo te sentiste con esta lectura? ¿Se cumplió algo, te dolió, te hizo pensar?"
          class="theme-input w-full resize-none px-4 py-3"
        />
        <div class="mt-3 flex items-center gap-3">
          <button
            class="theme-btn rounded px-5 py-2 text-xs font-semibold"
            :disabled="notesForm.processing"
            @click="saveNotes"
          >
            Guardar notas
          </button>
          <span v-if="notesSaved" class="theme-label theme-accent text-[9px]">✓ Guardado</span>
        </div>
      </div>
    </div>
  </OracleLayout>
</template>

<style scoped>
.theme-accent-badge {
  background-color: var(--t-accent);
  color: var(--t-bg);
  box-shadow: 0 1px 4px rgb(0 0 0 / 0.35);
}

.card-back {
  background-color: var(--t-card-back);
  border-color: color-mix(in srgb, var(--t-accent) 60%, transparent);
}

.card-face-up {
  border-color: var(--t-accent);
  background-color: color-mix(in srgb, var(--t-ink) 7%, transparent);
}

.card-face-down {
  border-color: var(--t-danger);
  background-color: color-mix(in srgb, var(--t-danger) 14%, transparent);
}

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
