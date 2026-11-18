/**
 * Formatea una fecha ISO en español, con una sola fuente de verdad.
 *
 * Se usa `es` (neutro) para que el formato sea correcto en cualquier país
 * hispanohablante. Si el navegador no soporta `Intl`, devuelve la fecha tal
 * cual en lugar de romper la vista.
 *
 * @param {string|Date|null|undefined} value
 * @returns {string}
 */
export function formatDate(value) {
    if (!value) {
        return '';
    }

    const date = value instanceof Date ? value : new Date(value);

    if (Number.isNaN(date.getTime())) {
        return '';
    }

    try {
        return new Intl.DateTimeFormat('es', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        }).format(date);
    } catch {
        return date.toISOString();
    }
}
