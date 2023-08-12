<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePomoRequest;
use App\Http\Requests\UpdatePomoRequest;
use App\Http\Resources\PomoResource;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
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
     * Display a listing of the resource.
     */
    public function index(Request $request): PomoResource|Application|Factory|View
    {
        if ($request->expectsJson()){
            $todo = Todo::find($request->todo_id);
            $pomos = $todo->pomo()->paginate(4);

            return new PomoResource($pomos);

        }
        return view('pomo.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($todo_id = null)
    {
        $this->authorize('create', Pomo::class);

        return view('pomo.create', [
            'todo_id' => $todo_id,
            'pomo' => new Pomo(),
            'editing' => false,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePomoRequest $request)
    {
        $this->authorize('create', Pomo::class);

        // Convert due_start and end to unix timestamp and save
        $pomo = new Pomo();
        $pomo->todo_id = $request->safe()->todo_id;
        $pomo->pomo_start = strtotime($request->safe()->pomo_start);
        $pomo->pomo_end = strtotime($request->safe()->pomo_end);
        $pomo->notes = $request->safe()->notes;
        $pomo->save();

        if ($request->expectsJson()){
            return response()->json([
                'message' => 'Pomo created successfully.',
                'data' => $pomo,
            ], 201);
        }

        return redirect()->route('pomo.index')
            ->with('success', 'Pomo created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Pomo $pomo)
    {
        $this->authorize('view', $pomo);

        if ($request->expectsJson()){
            return response()->json([
                'message' => 'Pomo retrieved successfully.',
                'data' => $pomo,
            ], 200);
        }

        return view('pomo.show', compact('pomo'));
    }

    /**
     * Show the form for editing the specified resource.
     * @throws AuthorizationException
     */
    public function edit(Pomo $pomo)
    {
        $this->authorize('view', $pomo);

        $editing = true;

        return view('pomo.create', compact('pomo', 'editing'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePomoRequest $request, Pomo $pomo)
    {
        $this->authorize('update', $pomo);

        // Convert due_start and end to unix timestamp and save
        $pomo->pomo_start = strtotime($request->pomo_start);
        $pomo->pomo_end = strtotime($request->pomo_end);
        $pomo->notes = $request->notes;
        $pomo->save();

        if ($request->expectsJson()){
            return response()->json([
                'message' => 'Pomo updated successfully.',
                'data' => $pomo,
            ], 200);
        }

        return redirect()->route('pomo.index')
            ->with('success', 'Pomo updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * @throws AuthorizationException
     */
    public function destroy(Request $request, Pomo $pomo)
    {
        // Validate that the user is authorized to delete the pomo
        $this->authorize('delete', [Pomo::class, $pomo]);

        $pomo->delete();

        if ($request->expectsJson()){
            return response()->json([
                'message' => 'Pomo deleted successfully.',
            ], 200);
        }

        return redirect()->route('pomo.index')
            ->with('success', 'Pomo deleted successfully.');

    }
}
