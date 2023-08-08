<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePomoRequest;
use App\Http\Requests\UpdatePomoRequest;
use Illuminate\Auth\Access\AuthorizationException;
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
    public function index()
    {
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
//        $this->authorize('create', Pomo::class);

        // Convert due_start and end to unix timestamp and save
        $pomo = new Pomo();
        $pomo->todo_id = $request->todo_id;
        $pomo->pomo_start = strtotime($request->pomo_start);
        $pomo->pomo_end = strtotime($request->pomo_end);
        $pomo->notes = $request->notes;
        $pomo->save();

        return redirect()->route('pomo.index')
            ->with('success', 'Pomo created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pomo $pomo)
    {
        $this->authorize('view', $pomo);

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
//        $this->authorize('update', $pomo);

        // Convert due_start and end to unix timestamp and save
        $pomo->pomo_start = strtotime($request->pomo_start);
        $pomo->pomo_end = strtotime($request->pomo_end);
        $pomo->notes = $request->notes;
        $pomo->save();

        return redirect()->route('pomo.index')
            ->with('success', 'Pomo updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * @throws AuthorizationException
     */
    public function destroy(Pomo $pomo)
    {
        // Validate that the user is authorized to delete the pomo
//        $this->authorize('delete', $pomo);

        $pomo->delete();

        return redirect()->route('pomo.index')
            ->with('success', 'Pomo deleted successfully.');

    }
}
