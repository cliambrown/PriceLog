<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // This process doesn't look very Eloquent
        // But it gets the data into a nicer structure for Alpine ¯\_(ツ)_/¯
        
        $items = Item::orderBy('last_checked_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->keyBy('id')
            ->map(function ($item) {
                $item->entry_ids = [];
                return $item;
            });
        
        $itemIDs = $items->pluck('id');
        
        $entries = Entry::orderBy('price', 'asc')
            ->orderBy('seen_on', 'desc')
            ->whereIn('item_id', $itemIDs)
            ->get()
            ->keyBy('id');
        
        foreach ($entries as $entry) {
            $items[$entry->item_id]->entry_ids = array_merge($items[$entry->item_id]->entry_ids, [$entry->id]);
        }
        
        return view('item.index', [
            'items' => $items,
            'itemIDs' => $itemIDs,
            'entries' => $entries,
            'entryIDs' => $entries->pluck('id'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);
        
        $item = new Item;
        $item->name = $request->name;
        $item->last_checked_at = now();
        $item->save();
        
        return response()->json(['id' => $item->id], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        $request->validate([
            'name' => 'required|string',
        ]);
        
        $item->name = $request->name;
        $item->save();
        
        return response()->json(['id' => $item->id], 200);
    }
    
    public function updateLastCheckedAt(Request $request, Item $item) {
        $item->last_checked_at = now();
        $item->save();
        return response('Item updated', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        $item->entries()->delete();
        $item->delete();
        return response('Item deleted', 200);
    }
}
