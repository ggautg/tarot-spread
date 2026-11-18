<script setup>
import { computed, onMounted, ref } from 'vue';

/**
 * Campo de texto con etiqueta, ayuda y error integrados.
 *
 * Evita repetir el mismo bloque de label/input/error en cada formulario y
 * garantiza que los mensajes de validación sean accesibles (`aria-describedby`,
 * `aria-invalid`).
 */
let autoId = 0;

const props = defineProps({
    id: { type: String, default: null },
    label: { type: String, default: '' },
    type: { type: String, default: 'text' },
    error: { type: String, default: '' },
    hint: { type: String, default: '' },
    autocomplete: { type: String, default: 'off' },
    placeholder: { type: String, default: '' },
    required: { type: Boolean, default: false },
    autofocus: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    maxlength: { type: [String, Number], default: undefined },
});

const model = defineModel({ type: String, default: '' });

const input = ref(null);
const fieldId = props.id ?? `app-input-${++autoId}`;
const hintId = computed(() => `${fieldId}-hint`);
const errorId = computed(() => `${fieldId}-error`);

const describedBy = computed(() => {
    const ids = [];
    if (props.hint) ids.push(hintId.value);
    if (props.error) ids.push(errorId.value);
    return ids.length ? ids.join(' ') : undefined;
});

onMounted(() => {
    if (props.autofocus) {
        input.value?.focus();
    }
});

/** Permite que el formulario padre enfoque el campo tras un error. */
defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <div>
        <label
            v-if="label"
            :for="fieldId"
            class="theme-label theme-faint mb-1.5 block text-[9px]"
        >
            {{ label }}
        </label>

        <input
            :id="fieldId"
            ref="input"
            v-model="model"
            :type="type"
            :placeholder="placeholder"
            :autocomplete="autocomplete"
            :required="required"
            :disabled="disabled"
            :maxlength="maxlength"
            :aria-invalid="error ? 'true' : undefined"
            :aria-describedby="describedBy"
            class="theme-input"
        />

        <p v-if="hint && !error" :id="hintId" class="theme-faint mt-1.5 text-xs">
            {{ hint }}
        </p>

        <p
            v-if="error"
            :id="errorId"
            class="theme-danger-text mt-1.5 text-sm"
            role="alert"
        >
            {{ error }}
        </p>
    </div>
</template>
