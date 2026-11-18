<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReadingRequest;
use App\Http\Requests\UpdateReadingNotesRequest;
use App\Models\Reading;
use App\Models\Spread;
use App\Services\ReadingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Gestiona el ciclo visible de una tirada: elegirla, comprometerla, revelarla,
 * guardarla en el historial y anotarla.
 *
 * La lógica pesada vive en `ReadingService` y `TarotDrawService`; aquí solo se
 * resuelve HTTP (validación, permisos, respuestas Inertia y mensajes flash).
 */
class ReadingController extends Controller
{
    public function __construct(
        private readonly ReadingService $readings,
    ) {}

    /**
     * Muestra la pantalla previa a la tirada: descripción y pregunta opcional.
     */
    public function create(Spread $spread): Response
    {
        return Inertia::render('Readings/Create', [
            'spread' => $spread->load('positions'),
        ]);
    }

    /**
     * Paso 1 — COMMIT.
     *
     * El servidor genera su seed secreto y publica solo el hash. A partir de
     * aquí el resultado queda comprometido: nadie puede cambiarlo sin que la
     * verificación posterior lo detecte.
     */
    public function store(StoreReadingRequest $request, Spread $spread): RedirectResponse
    {
        $reading = $this->readings->commit(
            spread: $spread,
            user: $request->user(),
            sessionId: $request->session()->getId(),
            question: $request->validated('pregunta'),
        );

        return redirect()
            ->route('readings.show', $reading->uuid)
            ->with('success', 'El sobre quedó sellado. Ábrelo cuando estés listo.');
    }

    /**
     * Paso 2 — REVEAL.
     *
     * Combina `server_seed` + `client_seed`, reparte las cartas y marca
     * `revealed_at`. Es idempotente: repetir la petición no vuelve a tirar.
     */
    public function reveal(Reading $reading): RedirectResponse
    {
        $reading = $this->readings->reveal($reading);

        return redirect()
            ->route('readings.show', $reading->uuid)
            ->with('success', 'El sobre se abrió. Que las cartas te iluminen.');
    }

    /**
     * Muestra el tablero. Incluye el resultado de la verificación criptográfica
     * cuando la lectura ya fue revelada.
     */
    public function show(Request $request, Reading $reading): Response
    {
        $reading->load([
            'spread.positions',
            'cards.card',
            'cards.position',
        ]);

        $canEdit = $reading->isOwnedBy($request->user(), $request->session()->getId());

        // El identificador de sesión nunca sale del servidor. Las notas son
        // privadas: solo su dueño las recibe (y solo él ve el formulario).
        $reading->makeHidden('session_id');

        // Mientras el sobre está sellado, `server_seed` es el secreto del
        // compromiso: exponerlo antes de revelar permitiría anticipar el
        // resultado. Se publica únicamente después de abrir la lectura.
        if (! $reading->isRevealed()) {
            $reading->makeHidden('server_seed');
        }

        if (! $canEdit) {
            $reading->makeHidden('notas_personales');
        }

        return Inertia::render('Readings/Show', [
            'reading' => $reading,
            'canEdit' => $canEdit,
            'verified' => $reading->isRevealed() ? $this->readings->verify($reading) : null,
        ]);
    }

    /**
     * Historial del visitante: lecturas de su cuenta o, si es anónimo, de su
     * propia sesión. Nunca mezcla lecturas de otras personas.
     */
    public function history(Request $request): Response
    {
        $readings = Reading::query()
            ->with('spread')
            ->withCount('cards')
            ->visibleTo($request->user(), $request->session()->getId())
            ->latest()
            ->get();

        return Inertia::render('Readings/History', [
            'readings' => $readings,
        ]);
    }

    /**
     * Guarda las notas privadas. Solo el dueño de la lectura puede escribir.
     */
    public function updateNotes(UpdateReadingNotesRequest $request, Reading $reading): RedirectResponse
    {
        abort_unless(
            $reading->isOwnedBy($request->user(), $request->session()->getId()),
            403,
        );

        $reading->update([
            'notas_personales' => $request->validated('notas_personales'),
        ]);

        return back()->with('success', 'Notas guardadas.');
    }

    /**
     * Borra una lectura y, en cascada, sus cartas.
     */
    public function destroy(Request $request, Reading $reading): RedirectResponse
    {
        abort_unless(
            $reading->isOwnedBy($request->user(), $request->session()->getId()),
            403,
        );

        DB::transaction(fn () => $reading->delete());

        return back()->with('success', 'Lectura eliminada.');
    }
}
