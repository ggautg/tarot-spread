<script setup>
/**
 * Casilla de verificación temática.
 * El color lo aporta `accent-color`, así respeta la corriente visual activa.
 */
const model = defineModel({ type: Boolean, default: false });

defineProps({
    label: { type: String, default: '' },
    id: { type: String, default: null },
});
</script>

<template>
    <label class="flex cursor-pointer items-center gap-2.5">
        <input
            :id="id"
            v-model="model"
            type="checkbox"
            class="h-4 w-4 rounded border-2 accent-[var(--t-accent)]"
            style="border-color: color-mix(in srgb, var(--t-accent) 45%, transparent)"
        />
        <span class="theme-body text-sm text-[color-mix(in_srgb,var(--t-ink)_82%,transparent)]">
            {{ label }}
        </span>
    </label>
</template>
