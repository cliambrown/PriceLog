<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use Illuminate\Http\Request;

class EntryController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer|exists:items,id',
            'is_sale' => 'nullable',
            'location' => 'required|string',
            'notes' => 'nullable|string',
            'price' => 'required|numeric',
            'seen_on' => 'required|date_format:Y-m-d',
        ]);
        
        $entry = new Entry;
        $entry->item_id = $request->item_id;
        $entry->location = $request->location;
        $entry->is_sale = get_request_boolean($request->is_sale);
        $entry->notes = $request->notes;
        $entry->price = floatval($request->price);
        $entry->seen_on = $request->seen_on;
        $entry->save();
        
        return response()->json(['id' => $entry->id], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Entry $entry)
    {
        $request->validate([
            'is_sale' => 'nullable',
            'location' => 'required|string',
            'notes' => 'nullable|string',
            'price' => 'required|numeric',
            'seen_on' => 'required|date_format:Y-m-d',
        ]);
        
        $entry->location = $request->location;
        $entry->is_sale = get_request_boolean($request->is_sale);
        $entry->notes = $request->notes;
        $entry->price = floatval($request->price);
        $entry->seen_on = $request->seen_on;
        $entry->save();
        
        return response('Entry updated', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Entry $entry)
    {
        $entry->delete();
        return response('Entry deleted', 200);
    }
}
