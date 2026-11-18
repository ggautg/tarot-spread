<script setup>
import { computed, ref } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import AppButton from '@/Components/AppButton.vue';
import AppInput from '@/Components/AppInput.vue';
import AppPanel from '@/Components/AppPanel.vue';

/**
 * Datos personales de la cuenta.
 *
 * Si el correo cambia, Laravel lo marca como no verificado y aquí se ofrece
 * reenviar el enlace de verificación.
 */
const props = defineProps({
    mustVerifyEmail: { type: Boolean, default: false },
    status: { type: String, default: '' },
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const form = useForm({
    name: user.value.name,
    email: user.value.email,
});

const saved = ref(false);

function save() {
    form.patch(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            saved.value = true;
            setTimeout(() => (saved.value = false), 2500);
        },
    });
}
</script>

<template>
    <AppPanel
        eyebrow="Datos"
        title="Información personal"
        subtitle="Tu nombre y el correo con el que entras al oráculo."
    >
        <template #actions>
            <span v-if="user.email_verified_at" class="theme-chip">✓ Verificado</span>
        </template>

        <form class="space-y-4" @submit.prevent="save">
            <AppInput
                id="name"
                v-model="form.name"
                label="Nombre"
                autocomplete="name"
                required
                :error="form.errors.name"
            />

            <AppInput
                id="email"
                v-model="form.email"
                label="Correo electrónico"
                type="email"
                autocomplete="username"
                required
                :error="form.errors.email"
            />

            <div
                v-if="mustVerifyEmail && user.email_verified_at === null"
                class="theme-alert text-sm"
            >
                <p class="leading-relaxed">
                    Tu correo aún no está verificado.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="theme-link"
                    >
                        Reenviar el enlace
                    </Link>
                </p>

                <p v-if="status === 'verification-link-sent'" class="theme-accent mt-2">
                    Enviamos un enlace nuevo. Revisa también la carpeta de spam.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <AppButton
                    type="submit"
                    size="sm"
                    :loading="form.processing"
                    loading-label="Guardando…"
                >
                    Guardar cambios
                </AppButton>

                <Transition
                    enter-active-class="transition duration-200"
                    enter-from-class="opacity-0"
                    leave-active-class="transition duration-150"
                    leave-to-class="opacity-0"
                >
                    <span v-if="saved" class="theme-label theme-accent text-[9px]" role="status">
                        ✓ Guardado
                    </span>
                </Transition>
            </div>
        </form>
    </AppPanel>
</template>
