# Cambios de la reescritura

Resumen de la revisión completa: interfaz, componentes, español, temas, datos y
pruebas. Sirve como registro de qué cambió y por qué.

---

## 1. Idioma y contenido

- `APP_LOCALE` y `APP_FALLBACK_LOCALE` en `es`; `APP_TIMEZONE` en
  `America/Asuncion`; Faker en `es_ES`.
- Traducciones propias en `lang/es/`:
  - `auth.php`, `passwords.php`, `pagination.php`, `validation.php`.
- Toda la interfaz usa **español neutro con tuteo** («elige», «abre», «tus
  lecturas»), sin voseo ni regionalismos.
- Fechas formateadas con `Intl` en español (`resources/js/utils/formatDate.js`).
- Rutas visibles traducidas: `/perfil`, `/mis-lecturas`, `/tirada`, `/lectura`.

---

## 2. Base de datos y catálogo

- Configuración MySQL en `.env` y `.env.example` (base `tarot_spread`).
- `config/database.php`: la opción SSL usa `PDO::MYSQL_ATTR_SSL_CA`, porque
  `Pdo\Mysql` no existe en PHP 8.3.
- Catálogo versionado:
  - `database/data/cards.json`: fuente de las 78 cartas.
  - `database/data/cards.es.json`: nombre, significado y descripción en español.
- `CardSeeder` prioriza los archivos locales y deja la API como respaldo. Sigue
  siendo idempotente mediante `name_short`.
- `SpreadSeeder`: Tres Cartas (3 posiciones) y Cruz Celta (10 posiciones), con
  coordenadas y rotación para el tablero.
- Base migrada y sembrada: 2 tiradas, 78 cartas.

---

## 3. Backend

### Modelos documentados

`User`, `Card`, `Spread`, `SpreadPosition`, `Reading` y `ReadingCard` tienen
docblock de clase, de campos y de cada relación. Se explicó el esquema
commit–reveal y el alcance de `visibleTo()` / `isOwnedBy()`.

### Servicios nuevos

- `app/Services/ReadingService.php`: `commit`, `reveal` y `verify`.
- `app/Services/TarotDrawService.php`: reparto determinista y verificación.

Ventajas: el controlador queda en HTTP, el reparto se puede probar aislado y la
verificación es reutilizable.

### Controladores

- `ReadingController` reescrito y documentado:
  - oculta `session_id` siempre;
  - oculta `server_seed` mientras el sobre está sellado;
  - oculta `notas_personales` a quien no es dueño;
  - calcula `canEdit`;
  - devuelve `verified` al revelar;
  - valida propiedad antes de anotar o borrar.
- `ProfileController` y todos los controladores de `Auth` documentados en
  español; se eliminaron comentarios provisionales mezclados.
- `AuthenticatedSessionController` y `RegisteredUserController` adoptan las
  lecturas anónimas de la sesión al entrar o registrarse.
- `HandleInertiaRequests` comparte `auth`, `flash`, `appName` y Ziggy.

### FormRequests nuevos

`StoreReadingRequest`, `UpdateReadingNotesRequest` y `UpdateThemeRequest`, con
reglas y nombres de campo en español.

### Corrección

- `TarotDrawService@verify` usaba `orderBy('spread_positions.orden')` sobre una
  relación que ya ordenaba por `id`. Al hacer join, MySQL consideraba `id`
  ambiguo y devolvía **500** al abrir una lectura revelada. Se cambió a
  `reorder('spread_positions.orden')`, que descarta el orden previo.

---

## 4. Frontend

### Sistema visual

- `resources/css/app.css` reescrito:
  - tokens `--t-*` (fondo, superficie, tinta, acento, peligro, radios, fuentes);
  - bloques `[data-theme="…"]` para los ocho temas;
  - superficies, botones, inputs, chips, alertas, overlays y reglas;
  - entrada de página y respeto por `prefers-reduced-motion`.
- `tailwind.config.js` toma las familias `sans` y `display` de variables CSS.
- Se eliminó GSAP; el volteo y la cascada son CSS puro.

### Temas

- `themes.js` con ficha editorial y `scheme` por tema.
- `useTheme.js`: tema compartido, `localStorage`, persistencia en la cuenta,
  `color-scheme`, `theme-color` y sincronización al cambiar de sesión.
