<script setup>
import ThemePicker from '@/Components/ThemePicker.vue';
</script>

<template>
    <div class="theme-page relative min-h-screen overflow-x-hidden">
        <slot />
        <ThemePicker />
    </div>
</template>
