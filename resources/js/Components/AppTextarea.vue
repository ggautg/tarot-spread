<script setup>
import { computed, onMounted, ref } from 'vue';

/**
 * Área de texto con etiqueta, ayuda y error integrados.
 * Comparte el lenguaje visual de `AppInput`.
 */
let autoId = 0;

const props = defineProps({
    id: { type: String, default: null },
    label: { type: String, default: '' },
    error: { type: String, default: '' },
    hint: { type: String, default: '' },
    rows: { type: [String, Number], default: 4 },
    placeholder: { type: String, default: '' },
    required: { type: Boolean, default: false },
    autofocus: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    maxlength: { type: [String, Number], default: undefined },
    resize: { type: Boolean, default: false },
});

const model = defineModel({ type: String, default: '' });

const textarea = ref(null);
const fieldId = props.id ?? `app-textarea-${++autoId}`;
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
        textarea.value?.focus();
    }
});

defineExpose({ focus: () => textarea.value?.focus() });
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

        <textarea
            :id="fieldId"
            ref="textarea"
            v-model="model"
            :rows="rows"
            :placeholder="placeholder"
            :required="required"
            :disabled="disabled"
            :maxlength="maxlength"
            :aria-invalid="error ? 'true' : undefined"
            :aria-describedby="describedBy"
            :class="['theme-input', resize ? 'resize-y' : 'resize-none']"
        />

        <p v-if="hint && !error" :id="hintId" class="theme-faint mt-1.5 text-xs">
            {{ hint }}
        </p>

        <p v-if="error" :id="errorId" class="theme-danger-text mt-1.5 text-sm" role="alert">
            {{ error }}
        </p>
    </div>
</template>
