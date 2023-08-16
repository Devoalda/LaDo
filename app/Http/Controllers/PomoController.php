<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePomoRequest;
use App\Http\Requests\UpdatePomoRequest;
use App\Http\Resources\PomoResource;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use App\Models\{
    Pomo,
    Project,
    User,
    Todo
};
use Illuminate\Http\Request;

class PomoController extends Controller
{
    /**
     * Display a listing of Pomos of a given todo
     * @param Request $request - Request object (Should contain todo_id if API Request)
     * @return PomoResource|Application|Factory|View - Returns PomoResource if API, Index View if not
     */
    public function index(Request $request): PomoResource|Application|Factory|View
    {
        if ($request->expectsJson()) {
            $todo = Todo::find($request->todo_id);
            $pomos = $todo->pomo()->paginate(4);

            return new PomoResource($pomos);

        }
        return view('pomo.index');
    }

    /**
     * Show the form for creating a new Pomo
     * @param null $todo_id - ID of Todo to create Pomo for
     * @return Application|Factory|View - Returns view of create Pomo form
     * @throws AuthorizationException - Throws AuthorizationException if user is not authorized to create Pomo
     */
    public function create($todo_id = null): Application|Factory|View
    {
        $this->authorize('create', Pomo::class);

        return view('pomo.create', [
            'todo_id' => $todo_id,
            'pomo' => new Pomo(),
            'editing' => false,
        ]);
    }

    /**
     * Store a newly created Pomo in storage.
     * @param StorePomoRequest $request - Request object containing Validated data from create Pomo form
     * @return RedirectResponse|JsonResponse - Returns redirect to Pomo index if not API, JSON response if API
     * @throws AuthorizationException - Throws AuthorizationException if user is not authorized to create Pomo
     */
    public function store(StorePomoRequest $request): RedirectResponse|JsonResponse
    {
        $this->authorize('create', Pomo::class);

        // Convert due_start and end to unix timestamp and save
        $pomo = new Pomo();
        $pomo->todo_id = $request->safe()->todo_id;
        $pomo->pomo_start = strtotime($request->safe()->pomo_start);
        $pomo->pomo_end = strtotime($request->safe()->pomo_end);
        $pomo->notes = $request->safe()->notes;
        $pomo->save();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Pomo created successfully.',
                'data' => $pomo,
            ], 201);
        }

        return redirect()->route('pomo.index')
            ->with('success', 'Pomo created successfully.');
    }

    /**
     * Display the specified Pomo.
     * @param Request $request - Request object
     * @param Pomo $pomo - Pomo to be displayed
     * @return JsonResponse|Application|Factory|View - Returns JSON response of Pomo if API, Pomo view if not
     * @throws AuthorizationException - Throws AuthorizationException if user is not authorized to view the Pomo
     */
    public function show(Request $request, Pomo $pomo): JsonResponse|Application|Factory|View
    {
        $this->authorize('view', $pomo);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Pomo retrieved successfully.',
                'data' => $pomo,
            ], 200);
        }

        return view('pomo.show', compact('pomo'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param Pomo $pomo - Pomo to be edited
     * @return Application|Factory|View - Returns view of edit Pomo form
     * @throws AuthorizationException - Throws AuthorizationException if user is not authorized to edit the Pomo
     */
    public function edit(Pomo $pomo): Application|Factory|View
    {
        $this->authorize('view', $pomo);

        $editing = true;

        return view('pomo.create', compact('pomo', 'editing'));
    }

    /**
     * Update the specified Pomo in storage.
     * @param UpdatePomoRequest $request - Request object containing Validated data from edit Pomo form
     * @param Pomo $pomo - Pomo to be updated
     * @return RedirectResponse|JsonResponse - Returns redirect to Pomo index if not API, JSON response if API
     * @throws AuthorizationException - Throws AuthorizationException if user is not authorized to update the Pomo
     */
    public function update(UpdatePomoRequest $request, Pomo $pomo): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $pomo);

        // Convert due_start and end to unix timestamp and save
        $pomo->pomo_start = strtotime($request->pomo_start);
        $pomo->pomo_end = strtotime($request->pomo_end);
        $pomo->notes = $request->notes;
        $pomo->save();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Pomo updated successfully.',
                'data' => $pomo,
            ], 200);
        }

        return redirect()->route('pomo.index')
            ->with('success', 'Pomo updated successfully.');
    }

    /**
     * Remove the specified Pomo from storage.
     * @param Request $request - Request object
     * @param Pomo $pomo - Pomo to be deleted
     * @return RedirectResponse|JsonResponse - Returns redirect to Pomo index if not API, JSON response if API
     * @throws AuthorizationException - Throws AuthorizationException if user is not authorized to delete the Pomo
     */
    public function destroy(Request $request, Pomo $pomo): RedirectResponse|JsonResponse
    {
        // Validate that the user is authorized to delete the pomo
        $this->authorize('delete', [Pomo::class, $pomo]);

        $pomo->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Pomo deleted successfully.',
            ], 200);
        }

        return redirect()->route('pomo.index')
            ->with('success', 'Pomo deleted successfully.');

    }
}
