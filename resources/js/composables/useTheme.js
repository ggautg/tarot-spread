import { computed, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { route as ziggyRoute } from '../../../vendor/tightenco/ziggy';
import { DEFAULT_THEME, getTheme, themes } from '@/themes';

const STORAGE_KEY = 'oraculo.theme';

/**
 * Tema activo compartido por toda la aplicación.
 *
 * Es un `ref` de módulo: cualquier componente que llame a `useTheme()` ve el
 * mismo valor y reacciona al cambio sin necesidad de un store externo.
 */
const currentId = ref(DEFAULT_THEME);

/** Lee la preferencia guardada en el dispositivo (puede fallar en modo privado). */
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

/** Guarda la preferencia local; un fallo de almacenamiento no es crítico. */
function storeTheme(id) {
    if (typeof window === 'undefined') {
        return;
    }

    try {
        window.localStorage.setItem(STORAGE_KEY, id);
    } catch {
        // Modo privado o almacenamiento bloqueado: el tema dura la sesión.
    }
}

/**
 * Aplica el tema al documento y sincroniza el color de la barra del navegador.
 * El atributo `data-theme` es lo que activa las variables CSS del tema.
 */
function applyToDocument(id) {
    if (typeof document === 'undefined') {
        return;
    }

    const theme = getTheme(id);

    document.documentElement.dataset.theme = theme.id;
    document.documentElement.style.colorScheme = theme.scheme ?? 'dark';

    const meta = document.querySelector('meta[name="theme-color"]');
    if (meta) {
        const bg = getComputedStyle(document.documentElement)
            .getPropertyValue('--t-bg')
            .trim();

        if (bg) {
            meta.setAttribute('content', bg);
        }
    }
}

/**
 * Se ejecuta una sola vez desde `app.js`, antes de montar Inertia.
 *
 * Prioridad: tema de la cuenta > preferencia local > tema por defecto.
 * Aplicarlo antes del montaje evita el "flash" de un tema equivocado.
 */
export function initTheme(pageProps) {
    const stored = readStoredTheme();
    const userTheme = pageProps?.auth?.user?.theme;
    const id = getTheme(userTheme || stored || DEFAULT_THEME).id;

    currentId.value = id;
    storeTheme(id);
    applyToDocument(id);
}

/**
 * Sincroniza el tema cuando cambia la sesión (por ejemplo, al iniciar sesión).
 * No envía nada al servidor: la preferencia ya viene en las props de Inertia.
 */
export function syncUserTheme(userTheme) {
    if (!userTheme) {
        return;
    }

    const id = getTheme(userTheme).id;

    if (id === currentId.value) {
        return;
    }

    currentId.value = id;
    storeTheme(id);
    applyToDocument(id);
}

/**
 * API pública del tema para los componentes.
 */
export function useTheme() {
    const page = usePage();

    const current = computed(() => getTheme(currentId.value));

    /**
     * Cambia el tema al instante y, si hay sesión, lo guarda en la cuenta.
     * Los invitados conservan la preferencia solo en este dispositivo.
     */
    function select(id) {
        const theme = getTheme(id);

        if (theme.id === currentId.value) {
            return;
        }

        currentId.value = theme.id;
        applyToDocument(theme.id);
        storeTheme(theme.id);

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
