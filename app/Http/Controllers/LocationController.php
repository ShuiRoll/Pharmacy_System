<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        return redirect()->to(route('items.index').'#locations');
    }

    public function create()
    {
        return view('locations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:locations',
        ]);

        Location::create($validated);

        return redirect()->to(route('items.index').'#locations')
            ->with('success', 'Location created successfully.');
    }

    public function edit(Location $location)
    {
        return view('locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:locations,name,' . $location->locationID . ',locationID',
        ]);

        $location->update($validated);

        return redirect()->to(route('items.index').'#locations')
            ->with('success', 'Location updated successfully.');
    }

    public function destroy(Location $location)
    {
        $location->delete();

        return redirect()->to(route('items.index').'#locations')
            ->with('success', 'Location deleted successfully.');
    }
}