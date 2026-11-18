<script setup>
/**
 * Panel de contenido reutilizable.
 *
 * Envoltorio semántico con el fondo, borde y radio del tema activo. Sirve para
 * agrupar formularios, listas y bloques de lectura sin repetir clases.
 *
 * Props:
 *  - `as`       etiqueta HTML del contenedor (`section`, `article`, `div`…).
 *  - `eyebrow`  texto pequeño sobre el título.
 *  - `title`    título visible del panel.
 *  - `subtitle` aclaración bajo el título.
 *  - `padded`   activa/desactiva el relleno interno.
 */
defineProps({
    as: { type: String, default: 'section' },
    eyebrow: { type: String, default: '' },
    title: { type: String, default: '' },
    subtitle: { type: String, default: '' },
    padded: { type: Boolean, default: true },
});
</script>

<template>
    <component
        :is="as"
        class="theme-panel"
        :class="padded ? 'p-6 sm:p-8' : ''"
    >
        <header
            v-if="eyebrow || title || subtitle || $slots.header || $slots.actions"
            class="mb-5 flex flex-wrap items-start justify-between gap-4"
        >
            <div class="min-w-0">
                <slot name="header">
                    <p v-if="eyebrow" class="theme-label theme-accent text-[9px]">
                        {{ eyebrow }}
                    </p>
                    <h2 v-if="title" class="theme-display mt-1 text-2xl">
                        {{ title }}
                    </h2>
                    <p v-if="subtitle" class="theme-body theme-muted mt-1.5 text-base leading-relaxed">
                        {{ subtitle }}
                    </p>
                </slot>
            </div>

            <div v-if="$slots.actions" class="shrink-0">
                <slot name="actions" />
            </div>
        </header>

        <slot />
    </component>
</template>
