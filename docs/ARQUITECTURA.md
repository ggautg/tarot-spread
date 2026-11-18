# Arquitectura

Documento de referencia para quien mantenga El Oráculo. Explica las capas, el
modelo de datos y las decisiones que sostienen el comportamiento actual.

---

## 1. Vista general

```
Navegador
  │  Vue 3 + Inertia (sin API REST intermedia)
  ▼
Laravel
  ├─ Rutas           routes/web.php · routes/auth.php
  ├─ Controladores   HTTP, permisos y props de Inertia
  ├─ FormRequests    Validación y mensajes en español
  ├─ Servicios       ReadingService · TarotDrawService
  └─ Modelos         Eloquent
  ▼
MySQL 8  (tarot_spread)
```

Inertia hace de puente: el servidor devuelve componentes y props, no JSON
genérico. No hay una SPA separada ni una API pública que mantener.

---

## 2. Capas del backend

### Rutas

`routes/web.php` agrupa:

- flujo público de lectura (`/tirada`, `/lectura`, `/mis-lecturas`);
- rutas autenticadas de tema y perfil (`/tema`, `/perfil`);
- `routes/auth.php` con las rutas estándar de Breeze en inglés.

Los modelos usan claves públicas legibles: `Spread` por `slug` y `Reading` por
`uuid`. El `id` numérico no aparece en las URLs.

### Controladores

| Controlador | Responsabilidad |
| --- | --- |
| `ReadingController` | Crear, revelar, mostrar, historiar, anotar y borrar lecturas |
| `ThemeController` | Persistir la corriente visual en la cuenta |
| `ProfileController` | Datos personales y borrado de cuenta |
| `Auth\*` | Acceso, registro, verificación y contraseñas |

Los controladores no calculan repartos ni hashes: delegan en servicios. Solo
resuelven HTTP, autorización, ocultamiento de campos y mensajes flash.

### FormRequests

- `StoreReadingRequest`: pregunta opcional, máximo 255 caracteres.
- `UpdateReadingNotesRequest`: notas opcionales, máximo 2000 caracteres.
- `UpdateThemeRequest`: solo ids del catálogo de temas.
- `ProfileUpdateRequest` y `LoginRequest`: validación estándar.

La autorización de las notas y el borrado se hace en el controlador con
`Reading::isOwnedBy()`, porque depende de la sesión y de la cuenta a la vez.

### Servicios

**`ReadingService`** orquesta el ciclo de vida:

- `commit()` genera `server_seed`, publica `server_seed_hash` y guarda la
  semilla pública. No reparte cartas.
- `reveal()` abre una transacción con `lockForUpdate`, calcula la semilla final,
  pide el reparto y persiste `ReadingCard`. Es idempotente.
- `verify()` delega en el motor.

**`TarotDrawService`** es determinista:

- `draw($seed, $n)` siembra Mersenne Twister con los primeros 32 bits del
  SHA-256, baraja con Fisher–Yates y elige orientación con `mt_rand`.
- `verify()` comprueba el hash del secreto y vuelve a calcular el reparto para
  compararlo con lo guardado.
- La semilla global se restaura en `finally` para no contaminar el resto de la
  petición.

> **No cambiar el algoritmo de reparto** sin una migración de compatibilidad:
> las lecturas históricas dejarían de verificar.

---

## 3. Modelo de datos

```
users ──< readings >── spreads ──< spread_positions
                 │                        │
                 └──< reading_cards >─────┘
                            │
                          cards
```

| Tabla | Campos relevantes | Notas |
| --- | --- | --- |
| `users` | `name`, `email`, `password`, `theme` | `theme` por defecto `fortune` |
| `spreads` | `nombre`, `slug`, `descripcion`, `cantidad_cartas` | Catálogo de tiradas |
| `spread_positions` | `spread_id`, `orden`, `coord_x`, `coord_y`, `rotacion` | Posición visual en el tablero |
| `cards` | `name`, `name_short`, `type`, `suit`, significados | 78 cartas |
| `readings` | `uuid`, `spread_id`, `user_id`, `session_id`, semillas, `revealed_at`, `pregunta`, `notas_personales` | `user_id` nulo = anónima |
| `reading_cards` | `reading_id`, `spread_position_id`, `card_id`, `invertida` | Resultado congelado |

Reglas:

- `reading_cards` guarda **referencias** a cartas, no copias de texto. Corregir
  un significado no rompe lecturas antiguas.
- Borrar una lectura elimina sus `reading_cards` en cascada.
- Borrar una cuenta elimina explícitamente sus lecturas en
  `ProfileController@destroy`; la clave foránea sola las dejaría anónimas.
- `readings.uuid` se genera en `booted()` si no viene informado.

---

## 4. Privacidad de las props

`ReadingController@show` construye la respuesta así:

1. Carga `spread.positions`, `cards.card` y `cards.position`.
2. Calcula `canEdit` con `isOwnedBy(user, sessionId)`.
3. **Siempre** oculta `session_id`.
4. Si la lectura sigue sellada, oculta `server_seed`.
5. Si el visitante no es el dueño, oculta `notas_personales`.
6. Añade `verified` solo cuando ya se reveló.

El historial usa el scope `visibleTo()`:

- usuario autenticado → solo sus lecturas;
- invitado → solo lecturas anónimas de **su** sesión.

Esto evita fugas entre sesiones anónimas y entre cuentas.

---

## 5. Frontend

### Punto de entrada

- `resources/js/app.js`: crea la app Inertia, resuelve páginas con
  `import.meta.glob`, aplica el tema inicial y configura la barra de progreso.
- `resources/views/app.blade.php`: aplica el tema guardado antes del primer
  pintado para evitar el destello, carga fuentes y monta Inertia.

### Layouts y páginas

- `OracleLayout.vue`: cabecera, flash, contenido y pie. Único layout de la app.
- `AuthCard.vue`: marco centrado para acceso, registro y contraseñas.
- Páginas en `resources/js/Pages`:
  - `Home`, `Readings/Create`, `Readings/Show`, `Readings/History`;
  - `Auth/*` (6 pantallas);
  - `Profile/Edit` y sus tres parciales.

### Componentes

| Componente | Uso |
| --- | --- |
| `SiteHeader` | Navegación, cuenta y selector de tema |
| `ThemePicker` / `ThemePreviewCard` | Diálogo de corrientes visuales |
| `SpreadCard` | Ficha de tirada en la portada |
| `PositionList` | Lista ordenada de posiciones |
| `ReadingBoard` / `TarotCard` | Tablero y volteo en cascada |
| `ReadingCardDetail` | Significado según orientación |
| `ReadingHistoryItem` | Fila del historial |
| `ReadingNotesForm` | Notas privadas |
| `PageHeader`, `AppPanel`, `EmptyState` | Estructura visual |
| `AppButton`, `AppInput`, `AppTextarea`, `AppCheckbox` | Controles accesibles |
| `ConfirmDialog` | Confirmación de acciones destructivas |
| `FlashMessage` | Éxito/error tras una acción |
| `OracleMark` | Marca gráfica |

### Tema

- `resources/js/themes.js`: catálogo editorial y `scheme`.
- `resources/js/composables/useTheme.js`: estado compartido, persistencia local,
  sincronización con la cuenta y `data-theme`.
- `resources/css/app.css`: tokens `--t-*`, bloques por tema, superficies,
  botones, formularios, overlays y animaciones.

Los componentes no escriben colores literales: consumen variables `--t-*`.
Cambiar un tema no requiere tocar Vue.

### Movimiento

- Las cartas se revelan en cascada con un retardo CSS (`index * 110 ms`).
- No hay animación por clic ni GSAP.
- `prefers-reduced-motion` desactiva transiciones y animaciones.
- El foco visible y los estados `:focus-visible` son parte del sistema base.

---

## 6. Flujo de una petición de lectura

```
GET /tirada/tres-cartas
  └─ ReadingController@create → Inertia('Readings/Create', spread)

POST /tirada/tres-cartas
  └─ StoreReadingRequest
  └─ ReadingService@commit
  └─ redirect /lectura/{uuid}  (flash: sobre sellado)

POST /lectura/{uuid}/revelar
  └─ ReadingService@reveal (transacción + lock)
       ├─ semilla final = sha256(server_seed + client_seed)
       ├─ TarotDrawService@draw
       ├─ crea reading_cards
       └─ marca revealed_at
  └─ redirect /lectura/{uuid}

GET /lectura/{uuid}
  └─ carga relaciones
  └─ oculta secretos/notas según permiso
  └─ Inertia('Readings/Show', reading, canEdit, verified)
```

---

## 7. Pruebas

`php artisan test` ejecuta 40 pruebas sobre SQLite en memoria:

- autenticación, verificación y contraseñas;
- perfil y borrado de cuenta;
- validación y persistencia de temas;
- lecturas: sellado, secreto oculto, revelado, idempotencia, manipulación,
  notas privadas, permisos, historial y adopción al iniciar sesión.

Las pruebas de sesión anónima envían explícitamente la cookie de sesión, porque
el cliente de pruebas no conserva cookies entre llamadas.

---

## 8. Decisiones y compromisos

| Decisión | Motivo |
| --- | --- |
| Mersenne Twister en vez de un CSPRNG | Reproducibilidad y compatibilidad con lecturas ya guardadas |
| Inertia en vez de API + SPA | Menos superficie, menos estados duplicados |
| Variables CSS por tema | Un solo sistema visual para toda la app |
| Catálogo local de cartas | Desarrollo sin red y resultados estables |
| Sesión de base de datos | Historial anónimo persistente entre peticiones |
| Español neutro con tuteo | Coherencia con el público objetivo |
| Sin GSAP | El volteo en CSS cubre la necesidad con menos peso |