- `ThemePicker` sin GSAP, con diálogo accesible, foco atrapado y cierre con
  `Escape`.
- `ThemePreviewCard` muestra la paleta de cada corriente.
- El tema se aplica antes del primer pintado desde `app.blade.php`.

### Componentes

Se sustituyó todo el scaffold de Breeze por un sistema propio:

- **Base**: `AppButton`, `AppInput`, `AppTextarea`, `AppCheckbox`, `AppPanel`,
  `PageHeader`, `EmptyState`, `ConfirmDialog`, `FlashMessage`, `OracleMark`.
- **Navegación**: `SiteHeader`, `AuthCard`, `OracleLayout`.
- **Lecturas**: `SpreadCard`, `PositionList`, `TarotCard`, `ReadingBoard`,
  `ReadingCardDetail`, `ReadingHistoryItem`, `ReadingNotesForm`.

Se eliminaron `ApplicationLogo`, `Checkbox`, `DangerButton`, `Dropdown`,
`DropdownLink`, `InputError`, `InputLabel`, `Modal`, `NavLink`,
`PrimaryButton`, `ResponsiveNavLink`, `SecondaryButton`, `TextInput`,
`AuthenticatedLayout`, `GuestLayout`, `Welcome.vue` y `Dashboard.vue`, junto
con sus referencias.

### Páginas

- `Home`: portada con tiradas, explicación del proceso y corriente activa.
- `Readings/Create`: formulario simple con pregunta opcional y posiciones.
- `Readings/Show`: tablero, revelado, interpretación, verificación y notas.
- `Readings/History`: historial propio, apertura y borrado confirmado.
- `Auth/*`: seis pantallas coherentes con el resto del tema.
- `Profile/Edit`: datos, apariencia, contraseña y zona sensible.

### Movimiento y accesibilidad

- Revelado automático en cascada (`index * 110 ms`), sin clic por carta.
- El volteo se dispara en dos casos: al montar una lectura ya abierta y al abrir
  el sobre sobre la misma página. Inertia actualiza las props sin recrear la
  carta, así que `TarotCard` observa `revealed` además de reaccionar al montaje.
- Foco visible en todos los controles.
- Diálogos con `role="dialog"`, `aria-modal`, cierre con `Escape` y retorno del
  foco.
- Avisos flash con `role="status"` y cierre manual.
- Se respeta `prefers-reduced-motion`.

---

## 5. Arranque y assets

- `app.blade.php` limpio: sin fuentes duplicadas ni restos del scaffold.
- Script inline que aplica `data-theme` antes del primer pintado.
- Preconexión y carga única de las fuentes de los temas.
- Barra de progreso de Inertia con el color de acento y sin spinner.
- `npm run build` verificado (804 módulos, sin errores).

---

## 6. Pruebas

- `tests/Feature/ReadingTest.php` nuevo (10 pruebas):
  - sellado sin repartir cartas;
  - secreto oculto antes de revelar;
  - revelado y verificación correcta;
  - revelado idempotente;
  - detección de manipulación;
  - notas y secretos ocultos a terceros;
  - 403 al anotar o borrar una lectura ajena;
  - notas guardadas por el dueño;
  - historial limitado al propietario;
  - adopción de lecturas al iniciar sesión.
- `ProfileTest` actualizado a `/perfil`.
- `ExampleTest` con `RefreshDatabase`.
- Resultado: **40 pruebas, 188 aserciones, todas en verde**.

---

## 7. Documentación

- `README.md`: instalación, requisitos, rutas, temas, estructura y pruebas.
- `docs/ARQUITECTURA.md`: capas, modelo de datos, privacidad, flujo y decisiones.
- `docs/CAMBIOS.md`: este registro.

---

## 8. Pendientes sugeridos

- Revisar `npm audit` (24 avisos: 2 moderados, 20 altos, 2 críticos) y actualizar
  dependencias cuando sea compatible.
- Añadir pruebas de navegador (Dusk/Playwright) para el volteo y el diálogo de
  temas.
- Valorar caché de las cartas del catálogo si el tráfico crece.
- Añadir un comando de verificación masiva de lecturas históricas.
