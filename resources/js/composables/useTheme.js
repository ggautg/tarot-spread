import { computed, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { route as ziggyRoute } from '../../../vendor/tightenco/ziggy';
import { DEFAULT_THEME, getTheme, themes } from '@/themes';

const STORAGE_KEY = 'oraculo.theme';
const currentId = ref(DEFAULT_THEME);

function applyToDocument(id) {
    if (typeof document === 'undefined') {
        return;
    }

    document.documentElement.dataset.theme = id;
    document.documentElement.style.colorScheme = getTheme(id).id === 'levi' ? 'light' : 'dark';
}

function readStoredTheme() {
    if (typeof window === 'undefined') {
        return null;
    }

    try {
        return window.localStorage.getItem(STORAGE_KEY);
    } catch {
        return null;
    }
}

function storeTheme(id) {
    if (typeof window === 'undefined') {
        return;
    }

    try {
        window.localStorage.setItem(STORAGE_KEY, id);
    } catch {
        // localStorage puede estar bloqueado (modo privado): no es crítico.
    }
}

/**
 * Se llama una sola vez desde app.js, antes de montar Inertia.
 * El tema del usuario logueado manda; si no hay, vale el guardado local.
 */
export function initTheme(pageProps) {
    const stored = readStoredTheme();
    const userTheme = pageProps?.auth?.user?.theme;
    const id = getTheme(userTheme || stored || DEFAULT_THEME).id;

    currentId.value = id;
    storeTheme(id);
    applyToDocument(id);
}

export function useTheme() {
    const page = usePage();

    const current = computed(() => getTheme(currentId.value));

    function select(id) {
        const theme = getTheme(id);

        if (theme.id === currentId.value) {
            return;
        }

        currentId.value = theme.id;
        applyToDocument(theme.id);
        storeTheme(theme.id);

        // Los invitados viven en localStorage; los usuarios logueados
        // además guardan la preferencia en su cuenta.
        if (page.props.auth?.user) {
            router.patch(
                ziggyRoute('theme.update', undefined, undefined, page.props.ziggy),
                { theme: theme.id },
                { preserveScroll: true, preserveState: true },
            );
        }
    }

    return { themes, current, select };
}
