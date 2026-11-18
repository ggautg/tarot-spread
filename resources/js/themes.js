/**
 * Catálogo de corrientes visuales de El Oráculo.
 *
 * Cada tema lleva el nombre de un mago u ocultista histórico y define su
 * propia paleta, tipografías y textura. Los valores de color/fuente viven en
 * `resources/css/app.css` bajo el selector `[data-theme="<id>"]`; aquí solo
 * está la ficha editorial que muestra el selector.
 *
 * `scheme` indica al navegador si el tema es claro u oscuro (afecta a
 * controles nativos, barras de scroll y al meta `theme-color`).
 *
 * Para agregar un tema: añade la ficha aquí y su bloque `[data-theme="id"]`
 * en el CSS. Nada más: el resto de la app consume variables CSS.
 */

export const DEFAULT_THEME = 'fortune';

export const themes = [
    {
        id: 'merlin',
        name: 'Merlin',
        epithet: 'El arquitecto de Camelot',
        era: 'Britania · siglo VI',
        glyph: '☾',
        scheme: 'dark',
        description:
            'Bruma de Broceliande, musgo y plata. La magia antigua que escucha al bosque antes de hablar.',
    },
    {
        id: 'crowley',
        name: 'Aleister Crowley',
        epithet: 'La Bestia del Apocalipsis',
        era: 'Inglaterra · 1875–1947',
        glyph: '☉',
        scheme: 'dark',
        description:
            'Sangre, incienso y oro. «Haz tu voluntad»: la corriente más feroz y teatral del siglo XX.',
    },
    {
        id: 'dee',
        name: 'John Dee',
        epithet: 'El vidente de la reina',
        era: 'Inglaterra · 1527–1608',
        glyph: '⚚',
        scheme: 'dark',
        description:
            'Obsidiana, astros y lenguaje enochiano. La geometría secreta que los ángeles dictaron a un matemático.',
    },
    {
        id: 'levi',
        name: 'Eliphas Lévi',
        epithet: 'El Baphomet revelado',
        era: 'Francia · 1810–1875',
        glyph: '✠',
        scheme: 'light',
        description:
            'Tinta sepia y cera roja. El grimorio francés que devolvió el tarot al centro del ocultismo.',
    },
    {
        id: 'blavatsky',
        name: 'Helena Blavatsky',
        epithet: 'La voz de la doctrina secreta',
        era: 'Rusia · 1831–1891',
        glyph: '☸',
        scheme: 'dark',
        description:
            'Índigo profundo y oro tibetano. Siete rayos, siete planos y una biblioteca de sabiduría perdida.',
    },
    {
        id: 'agrippa',
        name: 'Cornelius Agrippa',
        epithet: 'El filósofo oculto',
        era: 'Alemania · 1486–1535',
        glyph: '✶',
        scheme: 'dark',
        description:
            'Vitela, latín y oro viejo. Tres libros para ordenar el cosmos en números, nombres y simpatías.',
    },
    {
        id: 'nostradamus',
        name: 'Nostradamus',
        epithet: 'El profeta de las centurias',
        era: 'Francia · 1503–1566',
        glyph: '✵',
        scheme: 'dark',
        description:
            'Azul de medianoche y ámbar. Cuartetas que miran al futuro desde el humo de la peste.',
    },
    {
        id: 'fortune',
        name: 'Dion Fortune',
        epithet: 'La luz astral',
        era: 'Inglaterra · 1890–1946',
        glyph: '☽',
        scheme: 'dark',
        description:
            'Violeta lunar y oro pálido. El tarot como psicología sagrada y la magia como disciplina interior.',
    },
];

const themesById = Object.fromEntries(themes.map((theme) => [theme.id, theme]));

export function getTheme(id) {
    return themesById[id] ?? themesById[DEFAULT_THEME];
}
