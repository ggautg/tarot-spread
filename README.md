# El Oráculo

Aplicación de tarot con tiradas selladas y verificables. Eliges una tirada, el
servidor publica el hash de un secreto y, al abrir el sobre, combina ese secreto
con una semilla pública para repartir las cartas. Cualquiera puede comprobar
después que el resultado no se alteró.

Construida con **Laravel 13**, **Inertia 2**, **Vue 3**, **Tailwind CSS** y
**MySQL 8**. Toda la interfaz está en español neutro.

---

## Qué incluye

- **Dos tiradas**: Tres Cartas (3 posiciones) y Cruz Celta (10 posiciones).
- **Compromiso criptográfico** (commit–reveal) con SHA-256 y verificación pública.
- **Lecturas anónimas**: sin cuenta también hay historial, atado a la sesión del navegador.
- **Cuenta opcional**: al entrar o registrarte, tus lecturas anónimas pasan a tu cuenta.
- **Ocho corrientes visuales** (temas) que cambian paleta y tipografía en toda la app.
- **Notas privadas** por lectura, visibles solo para su dueño.
- **Historial** con borrado confirmado.
- **Diseño accesible**: foco visible, contraste por tema, movimiento reducido.

---

## Requisitos

| Herramienta | Versión |
| --- | --- |
| PHP | 8.3 o superior |
| Composer | 2.x |
| Node.js | 20 o superior |
| npm | 10 o superior |
| MySQL | 8.0 o superior |

---

## Instalación

### 1. Dependencias

```bash
composer install
npm install
```

### 2. Entorno

```bash
cp .env.example .env
php artisan key:generate
```

Edita `.env` con tus datos de MySQL:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tarot_spread
DB_USERNAME=root
DB_PASSWORD=tu_contraseña
```

Crea la base si no existe:

```sql
CREATE DATABASE tarot_spread CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. Base de datos y catálogo

```bash
php artisan migrate --seed
```

El seeder carga **78 cartas** desde `database/data/cards.json` y sus textos en
español desde `database/data/cards.es.json`. Si el archivo local no existe,
intenta la API pública de tarotapi.dev como respaldo. Volver a ejecutar el
seeder no duplica cartas.

### 4. Compilar el frontend

```bash
npm run build
```

Para desarrollo con recarga en caliente:

```bash
npm run dev
```

### 5. Levantar el servidor

```bash
php artisan serve
```

La app queda en <http://localhost:8000>.

También puedes usar el script que arranca servidor, cola, logs y Vite juntos:

```bash
composer run dev
```

---

## Flujo de una lectura

1. **Elegir** — la portada lista las tiradas; puedes escribir una pregunta opcional.
2. **Sellar** — `POST /tirada/{spread}` crea la lectura con:
   - `server_seed`: secreto aleatorio del servidor.
   - `server_seed_hash`: SHA-256 del secreto, público desde el inicio.
   - `client_seed`: semilla pública que aporta variedad al resultado.
   - Todavía **no se reparten cartas**.
3. **Abrir** — `POST /lectura/{uuid}/revelar` calcula
   `sha256(server_seed + client_seed)`, baraja el mazo con ese valor, guarda las
   cartas y marca `revealed_at`. La operación es idempotente.
4. **Verificar** — al mostrar la lectura revelada se recalcula el reparto y se
   compara con lo guardado. Si no coincide, la interfaz lo advierte.

El generador es `mt_srand` / `mt_rand` de PHP, determinista y compatible con las
lecturas ya guardadas. Ver `app/Services/TarotDrawService.php`.

---

## Rutas principales

| Método | URL | Nombre | Descripción |
| --- | --- | --- | --- |
| GET | `/` | `home` | Portada y tiradas disponibles |
| GET | `/tirada/{spread:slug}` | `readings.create` | Formulario previo |
| POST | `/tirada/{spread:slug}` | `readings.store` | Sella el sobre |
| GET | `/lectura/{reading:uuid}` | `readings.show` | Tablero y resultado |
| POST | `/lectura/{reading:uuid}/revelar` | `readings.reveal` | Abre el sobre |
| GET | `/mis-lecturas` | `readings.history` | Historial propio |
| DELETE | `/lectura/{reading:uuid}` | `readings.destroy` | Borra una lectura |
| PATCH | `/lectura/{reading:uuid}/notas` | `readings.notes` | Guarda notas privadas |
| PATCH | `/tema` | `theme.update` | Guarda el tema (con sesión) |
| GET | `/perfil` | `profile.edit` | Perfil de la cuenta |
| PATCH | `/perfil` | `profile.update` | Actualiza datos |
| DELETE | `/perfil` | `profile.destroy` | Borra la cuenta |

Las rutas de acceso, registro y recuperación de contraseña viven en
`routes/auth.php` y usan las URLs estándar de Laravel (`/login`, `/register`,
`/forgot-password`, etc.).

---

## Privacidad y seguridad

- `session_id` **nunca** se envía al navegador.
- `server_seed` permanece oculto hasta que la lectura se revela.
- Las notas personales solo se incluyen en las props del dueño.
- Modificar o borrar una lectura ajena devuelve **403**.
- El historial de invitado se limita a la sesión actual; el de una cuenta, a sus lecturas.
- Borrar la cuenta elimina en cascada sus lecturas, cartas y notas.
- El borrado de cuenta y el cambio de contraseña exigen la contraseña actual.

---

## Corrientes visuales (temas)

Cada tema es una ficha editorial en `resources/js/themes.js` y un bloque de
variables CSS `[data-theme="<id>"]` en `resources/css/app.css`.

| Tema | Esquema |
| --- | --- |
| `merlin` | oscuro |
| `crowley` | oscuro |
| `dee` | oscuro |
| `levi` | claro |
| `blavatsky` | oscuro |
| `agrippa` | oscuro |
| `nostradamus` | oscuro |
| `fortune` (por defecto) | oscuro |

Prioridad al cargar: **tema de la cuenta → preferencia local → `fortune`**. El
tema se aplica antes del primer pintado con un script en `app.blade.php` para
evitar el destello de un tema equivocado. Los invitados lo guardan en
`localStorage`; con sesión, se persiste en `users.theme`.

Para añadir un tema: agrega la ficha en `themes.js` y su bloque de variables en
`app.css`. No hay que tocar componentes.

---

## Estructura del proyecto

```
app/
  Http/Controllers/        Lectura, tema, perfil y autenticación
  Http/Requests/           Validación de lecturas, notas, tema y perfil
  Models/                  Card, Spread, SpreadPosition, Reading, ReadingCard, User
  Services/                TarotDrawService (reparto) y ReadingService (ciclo)
database/
  data/                    Catálogo local de cartas (ES/EN)
  migrations/              Esquema
  seeders/                 Cartas y tiradas
resources/
  css/app.css              Tokens, temas y utilidades visuales
  js/Components/           Componentes Vue reutilizables
  js/Layouts/              OracleLayout
  js/Pages/                Páginas Inertia
  js/composables/          useTheme
  js/utils/                formatDate
routes/
  web.php                  Rutas de la app
  auth.php                 Rutas de autenticación
tests/Feature/             Lecturas, perfil, temas y autenticación
```

---

## Pruebas

```bash
php artisan test
```

Los tests usan SQLite en memoria (`phpunit.xml`), así que no tocan la base de
desarrollo. Cubren autenticación, perfil, temas y el ciclo completo de lectura:
sellado, ocultamiento del secreto, revelado, verificación, notas privadas y
permisos.

Para revisar el estilo del código PHP:

```bash
./vendor/bin/pint --test
```

---

## Documentación adicional

- [`docs/ARQUITECTURA.md`](docs/ARQUITECTURA.md): capas, modelo de datos,
  decisiones y flujo de una petición.
- [`docs/CAMBIOS.md`](docs/CAMBIOS.md): resumen de la reescritura de interfaz,
  componentes y comportamiento.
